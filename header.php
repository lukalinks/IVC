<!DOCTYPE html>

<html>



<head>

<meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>International Vacation Club</title>
<?php
if (!isset($base)) {
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    if ($base === '.' || $base === '/') {
        $base = '';
    }
}
?>
<link rel="stylesheet" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/css/bootstrap.min.css">

<link rel="stylesheet" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/ivc/css/webticker.css">

<script src="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/js/jquery-3.5.1.min.js"></script>
<script src="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/js/bootstrap.bundle.min.js"></script>

<script src="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/ivc/js/jquery.webticker.min.js"></script>






<meta name="msapplication-TileColor" content="#da532c">

<meta name="theme-color" content="#ffffff">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->

<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

<!--[if lt IE 9]>

      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>

      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

    <![endif]-->

<!--<link rel="stylesheet" href="assets/css/styles.css">-->

<style>

body

{

  font-family: Century Gothic, Calibri;
  /*background: url(back.jpg) no-repeat;
  background-position: center 0px;
  background-attachment: fixed;*/
  background-color:#fff;

}



body{
   

}


.bg-light{
    background:#fff;
}

.embed-responsive-4by3::before {
    padding-top: 56%;
}
@media screen and (max-width: 600px) {
    p{
        font-size:14px!important;
    }

    button{
        width:300px!important;
    }

    .top{
        margin-top:-20px!important;
    }
}
</style>



</head>



<body style="text-align:center;">
<?php
if (!isset($base)) {
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    if ($base === '.' || $base === '/') {
        $base = '';
    }
}
$ivcBase = $base . '/ivc';
$publicHome = ($base === '') ? '/' : $base . '/';
$navHome = empty($_SESSION['uid']) ? $publicHome : $ivcBase . '/home.php';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbar" style="width:100%; text-align:left; margin-bottom:10px;">
    <a class="navbar-brand" href="<?= htmlspecialchars($navHome) ?>"><img src="<?= htmlspecialchars($ivcBase) ?>/assets/img/Picture7.png" alt="IVC Home" style="max-height:50px;"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#publicNavbar" aria-controls="publicNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="publicNavbar">
        <div class="navbar-nav ml-auto">
            <a class="nav-item nav-link" href="<?= htmlspecialchars($navHome) ?>">Home</a>
            <a class="nav-item nav-link" href="<?= htmlspecialchars($ivcBase) ?>/partners.php">Partners</a>
            <?php if (empty($_SESSION['uid'])): ?>
            <a class="nav-item nav-link" href="<?= htmlspecialchars($ivcBase) ?>/login.php?role=partner">Partner Login</a>
            <a class="nav-item nav-link" href="<?= htmlspecialchars($ivcBase) ?>/login.php">Login</a>
            <?php else: ?>
            <a class="nav-item nav-link" href="<?= htmlspecialchars($ivcBase) ?>/logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
 