<?php
include("config.php");
if (empty($_SESSION['uid'])) {
    header("Location: login.php");
    exit();
}
include('header.php');
$siteNotice = function_exists('ivc_setting') ? trim((string) ivc_setting('site_notice')) : '';
$homeResorts = function_exists('ivc_active_resorts') ? ivc_active_resorts() : array();
if (!$homeResorts) {
    $homeResorts = array(
        array('code' => 'LAGUNA PALACE', 'name' => 'Laguna Palace Resort', 'image' => 'laguna.png'),
        array('code' => "GATOR'S HIDEAWAY", 'name' => "Gator's Hideaway", 'image' => 'gator.png'),
    );
}
?>
    
	<div class="row" style="max-width:1000px; margin:0 auto; border: 1px #650B14 solid; padding: 20px; margin-top: 100px;">

					<div class="col-md-12" style="text-align: center;">
                        <?php if (function_exists('ivc_is_admin') && ivc_is_admin($_SESSION['uid'])): ?>
                        <p><a href="admin/index.php" class="btn btn-primary" style="background:#650B14; border-color:#650B14; margin-bottom:16px;">OPEN ADMIN CONSOLE</a></p>
                        <?php endif; ?>
                        <?php if ($siteNotice !== ''): ?>
                        <p class="alert alert-warning"><?= htmlspecialchars($siteNotice) ?></p>
                        <?php endif; ?>
                        <div style="border: 1px solid #018EF2; padding: 16px; margin-bottom: 24px; background: #f4f9fd;">
                            <p style="font-size: 22px; font-weight: bold; color: #018EF2; margin-bottom: 8px;">Partner Login</p>
                            <p style="color:#650B14; margin-bottom: 16px;">Hotels, travel agencies, and industry partners: manage your listing or browse the partner directory.</p>
                            <a href="partners.php" class="btn btn-primary" style="background:#018EF2; border-color:#018EF2; margin: 4px 8px;">PARTNER DIRECTORY</a>
                            <a href="partner_submit.php" class="btn btn-primary" style="background:#650B14; border-color:#650B14; margin: 4px 8px;">LIST YOUR BUSINESS</a>
                        </div>
						<div class="row">
                            <?php foreach ($homeResorts as $resort):
                                $img = !empty($resort['image']) ? $resort['image'] : 'laguna.png';
                                $label = !empty($resort['name']) ? $resort['name'] : $resort['code'];
                            ?>
							<div class="col-md-6" style="text-align: center; margin-bottom: 20px;">
								<a href="bookings.php?resort=<?= urlencode($resort['code']) ?>"><img src="<?= htmlspecialchars($img) ?>" class="img-fluid" alt="<?= htmlspecialchars($label) ?>" style="margin: 0 auto;"></a>
							</div>
                            <?php endforeach; ?>
						</div>
					</div>

				</div>
    
<style>
.container {
    padding-left: 0px !important;
}
</style>
<script>
var navbar = document.getElementById("navbar");
if (navbar) {
    navbar.classList.add("sticky");
}
</script>				
<?php
include('footer.php');
?>
