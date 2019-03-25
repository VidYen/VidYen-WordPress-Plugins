<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//I'm going to do this all in a slew of both the balance if ww or not
//NOTE: I've decided to this this with just one point and the WooWallet output for simplicity. If people want more they can ask.

function vyps_mmo_ajax_bal_func()
{
	//Check if user is logged in and stop the code.
	//NOTE:I moved this here. I realized, its more likely that 10,000 users are bashing site mem while the admin is almost always logged in.
	if ( !is_user_logged_in() )
	{
		return; //You get nothing. Use the LG code.
	}

	//NOTE: Guess what. We pull for SQL instead of short code. This helps with the AJAX

	$point_id = vyps_mmo_sql_point_id_func();
	$output_id = vyps_mmo_sql_output_id_func();
	$div_id = $point_id.'vymmodiv';
	$user_id = get_current_user_id();

	//Get the url for the solver
	$mmo_ajax_js_url = plugins_url( 'js/mmo_bal.js', dirname(__FILE__) );

	$vyps_bal_html_output = '<div id="'.$div_id.'">'.vyps_point_icon_func($point_id).' '.vyps_point_balance_func($point_id, $user_id).'</div>';
	$www_bal_html_output = '<div id="'.$div_id.'">'.vyps_point_icon_func($point_id).' '.vyps_point_balance_func($point_id, $user_id).'</div>';
	$js_html_output = '<script src="'.$mmo_ajax_js_url.'"></script><script>var point_id = '.$point_id.';</script>'; //this sets the poitn id for the mmo_bal.js

	return $ww_bal_html_output;
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-mmo-bal', 'vyps_wc_ajax_bal_func');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */
