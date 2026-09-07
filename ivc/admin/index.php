<?php
require_once __DIR__ . '/_init.php';

$members = admin_count("SELECT COUNT(*) c FROM pi_account WHERE deleted=0");
$blocked = admin_count("SELECT COUNT(*) c FROM pi_account WHERE blocked=1 AND deleted=0");
$bookings = admin_count("SELECT COUNT(*) c FROM ivc_bookings");
$pendingBookings = admin_count("SELECT COUNT(*) c FROM ivc_bookings WHERE status='pending' OR status='' OR status IS NULL");
$pendingListings = admin_count("SELECT COUNT(*) c FROM ivc_listings WHERE status='pending'");
$listings = admin_count("SELECT COUNT(*) c FROM ivc_listings");
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
    <div class="stat"><b><?= (int) $members ?></b><span>Active members</span></div>
    <div class="stat"><b><?= (int) $blocked ?></b><span>Blocked accounts</span></div>
    <div class="stat"><b><?= (int) $bookings ?></b><span>Bookings</span></div>
    <div class="stat"><b><?= (int) $pendingBookings ?></b><span>Pending bookings</span></div>
    <div class="stat"><b><?= (int) $pendingListings ?></b><span>Listings to review</span></div>
    <div class="stat"><b><?= (int) $listings ?></b><span>Partner listings</span></div>
    <div class="stat"><b><?= (int) $paidMemberships ?></b><span>Paid memberships</span></div>
    <div class="stat"><b><?= (int) $vacations ?></b><span>Open vacations</span></div>
</div>

<div class="panel">
    <h2>Quick actions</h2>
    <div class="toolbar">
        <a class="btn" href="members.php">Find a member</a>
        <a class="btn btn-wait" href="bookings.php?status=pending">Review bookings</a>
        <a class="btn btn-ok" href="listings.php?status=pending">Approve partners</a>
        <a class="btn btn-ghost" href="resorts.php">Edit resorts</a>
        <a class="btn btn-ghost" href="settings.php">Platform settings</a>
    </div>
</div>

<div class="panel">
    <h2>Latest bookings</h2>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>ID</th><th>Member</th><th>Resort</th><th>Arrival</th><th>Status</th><th></th></tr>
            <?php if (!$recentBookings): ?>
                <tr><td colspan="6" class="muted">No bookings yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($recentBookings as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><a href="member.php?uid=<?= (int) $row['uid'] ?>"><?= admin_h($row['email']) ?></a></td>
                    <td><?= admin_h($row['resort']) ?></td>
                    <td><?= admin_h($row['arrival_date']) ?></td>
                    <td><span class="badge <?= admin_status_class($row['status']) ?>"><?= admin_h($row['status'] ?: 'pending') ?></span></td>
                    <td><a href="booking.php?id=<?= (int) $row['id'] ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<div class="panel">
    <h2>Newest members</h2>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>UID</th><th>Name</th><th>Email</th><th>Created</th><th></th></tr>
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
</div>

<div class="panel">
    <h2>Partner listings waiting for approval</h2>
    <div class="table-wrap">
        <table class="admin">
            <tr><th>Name</th><th>Type</th><th>Location</th><th>Email</th><th></th></tr>
            <?php if (!$pendingPartners): ?>
                <tr><td colspan="5" class="muted">Nothing pending.</td></tr>
            <?php endif; ?>
            <?php foreach ($pendingPartners as $row): ?>
                <tr>
                    <td><?= admin_h($row['name']) ?></td>
                    <td><?= admin_h(ivc_listing_type_label($row['business_type'])) ?></td>
                    <td><?= admin_h(trim($row['city'] . ' ' . $row['country'])) ?></td>
                    <td><?= admin_h($row['email']) ?></td>
                    <td><a href="listing.php?id=<?= (int) $row['id'] ?>">Review</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php admin_layout_end(); ?>
