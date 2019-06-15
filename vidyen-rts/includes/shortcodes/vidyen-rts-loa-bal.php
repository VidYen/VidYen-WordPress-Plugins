<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//I'm going to do this all in a slew of both the balance if ww or not
//Using Ajax will update autoamtically
//NOTE: I've decided to this this with just one point and the WooWallet output for simplicity. If people want more they can ask.

function vidyen_rts_loa_bal_func()
{

	//NOTE: needs to replace  get_current_user_id()
	//Also NOTE game_id is not user id. Dumb $WPDB
	if(isset($_GET['user_id'])) //AND $_GET('get_key')
	{
		$game_id = sanitize_text_field(htmlspecialchars($_GET['user_id']));
		$user_id = 0; //Represents no user

		//$get_key = sanitize_text_field(htmlspecialchars($_GET['get_key']));

		//NOTE: I've changed my mind about the get key security.
		/*
		if($get_key != 'csod6132019')
		{
			return; //small security check. Will fix later. Basically people be playing game for you is the worst could happen.
		}
		*/
	}
	else
	{
		return; //You get nothing. Otherwise, your mining for no reward.
	}

	//Military
  $light_solider_point_id = vyps_rts_sql_light_soldier_id_func();

	//Civilian
	$laborer_point_id = vyps_rts_sql_laborer_id_func();

  //Resource IDs
  $currency_point_id = vyps_rts_sql_currency_id_func();
  $wood_point_id = vyps_rts_sql_wood_id_func();
  $iron_point_id = vyps_rts_sql_iron_id_func();
  $stone_point_id = vyps_rts_sql_stone_id_func();

  //Get user id
  $user_id = 0; //As I'm keeping both shortcodes the hell away from each other this will be zero.

  $currency_balance = intval(vyps_point_balance_func($currency_point_id, $user_id, $game_id)); //Yes we always be santising so whelp
  $wood_balance = intval(vyps_point_balance_func($wood_point_id, $user_id, $game_id)); //Yes we always be santising so whelp
  $iron_balance = intval(vyps_point_balance_func($iron_point_id, $user_id, $game_id)); //Yes we always be santising so whelp
  $stone_balance = intval(vyps_point_balance_func($stone_point_id, $user_id, $game_id)); //Yes we always be santising so whelp

	//Soldier balance
	$light_soldier_balance = intval(vyps_point_balance_func($light_solider_point_id, $user_id, $game_id)); //Yes we always be santising so whelp

	//Laborer Balance
	$laborer_balance = intval(vyps_point_balance_func($laborer_point_id, $user_id, $game_id)); //Yes we always be santising so whelp

	//Get the url for the solver
	$rts_ajax_js_url = plugins_url( 'js/rts_bal.js', dirname(__FILE__) );


	//Concatenate the values into the output
	$rts_bal_html_output =
		'<table>
			<tr>
			<td><div style="font-size: 21px;"><span style="vertical-align: top;">'.vidyen_rts_icon_func($currency_point_id).'</span> <span id="currency_balance" style="vertical-align: bottom;">'.$currency_balance.'</span></div></td>
			<td><div style="font-size: 21px;"><span style="vertical-align: top;">'.vidyen_rts_icon_func($wood_point_id).'</span> <span id="wood_balance" style="vertical-align: bottom;">'.$wood_balance.'</span></div></td>
			<td><div style="font-size: 21px;"><span style="vertical-align: top;">'.vidyen_rts_icon_func($iron_point_id).'</span> <span id="iron_balance" style="vertical-align: bottom;">'.$iron_balance.'</span></div></td>
			<td><div style="font-size: 21px;"><span style="vertical-align: top;">'.vidyen_rts_icon_func($stone_point_id).'</span> <span id="stone_balance" style="vertical-align: bottom;">'.$stone_balance.'</span></div></td>
			<td><div style="font-size: 21px;"><span style="vertical-align: top;">'.vidyen_rts_icon_func($light_solider_point_id).'</span> <span id="light_soldier_balance" style="vertical-align: bottom;">'.$light_soldier_balance.'</span></div></td>
			<td><div style="font-size: 21px;"><span style="vertical-align: top;">'.vidyen_rts_icon_func($laborer_point_id).'</span> <span id="laborer_balance" style="vertical-align: bottom;">'.$laborer_balance.'</span></div></td>
			</tr>
		</table>';

	$rts_bal_html_output .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="'.$rts_ajax_js_url.'"></script>';

	return $rts_bal_html_output;
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vidyen-rts-loa-bal', 'vidyen_rts_loa_bal_func');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */
