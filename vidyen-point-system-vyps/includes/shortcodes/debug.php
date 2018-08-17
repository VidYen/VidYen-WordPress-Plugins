<?php

//Shortcode itself.

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//NOTE: This is the shortcode we need to use going forward
//NOTE: Also, going forward there will be no simple miner you can display without consent button. Sorry. Not. Sorry.

//$remote_url = "http://vy256.com:8081/?userid=" . $miner_id;

function vyps_debug_func(){

  //wp_remote_get
  $remote_url = "http://vy256.com:8081/?userid=worker_11_48Vi6kadiTtTyemhzigSDrZDKcH6trUTA7zXzwamziSmAKWYyBpacMjWbwaVe4vUMveKAzAiA4j8xgUi29TpKXpm3wL5K5a_vidyenlive355";
  $remote_response =  wp_remote_get( esc_url_raw( $remote_url ) );
  $balance =  intval($remote_response['body']);

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
  $curl_result = $result;


  $remote_debug = "wp_remote_get test to VY256:<br>
    $balance<br>
    <br>
    curl test to VY256:<br>
    $result";

  return $remote_debug;

}

add_shortcode( 'vyps-debug', 'vyps_debug_func');
