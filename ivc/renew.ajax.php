<?
session_start();
if ($_SESSION['uid'] <= 0) {
    print "err||Please login first.";
    exit();
}
include("config.php");
include("functions.php");

$GLOBALS ['mysqli']->query ("SET NAMES UTF8") or die ($GLOBALS ['mysqli']->error . __LINE__);

$uid = $_SESSION['uid'];
$val=$GLOBALS ['mysqli']->real_escape_string($_POST['val']);


if($val=='')
{
    $val=0;
}

        
$select = "update ivc_membership set auto_renew='$val' where uid=$uid";
//print $select;
$GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);
    
?>