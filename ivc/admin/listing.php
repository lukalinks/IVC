<?php
require_once __DIR__ . '/_init.php';

$types = ivc_business_types();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$row = array(
    'id' => 0,
    'uid' => 0,
    'business_type' => 'hotel',
    'name' => '',
    'description' => '',
    'country' => '',
    'city' => '',
    'address' => '',
    'phone' => '',
    'email' => '',
    'website' => '',
    'status' => 'pending',
);
if ($id > 0) {
    $found = admin_one("SELECT * FROM ivc_listings WHERE id=? LIMIT 1", 'i', array($id));
    if (!$found) {
        admin_flash('bad', 'Listing not found.');
        admin_go('listings.php');
    }
    $row = $found;
}

if (admin_posted()) {
    $action = isset($_POST['action']) ? $_POST['action'] : 'save';
    if ($action === 'delete' && $id > 0) {
        admin_exec("DELETE FROM ivc_listings WHERE id=?", 'i', array($id));
        ivc_admin_log($adminUid, 'delete_listing', 'listing', $id, '');
        admin_flash('ok', 'Listing deleted.');
        admin_go('listings.php');
    }
    $data = array(
        'uid' => (int) $_POST['uid'],
        'business_type' => isset($types[$_POST['business_type']]) ? $_POST['business_type'] : 'other',
        'name' => trim((string) $_POST['name']),
        'description' => trim((string) $_POST['description']),
        'country' => trim((string) $_POST['country']),
        'city' => trim((string) $_POST['city']),
        'address' => trim((string) $_POST['address']),
        'phone' => trim((string) $_POST['phone']),
        'email' => trim((string) $_POST['email']),
        'website' => trim((string) $_POST['website']),
        'status' => in_array($_POST['status'], array('pending', 'approved', 'rejected'), true) ? $_POST['status'] : 'pending',
    );
    if ($data['name'] === '') {
        admin_flash('bad', 'Business name is required.');
        admin_go($id ? 'listing.php?id=' . $id : 'listing.php');
    }
    if ($id > 0) {
        admin_exec(
            "UPDATE ivc_listings SET uid=?, business_type=?, name=?, description=?, country=?, city=?, address=?, phone=?, email=?, website=?, status=? WHERE id=?",
            'issssssssssi',
            array($data['uid'], $data['business_type'], $data['name'], $data['description'], $data['country'], $data['city'], $data['address'], $data['phone'], $data['email'], $data['website'], $data['status'], $id)
        );
        ivc_admin_log($adminUid, 'update_listing', 'listing', $id, $data['status']);
        admin_flash('ok', 'Listing saved.');
        admin_go('listing.php?id=' . $id);
    }
    admin_exec(
        "INSERT INTO ivc_listings (uid, business_type, name, description, country, city, address, phone, email, website, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
        'issssssssss',
        array($data['uid'], $data['business_type'], $data['name'], $data['description'], $data['country'], $data['city'], $data['address'], $data['phone'], $data['email'], $data['website'], $data['status'])
    );
    $newId = (int) $GLOBALS['mysqli']->insert_id;
    ivc_admin_log($adminUid, 'create_listing', 'listing', $newId, $data['name']);
    admin_flash('ok', 'Listing created.');
    admin_go('listing.php?id=' . $newId);
}

admin_layout_start($id ? 'Listing #' . $id : 'New listing', 'listings');
?>
<p><a href="listings.php">← All listings</a></p>
<div class="panel">
    <form method="post" class="form-grid">
        <?php admin_csrf_field(); ?>
        <input type="hidden" name="action" value="save">
        <div>
            <label>Status</label>
            <select name="status">
                <?php foreach (array('pending', 'approved', 'rejected') as $st): ?>
                    <option value="<?= $st ?>"<?= $row['status'] === $st ? ' selected' : '' ?>><?= ucfirst($st) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Type</label>
            <select name="business_type">
                <?php foreach ($types as $key => $label): ?>
                    <option value="<?= admin_h($key) ?>"<?= $row['business_type'] === $key ? ' selected' : '' ?>><?= admin_h($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div><label>Owner UID</label><input type="number" name="uid" value="<?= (int) $row['uid'] ?>"></div>
        <div><label>Name</label><input type="text" name="name" value="<?= admin_h($row['name']) ?>" required></div>
        <div><label>Country</label><input type="text" name="country" value="<?= admin_h($row['country']) ?>"></div>
        <div><label>City</label><input type="text" name="city" value="<?= admin_h($row['city']) ?>"></div>
        <div class="span-2"><label>Address</label><input type="text" name="address" value="<?= admin_h($row['address']) ?>"></div>
        <div><label>Phone</label><input type="text" name="phone" value="<?= admin_h($row['phone']) ?>"></div>
        <div><label>Email</label><input type="email" name="email" value="<?= admin_h($row['email']) ?>"></div>
        <div class="span-2"><label>Website</label><input type="text" name="website" value="<?= admin_h($row['website']) ?>"></div>
        <div class="span-2"><label>Description</label><textarea name="description" rows="5"><?= admin_h($row['description']) ?></textarea></div>
        <div><button class="btn" type="submit">Save listing</button></div>
    </form>
    <?php if ($id > 0): ?>
    <form method="post" onsubmit="return confirm('Delete this listing?');" style="margin-top:12px;">
        <?php admin_csrf_field(); ?>
        <button class="btn btn-bad" name="action" value="delete">Delete listing</button>
    </form>
    <?php endif; ?>
</div>
<?php admin_layout_end(); ?>
