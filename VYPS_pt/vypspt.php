<?php
/*
  Plugin Name: VYPS Point Transfer Plugin Addon
  Description: Allows users to transfer VYPS point type to another at different rates.
  Version: 0.0.02
  Author: VidYen, LLC
  Author URI: https://vidyen.com/
  License: GPLv2 or later
 */


 
register_activation_hook(__FILE__, 'vyps_pt_install');

/* vypspt does not need its own table. It will need to call the vyps_point_log and the 
*  No need for an uninstall file as it just adds shortcode fuctionality. 
*  BTW if you haven't notice this is a copy and paste of the VYPS WooWallet system
*  as its just transfering points from one point type to another.
*  Will just use shortcodes like the WW to deduct and add
*  Some design philosophy discussion here.  Even though one makes much more income
*  through things like AdScend, I'm still a promoter of Coinhive because it gives you
*  independence from ad companies, but the exchange is like 1,000,000 hases to 0.009 USD
*  So there needs to be a system to transfer down depending. I'm not calling this an exchange
*  as an exhcange would be users buying and selling points for other points (or USD but not
*  one of our sites) with variable prices but that is a project down the road -Felty
*/



add_action('admin_menu', 'vyps_pt_submenu', 24 );

/* Creates the PT submenu on the main VYPS plugin to show instructions and that its installed */
/* The next time I do this I'm going to write up what all the plugin menus should be in order */

function vyps_pt_submenu() 
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "Point Transfer Plugin";
    $menu_title = 'Point Transfer Plugin';
	$capability = 'manage_options';
    $menu_slug = 'vyps_pt_page';
    $function = 'vyps_pt_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* Below is the functions for the shortcode */

function vyps_pt_sub_menu_page() 
{ 
	/* Actually I don't think I need to do calls on this page */
    
	echo
	"<br><br><img src=\"../wp-content/plugins/VYPS_base/logo.png\">
	<h1>Welcome to VPoints Transfer Shortcode Plugin</h1>
	<p>This plugin needs VYPS Base and two point types to function. The intention is to allow a quick and easy way for users to transfer one type of point to another at varrying rates</p>
	<h2>Shortcodes Syntax</h2>
	<p><b>[vyps-pt spid=1 dpid=2 samount=100000 damount=100]</b></p>
	<p>Function debits points from one point type to another with in being how many points used to transfer and out as how many points they get in the new point type</p>
	<p>The spid is the source pointID and the dpid is the destination seen on the points list page. This shortcode always requires the user to be logged in and will not let you use set the user id as you do not want other users messing with the balances.</p>
	<p>The earn attribute is how much currency the user gets in the other poitn type. The spend attribute is how many VYPS points is spent.</p>
	<p>All attributes must be set for this to function. There is no interfact and is up to the site admin to add shortcode to a page or button. Future versions will include a better interface.</p>
	<br><br>
	<h2>Here is a list of our other addons that go along with this system:</h2>
	<ul>
		<li>Coinhive addon plugin</li>
		<li><th>AdScend Plugin</li>
		<li>WooWallet Bridge Plugin</li>
		<li>CoinFlip Game Plugin</li>
		<li>Balance Shortcode Plugin</li>
		<li>Plublic Log Plugin</li>
	</ul>
	";
} 

function pt_func( $atts ) {
	
	/* Check to see if user is logged in and boot them out of function if they aren't. */
	
	if ( is_user_logged_in() ) {
		
		//I probaly don't have to have this part of the if
		
	} else {
		
		return "Well user doesn't seem logged in.";
		
	}
	
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$current_user_id = get_current_user_id();
	
	/* 
	*  I feel like I should just reuse this to have an override
    *  For earn and spend the defaults are 0 if the admin forgets
	*  to specify it in the shortcode
	*  
	*/
	
	$atts = shortcode_atts(
		array(
				'spid' => '0',
				'dpid' => '0',
				'samount' => '0',
				'damount' => '0',
		), $atts, 'vyps-pt' );
		
	$sourcePointID = $atts['spid'];
	$destinationPointID = $atts['dpid'];
	$pt_sAmount = $atts['samount'];
	$pt_dAmount = $atts['damount'];

	/* if either earn or spend are 0 it means the admin messed up
	*  the shortcode atts and that you need to return out
	*  Shouldn't this all be set to elseifs? 
	*/
	
	if ( $pt_sAmount == 0 ) {
		
		return "Source amount was 0!";
		
	}
	
	if ( $pt_dAmount == 0 ) {
		
		return "Destination amount was 0!";
		
	}
	
	/* Oh yeah. Checking to see if source pid was set */
	
	if ( $sourcePointID == 0 ) {
		
		return "You did not set source pid!";
		
	}
	
	/* And the destination pid */
	
	if ( $destinationPointID == 0 ) {
		
		return "You did not set destination pid!";
		
	}

	
	//Ok. Now we get balance. If it is not enough for the spend variable, we tell them that and return out. NO EXCEPTIONS
	
	$balance_points = $wpdb->get_var( "SELECT sum(points_amount) FROM $table_name_log WHERE user_id = $current_user_id AND points = $sourcePointID");
	
	if ( $pt_sAmount > $balance_points ) {
		
		return "You don't have enought points to transfer!";
		
	}
	

	
	
	/* All right. If user is still in the function, that means they are logged in and have enough points.
	*  It dawned on me an admin might put in a negative number but that's on them.
	*  Now the danergous part. Deduct points and then add the VYPS log to the WooWallet
	*  I'm just going to reuse the CH code for ads and ducts
	*/
	
	/* The CH add code to insert in the vyps log */
	
	$table_log = $wpdb->prefix . 'vyps_points_log';
	$reason = "Point Transfer";
	$amount = $pt_sAmount * -1; //Seems like this is a bad idea to just multiply it by a negative 1 but hey

	$PointType = $sourcePointID; //Originally this was a table call, but seems easier this way
	$user_id = $current_user_id;
	
	/* In my heads points out should happen first and then points destination. */
	
	$data = [
			'reason' => $reason,
			'points' => $PointType,
			'points_amount' => $amount,
			'user_id' => $user_id,
			'time' => date('Y-m-d H:i:s')
			];
	$wpdb->insert($table_log, $data);
	
	/* Ok. Now we put the destination points in. Reason should stay the same */
	
	$amount = $pt_dAmount; //Destination amount should be positive

	$PointType = $destinationPointID; //Originally this was a table call, but seems easier this way
	
	$data = [
			'reason' => $reason,
			'points' => $PointType,
			'points_amount' => $amount,
			'user_id' => $user_id,
			'time' => date('Y-m-d H:i:s')
			];
	$wpdb->insert($table_log, $data);
	


	return "$pt_sAmount points spent for $pt_dAmount earned";
	
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-pt', 'pt_func');	

/* Shortcode for the API call to create a lot entry */
/* There is some debate if this should be a button, but I'm just going to run on the code on page load and the admins can just make a button that runs the smart code if they want */