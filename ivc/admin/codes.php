<?php
require_once __DIR__ . '/_init.php';

if (admin_posted()) {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($action === 'generate') {
        $count = max(1, min(50, (int) $_POST['count']));
        $made = 0;
        for ($i = 0; $i < $count; $i++) {
            $code = strtoupper(bin2hex(random_bytes(4)));
            if (admin_exec("INSERT INTO rb_invitation_codes (code, used_by) VALUES (?, 0)", 's', array($code))) {
                $made++;
            }
        }
        ivc_admin_log($adminUid, 'generate_codes', 'code', $made, $count . ' requested');
        admin_flash('ok', $made . ' invitation code' . ($made === 1 ? '' : 's') . ' generated.');
        admin_go('codes.php');
    }
    if ($action === 'add' && trim((string) $_POST['code']) !== '') {
        $code = trim((string) $_POST['code']);
        admin_exec("INSERT INTO rb_invitation_codes (code, used_by) VALUES (?, 0)", 's', array($code));
        ivc_admin_log($adminUid, 'add_code', 'code', $code, '');
        admin_flash('ok', 'Code added.');
        admin_go('codes.php');
    }
    if ($action === 'delete') {
        $id = (int) $_POST['id'];
        admin_exec("DELETE FROM rb_invitation_codes WHERE id=?", 'i', array($id));
        ivc_admin_log($adminUid, 'delete_code', 'code', $id, '');
        admin_flash('ok', 'Code deleted.');
        admin_go('codes.php');
    }
    if ($action === 'reset') {
        $id = (int) $_POST['id'];
        admin_exec("UPDATE rb_invitation_codes SET used_by=0 WHERE id=?", 'i', array($id));
        ivc_admin_log($adminUid, 'reset_code', 'code', $id, '');
        admin_flash('ok', 'Code marked unused.');
        admin_go('codes.php');
    }
}

$q = trim((string) (isset($_GET['q']) ? $_GET['q'] : ''));
$where = 'WHERE 1=1';
$types = '';
$params = array();
if ($q !== '') {
    $where .= ' AND (code LIKE ? OR used_by=?)';
    $types .= 'si';
    $params[] = '%' . $q . '%';
    $params[] = ctype_digit($q) ? (int) $q : 0;
}
$total = admin_count("SELECT COUNT(*) c FROM rb_invitation_codes $where", $types, $params);
list($page, $per, $offset, $pages) = admin_page($total, 40);
$rows = admin_query("SELECT * FROM rb_invitation_codes $where ORDER BY id DESC LIMIT $per OFFSET $offset", $types, $params) ?: array();

admin_layout_start('Invite codes', 'codes');
?>
<div class="panel">
    <form method="post" class="toolbar">
        <?php admin_csrf_field(); ?>
        <input type="number" name="count" value="5" min="1" max="50">
        <button class="btn" name="action" value="generate">Generate codes</button>
    </form>
    <form method="post" class="toolbar">
        <?php admin_csrf_field(); ?>
        <input type="text" name="code" placeholder="Custom code">
        <button class="btn btn-ghost" name="action" value="add">Add code</button>
    </form>
</div>
<div class="panel">
    <form class="toolbar" method="get">
        <input type="text" name="q" value="<?= admin_h($q) ?>" placeholder="Code or used-by UID">
        <button class="btn" type="submit">Search</button>
    </form>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>ID</th><th>Code</th><th>Used by</th><th></th></tr>
            <?php if (!$rows): ?><tr><td colspan="4" class="muted">No codes.</td></tr><?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= admin_h($row['code']) ?></td>
                    <td>
                        <?php if ((int) $row['used_by'] > 0): ?>
                            <a href="member.php?uid=<?= (int) $row['used_by'] ?>"><?= (int) $row['used_by'] ?></a>
                        <?php else: ?>
                            <span class="badge ok">Unused</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="post" style="display:inline;">
                            <?php admin_csrf_field(); ?>
                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                            <?php if ((int) $row['used_by'] > 0): ?>
                                <button class="btn btn-ghost" name="action" value="reset">Mark unused</button>
                            <?php endif; ?>
                            <button class="btn btn-ghost" name="action" value="delete" onclick="return confirm('Delete this code?');">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php admin_pager($page, $pages, array('q' => $q)); ?>
</div>
<?php admin_layout_end(); ?>
