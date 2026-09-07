<?php
include("config.php");
include("functions.php");
include("admin.inc.php");
ivc_ensure_admin_schema();
if (empty($_SESSION['uid']) || !ivc_is_admin($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}
header("Location: admin/listings.php");
exit;
