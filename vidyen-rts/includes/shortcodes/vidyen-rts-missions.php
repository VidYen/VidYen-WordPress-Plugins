<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//I'm going to do this all in a slew of both the balance if ww or not
//Using Ajax will update autoamtically
//NOTE: I've decided to this this with just one point and the WooWallet output for simplicity. If people want more they can ask.

function vidyen_rts_mission_func()
{

	//Check if user is logged in and stop the code.
	//NOTE:I moved this here. I realized, its more likely that 10,000 users are bashing site mem while the admin is almost always logged in.
	if ( !is_user_logged_in() )
	{
		return; //You get nothing. Use the LG code.
	}

	//NOTE: Guess what. We pull for SQL instead of short code. This helps with the AJAX

	//Currency will be listed as the poitn id
	$currency_id = intval(vyps_rts_sql_currency_id_func());
	$light_soldier_id = intval(vyps_rts_sql_light_soldier_id_func());
	$div_id = $point_id.'vyrtsdiv';
	$user_id = get_current_user_id();
	$light_soldier_amount = number_format(vyps_point_balance_func($light_soldier_id, $user_id), 0);
	//$vyps_points_output = vyps_point_balance_func($point_id, $user_id);

	//Get the url for the solver
	$mmo_ajax_js_url = plugins_url( 'js/mmo_bal.js', dirname(__FILE__) );

	//Concatenate the values into the output
	$mission_html_output =
		'<table>
			<tr>
				<th>Send Soldiers to raid poor peasant village for loot.<th>
			</tr>
			<tr>
				<td>Soldiers Required</td>
			</tr>
			<tr>
				<td><td><div style="font-size: 21px;"><span style="vertical-align: top;">'.vyps_point_icon_func($light_soldier_id).'</span> <span id="vyps_points" style="vertical-align: bottom;">'.$light_soldier_amount.'</span></div></td></td>
			</tr>
			<tr>
				<td>
				<div align="center">
					<button onclick="attack_peasants()">Tally hoe!!</button>
				</div>
				</td>
			<tr>
			<tr>

		</table>';

		return $ww_bal_html_output;

	$mission_html_output .= '<script src="'.$mmo_ajax_js_url.'"></script><script>var point_id = '.$point_id.';</script>';

	//A simple hack to run the part that if woocommerce installed.
	if (vidyen_mmo_woocommerce_check_func())
	{
		$ww_bal_html_output .= '<script>var woo_installed = 1;</script>';
	}
	else
	{
		$ww_bal_html_output .= '<script>var woo_installed = 0;</script>';
	}

	return $ww_bal_html_output;
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vidyen-rts-missions', 'vidyen_mmo_bal_func');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */
