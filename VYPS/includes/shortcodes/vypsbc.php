<?php

/*
*	Balance shortcode revision.
*	I realized that I should give more power to the admins in their flexibility
*	so made shortcodes to call specific pointIDs and user IDs.
*	Scrapping the old code as it was terrible.
*/

function vyps_balance( $atts ) {
	
	//Make sure user is logged in.
	if ( is_user_logged_in() ) {
		
		//Nothing
		
	} else {
		
		//If user is not logged in. Code needs to cease. I said CEASE!
		return;
		
	}
			
	$atts = shortcode_atts(
		array(
				'pid' => '0',
				'uid' => '0',
				'icon' => '1',
		), $atts, 'vyps-balance' );
	
	$pointID = $atts['pid'];
	$userID = $atts['uid'];
	$isIcon = $atts['icon'];

	//Ok if admin set an UID, they can override current user to see anyone
	//I realize in theory you could do this with WW as well.
	//Note. I set icon on by default. As if we have them. You should use them
	//But its possible for admin to set to no if they want
	
	if ( $userID == 0 ){
		
		$userID = get_current_user_id();
		
	}
	
	if ( $pointID == 0 ){
		
		return "Admin Error: pid not set!";
		
	}
	
	//Now for the balances.
	global $wpdb;
	$table_vyps_log = $wpdb->prefix . 'vyps_points_log';
	$table_vyps_points = $wpdb->prefix . 'vyps_points';
	
	$sourcePointID = $pointID; //reuse of code
	
	//name and icon
	$sourceName = $wpdb->get_var( "SELECT name FROM $table_vyps_points WHERE id= '$sourcePointID'" );
	$sourceIcon = $wpdb->get_var( "SELECT icon FROM $table_vyps_points WHERE id= '$sourcePointID'" );
	
	//balance
	$balance_points = $wpdb->get_var( "SELECT sum(points_amount) FROM $table_vyps_log WHERE user_id = $userID AND points = $pointID"); //Oooh. I love it when I get my variable names the same.
	
	if ($balance_points == ''){
		
		//Just a quick check to see if there were not points that it at least shows zero.
		$balance_points = 0;
		
	} 
	
	//Since we now can confirm its a number, let's add the commas
	$balance_points = number_format( $balance_points );
	
	if ( $isIcon == 1 ){
		
		//Make the output html have the Icon.
		$balance_output =  "<img src=\"$sourceIcon\" width=\"16\" hight=\"16\" title=\"$sourceName\"> $balance_points<br>";
						
	
	} elseif ( $isIcon == 0 ){
		
		$balance_output = $balance_points; //Just the raw data please
		
		//Return out since not logged in		
	} else {
		
		//If this else fires an admin put something other than a 1 or a zero which means they messed up.
		$balance_output = "Admin Error: Icon set to something other than 1 or 0.";
	}
	
	//Out it goes!
	return $balance_output;	
		
}

/* Send shortcode into WP */
add_shortcode( 'vyps-balance', 'vyps_balance');
