<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//VY256 Worker Shortcode. Note the euphemisms.

function vidyen_discord_webhook_func($message)
{
    $data = array("content" => $message, "username" => "Webhooks");
    $curl = curl_init("https://discordapp.com/api/webhooks/590563023735881733/ps94Xw1QDIV4Qd9ACpCYGhHb-y678CrrN0gDfJ9KSxTP1zbz4Nhf4uMcGGrUeakBIjXo");
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($curl);
}

function vidyen_discord_webhook_test_func()
{
  $message = 'Here arrives the great and terrible webhook!';

  vidyen_discord_webhook_func($message);

  return 'Hello! It has run!'
}



/* Telling WP to use function for shortcode for sm-consent*/

add_shortcode( 'vidyen-discord-webhook-test', 'vidyen_discord_webhook_test_func');
