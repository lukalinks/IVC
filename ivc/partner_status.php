<?php
include("config.php");
include("functions.php");
include("partners.inc.php");

if (empty($_SESSION['uid'])) {
    header('Location: login.php?role=partner');
    exit();
}

$uid = (int) $_SESSION['uid'];
$listing = ivc_user_latest_listing($uid);

include('header.php');
?>

<div class="row" style="max-width:800px; margin:0 auto; border: 1px #650B14 solid; padding: 20px; margin-top: 100px;">
    <div class="col-md-12" style="text-align:center;">
        <h2 style="color:#650B14;">Partner Application Status</h2>
        <?php if (!$listing): ?>
            <p class="alert alert-info">You have not registered as a partner yet.</p>
            <p><a href="partner_submit.php" class="btn btn-primary" style="background:#018EF2; border-color:#018EF2;">REGISTER AS PARTNER</a></p>
        <?php else: ?>
            <?php
            $status = strtolower((string) $listing['status']);
            $alertClass = 'alert-info';
            if ($status === 'approved') {
                $alertClass = 'alert-success';
            } elseif ($status === 'rejected') {
                $alertClass = 'alert-danger';
            } elseif ($status === 'pending') {
                $alertClass = 'alert-warning';
            }
            ?>
            <p class="alert <?= htmlspecialchars($alertClass) ?>">
                Status: <strong><?= htmlspecialchars(ivc_partner_status_label($listing['status'])) ?></strong>
            </p>
            <div style="text-align:left; max-width:520px; margin:0 auto; border:1px solid #ddd; padding:16px; margin-bottom:20px;">
                <p><strong><?= htmlspecialchars($listing['name']) ?></strong></p>
                <p class="mb-1"><?= htmlspecialchars(ivc_listing_type_label($listing['business_type'])) ?></p>
                <p class="mb-1"><?= htmlspecialchars(trim($listing['city'] . ($listing['city'] && $listing['country'] ? ', ' : '') . $listing['country'])) ?></p>
                <?php if ($listing['email'] !== ''): ?>
                    <p class="mb-0"><?= htmlspecialchars($listing['email']) ?></p>
                <?php endif; ?>
                <p class="text-muted" style="margin-top:10px; font-size:14px;">Submitted <?= htmlspecialchars(date('M j, Y', strtotime($listing['created_at']))) ?></p>
            </div>
            <?php if ($status === 'pending'): ?>
                <p style="color:#650B14;">Your application is waiting for IVC admin approval. You will appear in the partner directory once approved.</p>
            <?php elseif ($status === 'approved'): ?>
                <p style="color:#650B14;">Your business is listed in the IVC partner directory.</p>
                <p><a href="partners.php" class="btn btn-primary" style="background:#650B14; border-color:#650B14;">VIEW DIRECTORY</a></p>
            <?php elseif ($status === 'rejected'): ?>
                <p style="color:#650B14;">Your previous application was not approved. You may update your details and submit again.</p>
                <p><a href="partner_submit.php" class="btn btn-primary" style="background:#018EF2; border-color:#018EF2;">SUBMIT AGAIN</a></p>
            <?php endif; ?>
        <?php endif; ?>
        <p style="margin-top:20px;"><a href="home.php">Back to home</a></p>
    </div>
</div>
<script>
var navbar = document.getElementById("navbar");
if (navbar) { navbar.classList.add("sticky"); }
</script>
<?php include('footer.php'); ?>
