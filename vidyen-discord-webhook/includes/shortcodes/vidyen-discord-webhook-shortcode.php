<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function vidyen_discord_webhook_test_func()
{
  $message = 'Here arrives the great and terrible webhook!';

  $username = 'Webhook';

  $url = '';

  vidyen_discord_webhook_func($message, $username, $url);

  return 'Hello! It has run!';
}



/* Telling WP to use function for shortcode for sm-consent*/

add_shortcode( 'vidyen-discord-webhook-test', 'vidyen_discord_webhook_test_func');
