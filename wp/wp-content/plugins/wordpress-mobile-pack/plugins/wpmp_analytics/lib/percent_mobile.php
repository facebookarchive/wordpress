<?php

//Note on variable names, they all start with percent_mobile to avoid naming conflicts

//COMMENT FOR USER:path of the path, change that if a tracking a specific path
define("PERCENT_MOBILE_COOKIE_PATH", "/");
//COMMENT FOR USER:duration of a visit/session in seconds, change that if you need different durations
define("PERCENT_MOBILE_VISIT_DURATION", 60*60);


//COMMENT FOR USER:IMPORTANT: change none of code below
define("PERCENT_MOBILE_VERSION", "php_wp_062710");
define("PERCENT_MOBILE_COOKIE_NAME", "_percent_mobile_c");


$percent_mobile_u=percent_mobile_rand();
$percent_mobile_v=percent_mobile_rand();

//is there an existing cookie that follows our cookie value pattern? (userid_expirationdate_visitid)
if (isset($_COOKIE[PERCENT_MOBILE_COOKIE_NAME]) && preg_match("/(\d+)[01]_(\d+)_(\d+)[01]/",$_COOKIE[PERCENT_MOBILE_COOKIE_NAME],$percent_mobile_matches) ){
	
	//read the userid and flag it with 0 to indicate already exisiting user
	$percent_mobile_u=$percent_mobile_matches[1]."0";
	//is the session still valid?
	if (($percent_mobile_matches[2]+0)>time()){

		//read the visit and flag it with 1 to indicate already exisiting user
		$percent_mobile_v=$percent_mobile_matches[3]."0";	
	}		
}
//set the cookie
setcookie (PERCENT_MOBILE_COOKIE_NAME,$percent_mobile_u.'_'.(time()+PERCENT_MOBILE_VISIT_DURATION).'_'.$percent_mobile_v,time()+(3600*24*365),PERCENT_MOBILE_COOKIE_PATH);

//write the html/js code that submits the tracking to the server
function percent_mobile_track($site_id,$url="") {	
	global $percent_mobile_u,$percent_mobile_v;
	
	if($url==""){ $url=percent_mobile_self_url();}
	
	//construct the image URL, all values are submitted via URL parameters, urls are urlencoded


	
	//if iphone submit the image via a dynamic image and determine if we got an 3gs with a fast performance test that consumes 20ms
	if(isset($_SERVER['HTTP_USER_AGENT'])&&preg_match("/\(iPhone/",$_SERVER['HTTP_USER_AGENT'])) {
		$image_url=((isset($_SERVER['HTTPS'])&&$_SERVER["HTTPS"]=="on")?"https":"http")."://tracking.percentmobile.com/pixel/".$site_id."/".rand(0, 0xffff).".gif?v=".PERCENT_MOBILE_VERSION."&us=".$percent_mobile_u."&vi=".$percent_mobile_v."&url=".urlencode($url)."&referer=".urlencode($_SERVER['HTTP_REFERER']);

echo<<<EOT
	<script type="text/javascript">
    {
	var m="";var s=document.createElement('style');var d=document.createElement('div');d.id="pm_IS_$site_id";
        s.innerText='@media (-webkit-min-device-pixel-ratio:2) {#'+d.id+'{display:none !important;}}';
        document.documentElement.appendChild(s).appendChild(d);
        var r=getComputedStyle(d,null).getPropertyValue('display')=='none';
        s.parentNode.removeChild(s);d.parentNode.removeChild(d);
		if(r){m="&m=4";}else{r=new Date().getTime();for(var s=0;new Date().getTime()-r<20;s++){Math.random();}m = "&m="+((s>1000)?"3":"2");}
        new Image().src='$image_url'+m;
    }	
	</script>
EOT;
		
	}
	//just render the image for all other platforms
	else
	{
		$image_url=((isset($_SERVER['HTTPS'])&&$_SERVER["HTTPS"]=="on")?"https":"http")."://tracking.percentmobile.com/pixel/".$site_id."/".rand(0, 0xffff).".gif?v=".PERCENT_MOBILE_VERSION."&amp;us=".$percent_mobile_u."&amp;vi=".$percent_mobile_v."&amp;url=".urlencode($url)."&amp;referer=".urlencode($_SERVER['HTTP_REFERER']);			
		echo '<img src="'.$image_url.'" width="2" height="2" alt="" />';
	}
}

function percent_mobile_rand() {
	
  	return rand(0, 0x7fffffff).rand(0, 99999)."1";
}
//recreate the current URL
function percent_mobile_self_url() {
	return "http".((isset($_SERVER['HTTPS'])&&$_SERVER["HTTPS"]=="on")?"s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}

?>