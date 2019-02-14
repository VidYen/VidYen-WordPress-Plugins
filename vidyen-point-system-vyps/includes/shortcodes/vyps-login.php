<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/*** Shortcodes used to notify user needs lot log in ***/

//Developer NOTE: I have often found it necessary to make the user log in. I added a profile grid, but thats only works if you have profile grid installed.

/*** Function for text login awareness ***/

function vyps_login_func( $atts )
{
	// ATTR for admins to set whatever message they want or in whatever language
	$atts = shortcode_atts(
	array(
			'message' => 'You need to be logged in.',

	), $atts, 'vyps-lg' );

	$lgMessage = $atts['message'];

	// Check to see if user is logged in and boot them out of function if they aren't.
	if ( !is_user_logged_in() )
	{
		return $lgMessage;
	}
}

/*** Telling WP to use function for shortcode ***/

add_shortcode( 'vyps-lg', 'vyps_login_func');


/*** Profilegrid short code ***/

function vyps_profilegrid_login_func()
{
	//Only works if user is not logged in and profilegrid is  installed.... Hrm... just realized they use profiile magic and didn't chagne functions... lol
	if ( !is_user_logged_in() AND function_exists('vprofile_magic_login_form') )
	{
		$content = '[PM_Login]';

		return do_shortcode($content);
		//return ”;
	}
}

/*** Telling WP to use function for shortcode ***/
add_shortcode( 'vyps-pg-lg', 'vyps_profilegrid_login_func');
