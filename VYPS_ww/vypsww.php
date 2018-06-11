<?php
/*
  Plugin Name: VYPS WooWallet Plugin Addon
  Description: Adds user WooWallet interaction to the VYPS Plugin (requires WooWallet and VYPS)
  Version: 0.0.10
  Author: VidYen, LLC
  Author URI: https://vidyen.com/
  License: GPLv2 or later
 */


 
register_activation_hook(__FILE__, 'vyps_ww_install');

/* vypsww does not need its own table. It will need to call the vyps_point_log and the 
*  No need for an uninstall file as it just adds code fuctionality. 
*  Some design philosophy discussion here. WW Author has a log and a balance. Everyone wants to add
*  a balance because you don't need to sum the log, but I violently disagree. The log should be the truth,
*  not the balance. It should be summed every time and all the time. One could say... "Well you are taking up too
*  much resources doing the calculation each time." Yes, but if you have more than 10,000 users, well...
*  I feel good that you are sucessful using VYPS, but maybe you should roll your own. ALSO, because every VYPS plugin
*  will only touch the log, it only means there is one write for every log transaction. Where if you have a
*  balance, you have to touch the log and the balance every single time. Anyways... For the sake of campatiblity
*  I'm not going to rewrite WW but rather only touch the VYPS log (like CH in reverse) and then the balance and log for the WW
*  Its all in the same table anyways. Touching the log does not affect the actual balance in WW so I don't actually
*  have to mess with it if I was really lazy, but I'm sure if someone starts giving them free gift cards it would
*  be useful to trouble shoot. Rant off. -Felty
*/



add_action('admin_menu', 'vyps_ww_submenu', 13 );

/* Creates the Coin Hive submenu on the main VYPS plugin */

function vyps_ww_submenu() 
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "WooWallet Bridge";
    $menu_title = 'WooWallet Bridge';
	$capability = 'manage_options';
    $menu_slug = 'vyps_ww_page';
    $function = 'vyps_ww_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* Below is the functions for the shortcode */

function vyps_ww_sub_menu_page() 
{ 
	/* Actually I don't think I need to do calls on this page */
    
	echo
	"<h1>Welcome to VYPS WooWallet Shortcode Addon Plugin</h1>
	<p>This plugin needs both VYPS and WooWallet to function. The intention is to allow a quick and easy bridge to use points for users to buy things with points on WooCommerce from their monetization activities.</p>
	<h2>Shortcodes Syntax</h2>
	<p><b>[vyps-ww earn=1.25 spend=1000 pid=1]</b></p>
	<p>Function debits points from the VYPS system and credits it to the WooWallet system. Do not use quotes aroudn the nubmers.</p>
	<p>The pid is the pointID number seen on the points list page. This shortcode always requires the user to be logged in and will not let you use set the user id as you do not want other users messing with the balances.</p>
	<p>The earn attribute is how much currency the user earns in WooWallet. The spend attribute is how many VYPS points is spent.</p>
	<p>All attributes must be set for this to function. There is no interfact and is up to the site admin to add shortcode to a page or button. Future versions will include a better interface.</p>
	<h2>Here is a list of our other addons that go along with this system:</h2>
	<p>Coin Hive addon plugin</p>
	<p>AdScend Plugin</p>
	<p>WooWallet Bridge Plugin</p>
	<p>CoinFlip Game Plugin</p>
	<p>Balance Shortcode Plugin</p>
	<p>Plublic Log Plugin</p>";
} 

/* In my head I only think I will need one shortcode as it can specify which currency
*  to transfer and at which rate. I realize I can get really complicated, but 
*  why don't i just KISS and do it all shortcode rather than making a table for settings
*  yeah I could have did that with CH, but API keys in the shortcode are annoying.
*  so in that in mind. I will do it like this. You run the short code. It checks to see
*  if you have enough of a point. Then it transfers it at intervals. Like... You need
*  10,000 points to get $0.10 for AS points and 10,000,000 to get $0.01
*  Say [vyps-ww pid="2" spend="10000" earn="0.10"]
*  I could make a form, but that would take longer than I'd like and I'd put that in a
*  pro version if I was pressed. Sometimes giving the user too much freedom causes them
*  to do dumb things. Just press a button or open a page and get the credit if you have it. KISS
*  Considering, one should not be using other people's points, we assume that this is
*  always current user and that they are logged in (they must be and should be checked)
*  Also the log should be summed to see if they have the points to do the transation
*  along with an error telling them not enough points for transfer... Sooo...
*  1. Check for user logged in, 2. check to see if they have points, 3. add negative number to log
*  with deduction, 4. add addition to the woo_wallet_transaction 5. This could all go horribly wrong.
*/

function ww_func( $atts ) {
	
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
				'pid' => '0',
				'earn' => '0',
				'spend' => '0',
		), $atts, 'vyps-ww' );
		
	$pointID = $atts['pid'];
	$ww_earn = $atts['earn'];
	$ww_spend = $atts['spend'];

	/* if either earn or spend are 0 it means the admin messed up
	*  the shortcode atts and that you need to return out
	*  Shouldn't this all be set to elseifs? 
	*/
	
	if ( $ww_earn == 0 ) {
		
		return "Earn was 0!";
		
	}
	
	if ( $ww_spend == 0 ) {
		
		return "Spend was 0!";
		
	}
	
	/* Oh yeah. Checking to see if no pid was set */
	
	if ( $pointID == 0 ) {
		
		return "You did not set pid!";
		
	}

	
	//Ok. Now we get balance. If it is not enough for the spend variable, we tell them that and return out. NO EXCEPTIONS
	
	$balance_points = $wpdb->get_var( "SELECT sum(points_amount) FROM $table_name_log WHERE user_id = $current_user_id AND points = $pointID");
	
	if ( $ww_spend >= $balance_points ) {
		
		return "You don't have enought points!";
		
	}
	

	
	
	/* All right. If user is still in the function, that means they are logged in and have enough points.
	*  It dawned on me an admin might put in a negative number but that's on them.
	*  Now the danergous part. Deduct points and then add the VYPS log to the WooWallet
	*  I'm just going to reuse the CH code for ads and ducts
	*/
	
	/* The CH add code to insert in the vyps log */
	
	$table_log = $wpdb->prefix . 'vyps_points_log';
	$reason = "Market Transfer";
	$amount = $ww_spend * -1; //Seems like this is a bad idea to just multiply it by a negative 1 but hey

	$pointType = $pointID; //Originally this was a table call, but seems easier this way
	$user_id = $current_user_id;
	$data = [
			'reason' => $reason,
			'points' => $pointType,
			'points_amount' => $amount,
			'user_id' => $user_id,
			'time' => date('Y-m-d H:i:s')
			];
	$wpdb->insert($table_log, $data);
	
	/* Ok now we got to do it to the WooWallet transaction log
	*  Its pretty mcuh the same except different table column names
	*/
	
	$table_ww = $wpdb->prefix . 'woo_wallet_transactions';
	/* I feel like if WooWallet admin was more competant, I wouldn't have to do the following */
	/* I'm pulling the max transaction_id for the user and then creating a new one with the balance + earn to get the new balance on new row */
	$last_trans_id = $wpdb->get_var( "SELECT max(transaction_id) FROM $table_ww WHERE user_id = $current_user_id");
	//return $last_trans_id; //this was 7
	//$new_trans_id = $last_trans_id + 1; //Not needed as i think its auto increment
	$old_balance = $wpdb->get_var( "SELECT sum(balance) FROM $table_ww WHERE user_id = $current_user_id AND transaction_id = $last_trans_id");
	//return $old_balance; //this was 1.01 which is correct
	$new_balance = $old_balance + $ww_earn;
	//return $new_balance; //this was 3.01 which is also correct so it means the feed is not working
	$data_ww = [
		//'blog_id' => '1',
		'user_id' => $user_id,
		'type' => 'credit',
		'balance' => $new_balance,
		'currency' => 'VYP',
		'details' => 'VYPS',
		//'deleted' => 0,
		//'date' => date('Y-m-d H:i:s'),
		'amount' => $ww_earn,
		];
			
	//return $table_ww;
			
		$wpdb->insert($table_ww, $data_ww);
			
		//'transaction_id' => $new_trans_id,
		//I think the t_id gets autoinc

	return "$ww_spend points spent for $ww_earn earned";
	
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-ww', 'ww_func');	

/* Shortcode for the API call to create a lot entry */
/* There is some debate if this should be a button, but I'm just going to run on the code on page load and the admins can just make a button that runs the smart code if they want */