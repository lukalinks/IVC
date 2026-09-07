<?php
include("config.php");
include("functions.php");

function getRandFields($str, &$opt=array()){
    if(count($opt)>2){
        return true;			
    }else{
        $pos = rand(0,5);
            if(!isset($opt[$pos])){	
            $opt[$pos] = $str[$pos];
            }		
        getRandFields($str,$opt);	
    }			
}
$data34 = array();
$sKey = array();
$str = 'ab21cd';
getRandFields($str,$sKey);	
foreach($sKey as $i=>$key){ 
    $data34[$i]  = $i;
}
$time = time()+3600*24*365*10;
$_SESSION["skey"] = $data34;
$pinkey = base64_encode(json_encode($data34));

echo "Enter ONLY these digits, in this order:<br>";
                    
foreach ($_SESSION["skey"] as $b=>$rr){ echo " #".intval($rr+1); } 
echo "<br>of your Master PIN (or enter the full PIN).";
echo '<span id="pinkey-data" data-pinkey="' . htmlspecialchars($pinkey, ENT_QUOTES, 'UTF-8') . '" style="display:none"></span>';
?>