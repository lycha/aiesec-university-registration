<?php

$utm_source = urlencode(htmlspecialchars($_GET["utm_source"]));
$utm_medium = urlencode(htmlspecialchars($_GET["utm_medium"]));
$utm_campaign = urlencode(htmlspecialchars($_GET["utm_campaign"]));
$program = urlencode(htmlspecialchars($_GET["program"]));
$bucket = urlencode(htmlspecialchars($_GET["bucket"]));
$lc = urlencode(htmlspecialchars($_GET["lc"]));
$uniqid = urlencode(htmlspecialchars($_GET["uniqid"]));

if($bucket===""){
    $bucket = "n/d";   
}
//check if lead hererovided if not set to generic
if($utm_source===""){
    $utm_source = "generic";   
}
if($utm_medium===""){
    $utm_medium = "generic";   
}
if($utm_campaign===""){
    $utm_campaign = "generic";   
}

$cookie_name = 'aiesec_'.$program;
if(!isset($_COOKIE[$cookie_name])) {
    $cookie_value = array(
        'utm_source' => $utm_source,
        'utm_medium' => $utm_medium,
        'utm_campaign' => $utm_campaign,
        'bucket' => $bucket,
        'uniqid' => $uniqid,
        'registered' => 0,
        'lc' => $lc);
    $json = json_encode($cookie_value);
    setcookie($cookie_name, $json, time() + (86400 * 60), "/"); // 86400 = 1 day

    $string = file_get_contents("config.json");
    $config = json_decode($string, true);

	$url = $config['api_host'].'/api/v1/save-lead?uniqid='.$uniqid.'&utm_source='.$utm_source.'&utm_medium='.$utm_medium.'&utm_campaign='.$utm_campaign.'&program='.$program.'&bucket='.$bucket.'&lc='.$lc;
    //var_dump($url);
	$crl = curl_init($url);

	$headr = array();
	$accesstoken = $config['api_auth_token'];
	$headr[] = 'X-Authorization: '.$accesstoken;

	curl_setopt($crl, CURLOPT_HTTPHEADER,$headr);
	curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);

	$rest = curl_exec($crl);

	curl_close($crl);

	print_r($rest);
} else {
	print_r(json_encode(array(message=>'Lead already exists in database')));
}

?>