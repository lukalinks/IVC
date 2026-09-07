<?php
require_once __DIR__ . '/_init.php';

$total = admin_count("SELECT COUNT(*) c FROM ivc_admin_log");
list($page, $per, $offset, $pages) = admin_page($total, 40);
$rows = admin_query(
    "SELECT l.*, a.email FROM ivc_admin_log l LEFT JOIN pi_account a ON a.uid=l.admin_uid ORDER BY l.id DESC LIMIT $per OFFSET $offset"
) ?: array();

admin_layout_start('Activity log', 'logs');
?>
<div class="panel">
    <p class="muted">Every admin change is recorded here.</p>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>When</th><th>Admin</th><th>Action</th><th>Entity</th><th>ID</th><th>Details</th></tr>
            <?php if (!$rows): ?><tr><td colspan="6" class="muted">No activity yet.</td></tr><?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= admin_h($row['created_at']) ?></td>
                    <td><?= admin_h($row['email'] ?: $row['admin_uid']) ?></td>
                    <td><?= admin_h($row['action']) ?></td>
                    <td><?= admin_h($row['entity']) ?></td>
                    <td><?= admin_h($row['entity_id']) ?></td>
                    <td><?= admin_h($row['details']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php admin_pager($page, $pages, array()); ?>
</div>
<?php admin_layout_end(); ?>
