<?php
require_once __DIR__ . '/_init.php';

$members = admin_count("SELECT COUNT(*) c FROM pi_account WHERE deleted=0");
$blocked = admin_count("SELECT COUNT(*) c FROM pi_account WHERE blocked=1 AND deleted=0");
$pendingBookings = admin_count("SELECT COUNT(*) c FROM ivc_bookings WHERE status='pending' OR status='' OR status IS NULL");
$pendingListings = admin_count("SELECT COUNT(*) c FROM ivc_listings WHERE status='pending'");
$paidMemberships = admin_count("SELECT COUNT(*) c FROM ivc_membership WHERE paid_status='paid'");
$vacations = admin_count("SELECT COUNT(*) c FROM ivc_vacations WHERE closed=0");

$recentBookings = admin_query("SELECT id, uid, email, resort, arrival_date, status, date FROM ivc_bookings ORDER BY id DESC LIMIT 8") ?: array();
$recentMembers = admin_query("SELECT a.uid, a.email, a.username, a.created, a.blocked, p.fname, p.lname
    FROM pi_account a LEFT JOIN pi_profile p ON p.uid=a.uid
    WHERE a.deleted=0 ORDER BY a.uid DESC LIMIT 8") ?: array();
$pendingPartners = admin_query("SELECT id, name, business_type, city, country, email, created_at FROM ivc_listings WHERE status='pending' ORDER BY id DESC LIMIT 8") ?: array();

admin_layout_start('Dashboard', 'dashboard');
?>
<div class="stats">
    <div class="stat"><b><?= (int) $members ?></b><span>Members</span></div>
    <div class="stat"><b><?= (int) $paidMemberships ?></b><span>Paid memberships</span></div>
    <div class="stat"><b><?= (int) $pendingBookings ?></b><span>Pending bookings</span></div>
    <div class="stat"><b><?= (int) $pendingListings ?></b><span>Partners to approve</span></div>
    <div class="stat"><b><?= (int) $blocked ?></b><span>Blocked accounts</span></div>
    <div class="stat"><b><?= (int) $vacations ?></b><span>Open vacation packages</span></div>
</div>

<div class="dash-actions" style="margin-bottom:22px;">
    <a class="dash-action" href="listings.php?status=pending">
        <strong>Approve partners</strong>
        <span><?= (int) $pendingListings ?> application<?= $pendingListings === 1 ? '' : 's' ?> waiting</span>
    </a>
    <a class="dash-action" href="bookings.php?status=pending">
        <strong>Review bookings</strong>
        <span><?= (int) $pendingBookings ?> resort request<?= $pendingBookings === 1 ? '' : 's' ?> pending</span>
    </a>
    <a class="dash-action" href="members.php">
        <strong>Find a member</strong>
        <span><?= (int) $members ?> active account<?= $members === 1 ? '' : 's' ?></span>
    </a>
    <a class="dash-action" href="resorts.php">
        <strong>Manage resorts</strong>
        <span>Catalog shown to members</span>
    </a>
    <a class="dash-action" href="settings.php">
        <strong>Platform settings</strong>
        <span>Bookings, directory, admins</span>
    </a>
</div>

<div class="dash-grid">
    <div class="panel">
        <h2>Partner applications to review</h2>
        <div class="table-wrap">
            <table class="admin">
                <tr><th>Business</th><th>Type</th><th>Location</th><th></th></tr>
                <?php if (!$pendingPartners): ?>
                    <tr><td colspan="4" class="muted">No partner applications waiting.</td></tr>
                <?php endif; ?>
                <?php foreach ($pendingPartners as $row): ?>
                    <tr>
                        <td><?= admin_h($row['name']) ?></td>
                        <td><?= admin_h(ivc_listing_type_label($row['business_type'])) ?></td>
                        <td><?= admin_h(trim($row['city'] . ' ' . $row['country'])) ?></td>
                        <td><a href="listing.php?id=<?= (int) $row['id'] ?>">Review</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <p style="margin:12px 0 0;"><a href="listings.php">All partner applications</a></p>
    </div>

    <div class="panel">
        <h2>Latest resort bookings</h2>
        <div class="table-wrap">
            <table class="admin">
                <tr><th>Member</th><th>Resort</th><th>Status</th><th></th></tr>
                <?php if (!$recentBookings): ?>
                    <tr><td colspan="4" class="muted">No bookings yet.</td></tr>
                <?php endif; ?>
                <?php foreach ($recentBookings as $row): ?>
                    <tr>
                        <td><a href="member.php?uid=<?= (int) $row['uid'] ?>"><?= admin_h($row['email']) ?></a></td>
                        <td><?= admin_h($row['resort']) ?></td>
                        <td><span class="badge <?= admin_status_class($row['status']) ?>"><?= admin_h($row['status'] ?: 'pending') ?></span></td>
                        <td><a href="booking.php?id=<?= (int) $row['id'] ?>">Open</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <p style="margin:12px 0 0;"><a href="bookings.php">All resort bookings</a></p>
    </div>
</div>

<div class="panel">
    <h2>Newest members</h2>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>UID</th><th>Name</th><th>Email</th><th>Created</th><th></th></tr>
            <?php if (!$recentMembers): ?>
                <tr><td colspan="5" class="muted">No members found.</td></tr>
            <?php endif; ?>
            <?php foreach ($recentMembers as $row): ?>
                <tr>
                    <td><?= (int) $row['uid'] ?></td>
                    <td><?= admin_h(trim($row['fname'] . ' ' . $row['lname'])) ?></td>
                    <td><?= admin_h($row['email']) ?></td>
                    <td><?= $row['created'] ? admin_h(date('Y-m-d', (int) $row['created'])) : '' ?></td>
                    <td><a href="member.php?uid=<?= (int) $row['uid'] ?>">Manage</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <p style="margin:12px 0 0;"><a href="members.php">All members</a></p>
</div>
<?php admin_layout_end(); ?>
