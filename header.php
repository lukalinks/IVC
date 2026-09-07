<!DOCTYPE html>

<html>



<head>

<meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>International Vacation Club</title>

<link rel="stylesheet" href="https://safe.zone/cdn/css/bootstrap.min.css" crossorigin="anonymous">

<link rel="stylesheet" href="https://safe.zone/cdn/css/webticker.css" crossorigin="anonymous">

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

<!--<script

	src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>-->

<!-- Include all compiled plugins (below), or include individual files as needed -->

<!--<script src="assets/bootstrap/js/bootstrap.min.js"></script>-->

<script src="https://safe.zone/cdn/js/jquery-3.5.1.min.js" crossorigin="anonymous"></script>

<script src="https://safe.zone/cdn/js/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

<script src="https://safe.zone/cdn/js/bootstrap.min.js" crossorigin="anonymous"></script>


<script src="jquery.webticker.min.js" crossorigin="anonymous"></script>






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
 