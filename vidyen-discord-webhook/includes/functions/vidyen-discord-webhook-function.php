<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function vidyen_discord_webhook_func($message, $username, $url)
{
    $data = array("content" => $message, "username" => $username);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($curl);
}

function vidyen_discord_message_generator()
{

}
