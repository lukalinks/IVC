<?php
require_once __DIR__ . '/_init.php';

$uid = isset($_GET['uid']) ? (int) $_GET['uid'] : 0;
$member = admin_one(
    "SELECT a.*, p.fname, p.lname, p.mname, p.city, p.state, p.zip, p.phone, p.address, p.dob
     FROM pi_account a LEFT JOIN pi_profile p ON p.uid=a.uid WHERE a.uid=? LIMIT 1",
    'i',
    array($uid)
);
if (!$member) {
    admin_flash('bad', 'Member not found.');
    admin_go('members.php');
}

$newPin = isset($_SESSION['admin_reset_pin']) ? $_SESSION['admin_reset_pin'] : null;
unset($_SESSION['admin_reset_pin']);

if (admin_posted()) {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($action === 'save') {
        $email = trim((string) $_POST['email']);
        $username = trim((string) $_POST['username']);
        $fname = trim((string) $_POST['fname']);
        $lname = trim((string) $_POST['lname']);
        $city = trim((string) $_POST['city']);
        $phone = trim((string) $_POST['phone']);
        $blockedMsg = trim((string) $_POST['blocked_msg']);
        admin_exec("UPDATE pi_account SET email=?, username=?, blocked_msg=? WHERE uid=?", 'sssi', array($email, $username, $blockedMsg, $uid));
        $profile = admin_one("SELECT uid FROM pi_profile WHERE uid=? LIMIT 1", 'i', array($uid));
        if ($profile) {
            admin_exec("UPDATE pi_profile SET fname=?, lname=?, city=?, phone=? WHERE uid=?", 'ssssi', array($fname, $lname, $city, $phone, $uid));
        } else {
            admin_exec("INSERT INTO pi_profile (uid, fname, lname, city, phone, gender, address, state, zip, dob, proof_file, mname) VALUES (?, ?, ?, ?, ?, 1, '', '', '', '1970-01-01', '', '')", 'issss', array($uid, $fname, $lname, $city, $phone));
        }
        ivc_admin_log($adminUid, 'update_member', 'member', $uid, $email);
        admin_flash('ok', 'Member profile saved.');
        admin_go('member.php?uid=' . $uid);
    }
    if ($action === 'password') {
        $password = (string) $_POST['password'];
        if (strlen($password) < 6) {
            admin_flash('bad', 'Password must be at least 6 characters.');
        } else {
            $hash = md5($password);
            admin_exec("UPDATE pi_account SET password=? WHERE uid=?", 'si', array($hash, $uid));
            ivc_admin_log($adminUid, 'reset_password', 'member', $uid, '');
            admin_flash('ok', 'Password updated.');
        }
        admin_go('member.php?uid=' . $uid);
    }
    if ($action === 'pin') {
        $pinPlain = str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        $pinEnc = ivc_encrypt_pin($pinPlain);
        admin_exec("UPDATE pi_account SET pin=?, pin_tries=0 WHERE uid=?", 'si', array($pinEnc, $uid));
        ivc_admin_log($adminUid, 'reset_pin', 'member', $uid, '');
        $_SESSION['admin_reset_pin'] = $pinPlain;
        admin_flash('ok', 'A new Master PIN was generated.');
        admin_go('member.php?uid=' . $uid);
    }
    if ($action === 'clear_pin_tries') {
        admin_exec("UPDATE pi_account SET pin_tries=0 WHERE uid=?", 'i', array($uid));
        ivc_admin_log($adminUid, 'clear_pin_tries', 'member', $uid, '');
        admin_flash('ok', 'PIN attempts reset.');
        admin_go('member.php?uid=' . $uid);
    }
    if ($action === 'block') {
        admin_exec("UPDATE pi_account SET blocked=1 WHERE uid=?", 'i', array($uid));
        ivc_admin_log($adminUid, 'block_member', 'member', $uid, '');
        admin_flash('ok', 'Account blocked.');
        admin_go('member.php?uid=' . $uid);
    }
    if ($action === 'unblock') {
        admin_exec("UPDATE pi_account SET blocked=0 WHERE uid=?", 'i', array($uid));
        ivc_admin_log($adminUid, 'unblock_member', 'member', $uid, '');
        admin_flash('ok', 'Account unblocked.');
        admin_go('member.php?uid=' . $uid);
    }
    if ($action === 'ban') {
        admin_exec("INSERT IGNORE INTO banned_users (uid) VALUES (?)", 'i', array($uid));
        admin_exec("UPDATE pi_account SET blocked=1 WHERE uid=?", 'i', array($uid));
        ivc_admin_log($adminUid, 'ban_member', 'member', $uid, '');
        admin_flash('ok', 'Account banned from login.');
        admin_go('member.php?uid=' . $uid);
    }
    if ($action === 'unban') {
        admin_exec("DELETE FROM banned_users WHERE uid=?", 'i', array($uid));
        ivc_admin_log($adminUid, 'unban_member', 'member', $uid, '');
        admin_flash('ok', 'Ban removed.');
        admin_go('member.php?uid=' . $uid);
    }
    if ($action === 'delete') {
        admin_exec("UPDATE pi_account SET deleted=1 WHERE uid=?", 'i', array($uid));
        ivc_admin_log($adminUid, 'delete_member', 'member', $uid, '');
        admin_flash('ok', 'Account marked deleted.');
        admin_go('member.php?uid=' . $uid);
    }
    if ($action === 'restore') {
        admin_exec("UPDATE pi_account SET deleted=0 WHERE uid=?", 'i', array($uid));
        ivc_admin_log($adminUid, 'restore_member', 'member', $uid, '');
        admin_flash('ok', 'Account restored.');
        admin_go('member.php?uid=' . $uid);
    }
    if ($action === 'make_admin') {
        admin_exec("INSERT IGNORE INTO ivc_admins (uid, added_at, added_by, note) VALUES (?, NOW(), ?, 'Granted from member page')", 'ii', array($uid, $adminUid));
        ivc_admin_log($adminUid, 'grant_admin', 'member', $uid, '');
        admin_flash('ok', 'This member can now open the admin console.');
        admin_go('member.php?uid=' . $uid);
    }
    if ($action === 'remove_admin') {
        if ($uid === $adminUid) {
            admin_flash('bad', 'You cannot remove your own admin access here.');
        } else {
            admin_exec("DELETE FROM ivc_admins WHERE uid=?", 'i', array($uid));
            ivc_admin_log($adminUid, 'revoke_admin', 'member', $uid, '');
            admin_flash('ok', 'Admin access removed.');
        }
        admin_go('member.php?uid=' . $uid);
    }
}

$banned = admin_one("SELECT uid FROM banned_users WHERE uid=? LIMIT 1", 'i', array($uid));
$isAdminUser = ivc_is_admin($uid);
$bookings = admin_query("SELECT id, resort, arrival_date, status, date FROM ivc_bookings WHERE uid=? ORDER BY id DESC LIMIT 20", 'i', array($uid)) ?: array();
$memberships = admin_query("SELECT id, membership, category, amount, currency, paid_status, date_payment FROM ivc_membership WHERE uid=? ORDER BY id DESC LIMIT 20", 'i', array($uid)) ?: array();
$listings = admin_query("SELECT id, name, status, business_type FROM ivc_listings WHERE uid=? ORDER BY id DESC LIMIT 20", 'i', array($uid)) ?: array();

admin_layout_start('Member ' . $uid, 'members');
?>
<p><a href="members.php">← All members</a></p>
<?php if ($newPin): ?>
<div class="flash flash-wait">New Master PIN: <strong><?= admin_h($newPin) ?></strong> (shown once).</div>
<?php endif; ?>

<div class="panel">
    <h2>Profile</h2>
    <p class="muted">Account # <?= admin_h(ivc_account_number($uid)) ?>
        · <?= $isAdminUser ? 'Administrator' : 'Member' ?>
        · PIN tries <?= (int) $member['pin_tries'] ?>
        <?= $banned ? ' · Banned' : '' ?>
    </p>
    <form method="post" class="form-grid">
        <?php admin_csrf_field(); ?>
        <input type="hidden" name="action" value="save">
        <div><label>Email</label><input type="email" name="email" value="<?= admin_h($member['email']) ?>" required></div>
        <div><label>Username</label><input type="text" name="username" value="<?= admin_h($member['username']) ?>"></div>
        <div><label>First name</label><input type="text" name="fname" value="<?= admin_h($member['fname']) ?>"></div>
        <div><label>Last name</label><input type="text" name="lname" value="<?= admin_h($member['lname']) ?>"></div>
        <div><label>City</label><input type="text" name="city" value="<?= admin_h($member['city']) ?>"></div>
        <div><label>Phone</label><input type="text" name="phone" value="<?= admin_h($member['phone']) ?>"></div>
        <div class="span-2"><label>Blocked login message</label><input type="text" name="blocked_msg" value="<?= admin_h($member['blocked_msg']) ?>"></div>
        <div><button class="btn" type="submit">Save profile</button></div>
    </form>
</div>

<div class="panel">
    <h2>Access</h2>
    <form method="post" class="toolbar">
        <?php admin_csrf_field(); ?>
        <input type="password" name="password" placeholder="New password" minlength="6">
        <button class="btn" name="action" value="password">Set password</button>
        <button class="btn btn-wait" name="action" value="pin">Generate new PIN</button>
        <button class="btn btn-ghost" name="action" value="clear_pin_tries">Clear PIN tries</button>
    </form>
    <form method="post" class="toolbar" onsubmit="return confirm('Apply this account action?');">
        <?php admin_csrf_field(); ?>
        <?php if ((int) $member['blocked'] === 1): ?>
            <button class="btn btn-ok" name="action" value="unblock">Unblock</button>
        <?php else: ?>
            <button class="btn btn-bad" name="action" value="block">Block</button>
        <?php endif; ?>
        <?php if ($banned): ?>
            <button class="btn btn-ok" name="action" value="unban">Remove ban</button>
        <?php else: ?>
            <button class="btn btn-bad" name="action" value="ban">Ban from login</button>
        <?php endif; ?>
        <?php if ((int) $member['deleted'] === 1): ?>
            <button class="btn btn-ok" name="action" value="restore">Restore</button>
        <?php else: ?>
            <button class="btn btn-ghost" name="action" value="delete">Mark deleted</button>
        <?php endif; ?>
        <?php if ($isAdminUser): ?>
            <button class="btn btn-ghost" name="action" value="remove_admin">Remove admin</button>
        <?php else: ?>
            <button class="btn" name="action" value="make_admin">Grant admin</button>
        <?php endif; ?>
    </form>
</div>

<div class="panel">
    <h2>Bookings</h2>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>ID</th><th>Resort</th><th>Arrival</th><th>Status</th><th></th></tr>
            <?php if (!$bookings): ?><tr><td colspan="5" class="muted">None</td></tr><?php endif; ?>
            <?php foreach ($bookings as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= admin_h($row['resort']) ?></td>
                    <td><?= admin_h($row['arrival_date']) ?></td>
                    <td><span class="badge <?= admin_status_class($row['status']) ?>"><?= admin_h($row['status'] ?: 'pending') ?></span></td>
                    <td><a href="booking.php?id=<?= (int) $row['id'] ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<div class="panel">
    <h2>Memberships</h2>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>ID</th><th>Level</th><th>Amount</th><th>Status</th><th></th></tr>
            <?php if (!$memberships): ?><tr><td colspan="5" class="muted">None</td></tr><?php endif; ?>
            <?php foreach ($memberships as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= admin_h($row['membership'] . ' / ' . $row['category']) ?></td>
                    <td><?= admin_h($row['amount'] . ' ' . $row['currency']) ?></td>
                    <td><?= admin_h($row['paid_status']) ?></td>
                    <td><a href="membership.php?id=<?= (int) $row['id'] ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<div class="panel">
    <h2>Partner listings</h2>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>ID</th><th>Name</th><th>Status</th><th></th></tr>
            <?php if (!$listings): ?><tr><td colspan="4" class="muted">None</td></tr><?php endif; ?>
            <?php foreach ($listings as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= admin_h($row['name']) ?></td>
                    <td><?= admin_h($row['status']) ?></td>
                    <td><a href="listing.php?id=<?= (int) $row['id'] ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php admin_layout_end(); ?>
