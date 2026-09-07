<?php
session_start();
include("config.php");

include("functions.php");

//print_r($_POST);
if($_POST)
{
	if(is_numeric($_POST['uid']) && $_POST['password']!='')
	{
	    if($_POST['uid']=='859132')
	    {
	        print "CONTACT SUPPORT via the SUPPORT hexagon in SafeZone.";
	        exit;
	        
	    }
	    if($_POST['afpin']==1)
		{
			$_SESSION['puid']=$_POST['uid'];
			$_SESSION['ppwd']=$_POST['password'];
			$_SESSION['redirectto']=$_POST['redirectto'];
			header("location: enterpin.php");
			exit;
		}
		else
		{
			$uidd=$_POST['uid'];
			
			
			if(login ($_POST['uid'],$_POST['password'], 1))
			{
				
				if($_SESSION['access_code']!='')
				{
					$code=$_SESSION['access_code'];
					$used_by = getSingleValue("rb_invitation_codes","where code='$code'","used_by");
					
					if($used_by<=0)
					{
						$uid=$_SESSION['uid'];
						$GLOBALS ['mysqli'] = $mysqli;
						$select = "update rb_invitation_codes set used_by=$uid where code='$code'";
						$GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);
					}
				}
				foreach ($_POST as $name => $value)
				{
					//echo $name."=".$value."<br>";
					$_SESSION[$name]=$value;
					
					$time = time()+3600*24*365*10;
					
					if($name=='uid')
					{
						setcookie("uid", $value, $time, "/", '.ivc.travel');
					}
					elseif($name=='password')
					{
						//setcookie("password",$value,$time,"/", '.twnklchain.com');
					}
				}
				setcookie("remember",1,$time,"/", '.ivc.travel');	
				
				$uid=$_SESSION['uid'];
				
				
			}	
		}
	}
	
	if($_SESSION['redirectto']!='')
	{
		header("location: ".$_SESSION['redirectto']);
		exit;
	}
	
	header("location: home.php");
}
?>
