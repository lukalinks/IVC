<?php
require_once __DIR__ . '/_init.php';

$createdPin = isset($_SESSION['admin_new_pin']) ? $_SESSION['admin_new_pin'] : null;
unset($_SESSION['admin_new_pin']);

if (admin_posted() && isset($_POST['create_member'])) {
    $email = trim((string) $_POST['email']);
    $username = trim((string) $_POST['username']);
    $password = (string) $_POST['password'];
    $fname = trim((string) $_POST['fname']);
    $lname = trim((string) $_POST['lname']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        admin_flash('bad', 'Enter a valid email address.');
        admin_go('members.php');
    }
    if (strlen($password) < 6) {
        admin_flash('bad', 'Password must be at least 6 characters.');
        admin_go('members.php');
    }
    $exists = admin_one("SELECT uid FROM pi_account WHERE email=? LIMIT 1", 's', array($email));
    if ($exists) {
        admin_flash('bad', 'That email is already in use.');
        admin_go('members.php');
    }
    if ($username === '') {
        $username = preg_replace('/[^a-z0-9]+/i', '', strtok($email, '@'));
        if ($username === '') {
            $username = 'member' . random_int(1000, 9999);
        }
    }
    $url = substr(bin2hex(random_bytes(8)), 0, 10);
    while (admin_one("SELECT uid FROM pi_account WHERE url=? LIMIT 1", 's', array($url))) {
        $url = substr(bin2hex(random_bytes(8)), 0, 10);
    }
    $hash = md5($password);
    $now = time();
    $pinPlain = str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
    $pinEnc = ivc_encrypt_pin($pinPlain);
    $ip = GetIP();
    $ok = admin_exec(
        "INSERT INTO pi_account (email, password, username, url, active, ip, created, signup, pre_reg, migrated, invitedby, verified, code, tmp_pwd, tmp_time, terms, blocked_msg, blocked_notes, deal_factor, deal_points, fupcheck, blocked, permissions, deleted, pidev_admin, factor, psm, psa, setup, member_count, last_login, signature, email2, unicorn_status, unicorn_level, unicorn_date, pin, invitedby_old, ether_wallet, member_status, terms_agree, website, free_yem, pin_tries, last_try, encrypted)
         VALUES (?, ?, ?, ?, 1, ?, ?, ?, 0, 0, '', 0, '', '', 0, 1, '', '', 0, 0, 0, 0, '{}', 0, 0, 0, 0, 0, '', 0, 0, '', '', 'BASIC', 'NEWBIE UNICORN', '0000-00-00 00:00:00', ?, '', '', '', 1, '', 0, 0, 0, 0)",
        'sssssiis',
        array($email, $hash, $username, $url, $ip, $now, $now, $pinEnc)
    );
    if (!$ok) {
        admin_flash('bad', 'Could not create the member. Check required account fields.');
        admin_go('members.php');
    }
    $uid = (int) $GLOBALS['mysqli']->insert_id;
    admin_exec(
        "INSERT INTO pi_profile (uid, fname, lname, gender, address, city, state, zip, phone, dob, proof_file, mname, about_me, business_name, address2)
         VALUES (?, ?, ?, 1, '', '', '', '', '', '1970-01-01', '', '', '', '', '')",
        'iss',
        array($uid, $fname, $lname)
    );
    $pernum = str_pad((string) ($uid + 1000000000), 10, '0', STR_PAD_LEFT);
    admin_exec("INSERT IGNORE INTO pernum (pernum, uid) VALUES (?, ?)", 'si', array($pernum, $uid));
    ivc_admin_log($adminUid, 'create_member', 'member', $uid, $email);
    $_SESSION['admin_new_pin'] = array('uid' => $uid, 'pernum' => $pernum, 'pin' => $pinPlain);
    admin_flash('ok', 'Member created. Save the account number and PIN shown below; the PIN is not shown again.');
    admin_go('members.php');
}

$q = trim((string) (isset($_GET['q']) ? $_GET['q'] : ''));
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'active';
$where = 'WHERE 1=1';
$types = '';
$params = array();
if ($filter === 'blocked') {
    $where .= ' AND a.blocked=1 AND a.deleted=0';
} elseif ($filter === 'deleted') {
    $where .= ' AND a.deleted=1';
} else {
    $where .= ' AND a.deleted=0';
}
if ($q !== '') {
    $like = '%' . $q . '%';
    $where .= ' AND (a.email LIKE ? OR a.username LIKE ? OR a.uid=? OR EXISTS (SELECT 1 FROM pernum pn WHERE pn.uid=a.uid AND pn.pernum LIKE ?))';
    $types .= 'siss';
    $uidExact = ctype_digit($q) ? (int) $q : 0;
    $params[] = $like;
    $params[] = $like;
    $params[] = $uidExact;
    $params[] = $like;
}

$total = admin_count("SELECT COUNT(*) c FROM pi_account a $where", $types, $params);
list($page, $per, $offset, $pages) = admin_page($total, 25);
$rows = admin_query(
    "SELECT a.uid, a.email, a.username, a.blocked, a.deleted, a.created, a.last_login, p.fname, p.lname
     FROM pi_account a LEFT JOIN pi_profile p ON p.uid=a.uid
     $where ORDER BY a.uid DESC LIMIT $per OFFSET $offset",
    $types,
    $params
) ?: array();

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=ivc-members.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, array('uid', 'account', 'email', 'username', 'name', 'blocked', 'deleted', 'created'));
    $all = admin_query(
        "SELECT a.uid, a.email, a.username, a.blocked, a.deleted, a.created, p.fname, p.lname
         FROM pi_account a LEFT JOIN pi_profile p ON p.uid=a.uid $where ORDER BY a.uid DESC LIMIT 5000",
        $types,
        $params
    ) ?: array();
    foreach ($all as $row) {
        fputcsv($out, array(
            $row['uid'],
            ivc_account_number($row['uid']),
            $row['email'],
            $row['username'],
            trim($row['fname'] . ' ' . $row['lname']),
            $row['blocked'],
            $row['deleted'],
            $row['created'] ? date('Y-m-d H:i:s', (int) $row['created']) : '',
        ));
    }
    fclose($out);
    exit;
}

admin_layout_start('Members', 'members');
$qs = array('q' => $q, 'filter' => $filter);
?>
<?php if ($createdPin): ?>
<div class="flash flash-wait">
    New member UID <?= (int) $createdPin['uid'] ?>.
    Account # <strong><?= admin_h($createdPin['pernum']) ?></strong>.
    Master PIN <strong><?= admin_h($createdPin['pin']) ?></strong> (shown once).
</div>
<?php endif; ?>

<div class="panel">
    <h2>Search</h2>
    <form class="toolbar" method="get">
        <input type="text" name="q" value="<?= admin_h($q) ?>" placeholder="Email, username, UID, or account #">
        <select name="filter">
            <option value="active"<?= $filter === 'active' ? ' selected' : '' ?>>Active</option>
            <option value="blocked"<?= $filter === 'blocked' ? ' selected' : '' ?>>Blocked</option>
            <option value="deleted"<?= $filter === 'deleted' ? ' selected' : '' ?>>Deleted</option>
        </select>
        <button class="btn" type="submit">Search</button>
        <a class="btn btn-ghost" href="members.php?<?= admin_h(http_build_query($qs + array('export' => 'csv'))) ?>">Export CSV</a>
    </form>
    <p class="muted"><?= (int) $total ?> member<?= $total === 1 ? '' : 's' ?></p>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>UID</th><th>Account #</th><th>Name</th><th>Email</th><th>Status</th><th></th></tr>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int) $row['uid'] ?></td>
                    <td><?= admin_h(ivc_account_number($row['uid'])) ?></td>
                    <td><?= admin_h(trim($row['fname'] . ' ' . $row['lname'])) ?></td>
                    <td><?= admin_h($row['email']) ?></td>
                    <td>
                        <?php if ((int) $row['deleted'] === 1): ?>
                            <span class="badge bad">Deleted</span>
                        <?php elseif ((int) $row['blocked'] === 1): ?>
                            <span class="badge bad">Blocked</span>
                        <?php else: ?>
                            <span class="badge ok">Active</span>
                        <?php endif; ?>
                    </td>
                    <td><a href="member.php?uid=<?= (int) $row['uid'] ?>">Manage</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php admin_pager($page, $pages, $qs); ?>
</div>

<div class="panel">
    <h2>Create member</h2>
    <form method="post" class="form-grid">
        <?php admin_csrf_field(); ?>
        <input type="hidden" name="create_member" value="1">
        <div><label>Email</label><input type="email" name="email" required></div>
        <div><label>Username</label><input type="text" name="username"></div>
        <div><label>Password</label><input type="text" name="password" required minlength="6"></div>
        <div><label>First name</label><input type="text" name="fname"></div>
        <div><label>Last name</label><input type="text" name="lname"></div>
        <div><button class="btn" type="submit">Create account</button></div>
    </form>
</div>
<?php admin_layout_end(); ?>
