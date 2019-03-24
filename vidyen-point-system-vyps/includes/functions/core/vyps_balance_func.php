<?php

/*
*	Balance shortcode revision.
*	I realized that I should give more power to the admins in their flexibility
*	so made shortcodes to call specific pointIDs and user IDs.
*	Scrapping the old code as it was terrible.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function vyps_balance_func( $atts ) {

	//Make sure user is logged in.
	if ( is_user_logged_in() )
	{
		//Nothing
	}
	else
	{
		//If user is not logged in. Code needs to cease. I said CEASE!
		return;
	}

	$atts = shortcode_atts(
		array(
				'pid' => '0',
				'uid' => '0',
				'raw' => FALSE,
				'decimal' => 0,
		), $atts, 'vyps-balance' );

	$point_id = $atts['pid'];
	$userID = $atts['uid'];
	$isRaw = $atts['raw'];
	$decimal_format_modifier = intval($atts['decimal']); //This has to be a int or will throw the number format

	//Ok if admin set an UID, they can override current user to see anyone
	//I realize in theory you could do this with WW as well.
	//Raw is for if you want just the number. Otherwise it comes with the icon and commas. Perhaps I should break this off into a 3rd way.
	//But its possible for admin to set to no if they want

	if ( $userID == 0 )
	{

		$userID = get_current_user_id();

	}

	if ( $point_id == 0 )
	{

		return "Admin Error: pid not set!";

	}

	//Now for the balances.
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_points = $wpdb->prefix . 'vyps_points';

	$sourcePointID = $point_id; //reuse of code //I do not mind $point_id being called $sourcePointID rather than the current versus userID semantic.

	//name and icon

	//$sourceName = $wpdb->get_var( "SELECT name FROM $table_vyps_points WHERE id= '$sourcePointID'" );
	$sourceName_query = "SELECT name FROM ". $table_name_points . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
  $sourceName_query_prepared = $wpdb->prepare( $sourceName_query, $sourcePointID );
  $sourceName = $wpdb->get_var( $sourceName_query_prepared );

	//$sourceIcon = $wpdb->get_var( "SELECT icon FROM $table_vyps_points WHERE id= '$sourcePointID'" );
	$sourceIcon_query = "SELECT icon FROM ". $table_name_points . " WHERE id= %d";
	$sourceIcon_query_prepared = $wpdb->prepare( $sourceIcon_query, $sourcePointID );
	$sourceIcon = $wpdb->get_var( $sourceIcon_query_prepared );

	//balance
	//$balance_points = $wpdb->get_var( "SELECT sum(points_amount) FROM $table_vyps_log WHERE user_id = $userID AND points = $point_id"); //Oooh. I love it when I get my variable names the same.
	$balance_points_query = "SELECT sum(points_amount) FROM ". $table_name_log . " WHERE user_id = %d AND point_id = %d";
	$balance_points_query_prepared = $wpdb->prepare( $balance_points_query, $userID, $sourcePointID ); //NOTE: Originally this said $current_user_id but although I could pass it through to something else it would not be true if admin specified a UID. Ergo it should just say it $userID
	$balance_points = $wpdb->get_var( $balance_points_query_prepared );

	if ($balance_points == '')
	{

		//Just a quick check to see if there were not points that it at least shows zero.
		$balance_points = 0;

	}

	if ( $isRaw == FALSE )
	{

		//NOTE: Huh. I must have took this out by accident in the order of operations and didn't know it missing *coughs*
		$balance_points = number_format( $balance_points, $decimal_format_modifier ); //Currently doesn't really do much, but if you wanted a decimal. Its there.

		//Make the output html have the Icon.
		$balance_output =  "<img src=\"$sourceIcon\" width=\"16\" hight=\"16\" title=\"$sourceName\"> $balance_points<br>";

		//Since we now can confirm its a number, let's add the commas
		//NOTE: I'm taking a bit damn leap of faith here and I'm going to have to go back and fix this, but we need to only format, if its for icon.
		//Else it should be raw AND RAW it should else the commans screw stuff up. But since this is default for shorcode... I may just do $isIcon == 0 for my functions. Gah its problematic.

	}
	elseif ( $isRaw == TRUE )
	{

		$balance_output = $balance_points; //Just the raw data please. No formatting. NOTE: Youy will have to call for it if you use this function. Hrm... Maybe that should be at top.

		//Return out since not logged in
	}
	else
	{

		//If this else fires an admin put something other than a 1 or a zero which means they messed up.
		$balance_output = "Admin Error: Raw setting issue.";
	}

	//Out it goes!
	return $balance_output;

}
