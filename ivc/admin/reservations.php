<?php
require_once __DIR__ . '/_init.php';

if (admin_posted() && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int) $_POST['id'];
    admin_exec("DELETE FROM ivc_reservations WHERE id=?", 'i', array($id));
    ivc_admin_log($adminUid, 'delete_reservation', 'reservation', $id, '');
    admin_flash('ok', 'Reservation deleted.');
    admin_go('reservations.php');
}

$q = trim((string) (isset($_GET['q']) ? $_GET['q'] : ''));
$where = 'WHERE 1=1';
$types = '';
$params = array();
if ($q !== '') {
    $where .= ' AND (r.uid=? OR r.vid=? OR a.email LIKE ?)';
    $types .= 'iis';
    $qid = ctype_digit($q) ? (int) $q : 0;
    $params[] = $qid;
    $params[] = $qid;
    $params[] = '%' . $q . '%';
}
$total = admin_count("SELECT COUNT(*) c FROM ivc_reservations r LEFT JOIN pi_account a ON a.uid=r.uid $where", $types, $params);
list($page, $per, $offset, $pages) = admin_page($total, 25);
$rows = admin_query(
    "SELECT r.*, a.email, v.title1 FROM ivc_reservations r
     LEFT JOIN pi_account a ON a.uid=r.uid
     LEFT JOIN ivc_vacations v ON v.id=r.vid
     $where ORDER BY r.id DESC LIMIT $per OFFSET $offset",
    $types,
    $params
) ?: array();

admin_layout_start('Reservations', 'reservations');
?>
<div class="panel">
    <form class="toolbar" method="get">
        <input type="text" name="q" value="<?= admin_h($q) ?>" placeholder="UID, vacation ID, or email">
        <button class="btn" type="submit">Search</button>
    </form>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>ID</th><th>Member</th><th>Vacation</th><th>Qty</th><th>TVC</th><th>Date</th><th>Target</th><th></th></tr>
            <?php if (!$rows): ?><tr><td colspan="8" class="muted">No reservations.</td></tr><?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><a href="member.php?uid=<?= (int) $row['uid'] ?>"><?= admin_h($row['email'] ?: $row['uid']) ?></a></td>
                    <td><a href="vacation.php?id=<?= (int) $row['vid'] ?>"><?= admin_h($row['title1'] ?: ('#' . $row['vid'])) ?></a></td>
                    <td><?= (int) $row['qty'] ?></td>
                    <td><?= admin_h($row['tvc']) ?></td>
                    <td><?= admin_h($row['date']) ?></td>
                    <td><?= admin_h($row['target'] . ' / ' . $row['target_date']) ?></td>
                    <td>
                        <form method="post" onsubmit="return confirm('Delete this reservation?');">
                            <?php admin_csrf_field(); ?>
                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                            <button class="btn btn-ghost" name="action" value="delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php admin_pager($page, $pages, array('q' => $q)); ?>
</div>
<?php admin_layout_end(); ?>
