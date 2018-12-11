<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** RNG Quad Game ***/
//Here we go. Will be AJAX to simply click and bet points on wheel spin rewards.
//Shortcode etc. I'm not sure if this should be a function per se, but I feel that this should go in base. As its damn interesting concept.
//Going to copy the Raffle format except there will be no PvP but PvE (well player vs house). I kind of forgot that we can just... Well... Print points...
//Players betting on the house would be interesting but not really needed.

/*** The RNG Quads Function ***/

function vyps_rng_quads_func( $atts )
{

  $atts = shortcode_atts(
    array(
        'pid' => 1,
        'uid' => '0',
        'raw' => FALSE,
        'decimal' => 0,
    ), $atts, 'vyps-quads' );

  //Login check.
  if ( ! is_user_logged_in() )
  {
      return;
  }

  //Get the url for the Quads js
  $vyps_quads_jquery_folder_url = plugins_url( 'js/jquery/', __FILE__ );
  $vyps_quads_jquery_folder_url = str_replace('shortcodes/', '', $vyps_quads_jquery_folder_url); //having to reomove the folder depending on where you plugins might happen to be
  $vyps_quads_js_url =  $vyps_quads_jquery_folder_url . 'jquery-1.8.3.min.js';

  $starting_balance_html = vyps_balance_func( $atts );
  $pointID = $atts['pid'];
  $icon_url = vyps_point_icon_func($pointID);

  $vyps_rng_quads_html_output = "
    <script>
      var randomtime = setInterval(timeframe, 36);
      function timeframe() {
        document.getElementById('animated_number_output').innerHTML = Math.floor(Math.random()*10000) + Math.floor(Math.random()*1000) + Math.floor(Math.random()*100) + Math.floor(Math.random()*10);
      }

      function gettherng(multi) {
        if (multi === undefined){
          multi = 0;
        }
        document.getElementById(\"animated_number_output\").style.display = 'block'; // disable button
        document.getElementById(\"number_output\").style.display = 'none'; // enable button
        jQuery(document).ready(function($) {
         var data = {
           'action': 'vyps_run_quads_action',
           'multicheck': multi,
         };
         // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
         jQuery.post(ajaxurl, data, function(response) {
           output_response = JSON.parse(response);
           document.getElementById('number_output').innerHTML = output_response.full_numbers;
           document.getElementById('current_balance').innerHTML = output_response.post_balance;
            document.getElementById('reward_balance').innerHTML = output_response.response_text  + ': ';
           document.getElementById('reward_balance').innerHTML = output_response.reward;
           var elem = document.getElementById(\"texta\");
           elem.value += \"Date:[\" + new Date().toLocaleString() + \"]\";
           elem.value += \"\\n\";
           elem.value += \"Post Balance:[\" + output_response.post_balance + \"] \";
           elem.value += \"\\n\";
           elem.value += \"Reward:[\" + output_response.reward + \"] \";
           elem.value += \"\\n\";
           elem.value += output_response.response_text;
           elem.value += \"\\n\";
           elem.scrollTop = elem.scrollHeight;
           document.getElementById(\"animated_number_output\").style.display = 'none'; // disable button
           document.getElementById(\"number_output\").style.display = 'block'; // enable button
           //clearInterval(randomtime);
         });
        });
      }

      function spin_numbers() {
        randomtime = setInterval(timeframe, 36);
      }

    </script>
    <div align=\"center\"><span id=\"animated_number_output\" style=\"display:block\">0000</span></div>
    <div align=\"center\"><spawn id=\"response_text\"></span><span>$icon_url</span><span id=\"number_output\" style=\"display:none\">0000</span></div>
    <div id=\"bet_action1\" align=\"center\" style=\"width:100%;\"><button onclick=\"gettherng(1)\" style=\"width:50%;\">$icon_url 1</button><button onclick=\"gettherng(10)\" style=\"width:50%;\">$icon_url 10</button></div>
    <div id=\"bet_action2\" align=\"center\" style=\"width:100%;\"><button onclick=\"gettherng(100)\" style=\"width:50%;\">$icon_url 100</button><button onclick=\"gettherng(1000)\" style=\"width:50%;\">$icon_url 1000</button></div>
    <table>
      <tr><div align=\"center\"><span id=\"current_balance\">$starting_balance_html</span></div></tr>
      <tr><div align=\"center\"><span id=\"reward_balance\">$icon_url 0</span></div></tr>
    <table>
    <div align=\"center\"><textarea rows=\"4\" cols=\"50\" id=\"texta\"></textarea></div>
    ";

    return $vyps_rng_quads_html_output;
}

/*** Short Code Name for RNG quads ***/

add_shortcode( 'vyps-quads', 'vyps_rng_quads_func');

/*** PHP Functions to handle AJAX request***/

// register the ajax action for authenticated users
add_action('wp_ajax_vyps_run_quads_action', 'vyps_run_quads_action');

// handle the ajax request
function vyps_run_quads_action()
{

  global $wpdb; // this is how you get access to the database

  $incoming_multiplier = intval( $_POST['multicheck'] );

  //$whatever += 10;

  // add your logic here...
  $atts = shortcode_atts(
		array(
				'outputid' => 1,
				'outputamount' => 555,
        'refer' => 0,
				'to_user_id' => 1,
        'comment' => '',
    		'reason' => '',
				'btn_name' => 'runME',
        'raw' => FALSE,
        'cost' => 1000,
    ), $atts, 'vyps-pe' );

  //This is hardcoded for now.
  //$bet_cost = $atts['cost'];
  $bet_cost = $incoming_multiplier;

  $atts['to_user_id'] = get_current_user_id();
  $atts['pid'] = 3;
  $atts['firstid'] = 3;
  $atts['firstamount'] = $bet_cost;

  //Get current balance.
  $pre_current_user_balance = vyps_balance_func($atts);

  $atts['reason'] = 'QUADBET';

  //Deduct. I figure there is a check when need to run.
  $deduct_results = vyps_deduct_func( $atts );

  if ( $deduct_results == 0 ){

    //Not enough to play!
    $post_current_user_balance = vyps_balance_func($atts);

    $response_text = "NOT ENOUGH POINTS!";

    $rng_numbers_combined = $response_text;

    $reward_amount = 0;

    $rng_array_server_response = array(
        'first' => $digit_first,
        'second' => $digit_second,
        'third' => $digit_third,
        'fourth' => $digit_fourth,
        'full_numbers' => $rng_numbers_combined,
        'response_text' => $response_text,
        'pre_balance' => $pre_current_user_balance,
        'post_balance' => $post_current_user_balance,
        'reward' => $reward_amount,
    );

    //Get the random 4 digit number. Just testing... will get a better check later.
    //$rng_server_response = $digit_first . $digit_second . $digit_third . $digit_fourth . $response_text;

    echo json_encode($rng_array_server_response);

    wp_die(); // this is required to terminate immediately and return a proper response

  }

  $digit_first = mt_rand(0, 9);
  $digit_second = mt_rand(0, 9);
  $digit_third = mt_rand(0, 9);
  $digit_fourth = mt_rand(0, 9);

  //Some math Matic. If A = B and C = D and A = D, then B = C

  if (($digit_first == $digit_second) AND ($digit_third == $digit_fourth) AND (($digit_first == $digit_fourth)))
  {
    //WE got quads
    $response_text = "QUADS";
    $reward_amount = $bet_cost * 1000;
    $rng_numbers_combined = '<b>' . $digit_first . $digit_second . $digit_third . $digit_fourth . '</b>'; //Bolding for end user
  }
  elseif (($digit_first == $digit_second) AND ($digit_first == $digit_third))
  {
    //We got trips on first 3
    $response_text = "TRIPS";
    $reward_amount = $bet_cost * 100;
    $rng_numbers_combined = '<b>' . $digit_first . $digit_second . $digit_third . '</b>' . $digit_fourth; //First three bold
  }
  elseif (($digit_second == $digit_third) AND ($digit_second == $digit_fourth))
  {
    //trips on last 3
    $response_text = "TRIPS";
    $reward_amount = $bet_cost * 100;
    $rng_numbers_combined = $digit_first . '<b>' . $digit_second . $digit_third . $digit_fourth . '</b>'; //Last three bold
  }
  elseif ($digit_first == $digit_second)
  {
    //dubs on first 2
    $response_text = "DUBS";
    $reward_amount = $bet_cost * 10;
    $rng_numbers_combined = '<b>' . $digit_first . $digit_second . '</b>' . $digit_third . $digit_fourth; //First two
  }
  elseif ($digit_second == $digit_third)
  {
    //dubs on  middle 2
    $response_text = "DUBS";
    $reward_amount = $bet_cost * 10;
    $rng_numbers_combined = $digit_first . '<b>' . $digit_second . $digit_third . '</b>' . $digit_fourth; //Middle two
  }
  elseif ($digit_third == $digit_fourth)
  {
    //dubs on last 2
    $response_text = "DUBS";
    $reward_amount = $bet_cost * 10;
    $rng_numbers_combined = $digit_first . $digit_second . '<b>' . $digit_third . $digit_fourth . '</b>'; //Last two
  }
  elseif ($digit_first == $digit_second AND $digit_third == $digit_fourth )
  {
    //ddouble dubs
    $response_text = "DOUBLEDUBS";
    $reward_amount = $bet_cost * 500;
    $rng_numbers_combined = $digit_first . $digit_second . '<b>' . $digit_third . $digit_fourth . '</b>'; //Last two
  }
  else
  {
      //YOU GET NOTHING!
      $response_text = "FAIL";
      $reward_amount = 0;
      $rng_numbers_combined = $digit_first . $digit_second . $digit_third . $digit_fourth; //Fail. Has no bolding.
  }

  //$rng_numbers_combined = $digit_first . $digit_second . $digit_third . $digit_fourth;

  $atts['pid'] = 3;

  //Well if they won. They should get something.
  if ($reward_amount > 0  AND $deduct_results == 1)
  {
    $atts['reason'] = $response_text;
    $atts['outputid'] = 3;
    $atts['outputamount'] = $reward_amount;
    vyps_add_func( $atts );

  }

  $post_current_user_balance = vyps_balance_func($atts);

  $rng_array_server_response = array(
      'first' => $digit_first,
      'second' => $digit_second,
      'third' => $digit_third,
      'fourth' => $digit_fourth,
      'full_numbers' => $rng_numbers_combined,
      'response_text' => $response_text,
      'pre_balance' => $pre_current_user_balance,
      'post_balance' => $post_current_user_balance,
      'reward' => $reward_amount,
  );

  //Get the random 4 digit number. Just testing... will get a better check later.
  //$rng_server_response = $digit_first . $digit_second . $digit_third . $digit_fourth . $response_text;

  echo json_encode($rng_array_server_response);

  wp_die(); // this is required to terminate immediately and return a proper response
}
