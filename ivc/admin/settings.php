<?php
require_once __DIR__ . '/_init.php';

if (admin_posted()) {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($action === 'settings') {
        ivc_set_setting('bookings_enabled', isset($_POST['bookings_enabled']) ? '1' : '0');
        ivc_set_setting('directory_submissions_enabled', isset($_POST['directory_submissions_enabled']) ? '1' : '0');
        ivc_set_setting('site_notice', trim((string) $_POST['site_notice']));
        ivc_admin_log($adminUid, 'update_settings', 'settings', 'platform', '');
        admin_flash('ok', 'Platform settings saved.');
        admin_go('settings.php');
    }
    if ($action === 'add_admin') {
        $lookup = trim((string) $_POST['admin_lookup']);
        $note = trim((string) $_POST['note']);
        $uid = 0;
        if (ctype_digit($lookup)) {
            $asUid = (int) $lookup;
            $found = admin_one("SELECT uid FROM pi_account WHERE uid=? LIMIT 1", 'i', array($asUid));
            if ($found) {
                $uid = $asUid;
            } else {
                $per = str_pad($lookup, 10, '0', STR_PAD_LEFT);
                $fromPernum = admin_one("SELECT uid FROM pernum WHERE pernum=? LIMIT 1", 's', array($per));
                if ($fromPernum) {
                    $uid = (int) $fromPernum['uid'];
                }
            }
        } else {
            $found = admin_one("SELECT uid FROM pi_account WHERE email=? LIMIT 1", 's', array($lookup));
            if ($found) {
                $uid = (int) $found['uid'];
            }
        }
        if ($uid <= 0) {
            admin_flash('bad', 'No member found for that UID, account number, or email.');
            admin_go('settings.php');
        }
        admin_exec("INSERT IGNORE INTO ivc_admins (uid, added_at, added_by, note) VALUES (?, NOW(), ?, ?)", 'iis', array($uid, $adminUid, $note));
        ivc_admin_log($adminUid, 'grant_admin', 'admin', $uid, $note);
        admin_flash('ok', 'Admin access granted to UID ' . $uid . '.');
        admin_go('settings.php');
    }
    if ($action === 'remove_admin') {
        $uid = (int) $_POST['uid'];
        if ($uid === $adminUid) {
            admin_flash('bad', 'You cannot remove yourself.');
            admin_go('settings.php');
        }
        $left = admin_count("SELECT COUNT(*) c FROM ivc_admins");
        if ($left <= 1) {
            admin_flash('bad', 'Keep at least one admin.');
            admin_go('settings.php');
        }
        admin_exec("DELETE FROM ivc_admins WHERE uid=?", 'i', array($uid));
        ivc_admin_log($adminUid, 'revoke_admin', 'admin', $uid, '');
        admin_flash('ok', 'Admin removed.');
        admin_go('settings.php');
    }
}

$admins = admin_query(
    "SELECT ad.uid, ad.added_at, ad.note, a.email, p.fname, p.lname
     FROM ivc_admins ad
     LEFT JOIN pi_account a ON a.uid=ad.uid
     LEFT JOIN pi_profile p ON p.uid=ad.uid
     ORDER BY ad.uid ASC"
) ?: array();

admin_layout_start('Settings', 'settings');
?>
<div class="panel">
    <h2>Platform switches</h2>
    <form method="post" class="stack">
        <?php admin_csrf_field(); ?>
        <input type="hidden" name="action" value="settings">
        <p><label><input type="checkbox" name="bookings_enabled" value="1"<?= ivc_setting('bookings_enabled', '1') === '1' ? ' checked' : '' ?>> Members can submit resort booking requests</label></p>
        <p><label><input type="checkbox" name="directory_submissions_enabled" value="1"<?= ivc_setting('directory_submissions_enabled', '1') === '1' ? ' checked' : '' ?>> Industry partners can submit directory listings</label></p>
        <p><label>Site notice (shown on member home)</label>
            <textarea name="site_notice" rows="3"><?= admin_h(ivc_setting('site_notice')) ?></textarea>
        </p>
        <button class="btn" type="submit">Save settings</button>
    </form>
</div>
<div class="panel">
    <h2>Administrators</h2>
    <p class="muted">Anyone listed here can open this console after SafeZone login. Legacy UIDs 234601, 373764, and 1286402 always remain admins.</p>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>UID</th><th>Name</th><th>Email</th><th>Note</th><th>Added</th><th></th></tr>
            <?php foreach ($admins as $row): ?>
                <tr>
                    <td><a href="member.php?uid=<?= (int) $row['uid'] ?>"><?= (int) $row['uid'] ?></a></td>
                    <td><?= admin_h(trim($row['fname'] . ' ' . $row['lname'])) ?></td>
                    <td><?= admin_h($row['email']) ?></td>
                    <td><?= admin_h($row['note']) ?></td>
                    <td><?= admin_h($row['added_at']) ?></td>
                    <td>
                        <?php if ((int) $row['uid'] !== $adminUid): ?>
                        <form method="post" onsubmit="return confirm('Remove this admin?');">
                            <?php admin_csrf_field(); ?>
                            <input type="hidden" name="uid" value="<?= (int) $row['uid'] ?>">
                            <button class="btn btn-ghost" name="action" value="remove_admin">Remove</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <form method="post" class="form-grid" style="margin-top:16px;">
        <?php admin_csrf_field(); ?>
        <input type="hidden" name="action" value="add_admin">
        <div><label>UID, account #, or email</label><input type="text" name="admin_lookup" required></div>
        <div><label>Note</label><input type="text" name="note" placeholder="Operations, support…"></div>
        <div><button class="btn" type="submit">Grant admin</button></div>
    </form>
</div>
<?php admin_layout_end(); ?>
