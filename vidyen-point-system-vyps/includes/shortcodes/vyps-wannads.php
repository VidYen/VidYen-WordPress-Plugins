<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** Wannads short code ***/

function vyps_wannads_func( $atts )
{

	// Check to see if user is logged in and boot them out of function if they aren't.

	if ( is_user_logged_in() )
	{

		//I probaly don't have to have this part of the if

	}

	else
	{

    return; //Admins should put the login shortcode for uniformity.
		//return "You need to be logged in to watch advertisments for points.";

	}



	$atts = shortcode_atts(
		array(
			'apikey' => '',
			'profile' => '0',
			'secretkey' => '',
			'outputid' => 0,
			'outputamount' => 0,
			'refer' => 0,
			'to_user_id' => 0,
			'comment' => '',
			'reason' => 'Wannads',
			'btn_name' => '';
		), $atts, 'vyps-as-watch' );

	//Honestly, the only think we need is the apikey. A bit different than Adscend in that regard.
	if ( $atts['apikey'] == '' ) {

		return "Wannads API Key not set was not set!";

	}

	$wannads_api_key = $atts['apikey'];

	//Get the user ID of course
	$current_user_id = get_current_user_id();

	//NOTE: Only post back determines API.

	//Branding!
	$VYPS_power_url = plugins_url() . '/vidyen-point-system-vyps/images/powered_by_vyps.png'; //Well it should work out.
	$VYPS_power_row = "Powered by <a href=\"https://wordpress.org/plugins/vidyen-point-system-vyps/\" target=\"_blank\"><img src=\"$VYPS_power_url\"></a><br><br>";

	//NOTE: Procheck is different that wannads referral *coughs*
	if (vyps_procheck_func($atts) == 1) {

		$VYPS_power_row = ''; //No branding if procheck is correct.

	}


	$wannads_iframe = '<iframe style="width:100%;height:800px;border:0;padding:0;margin:0;" scrolling="yes" "frameborder="0" src="https://wall.wannads.com/wall?apiKey=' . $wannads_api_key. . '&userId='$current_user_id'"></iframe>';

	return $VYPS_power_row . $wannads_iframe;

}

/*** Telling WP to use function for shortcode ***/
add_shortcode( 'vyps-wannads', 'vyps_wannads_func');
