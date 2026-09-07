<?php
require_once __DIR__ . '/_init.php';

if (admin_posted() && isset($_POST['create'])) {
    $title1 = trim((string) $_POST['title1']);
    $title2 = trim((string) $_POST['title2']);
    $seats = (int) $_POST['total_seats'];
    $usd = (string) $_POST['usd_value'];
    $trip = trim((string) $_POST['trip_date']);
    admin_exec(
        "INSERT INTO ivc_vacations (title1, title2, title3, title4, description, trip_date, total_seats, usd_value, tvc_price, member_type, per_account, target_value, closed) VALUES (?, ?, '', '', '', ?, ?, ?, 0, '', 0, 0, 0)",
        'sssis',
        array($title1, $title2, $trip, $seats, $usd)
    );
    $newId = (int) $GLOBALS['mysqli']->insert_id;
    ivc_admin_log($adminUid, 'create_vacation', 'vacation', $newId, $title1);
    admin_flash('ok', 'Vacation created.');
    admin_go('vacation.php?id=' . $newId);
}

$rows = admin_query("SELECT v.*, (SELECT COALESCE(SUM(qty),0) FROM ivc_reservations r WHERE r.vid=v.id) reserved FROM ivc_vacations v ORDER BY v.id DESC") ?: array();

admin_layout_start('Vacations', 'vacations');
?>
<div class="panel">
    <div class="table-wrap">
        <table class="admin">
            <tr><th>ID</th><th>Title</th><th>Trip date</th><th>Seats</th><th>Reserved</th><th>Value</th><th>Status</th><th></th></tr>
            <?php if (!$rows): ?><tr><td colspan="8" class="muted">No vacation packages yet.</td></tr><?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= admin_h($row['title1']) ?></td>
                    <td><?= admin_h($row['trip_date']) ?></td>
                    <td><?= (int) $row['total_seats'] ?></td>
                    <td><?= (int) $row['reserved'] ?></td>
                    <td>$<?= admin_h(number_format((float) $row['usd_value'], 2)) ?></td>
                    <td><span class="badge <?= (int) $row['closed'] ? 'bad' : 'ok' ?>"><?= (int) $row['closed'] ? 'Closed' : 'Open' ?></span></td>
                    <td><a href="vacation.php?id=<?= (int) $row['id'] ?>">Manage</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<div class="panel">
    <h2>Add vacation</h2>
    <form method="post" class="form-grid">
        <?php admin_csrf_field(); ?>
        <input type="hidden" name="create" value="1">
        <div><label>Title</label><input type="text" name="title1" required></div>
        <div><label>Subtitle</label><input type="text" name="title2"></div>
        <div><label>Trip date</label><input type="text" name="trip_date" placeholder="e.g. Dec 2026"></div>
        <div><label>Total seats</label><input type="number" name="total_seats" value="10"></div>
        <div><label>USD value</label><input type="text" name="usd_value" value="0"></div>
        <div><button class="btn" type="submit">Create</button></div>
    </form>
</div>
<?php admin_layout_end(); ?>
