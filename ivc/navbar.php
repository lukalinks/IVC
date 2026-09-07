<?php
$homeUrl = empty($_SESSION['uid']) ? '../index.php' : 'home.php';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbar" style="width:100%; max-width:1920px;">
    <a class="navbar-brand" href="<?= htmlspecialchars($homeUrl) ?>"><img src="assets/img/Picture7.png" class="img img-responsive" alt="IVC Home" style="max-height:50px;"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse navbar-right" id="navbarNavAltMarkup">
    <div class="navbar-nav ml-auto">
        <a class="nav-item nav-link active" href="<?= htmlspecialchars($homeUrl) ?>">Home <span class="sr-only">(current)</span></a>
        <a class="nav-item nav-link" href="partners.php">Partners</a>
        <!--<a class="nav-item nav-link" href="about.php">About</a>
        <a class="nav-item nav-link" href="benefits.php">Benefits</a>
        <a class="nav-item nav-link" href="membership.php">Memberships</a>
        <a class="nav-item nav-link" href="#">V.I.P Luxury</a>-->
        <?
        if(empty($_SESSION['uid']))
        {
        ?>
            <a class="nav-item nav-link" href="login.php?role=partner">Partner Login</a>
            <a class="nav-item nav-link" href="login.php">Login</a>
        <?
        }
        else
        {
            if (function_exists('ivc_is_admin') && ivc_is_admin($_SESSION['uid'])) {
                echo '<a class="nav-item nav-link" href="admin/index.php">Admin</a>';
            }
        ?>
            <a class="nav-item nav-link" href="logout.php">Logout</a>
        <?
        }
        ?>    
    </div>
    </div>
</nav>