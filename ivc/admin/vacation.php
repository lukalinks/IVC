<?php
require_once __DIR__ . '/_init.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$row = admin_one("SELECT * FROM ivc_vacations WHERE id=? LIMIT 1", 'i', array($id));
if (!$row) {
    admin_flash('bad', 'Vacation not found.');
    admin_go('vacations.php');
}

if (admin_posted()) {
    $action = isset($_POST['action']) ? $_POST['action'] : 'save';
    if ($action === 'delete') {
        admin_exec("DELETE FROM ivc_route_schedule WHERE vid=?", 'i', array($id));
        admin_exec("DELETE FROM ivc_vacations WHERE id=?", 'i', array($id));
        ivc_admin_log($adminUid, 'delete_vacation', 'vacation', $id, '');
        admin_flash('ok', 'Vacation deleted.');
        admin_go('vacations.php');
    }
    if ($action === 'add_day') {
        $day = trim((string) $_POST['day']);
        $city = trim((string) $_POST['city']);
        $activity = trim((string) $_POST['activity']);
        admin_exec("INSERT INTO ivc_route_schedule (vid, day, city, activity) VALUES (?, ?, ?, ?)", 'isss', array($id, $day, $city, $activity));
        ivc_admin_log($adminUid, 'add_itinerary', 'vacation', $id, $day);
        admin_flash('ok', 'Itinerary day added.');
        admin_go('vacation.php?id=' . $id);
    }
    if ($action === 'delete_day') {
        $did = (int) $_POST['day_id'];
        admin_exec("DELETE FROM ivc_route_schedule WHERE id=? AND vid=?", 'ii', array($did, $id));
        admin_flash('ok', 'Itinerary day removed.');
        admin_go('vacation.php?id=' . $id);
    }
    admin_exec(
        "UPDATE ivc_vacations SET title1=?, title2=?, title3=?, title4=?, description=?, trip_date=?, total_seats=?, usd_value=?, tvc_price=?, member_type=?, per_account=?, target_value=?, target_date=?, single=?, couple=?, family=?, grou=?, closed=? WHERE id=?",
        'ssssssisssissiiiiii',
        array(
            trim((string) $_POST['title1']),
            trim((string) $_POST['title2']),
            trim((string) $_POST['title3']),
            trim((string) $_POST['title4']),
            trim((string) $_POST['description']),
            trim((string) $_POST['trip_date']),
            (int) $_POST['total_seats'],
            (string) $_POST['usd_value'],
            (string) $_POST['tvc_price'],
            trim((string) $_POST['member_type']),
            (int) $_POST['per_account'],
            (string) $_POST['target_value'],
            $_POST['target_date'] !== '' ? $_POST['target_date'] : null,
            (int) $_POST['single'],
            (int) $_POST['couple'],
            (int) $_POST['family'],
            (int) $_POST['grou'],
            isset($_POST['closed']) ? 1 : 0,
            $id,
        )
    );
    ivc_admin_log($adminUid, 'update_vacation', 'vacation', $id, $_POST['title1']);
    admin_flash('ok', 'Vacation saved.');
    admin_go('vacation.php?id=' . $id);
}

$days = admin_query("SELECT * FROM ivc_route_schedule WHERE vid=? ORDER BY id ASC", 'i', array($id)) ?: array();
$reserved = admin_count("SELECT COALESCE(SUM(qty),0) c FROM ivc_reservations WHERE vid=?", 'i', array($id));

admin_layout_start('Vacation #' . $id, 'vacations');
?>
<p><a href="vacations.php">← All vacations</a> · <?= (int) $reserved ?> seats reserved</p>
<div class="panel">
    <form method="post" class="form-grid">
        <?php admin_csrf_field(); ?>
        <div class="span-2"><label>Title 1</label><input type="text" name="title1" value="<?= admin_h($row['title1']) ?>"></div>
        <div class="span-2"><label>Title 2</label><input type="text" name="title2" value="<?= admin_h($row['title2']) ?>"></div>
        <div><label>Title 3</label><input type="text" name="title3" value="<?= admin_h($row['title3']) ?>"></div>
        <div><label>Title 4</label><input type="text" name="title4" value="<?= admin_h($row['title4']) ?>"></div>
        <div><label>Trip date</label><input type="text" name="trip_date" value="<?= admin_h($row['trip_date']) ?>"></div>
        <div><label>Member type</label><input type="text" name="member_type" value="<?= admin_h($row['member_type']) ?>"></div>
        <div><label>Total seats</label><input type="number" name="total_seats" value="<?= (int) $row['total_seats'] ?>"></div>
        <div><label>Per account</label><input type="number" name="per_account" value="<?= (int) $row['per_account'] ?>"></div>
        <div><label>USD value</label><input type="text" name="usd_value" value="<?= admin_h($row['usd_value']) ?>"></div>
        <div><label>TVC price</label><input type="text" name="tvc_price" value="<?= admin_h($row['tvc_price']) ?>"></div>
        <div><label>Target value</label><input type="text" name="target_value" value="<?= admin_h($row['target_value']) ?>"></div>
        <div><label>Target date</label><input type="date" name="target_date" value="<?= admin_h($row['target_date']) ?>"></div>
        <div><label>Single</label><input type="number" name="single" value="<?= (int) $row['single'] ?>"></div>
        <div><label>Couple</label><input type="number" name="couple" value="<?= (int) $row['couple'] ?>"></div>
        <div><label>Family</label><input type="number" name="family" value="<?= (int) $row['family'] ?>"></div>
        <div><label>Group</label><input type="number" name="grou" value="<?= (int) $row['grou'] ?>"></div>
        <div class="span-2"><label>Description</label><textarea name="description" rows="6"><?= admin_h($row['description']) ?></textarea></div>
        <div><label><input type="checkbox" name="closed" value="1"<?= (int) $row['closed'] ? ' checked' : '' ?>> Closed</label></div>
        <div><button class="btn" type="submit">Save vacation</button></div>
    </form>
    <form method="post" onsubmit="return confirm('Delete this vacation and its itinerary?');" style="margin-top:12px;">
        <?php admin_csrf_field(); ?>
        <button class="btn btn-bad" name="action" value="delete">Delete vacation</button>
    </form>
</div>
<div class="panel">
    <h2>Itinerary</h2>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>Day</th><th>City</th><th>Activity</th><th></th></tr>
            <?php foreach ($days as $day): ?>
                <tr>
                    <td><?= admin_h($day['day']) ?></td>
                    <td><?= admin_h($day['city']) ?></td>
                    <td><?= admin_h($day['activity']) ?></td>
                    <td>
                        <form method="post" onsubmit="return confirm('Remove this day?');">
                            <?php admin_csrf_field(); ?>
                            <input type="hidden" name="day_id" value="<?= (int) $day['id'] ?>">
                            <button class="btn btn-ghost" name="action" value="delete_day">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <form method="post" class="form-grid" style="margin-top:12px;">
        <?php admin_csrf_field(); ?>
        <input type="hidden" name="action" value="add_day">
        <div><label>Day</label><input type="text" name="day" placeholder="Day 1"></div>
        <div><label>City</label><input type="text" name="city"></div>
        <div class="span-2"><label>Activity</label><input type="text" name="activity"></div>
        <div><button class="btn" type="submit">Add day</button></div>
    </form>
</div>
<?php admin_layout_end(); ?>
