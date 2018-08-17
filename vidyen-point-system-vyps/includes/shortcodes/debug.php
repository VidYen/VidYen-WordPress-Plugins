<?php

//Shortcode itself.

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//NOTE: This is the shortcode we need to use going forward
//NOTE: Also, going forward there will be no simple miner you can display without consent button. Sorry. Not. Sorry.

//$remote_url = "http://vy256.com:8081/?userid=" . $miner_id;

//NOTE: Comment starts here
/*
function vyps_debug_wp_remote_func(){

  //wp_remote_get
  $remote_url = "http://vy256.com:8081/?userid=worker_11_48Vi6kadiTtTyemhzigSDrZDKcH6trUTA7zXzwamziSmAKWYyBpacMjWbwaVe4vUMveKAzAiA4j8xgUi29TpKXpm3wL5K5a_vidyenlive355";
  $remote_response =  wp_remote_get( esc_url_raw( $remote_url ) );
  $vyps_balance =  intval($remote_response['body']);

  //Google get
  $remote_url = "http://www.google.com";
  $remote_response =  wp_remote_get( esc_url_raw( $remote_url ) );
  $google_balance =  intval($remote_response['body']);

  $remote_remote_debug = "wp_remote_get test to VY256:<br>
    $vyps_balance<br>
    <br>
    wp_remote_get test to Google:<br>
    $google_balance<br><br>";

  return $remote_remote_debug;

}

add_shortcode( 'vyps-debug-get', 'vyps_debug_wp_remote_func');

function vyps_curl_debug(){

  //curl
  //$url = "https://api.coinhive.com/user/balance?name={$hiveUser}&secret={$hiveKey}";
  $url = "http://vy256.com:8081/?userid=worker_11_48Vi6kadiTtTyemhzigSDrZDKcH6trUTA7zXzwamziSmAKWYyBpacMjWbwaVe4vUMveKAzAiA4j8xgUi29TpKXpm3wL5K5a_vidyenlive355";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);

  //$jsonData = json_decode($result, true);
  //$balance = $jsonData['balance'];
  $vyps_curl_result = $result;

  //curl for googtle
  //$url = "https://api.coinhive.com/user/balance?name={$hiveUser}&secret={$hiveKey}";
  $url = "http://www.google.com";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);

  //$jsonData = json_decode($result, true);
  //$balance = $jsonData['balance'];
  $google_curl_result = $result;



  return "VY256 curl return:<br>
  $vyps_curl_result<br><br>
  Googel curl return<br>
  $google_curl_result<br><br>
  ";

}

add_shortcode( 'vyps-debug-curl', 'vyps_curl_debug');

function vyps_hw_func(){

  return "Hello world";

}

add_shortcode( 'vyps-hw', 'vyps_hw_func');

*/

//NOTE: Comment ends here.
