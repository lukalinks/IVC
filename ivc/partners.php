<?php
include("config.php");
include("functions.php");
include("partners.inc.php");

$type = isset($_GET['type']) ? $_GET['type'] : '';
$types = ivc_business_types();
if ($type !== '' && !isset($types[$type])) {
    $type = '';
}

$typeEsc = $GLOBALS['mysqli']->real_escape_string($type);
$where = "WHERE status='approved'";
if ($type !== '') {
    $where .= " AND business_type='$typeEsc'";
}

$res = $GLOBALS['mysqli']->query("SELECT * FROM ivc_listings $where ORDER BY name ASC");

include('header.php');
?>
<style>
.partner-card {
    border: 1px solid #650B14;
    padding: 16px;
    margin-bottom: 16px;
    text-align: left;
    background: #fff;
    min-height: 180px;
}
.partner-type {
    display: inline-block;
    background: #650B14;
    color: #fff;
    font-size: 12px;
    padding: 3px 8px;
    margin-bottom: 8px;
}
.filter-link {
    margin: 0 6px 8px 0;
    display: inline-block;
}
</style>

<div class="row" style="max-width:1000px; margin:0 auto; border: 1px #650B14 solid; padding: 20px; margin-top: 100px;">
    <div class="col-md-12" style="text-align:center;">
        <h2 style="color:#650B14; margin-bottom:10px;">IVC Industry Partners</h2>
        <p style="color:#650B14;">Hotels, resorts, travel agencies, car rentals, and tour services listed with IVC.</p>
        <p>
            <a href="partner_submit.php" class="btn btn-primary" style="background:#018EF2; border-color:#018EF2;">LIST YOUR BUSINESS</a>
        </p>
        <p style="margin-top:15px;">
            <a class="btn btn-sm btn-outline-secondary filter-link" href="partners.php">All</a>
            <?php foreach ($types as $key => $label): ?>
                <a class="btn btn-sm <?= $type === $key ? 'btn-secondary' : 'btn-outline-secondary' ?> filter-link" href="partners.php?type=<?= urlencode($key) ?>"><?= htmlspecialchars($label) ?></a>
            <?php endforeach; ?>
        </p>
    </div>

    <div class="col-md-12">
        <div class="row">
        <?php
        $count = ($res && $res->num_rows) ? $res->num_rows : 0;
        if ($count === 0) {
            echo '<div class="col-12"><p class="alert alert-info">No approved listings in this category yet.</p></div>';
        } else {
            while ($row = $res->fetch_assoc()) {
                $website = trim($row['website']);
                if ($website !== '' && strpos($website, 'http') !== 0) {
                    $website = 'https://' . $website;
                }
                ?>
                <div class="col-md-6">
                    <div class="partner-card">
                        <div class="partner-type"><?= htmlspecialchars(ivc_listing_type_label($row['business_type'])) ?></div>
                        <h5 style="color:#650B14;"><?= htmlspecialchars($row['name']) ?></h5>
                        <p style="margin-bottom:6px;"><?= htmlspecialchars($row['city'] . ($row['city'] && $row['country'] ? ', ' : '') . $row['country']) ?></p>
                        <?php if ($row['description'] !== ''): ?>
                            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                        <?php endif; ?>
                        <?php if ($row['phone'] !== ''): ?>
                            <p style="margin-bottom:4px;">Phone: <?= htmlspecialchars($row['phone']) ?></p>
                        <?php endif; ?>
                        <?php if ($row['email'] !== ''): ?>
                            <p style="margin-bottom:4px;">Email: <?= htmlspecialchars($row['email']) ?></p>
                        <?php endif; ?>
                        <?php if ($website !== ''): ?>
                            <p style="margin-bottom:0;"><a href="<?= htmlspecialchars($website) ?>" target="_blank" rel="noopener">Website</a></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>
        </div>
    </div>
</div>
<script>
var navbar = document.getElementById("navbar");
if (navbar) { navbar.classList.add("sticky"); }
</script>
<?php include('footer.php'); ?>
