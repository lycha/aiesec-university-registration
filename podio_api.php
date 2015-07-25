<?php

$fields = array(
	'first_name' => htmlspecialchars($_POST['first_name']),
	'last_name' => htmlspecialchars($_POST['last_name']),
	'email' => htmlspecialchars($_POST['email']),
	'phone_number' => htmlspecialchars($_POST['phone_number']),
	'utm_source' => htmlspecialchars($_POST['utm_source']),
	'utm_medium' => htmlspecialchars($_POST['utm_medium']),
	'utm_campaign' => htmlspecialchars($_POST['utm_campaign']),
	'program' => htmlspecialchars($_POST['program']),
	'lc' => htmlspecialchars($_POST['lc']),
	'localcommittee' => htmlspecialchars($_POST['localcommittee']),
	'language' => (int)($_POST['language']),
	'language-group' => (int)($_POST['language-group']),
	'promo_code' => htmlspecialchars($_POST['promo_code']),
);

require_once 'podio-php-master/PodioAPI.php';
require_once 'podio_keys.php';

$keys  = new PodioKeys();
$miasto = (object)$keys->getLCKeys($fields['localcommittee']);		
$url = 'https://podio.com/oauth/token';
$data = array('grant_type' => 'app', 'app_id' => $miasto->kursanci_id, 'app_token' => $miasto->kursanci_token, 
'client_id' => $miasto->client_id, 'redirect_uri' => 'http://aiesec.pl', 'client_secret' => $miasto->client_secret);

// use key 'http' even if you send the request to https://...
$options = array(
	'http' => array(
		'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		'method'  => 'POST',
		'content' => http_build_query($data),
		),
	);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

Podio::setup($miasto->client_id, $miasto->client_secret);

if($fields['promo_code']==''){
	$fields['promo_code'] = " ";
}

if($fields['phone_number']==''){
	$fields['phone_number'] = " ";
}

try {
  
  $output = Podio::authenticate('app', array('app_id' => $miasto->kursanci_id, 'app_token' => $miasto->kursanci_token));
  //var_dump($output);
  PodioItem::create($miasto->kursanci_id, array('fields' => array(
	  "title" => $fields['first_name'],
	  "nazwisko" => $fields['last_name'],
	  "e-mail" => $fields['email'],
	  "telefon" => $fields['phone_number'],
	  "jezyk-3" => $fields['language'],
	  "grupy-jezykowe" => $fields['language-group'],
	  "kod-promocyjny" => $fields['promo_code']
	  
	)));

}
catch (PodioError $e) {
  $e->body['error_description'];
  var_dump('<br><br>'.$e);
  // Something went wrong. Examine $e->body['error_description'] for a description of the error.
}

$website_url = $_POST['website_url'];
    /*if(strpos($website_url, '?')!=null){
        header("Location: ".$website_url."&thank_you=true");
    } else {
        header("Location: ".$website_url."?thank_you=true");
    }*/
?>