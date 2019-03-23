<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Designed to show icon with the desired point

function vyps_wc_ww_bal_func($atts)
{
	//Only need to get the output id to show
	$atts = shortcode_atts(
		array(
				'outputid' => '0',
		), $atts, 'vyps-pe' );

	$pointID = $atts['outputid'];

  //Check if user is logged in and stop the code.
	//NOTE:I moved this here. I realized, its more likely that 10,000 users are bashing site mem while the admin is almost always logged in.
	if ( !is_user_logged_in() )
  {
		return; //You get nothing. Use the LG code.
	}

	$ww_current_balance = vyps_woowallet_bal_func();

	$ww_bal_html_output =  '<img src="'.vyps_point_icon_func($pointID).'" width="16" hight="16" title="'.vyps_point_name_func($pointID).'"> '.$ww_current_balance.'<br>"';

	return $ww_bal_html_output;
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-mmo-bal', 'vyps_wc_ww_bal_func');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */
