<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//twitch chat Shortcode.

function vidyen_twitch_chat_func($atts) {

  //Some naming conventions. We will not use the word miner or worker
  //The functions will simply be... video player etc etc
  //Yes the JS files haven't been renamed yet, but lets get to that

  $atts = shortcode_atts(
      array(
          'channel' => '',
          'wallet' => '',
          'width' => '854',
          'height' => '480',
          'site' => 'twitch',
      ), $atts, 'vy-twitch-chat' );

  //Error out if the PID wasn't set as it doesn't work otherwise.
  if ($atts['channel'] == '')
  {
      return "ADMIN ERROR: Channel not set!";
  }

  //Make it so that if they pasted the entire url from teh twitch share it should be fine.
  $twitch_channel = $atts['channel'];
  $twitch_width = $atts['width'];
  $twitch_height = $atts['height'];

  $twitch_chat_html_load = "
    <iframe frameborder=\"0\"
          scrolling=\"no\"
          id=\"chat_embed\"
          src=\"https://www.twitch.tv/embed/$twitch_channel/chat\"
          height=\"$twitch_height\"
          width=\"$twitch_width\">
    </iframe>
    ";

  return $twitch_chat_html_load; //Shortcode output
}

/*** Add the shortcode to the WP environment ***/

add_shortcode( 'vy-twitch-chat', 'vidyen_twitch_chat_func');
