<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function vidyen_discord_webhook_func($message, $username, $url)
{
      $response = wp_remote_post( $url, array(
      	'method' => 'POST',
      	'timeout' => 45,
      	'redirection' => 5,
      	'httpversion' => '1.0',
      	'blocking' => true,
      	'headers' => array(),
      	'body' =>  array("content" => $message, "username" => $username),
      	'cookies' => array()
          )
      );

      if ( is_wp_error( $response ) ) {
           $error_message = $response->get_error_message();
           $response = $error_message;
           //echo "Something went wrong: $error_message";
        }
        else
        {
           //echo 'Response:<pre>';
           //print_r( $response );
           //echo '</pre>';
           $result = $response;
        }

    return $result;
}
