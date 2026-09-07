<?php
require_once __DIR__ . '/_init.php';

if (admin_posted()) {
    $action = isset($_POST['action']) ? $_POST['action'] : 'save';
    $id = (int) $_POST['id'];
    if ($action === 'delete' && $id > 0) {
        admin_exec("DELETE FROM ivc_resorts WHERE id=?", 'i', array($id));
        ivc_admin_log($adminUid, 'delete_resort', 'resort', $id, '');
        admin_flash('ok', 'Resort removed.');
        admin_go('resorts.php');
    }
    $name = trim((string) $_POST['name']);
    $code = trim((string) $_POST['code']);
    $location = trim((string) $_POST['location']);
    $description = trim((string) $_POST['description']);
    $image = trim((string) $_POST['image']);
    $status = $_POST['status'] === 'hidden' ? 'hidden' : 'active';
    $sort = (int) $_POST['sort_order'];
    if ($name === '') {
        admin_flash('bad', 'Resort name is required.');
        admin_go('resorts.php');
    }
    if ($code === '') {
        $code = $name;
    }
    if ($id > 0) {
        admin_exec(
            "UPDATE ivc_resorts SET name=?, code=?, location=?, description=?, image=?, status=?, sort_order=? WHERE id=?",
            'ssssssii',
            array($name, $code, $location, $description, $image, $status, $sort, $id)
        );
        ivc_admin_log($adminUid, 'update_resort', 'resort', $id, $name);
        admin_flash('ok', 'Resort saved.');
    } else {
        admin_exec(
            "INSERT INTO ivc_resorts (name, code, location, description, image, status, sort_order, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
            'ssssssi',
            array($name, $code, $location, $description, $image, $status, $sort)
        );
        ivc_admin_log($adminUid, 'create_resort', 'resort', $GLOBALS['mysqli']->insert_id, $name);
        admin_flash('ok', 'Resort added. Members will see it on Home and in the booking form.');
    }
    admin_go('resorts.php');
}

$editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$edit = array('id' => 0, 'name' => '', 'code' => '', 'location' => '', 'description' => '', 'image' => '', 'status' => 'active', 'sort_order' => 0);
if ($editId > 0) {
    $found = admin_one("SELECT * FROM ivc_resorts WHERE id=? LIMIT 1", 'i', array($editId));
    if ($found) {
        $edit = $found;
    }
}
$rows = admin_query("SELECT * FROM ivc_resorts ORDER BY sort_order ASC, name ASC") ?: array();

admin_layout_start('Resorts', 'resorts');
?>
<div class="panel">
    <h2><?= $edit['id'] ? 'Edit resort' : 'Add resort' ?></h2>
    <p class="muted">These properties appear on the member home page and in the booking request form.</p>
    <form method="post" class="form-grid">
        <?php admin_csrf_field(); ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int) $edit['id'] ?>">
        <div><label>Display name</label><input type="text" name="name" value="<?= admin_h($edit['name']) ?>" required></div>
        <div><label>Booking code</label><input type="text" name="code" value="<?= admin_h($edit['code']) ?>" placeholder="Same as name unless you need a short code"></div>
        <div><label>Location</label><input type="text" name="location" value="<?= admin_h($edit['location']) ?>"></div>
        <div><label>Image file</label><input type="text" name="image" value="<?= admin_h($edit['image']) ?>" placeholder="laguna.png"></div>
        <div>
            <label>Status</label>
            <select name="status">
                <option value="active"<?= $edit['status'] === 'active' ? ' selected' : '' ?>>Active</option>
                <option value="hidden"<?= $edit['status'] === 'hidden' ? ' selected' : '' ?>>Hidden</option>
            </select>
        </div>
        <div><label>Sort order</label><input type="number" name="sort_order" value="<?= (int) $edit['sort_order'] ?>"></div>
        <div class="span-2"><label>Description</label><textarea name="description" rows="3"><?= admin_h($edit['description']) ?></textarea></div>
        <div><button class="btn" type="submit"><?= $edit['id'] ? 'Save resort' : 'Add resort' ?></button>
            <?php if ($edit['id']): ?> <a class="btn btn-ghost" href="resorts.php">Cancel</a><?php endif; ?>
        </div>
    </form>
</div>
<div class="panel">
    <h2>All resorts</h2>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>Name</th><th>Code</th><th>Location</th><th>Image</th><th>Status</th><th></th></tr>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= admin_h($row['name']) ?></td>
                    <td><?= admin_h($row['code']) ?></td>
                    <td><?= admin_h($row['location']) ?></td>
                    <td><?= admin_h($row['image']) ?></td>
                    <td><span class="badge <?= admin_status_class($row['status']) ?>"><?= admin_h($row['status']) ?></span></td>
                    <td>
                        <a href="resorts.php?id=<?= (int) $row['id'] ?>">Edit</a>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Remove this resort?');">
                            <?php admin_csrf_field(); ?>
                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                            <button class="btn btn-ghost" name="action" value="delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php admin_layout_end(); ?>
