<?php
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($base === '.' || $base === '/') {
    $base = '';
}

session_start();
include("header.php");
?>
<style>
.row{
    background:url(bgt.png);
    max-width:1200px;
    margin:0 auto;
    padding-left:20px;
    padding-right:20px;
}

.col-md-12{
    padding-left:0px;
    padding-right:0px;
}

.card-img-top {
    width: 100%;
    height: auto;
    display: block;
}

.ivc-cta {
    display: inline-block;
    max-width: 100%;
    width: 400px;
    margin: 0 auto 15px auto;
}

@media screen and (max-width: 600px) {
    .ivc-cta {
        width: 100%;
    }
}
</style>
        <div class="container" style="width:100%; max-width:1290px; margin-top:0px; padding-right:0px; padding-left:0px;color:#000;">

            <div class="row" style="margin-top:15px; max-width:1290px;">
                <div class="col-lg-12">
                    <a href="<?= htmlspecialchars($base === '' ? '/' : $base . '/') ?>"><img src="<?= htmlspecialchars($base) ?>/images/header_logo.png" class="img-fluid" alt="International Vacation Club" style="width:90%; max-width:860px; margin:0 auto; display:block;"></a>
                </div>
            </div>
            <div class="row" style="margin-top:15px; max-width:1290px;">
                <div class="col-lg-12" style="border:1px solid #000; padding-top:10px;">
                    <p style="font-size:1.1em; text-align:justify; margin-top:15px;">With more than 1.2 million members from all continents, IVC is one of the world’s fastest growing travel related communities. As a strategic service provider, we are happy to provide state-of-the-art solutions for the travel industry, and at the same time, producing happy travelers by offering the best deals through a wide variety of perks, rewards, and benefits rather than dumping the rates.</p>
                </div>
            </div>

        <div class="row" style="max-width: 1290px; margin-top:15px; color:#000;">
            <div class="col-lg-6 col-md-12" style="padding:0px; padding-top:10px;">
                <div class="card h-100" style="border:1px solid #000; margin:2px;">
                    <img class="card-img-top" src="<?= htmlspecialchars($base) ?>/images/pic1.jpg" alt="For the Industry">
                    <div class="card-body" style="font-size:1.1em; text-align:justify; min-height:303px;">
                        <p style="font-size:1.5em; text-align:center; text-decoration:underline;">For the Industry</p>
                        <p>As a hotel, resort, travel agent, car rental, tour service, or any other travel related business, you need strategies for more customers, more revenue, more profit.</p>
                        <p>As an IVC Partner, your services will be showcased to our millions of IVC Members from all over the world.</p>
                        <p>Hotels, travel agencies, resorts, and other travel businesses: log in with your SafeZone account, or list your company in the partner directory.</p>
                    </div>
                    <div class="card-footerx" style="padding-bottom:15px;">
                        <p style="text-align:center;"><a href="<?= htmlspecialchars($base) ?>/ivc/login.php?role=partner" class="btn btn-primary btn-lg ivc-cta" style="background:#018EF2; border-color:#018EF2;">PARTNER LOGIN</a></p>
                        <p style="text-align:center;"><a href="<?= htmlspecialchars($base) ?>/ivc/partners.php" class="btn btn-primary btn-lg ivc-cta" style="background:#650B14; border-color:#650B14;">PARTNER DIRECTORY</a></p>
                        <p style="text-align:center;"><a href="<?= htmlspecialchars($base) ?>/ivc/partner_submit.php" class="btn btn-primary btn-lg ivc-cta" style="background:#018EF2; border-color:#018EF2;">LIST YOUR BUSINESS</a></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12" style="padding:0px; padding-top:10px;">
                <div class="card h-100" style="border:1px solid #000; margin:2px;">
                    <img class="card-img-top" src="<?= htmlspecialchars($base) ?>/images/pic2.jpg" alt="For the Tourists">
                    <div class="card-body" style="font-size:1.1em; text-align:justify; min-height:303px;">
                        <p style="font-size:1.5em; text-align:center; text-decoration:underline;">For the Tourists</p>
                        <p>No matter what your travel plans are, with IVC your final destination is always happiness. Find the best deals, get the best rates, receive cashback, enjoy free upgrades, extend your stay with free extra nights, in short, make your next trip your best trip ever.</p>
                        <p>As an IVC Member, you always enjoy the best service at the best rates.</p>
                    </div>
                    <div class="card-footerx" style="padding-bottom:15px;">
                        <p style="text-align:center;"><a href="<?= htmlspecialchars($base) ?>/ivc/login.php" class="btn btn-primary btn-lg ivc-cta" style="background:#FDBA2C; border-color:#FDBA2C;">MEMBER LOGIN</a></p>
                        <p style="text-align:center;"><a href="<?= htmlspecialchars($base) ?>/ivc/" class="btn btn-primary btn-lg ivc-cta" style="background:#FDBA2C; border-color:#FDBA2C;">MORE INFO</a></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top:15px;">
            <div class="col-lg-12" style="text-align:center;">
                <img src="<?= htmlspecialchars($base) ?>/images/protected_small.png" class="img-fluid" alt="SafeZone protected" style="width:200px;">
            </div>
        </div>
    </div>

<?php
include("footer.php");
?>
