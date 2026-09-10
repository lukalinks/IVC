<?php
require_once __DIR__ . '/_init.php';

$statuses = ivc_booking_statuses();
$q = trim((string) (isset($_GET['q']) ? $_GET['q'] : ''));
$status = isset($_GET['status']) ? $_GET['status'] : '';
$where = 'WHERE 1=1';
$types = '';
$params = array();
if ($status !== '' && isset($statuses[$status])) {
    if ($status === 'pending') {
        $where .= " AND (b.status='pending' OR b.status='' OR b.status IS NULL)";
    } else {
        $where .= ' AND b.status=?';
        $types .= 's';
        $params[] = $status;
    }
}
if ($q !== '') {
    $like = '%' . $q . '%';
    $where .= ' AND (b.email LIKE ? OR b.resort LIKE ? OR b.uid=?)';
    $types .= 'ssi';
    $params[] = $like;
    $params[] = $like;
    $params[] = ctype_digit($q) ? (int) $q : 0;
}

$total = admin_count("SELECT COUNT(*) c FROM ivc_bookings b $where", $types, $params);
list($page, $per, $offset, $pages) = admin_page($total, 25);

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=ivc-bookings.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, array('id', 'date', 'uid', 'email', 'resort', 'guests', 'arrival', 'nights', 'accommodation', 'restaurant', 'status'));
    $all = admin_query("SELECT * FROM ivc_bookings b $where ORDER BY b.id DESC LIMIT 5000", $types, $params) ?: array();
    foreach ($all as $row) {
        fputcsv($out, array($row['id'], $row['date'], $row['uid'], $row['email'], $row['resort'], $row['no_of_guests'], $row['arrival_date'], $row['no_of_nights'], $row['accomodation'], $row['restaurant'], $row['status']));
    }
    fclose($out);
    exit;
}

$rows = admin_query(
    "SELECT b.* FROM ivc_bookings b $where ORDER BY b.id DESC LIMIT $per OFFSET $offset",
    $types,
    $params
) ?: array();

admin_layout_start('Resort bookings', 'bookings');
$qs = array('q' => $q, 'status' => $status);
?>
<div class="panel">
    <form class="toolbar" method="get">
        <input type="text" name="q" value="<?= admin_h($q) ?>" placeholder="Email, resort, or UID">
        <select name="status">
            <option value="">All statuses</option>
            <?php foreach ($statuses as $key => $label): ?>
                <option value="<?= admin_h($key) ?>"<?= $status === $key ? ' selected' : '' ?>><?= admin_h($label) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn" type="submit">Filter</button>
        <a class="btn btn-ghost" href="bookings.php?<?= admin_h(http_build_query($qs + array('export' => 'csv'))) ?>">Export CSV</a>
    </form>
    <p class="muted"><?= (int) $total ?> booking<?= $total === 1 ? '' : 's' ?></p>
    <div class="table-wrap">
        <table class="admin">
            <tr>
                <th>ID</th><th>Requested</th><th>Member</th><th>Resort</th><th>Arrival</th><th>Nights</th><th>Guests</th><th>Status</th><th></th>
            </tr>
            <?php if (!$rows): ?><tr><td colspan="9" class="muted">No bookings match.</td></tr><?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= admin_h($row['date']) ?></td>
                    <td><a href="member.php?uid=<?= (int) $row['uid'] ?>"><?= admin_h($row['email']) ?></a></td>
                    <td><?= admin_h($row['resort']) ?></td>
                    <td><?= admin_h($row['arrival_date']) ?></td>
                    <td><?= (int) $row['no_of_nights'] ?></td>
                    <td><?= (int) $row['no_of_guests'] ?></td>
                    <td><span class="badge <?= admin_status_class($row['status']) ?>"><?= admin_h($row['status'] ?: 'pending') ?></span></td>
                    <td><a href="booking.php?id=<?= (int) $row['id'] ?>">Manage</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php admin_pager($page, $pages, $qs); ?>
</div>
<?php admin_layout_end(); ?>
