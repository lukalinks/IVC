<?php
require_once __DIR__ . '/_init.php';

$types = ivc_business_types();
$status = isset($_GET['status']) ? $_GET['status'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$q = trim((string) (isset($_GET['q']) ? $_GET['q'] : ''));
$where = 'WHERE 1=1';
$bind = '';
$params = array();
if (in_array($status, array('pending', 'approved', 'rejected'), true)) {
    $where .= ' AND status=?';
    $bind .= 's';
    $params[] = $status;
}
if ($type !== '' && isset($types[$type])) {
    $where .= ' AND business_type=?';
    $bind .= 's';
    $params[] = $type;
}
if ($q !== '') {
    $like = '%' . $q . '%';
    $where .= ' AND (name LIKE ? OR email LIKE ? OR city LIKE ? OR country LIKE ?)';
    $bind .= 'ssss';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}
$total = admin_count("SELECT COUNT(*) c FROM ivc_listings $where", $bind, $params);
list($page, $per, $offset, $pages) = admin_page($total, 25);
$rows = admin_query("SELECT * FROM ivc_listings $where ORDER BY FIELD(status,'pending','approved','rejected'), id DESC LIMIT $per OFFSET $offset", $bind, $params) ?: array();

admin_layout_start('Partners', 'listings');
$qs = array('status' => $status, 'type' => $type, 'q' => $q);
?>
<div class="panel">
    <form class="toolbar" method="get">
        <input type="text" name="q" value="<?= admin_h($q) ?>" placeholder="Name, email, city">
        <select name="status">
            <option value="">All statuses</option>
            <option value="pending"<?= $status === 'pending' ? ' selected' : '' ?>>Pending</option>
            <option value="approved"<?= $status === 'approved' ? ' selected' : '' ?>>Approved</option>
            <option value="rejected"<?= $status === 'rejected' ? ' selected' : '' ?>>Rejected</option>
        </select>
        <select name="type">
            <option value="">All types</option>
            <?php foreach ($types as $key => $label): ?>
                <option value="<?= admin_h($key) ?>"<?= $type === $key ? ' selected' : '' ?>><?= admin_h($label) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn" type="submit">Filter</button>
        <a class="btn btn-ghost" href="listing.php">Add listing</a>
    </form>
    <p class="muted"><?= (int) $total ?> listing<?= $total === 1 ? '' : 's' ?></p>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>ID</th><th>Name</th><th>Type</th><th>Location</th><th>Email</th><th>Status</th><th></th></tr>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= admin_h($row['name']) ?></td>
                    <td><?= admin_h(ivc_listing_type_label($row['business_type'])) ?></td>
                    <td><?= admin_h(trim($row['city'] . ' ' . $row['country'])) ?></td>
                    <td><?= admin_h($row['email']) ?></td>
                    <td><span class="badge <?= admin_status_class($row['status']) ?>"><?= admin_h($row['status']) ?></span></td>
                    <td><a href="listing.php?id=<?= (int) $row['id'] ?>">Manage</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php admin_pager($page, $pages, $qs); ?>
</div>
<?php admin_layout_end(); ?>
