<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//I'm going to do this all in a slew of both the balance if ww or not
//NOTE: I've decided to this this with just one point and the WooWallet output for simplicity. If people want more they can ask.

function vyps_mmo_ajax_bal_func()
{
	/*
	//Check if user is logged in and stop the code.
	//NOTE:I moved this here. I realized, its more likely that 10,000 users are bashing site mem while the admin is almost always logged in.
	if ( !is_user_logged_in() )
	{
		return; //You get nothing. Use the LG code.
	}
	*/

	//NOTE: Guess what. We pull for SQL instead of short code. This helps with the AJAX

	$point_id = intval(vyps_mmo_sql_point_id_func());
	$output_id = intval(vyps_mmo_sql_output_id_func());
	$div_id = $point_id.'vymmodiv';
	$user_id = get_current_user_id();
	$vyps_points_output = number_format(vyps_point_balance_func($point_id, $user_id), 0);
	//$vyps_points_output = vyps_point_balance_func($point_id, $user_id);

	//Get the url for the solver
	$mmo_ajax_js_url = plugins_url( 'js/mmo_bal.js', dirname(__FILE__) );

	//Concatenate the values into the output
	$ww_bal_html_output =
		'<table>
			<tr>
			<td><div id="'.$div_id.'" style="font-size: 21px;"><span style="vertical-align: top;">'.vyps_point_icon_func($point_id).'</span> <span id="vyps_points" style="vertical-align: bottom;">'.$vyps_points_output.'</span>
			<span id="ww_points"><span style="vertical-align: top;">'.vyps_point_icon_func($output_id).' </span><span id="ww_points" style="vertical-align: bottom;"> '.vidyen_mmo_woowallet_bal_func($user_id).'</span></div>
			</td>
			</tr>
		</table>';

	$ww_bal_html_output .= '<script src="'.$mmo_ajax_js_url.'"></script><script>var point_id = '.$point_id.';</script>';

	return $ww_bal_html_output;
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-mmo-bal', 'vyps_mmo_ajax_bal_func');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */