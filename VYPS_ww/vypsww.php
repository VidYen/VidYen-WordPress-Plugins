<?php
/*
  Plugin Name: VYPS WooWallet Addon
  Description: Adds shortcode to transfer VYPS points to WooWallet credit (requires both WooWallet and VYPS)
  Version: 0.0.15
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
*  be useful to trouble shoot. -Felty
*/



add_action('admin_menu', 'vyps_ww_submenu', 460 );

/* Creates the WW submenu on the main VYPS plugin */

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
	//Logo from base. If a plugin is installed not on the menu they can't see it not showing.
	echo '<br><br><img src="' . plugins_url( '../VYPS_base/images/logo.png', __FILE__ ) . '" > ';
    
	//WooWallet instructions. I'm tempted to link to the WooCommerce Wallet page, but maybe down the road
	echo
	"<h1>Welcome to VYPS WooWallet Shortcode Addon Plugin</h1>
	<p>This plugin needs both VYPS and WooCommerce Wallet to function. The intention is to allow a quick and easy bridge to use points for users to buy things with points on WooCommerce from their monetization activities.</p>
	<h2>Shortcodes Syntax</h2>
	<p><b>[vyps-ww earn=0.01 spend=1000 pid=1]</b></p>
	<p>Function debits points from the VYPS system and credits it to the WooWallet system. Do not use quotes aroudn the nubmers.</p>
	<p>The pid is the pointID number seen on the points list page. This shortcode always requires the user to be logged in and will not let you use set the user id as you do not want other users messing with the balances.</p>
	<p>The earn attribute is how much currency the user earns in WooWallet. The spend attribute is how many VYPS points is spent.</p>
	<p>All attributes must be set for this to function. There is no interface and is up to the site admin to add shortcode to a page or button. Future versions will include an actual interface.</p>
	<br><br>
	";
	
	//Credits include
	include( plugin_dir_path( __FILE__ ) . '../VYPS_base/includes/credits.php'); 
} 

/* 
*  Shortcode functions below.
*/

function ww_func( $atts ) {
	
	/* Check to see if user is logged in and boot them out of function if they aren't. */
	
	if ( is_user_logged_in() ) {
		
		//I probaly don't have to have this part of the if
		
	} else {
		
		return 'You are not logged in.';
		
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
		
		return 'Shortcode Error. Earn was 0.';
		
	}
	
	if ( $ww_spend == 0 ) {
		
		return 'Shortcode Error. Spend was 0.';
		
	}
	
	/* Oh yeah. Checking to see if no pid was set */
	
	if ( $pointID == 0 ) {
		
		return 'Shortcode Error. The pid was no set.';
		
	}

	
	//Ok. Now we get balance. If it is not enough for the spend variable, we tell them that and return out. NO EXCEPTIONS
	
	$balance_points = $wpdb->get_var( "SELECT sum(points_amount) FROM $table_name_log WHERE user_id = $current_user_id AND points = $pointID");
	
	if ( $ww_spend >= $balance_points ) {
		
		return 'Not enough points. You need a minimum of ' . $ww_spend . ' points to transfer credit to the WooWallet.';
		
	}
	

	
	
	/* All right. If user is still in the function, that means they are logged in and have enough points.
	*  It dawned on me an admin might put in a negative number but that's on them.
	*  Now the danergous part. Deduct points and then add the VYPS log to the WooWallet
	*  I'm just going to reuse the CH code for ads and ducts
	*/
	
	/* The CH add code to insert in the vyps log */
	
	$table_log = $wpdb->prefix . 'vyps_points_log';
	$reason = "WooWallet Transfer";
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
	/* I feel like if WooWallet coder realized balances were bad and logs were good, I wouldn't have to do the following */
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

	return 'Success. ' . $ww_spend . ' points used to earn ' . $ww_earn . ' in credit on the WooCommerce Wallet.';
	
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-ww', 'ww_func');	

/* Shortcode for the API call to create a lot entry */
/* There is some debate if this should be a button, but I'm just going to run on the code on page load and the admins can just make a button that runs the smart code if they want */