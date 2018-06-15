<?php
/*
  Plugin Name: VYPS Balance Shortcode Addon
  Description: Adds user balance shortcodes to the VYPS Plugin
  Version: 0.0.41
  Author: VidYen, LLC
  Author URI: https://vidyen.com/
  License: GPLv2 or later
 */

 /* 
* Oh this a copy and paste of the Coin Hive plugin which has now forked.
* 
*
 */
 
register_activation_hook(__FILE__, 'vyps_bc_install');

/* Removed all the database and table creation call as this addon does not need its own table
*  Or even an uninstall file as it just adds code fuctionality. Yes you can get on me for not
*  adding this to the base, but it was a Chris Roberts call. You can make your own version if
*  it bothers you that much.
*/



add_action('admin_menu', 'vyps_bc_submenu', 41 );

/* Creates submenu on the main VYPS plugin */

function vyps_bc_submenu() 
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "Balance Shortcode";
    $menu_title = 'Balance Shortcode';
	$capability = 'manage_options';
    $menu_slug = 'vyps_bc_page';
    $function = 'vyps_bc_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* Below is the functions for this plugin */

function vyps_bc_sub_menu_page() 
{ 
	/* No need to do calls on thsi this page. */
    
	echo
	"<br><br><img src=\"../wp-content/plugins/VYPS_base/logo.png\">
	<h1>Welcome to Balance Shortcode Addon Plugin</h1>
	<p>This plugin addon to the VYPS allow you add shortcodes so your users can see their own and other users balances.</p>
	<br><br>
	<h2>Shortcodes Syntax</h2>
	<p><b>[vyps-balance-list]</b></p>
	<p> Shows a list of all points with the current balance along with name for logged in user. They must be logged into see this.</p>
	<p><b>[vyps-balance-list pid=1 uid=12]</b></p>
	<p>Replace the numbers with desired numerical value of the pid and uid.</p>
	<p>The pid is the pointID number seen on the points list page along with the uid which is the user id in WordPress. Leaving the uid option out (ie. [vyps-balance-list pid=1]  will default to the logged on user.</p>
	<p>Note: Leaving the uid blank will tell the user they need to log in if you intend to show this to users who are not log in.</p>
	<p>Also Note: pid will default to 1 which is the first point you have unless you delete it. I would recommend specifing pid at all times.</p>
	<br><br>
	<h2>Here is a list of our other addons that go along with this system:</h2>
	<p>VYPS AdScend Addon</p>
	<p>VYPS Balance Shortcode Addon</p>
	<p>VYPS CoinFlip Addon</p>
	<p>VYPS Coinhive Addon</p>
	<p>VYPS Point Transfer Addon</p>
	<p>VYPS Public Log Shortcode Addon</p>
	<p>VYPS WooWallet Addon</p>	
	";
} 

/* I'm shouting in caps as I need to tell which shortcode is which */

/* LIST FUCNTION SHORTCODE
*  Because an admin might just want a full list without messing around with
*  variables etc. Why not just make a single shortcode with differing variables?
*  Because this is how I would like it if I was admin with no coding experience.
*  I could in theory make a pid or uid for this, but honesty just recontruct the
*  shortcode [vyps-balance] to do that. It's easier for an admin to do that with
*  WP than me to mess around on the code end. Do not mistake my generosity for
*  generosity.
*/

function bc_list_current_func() {
	
	/* Should check to see if user is logged in */
	/* Or at least it shouldn't show anything */

	if ( is_user_logged_in() ) {
		
		global $wpdb;
		//$table_log = $wpdb->prefix . 'vyps_point_log';
		$current_user_id = get_current_user_id();
		//Originally I had no idea what Orion was doing but $query_row is just a string to feed into another query. Redudant but works.
		$query_row = "select *, sum(points_amount) as sum from {$wpdb->prefix}vyps_points_log group by points, user_id having user_id = '{$current_user_id}'";
		$row_data = $wpdb->get_results($query_row);
		
		$points = '';
		
		if (!empty($row_data)) {
			foreach($row_data as $type){
				$query_for_name = "select * from {$wpdb->prefix}vyps_points where id= '{$type->points}'";
				$row_data2 = $wpdb->get_row($query_for_name);
				$points .= $type->sum . ' ' . $row_data2->name. '<br>';
			}
		} else {
			$points = '';
		}

		return $points;
		//return $value; //The two reutrn values should be dropped in future versions.
	
	} else {
		
		return "You need to be logged in to see your balance.";
		
	}
}
	
/* Telling WP to use function for shortcode
* THE CURRENT USER LIST SHORTCODE
* If I ever get around to it I might just put all the shortcode calls in there own section
* Like an educated coder would.
*/

add_shortcode( 'vyps-balance-list', 'bc_list_current_func');

/* shorcote for icon balance */

function bc_icon_list_current_func() {
	
	/* Should check to see if user is logged in */
	/* Or at least it shouldn't show anything */

	if ( is_user_logged_in() ) {
		
		global $wpdb;
		//$table_log = $wpdb->prefix . 'vyps_point_log';
		$current_user_id = get_current_user_id();
		//Originally I had no idea what Orion was doing but $query_row is just a string to feed into another query. Redudant but works.
		$query_row = "select *, sum(points_amount) as sum from {$wpdb->prefix}vyps_points_log group by points, user_id having user_id = '{$current_user_id}'";
		$row_data = $wpdb->get_results($query_row);
		
		$points = '';
		
		if (!empty($row_data)) {
			foreach($row_data as $type){
				$query_for_name = "select * from {$wpdb->prefix}vyps_points where id= '{$type->points}'";
				$row_data2 = $wpdb->get_row($query_for_name);
				//$points .= $row_data2->name. $type->sum . ' ' . '<br>';
				$points .= '<img src="'. $row_data2->icon .'" width="16" hight="16" title="'. $row_data2->name . '" >' . ' ' . $type->sum . ' ' . '<br>';
				/* I am not 100% certain but I believe the line above may be responsible for character plugin warning. */
				/* btw, manage_point.php has $d->icon where this uses $row_data2 one day we should make this all universally descriptive */
			}
		} else {
			$points = '';
		}

		return $points;
		//return $value; //This was a copy and paste and does nothing except troubleshoot
	
	} else {
		
		return 'You need to be logged in to see your balance.';
		
	}
}
	
/* Telling WP to use function for shortcode
* ICON
* If I ever get around to it I might just put all the shortcode calls in there own section
* Like an educated coder would.
*/

add_shortcode( 'vyps-balance-icon', 'bc_icon_list_current_func');

/*  General BALANCE SHORTCODE
*  By default this shows specific balance for current user
*  Because sometimes an admin wants to return just the number of
*  a single currency and not any text so they can handle the formatting
*  and all that other junk on their own.
*/

function bc_current_func( $atts ) {
	
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$current_user_id = get_current_user_id();
	
	/* BTW by default it's going to set the point id to 1
	*  I feel like I should just reuse this to have an override
    *  For the uid to specify any user.
	*  Also I used pid and uid here for the admins, not the coders
	*  As they need less to type when they set this up
	*/
	
	$atts = shortcode_atts(
		array(
				'pid' => '1',
				'uid' => $current_user_id,
		), $atts, 'vyps-balance' );
		
	$pointID = $atts['pid']; 
	$current_user_id = $atts['uid'];
	//I feel like putting the current user id into the array and back out is somehow bad coding practice.
	
	$balance_points = $wpdb->get_var( "SELECT sum(points_amount) FROM $table_name_log WHERE user_id = $current_user_id AND points = $pointID");
	
	
	/* Should check to see if uid = 0 which means user is not logged in and there is no uid set
	*  This is one of those philosophical design questions I am not interested in that could be played
	*  with for a very long time and no one is satisifed. Just set the damn uid if it bothers you.
	* I am really tempted to set this to blank for what I would use it for.
	*/
	
	if ( $current_user_id == 0 ) {
		
		$balance_points = "You need to be logged to see your balance!";
		//Admins, in this case specify a uid in the shortcode.
		
	} 
	
	return $balance_points;
	
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-balance', 'bc_current_func');	

/* Shortcode for the API call to create a lot entry */
/* There is some debate if this should be a button, but I'm just going to run on the code on page load and the admins can just make a button that runs the smart code if they want */