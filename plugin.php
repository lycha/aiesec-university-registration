<?php
/*
Plugin Name: AIESEC University Registration 
Description: AIESEC University Registration plugin connected with AIESEC Poland Marketing tool
Version: 0.1
Author: Krzysztof Jackowski
Author URI: https://www.linkedin.com/profile/view?id=202008277&trk=nav_responsive_tab_profile_pic
License: GPL 
*/
wp_enqueue_script('jquery');
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );
require_once 'podio_keys.php';
require_once 'podio-php-master/PodioAPI.php';

function uni_form( $atts ) {
    $a = shortcode_atts( array(
        'lc' => '',
    ), $atts );
    
    $uniqid = uniqid();
    $utm_source = $_GET["utm_source"];
    $utm_medium = $_GET["utm_medium"];
    $utm_campaign = $_GET["utm_campaign"];
    $bucket = $_GET["bucket"];
    $lc = $a['lc'];
    $program = 'au';

    if($bucket==""){
        $bucket = "n/d";   
    }
    //check if lead parameters where provided if not set to generic
    if($utm_source==""){
        $utm_source = "generic";   
    }
    if($utm_medium==""){
        $utm_medium = "generic";   
    }
    if($utm_campaign==""){
        $utm_campaign = "generic";   
    }
    
    echo wp_enqueue_style( 'style-name', plugins_url('style.css', __FILE__ ));
    
    $form = file_get_contents('form.html',TRUE);
    
    $form = str_replace("{utm_source}",$utm_source,$form);
    $form = str_replace("{utm_medium}",$utm_medium,$form);
    $form = str_replace("{utm_campaign}",$utm_campaign,$form);
    $form = str_replace("{bucket}",$bucket,$form);
    $form = str_replace("{lc}",$lc,$form);
    $form = str_replace("{lc-form}",$a['lc'],$form);
    $form = str_replace("{uniqid}",$uniqid,$form);
    $form = str_replace("{program}",$program,$form);
    $form = str_replace("{path-manage_registration}",plugins_url('manage_registration.php', __FILE__ ),$form);
    $form = str_replace("{path-manage_leads}",plugins_url('manage_leads.php', __FILE__ ),$form);
    $form = str_replace("{path-gis_lcMapper}",plugins_url('gis_lcMapper.js', __FILE__ ),$form);
    $actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $form = str_replace("{website_url}",$actual_link,$form);
    $form = str_replace("{languages}",generateLanguages($a['lc']),$form);
    
    if($_GET["thank_you"]==="true"){
        return "<p>Dziękujemy bardzo za rejestrację. Wkrótce skontaktujemy się z Tobą w celu potwierdzenia uczestnictwa w kursie!</p>";  
    } elseif ($_GET["error"]!=""){
        
        $form = str_replace('<div id="error" class="error"><p></p></div>','<div id="error" class="error"><p>'.$_GET["error"].'</p></div>',$form);
        return $form;    
    }
    //var_dump( plugins_url('gis_reg_process.php', __FILE__ ));
    return $form;
}
add_shortcode( 'aiesec-uni-form', 'uni_form' );

function generateLanguages($lc)
{
    $keys  = new PodioKeys();
    $miasto = (object)$keys->getLCKeys($lc);
    //var_dump($miasto);

     //////////////////////GET languages//////////////
    $url = 'https://podio.com/oauth/token';
    $data = array('grant_type' => 'app', 'app_id' => $miasto->jezyki_id, 'app_token' => $miasto->jezyki_token, 
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
    //var_dump($result);

    $res = Podio::setup($miasto->client_id, $miasto->client_secret);

    try {
        $output = Podio::authenticate_with_app($miasto->jezyki_id, $miasto->jezyki_token);
        $jezykiRaw = PodioItem::filter($miasto->jezyki_id, array('sort_by' => 'created_on', "limit"=>200));
        //var_dump($jezykiRaw);
        $jezyki;
        if($jezykiRaw!=null){
            foreach ($jezykiRaw as $item) {
              //$jezyki[] = $item;
              if($item->fields[1]->values[0]['id'] == 1){
                $jezyki[$item->title] = array(
                    'language-id' => $item->item_id,
                    'language-groups' => array()
                    );
              }
            }
        }
    }
    catch (PodioError $e) {
      $e->body['error_description'];
      var_dump($e);
      // Something went wrong. Examine $e->body['error_description'] for a description of the error.
    }

    //////////////////////GET language groups//////////////
    $data = array('grant_type' => 'app', 'app_id' => $miasto->grupy_id, 'app_token' => $miasto->grupy_token, 
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
    //var_dump($result);

    $res = Podio::setup($miasto->client_id, $miasto->client_secret);


    try {
      
        $output = Podio::authenticate_with_app($miasto->grupy_id, $miasto->grupy_token);
        $grupyRaw = PodioItem::filter($miasto->grupy_id, array('sort_by' => 'created_on', "limit"=>200));
        //var_dump($grupyRaw);
        $grupy;
        if($grupyRaw!=null){
            foreach ($grupyRaw as $item) {
                $jezyk = $item->fields[1]->values[0]->title;
                if(array_key_exists($jezyk, $jezyki)){//language name
                    array_push($jezyki[$jezyk]['language-groups'], array(
                        'group-name' => $item->title, 
                        'group-id' => $item->item_id,
                        'group status' => $item->fields[2]->values[0]['text']));
                }
            }   
        }
        /*print "<pre>";
        print_r($jezyki);
        print "</pre>";*/
    }
    catch (PodioError $e) {
      $e->body['error_description'];
      //var_dump($e);
      // Something went wrong. Examine $e->body['error_description'] for a description of the error.
    }
    $form = '';
    //var_dump($jezyki);  
    if($jezyki!=null){
        ksort($jezyki);
    }
    $form = $form.'<div class="form-group languages">
        <div class="input-name">Wybierz język</div>
    
    <div class="input-field">';
            if($jezyki!=null){
                foreach ($jezyki as $keyJezyk => $valueJezyk) {

                $form = $form.' <input type="radio" onClick="languageRadioChange(this)" language="'.$keyJezyk.'" name="language" value="'.$valueJezyk['language-id'].'" required>'.$keyJezyk;
                }
            }
        $form = $form.'</div>
    </div>';
    if($jezyki!=null){
        foreach ($jezyki as $keyJezyk => $valueJezyk) {
            $form = $form.'<div class="form-group hidden no-padding" id="grupa-'.$keyJezyk.'">
                <div class="input-name">Wybierz grupę językową</div>
            <div class="input-field"> ';
                        
                        foreach ($valueJezyk['language-groups'] as $keyGrupa => $valueGrupa) {
                            $form = $form.'<input type="radio" name="language-group" value="'.$valueGrupa['group-id'].'" required>'.$valueGrupa['group-name'].'<br>';
                        }
                $form = $form.'</div>
            </div>';
        }
    }

    return $form;
}
?>
