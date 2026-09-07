<?php
require_once __DIR__ . '/_init.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$row = admin_one("SELECT * FROM ivc_membership WHERE id=? LIMIT 1", 'i', array($id));
if (!$row) {
    admin_flash('bad', 'Membership not found.');
    admin_go('memberships.php');
}

if (admin_posted()) {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        admin_exec("DELETE FROM ivc_membership WHERE id=?", 'i', array($id));
        ivc_admin_log($adminUid, 'delete_membership', 'membership', $id, '');
        admin_flash('ok', 'Membership deleted.');
        admin_go('memberships.php');
    }
    $membership = trim((string) $_POST['membership']);
    $category = trim((string) $_POST['category']);
    $currency = trim((string) $_POST['currency']);
    $amount = (string) $_POST['amount'];
    $paid = in_array($_POST['paid_status'], array('paid', 'pending', 'unpaid'), true) ? $_POST['paid_status'] : 'pending';
    $auto = isset($_POST['auto_renew']) ? 1 : 0;
    $txn = trim((string) $_POST['txnid']);
    $datePayment = trim((string) $_POST['date_payment']);
    if ($datePayment === '') {
        $datePayment = $paid === 'paid' ? date('Y-m-d H:i:s') : null;
    }
    admin_exec(
        "UPDATE ivc_membership SET membership=?, category=?, currency=?, amount=?, paid_status=?, auto_renew=?, txnid=?, date_payment=? WHERE id=?",
        'sssssissi',
        array($membership, $category, $currency, $amount, $paid, $auto, $txn, $datePayment, $id)
    );
    ivc_admin_log($adminUid, 'update_membership', 'membership', $id, $paid);
    admin_flash('ok', 'Membership saved.');
    admin_go('membership.php?id=' . $id);
}

admin_layout_start('Membership #' . $id, 'memberships');
?>
<p><a href="memberships.php">← All memberships</a> · <a href="member.php?uid=<?= (int) $row['uid'] ?>">Member <?= (int) $row['uid'] ?></a></p>
<div class="panel">
    <form method="post" class="form-grid">
        <?php admin_csrf_field(); ?>
        <div><label>Level</label><input type="text" name="membership" value="<?= admin_h($row['membership']) ?>"></div>
        <div><label>Category</label><input type="text" name="category" value="<?= admin_h($row['category']) ?>"></div>
        <div><label>Currency</label><input type="text" name="currency" value="<?= admin_h($row['currency']) ?>"></div>
        <div><label>Amount</label><input type="text" name="amount" value="<?= admin_h($row['amount']) ?>"></div>
        <div>
            <label>Status</label>
            <select name="paid_status">
                <?php foreach (array('paid', 'pending', 'unpaid') as $st): ?>
                    <option value="<?= $st ?>"<?= $row['paid_status'] === $st ? ' selected' : '' ?>><?= ucfirst($st) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div><label>Payment date</label><input type="text" name="date_payment" value="<?= admin_h($row['date_payment']) ?>" placeholder="YYYY-MM-DD HH:MM:SS"></div>
        <div><label>Transaction ID</label><input type="text" name="txnid" value="<?= admin_h($row['txnid']) ?>"></div>
        <div><label><input type="checkbox" name="auto_renew" value="1"<?= (int) $row['auto_renew'] === 1 ? ' checked' : '' ?>> Auto renew</label></div>
        <div><button class="btn" type="submit">Save</button></div>
    </form>
    <form method="post" onsubmit="return confirm('Delete this membership record?');" style="margin-top:12px;">
        <?php admin_csrf_field(); ?>
        <button class="btn btn-bad" name="action" value="delete">Delete</button>
    </form>
</div>
<?php admin_layout_end(); ?>
