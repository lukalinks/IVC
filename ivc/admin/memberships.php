<?php
require_once __DIR__ . '/_init.php';

if (admin_posted() && isset($_POST['create'])) {
    $uid = (int) $_POST['uid'];
    $membership = trim((string) $_POST['membership']);
    $category = trim((string) $_POST['category']);
    $currency = trim((string) $_POST['currency']);
    $amount = (string) $_POST['amount'];
    $paid = in_array($_POST['paid_status'], array('paid', 'pending', 'unpaid'), true) ? $_POST['paid_status'] : 'pending';
    if ($uid <= 0) {
        admin_flash('bad', 'Enter a member UID.');
        admin_go('memberships.php');
    }
    admin_exec(
        "INSERT INTO ivc_membership (uid, category, membership, currency, amount, date_added, date_payment, hash, paid_status, auto_renew, txnid) VALUES (?, ?, ?, ?, ?, NOW(), IF(?='paid', NOW(), NULL), '', ?, 0, '')",
        'issssss',
        array($uid, $category, $membership, $currency, $amount, $paid, $paid)
    );
    ivc_admin_log($adminUid, 'create_membership', 'membership', $GLOBALS['mysqli']->insert_id, $uid . ' ' . $membership);
    admin_flash('ok', 'Membership record created.');
    admin_go('memberships.php');
}

$q = trim((string) (isset($_GET['q']) ? $_GET['q'] : ''));
$status = isset($_GET['status']) ? $_GET['status'] : '';
$where = 'WHERE 1=1';
$types = '';
$params = array();
if ($status !== '') {
    $where .= ' AND m.paid_status=?';
    $types .= 's';
    $params[] = $status;
}
if ($q !== '') {
    $where .= ' AND (m.uid=? OR a.email LIKE ?)';
    $types .= 'is';
    $params[] = ctype_digit($q) ? (int) $q : 0;
    $params[] = '%' . $q . '%';
}
$total = admin_count("SELECT COUNT(*) c FROM ivc_membership m LEFT JOIN pi_account a ON a.uid=m.uid $where", $types, $params);
list($page, $per, $offset, $pages) = admin_page($total, 25);
$rows = admin_query(
    "SELECT m.*, a.email FROM ivc_membership m LEFT JOIN pi_account a ON a.uid=m.uid $where ORDER BY m.id DESC LIMIT $per OFFSET $offset",
    $types,
    $params
) ?: array();

admin_layout_start('Memberships', 'memberships');
?>
<div class="panel">
    <form class="toolbar" method="get">
        <input type="text" name="q" value="<?= admin_h($q) ?>" placeholder="UID or email">
        <select name="status">
            <option value="">All</option>
            <option value="paid"<?= $status === 'paid' ? ' selected' : '' ?>>Paid</option>
            <option value="pending"<?= $status === 'pending' ? ' selected' : '' ?>>Pending</option>
            <option value="unpaid"<?= $status === 'unpaid' ? ' selected' : '' ?>>Unpaid</option>
        </select>
        <button class="btn" type="submit">Filter</button>
    </form>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>ID</th><th>Member</th><th>Level</th><th>Amount</th><th>Paid</th><th>Payment date</th><th></th></tr>
            <?php if (!$rows): ?><tr><td colspan="7" class="muted">No membership records.</td></tr><?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><a href="member.php?uid=<?= (int) $row['uid'] ?>"><?= admin_h($row['email'] ?: $row['uid']) ?></a></td>
                    <td><?= admin_h($row['membership'] . ' / ' . $row['category']) ?></td>
                    <td><?= admin_h($row['amount'] . ' ' . $row['currency']) ?></td>
                    <td><span class="badge <?= admin_status_class($row['paid_status']) ?>"><?= admin_h($row['paid_status']) ?></span></td>
                    <td><?= admin_h($row['date_payment']) ?></td>
                    <td><a href="membership.php?id=<?= (int) $row['id'] ?>">Manage</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php admin_pager($page, $pages, array('q' => $q, 'status' => $status)); ?>
</div>
<div class="panel">
    <h2>Add membership</h2>
    <form method="post" class="form-grid">
        <?php admin_csrf_field(); ?>
        <input type="hidden" name="create" value="1">
        <div><label>Member UID</label><input type="number" name="uid" required></div>
        <div>
            <label>Level</label>
            <select name="membership">
                <option value="3">3***</option>
                <option value="4">4****</option>
                <option value="5">5*****</option>
                <option value="vip">VIP LUXURY</option>
            </select>
        </div>
        <div>
            <label>Category</label>
            <select name="category">
                <option value="single">Single</option>
                <option value="couple">Couple</option>
                <option value="family">Family</option>
            </select>
        </div>
        <div><label>Currency</label><input type="text" name="currency" value="USD"></div>
        <div><label>Amount</label><input type="text" name="amount" value="0"></div>
        <div>
            <label>Status</label>
            <select name="paid_status">
                <option value="paid">Paid</option>
                <option value="pending">Pending</option>
                <option value="unpaid">Unpaid</option>
            </select>
        </div>
        <div><button class="btn" type="submit">Create</button></div>
    </form>
</div>
<?php admin_layout_end(); ?>
