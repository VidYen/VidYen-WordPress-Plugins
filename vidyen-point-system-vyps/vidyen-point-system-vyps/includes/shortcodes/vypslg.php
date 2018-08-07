<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


//You are not logged in return shortcode


/*** Shortcode without button ***/

function lg_func( $atts ) {

	/* ATTR for admins to set whatever message they want. */
	$atts = shortcode_atts(
	array(
			'message' => 'You need to be logged in.',

	), $atts, 'vyps-lg' );

	$lgMessage = $atts['message'];


	/* Check to see if user is logged in and boot them out of function if they aren't. */

	if ( is_user_logged_in() ) {

		//I probaly don't have to have this part of the if

	} else {

		return $lgMessage;

	}

}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-lg', 'lg_func');
