<?php
require_once __DIR__ . '/_init.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$row = admin_one("SELECT * FROM ivc_bookings WHERE id=? LIMIT 1", 'i', array($id));
if (!$row) {
    admin_flash('bad', 'Booking not found.');
    admin_go('bookings.php');
}

$statuses = ivc_booking_statuses();
$resorts = admin_query("SELECT name, code FROM ivc_resorts ORDER BY sort_order, name") ?: array();

if (admin_posted()) {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($action === 'save') {
        $status = isset($statuses[$_POST['status']]) ? $_POST['status'] : 'pending';
        $resort = trim((string) $_POST['resort']);
        $email = trim((string) $_POST['email']);
        $guests = (int) $_POST['no_of_guests'];
        $arrival = trim((string) $_POST['arrival_date']);
        $nights = (int) $_POST['no_of_nights'];
        $accom = trim((string) $_POST['accomodation']);
        $rest = trim((string) $_POST['restaurant']);
        $notes = trim((string) $_POST['admin_notes']);
        admin_exec(
            "UPDATE ivc_bookings SET status=?, resort=?, email=?, no_of_guests=?, arrival_date=?, no_of_nights=?, accomodation=?, restaurant=?, admin_notes=?, updated_at=NOW() WHERE id=?",
            'sssisisssi',
            array($status, $resort, $email, $guests, $arrival, $nights, $accom, $rest, $notes, $id)
        );
        ivc_admin_log($adminUid, 'update_booking', 'booking', $id, $status);
        admin_flash('ok', 'Booking updated.');
        admin_go('booking.php?id=' . $id);
    }
    if ($action === 'delete') {
        admin_exec("DELETE FROM ivc_bookings WHERE id=?", 'i', array($id));
        ivc_admin_log($adminUid, 'delete_booking', 'booking', $id, '');
        admin_flash('ok', 'Booking deleted.');
        admin_go('bookings.php');
    }
}

admin_layout_start('Booking #' . $id, 'bookings');
?>
<p><a href="bookings.php">← All bookings</a> · <a href="member.php?uid=<?= (int) $row['uid'] ?>">Member <?= (int) $row['uid'] ?></a></p>
<div class="panel">
    <form method="post" class="form-grid">
        <?php admin_csrf_field(); ?>
        <input type="hidden" name="action" value="save">
        <div>
            <label>Status</label>
            <select name="status">
                <?php foreach ($statuses as $key => $label): ?>
                    <option value="<?= admin_h($key) ?>"<?= ($row['status'] ?: 'pending') === $key ? ' selected' : '' ?>><?= admin_h($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Resort</label>
            <select name="resort">
                <?php
                $seen = array();
                foreach ($resorts as $resort) {
                    $seen[$resort['code']] = true;
                    $sel = $row['resort'] === $resort['code'] ? ' selected' : '';
                    echo '<option value="' . admin_h($resort['code']) . '"' . $sel . '>' . admin_h($resort['name']) . '</option>';
                }
                if ($row['resort'] !== '' && empty($seen[$row['resort']])) {
                    echo '<option value="' . admin_h($row['resort']) . '" selected>' . admin_h($row['resort']) . '</option>';
                }
                ?>
            </select>
        </div>
        <div><label>Email</label><input type="email" name="email" value="<?= admin_h($row['email']) ?>"></div>
        <div><label>Guests</label><input type="number" name="no_of_guests" min="1" value="<?= (int) $row['no_of_guests'] ?>"></div>
        <div><label>Arrival</label><input type="date" name="arrival_date" value="<?= admin_h($row['arrival_date']) ?>"></div>
        <div><label>Nights</label><input type="number" name="no_of_nights" min="1" value="<?= (int) $row['no_of_nights'] ?>"></div>
        <div><label>Accommodation</label><input type="text" name="accomodation" value="<?= admin_h($row['accomodation']) ?>"></div>
        <div><label>Restaurant</label><input type="text" name="restaurant" value="<?= admin_h($row['restaurant']) ?>"></div>
        <div class="span-2"><label>Admin notes</label><textarea name="admin_notes" rows="4"><?= admin_h($row['admin_notes']) ?></textarea></div>
        <div><button class="btn" type="submit">Save booking</button></div>
    </form>
    <form method="post" onsubmit="return confirm('Delete this booking?');" style="margin-top:12px;">
        <?php admin_csrf_field(); ?>
        <button class="btn btn-bad" name="action" value="delete">Delete booking</button>
    </form>
</div>
<?php admin_layout_end(); ?>
