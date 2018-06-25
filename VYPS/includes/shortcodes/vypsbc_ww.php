<?php
/*

	WW Balance update. NOW WITH ICON.

 */

/* LIST FUCNTION SHORTCODE
*  Because an admin might just want a full list without messing around with
*  variables etc. Why not just make a single shortcode with differing variables?
*  Because this is how I would like it if I was admin with no coding experience.
*  I could in theory make a pid or uid for this, but honesty just recontruct the
*  shortcode [vyps-balance] to do that. It's easier for an admin to do that with
*  WP than me to mess around on the code end. Do not mistake my generosity for
*  generosity.
*/

/* shorcote for icon balance */

function bc_ww_func() {
	
	/* Should check to see if user is logged in */
	/* Or at least it shouldn't show anything */

	//I need to add shortcodes to check for if it's in a menu
	
	if ( is_user_logged_in() ) {
		
		global $wpdb;
		$table_ww = $wpdb->prefix . 'woo_wallet_transactions';
		$current_user_id = get_current_user_id();
		
		//Poke the WW table and get balance. Stolen from my own code in VYPS_ww
		$last_trans_id = $wpdb->get_var( "SELECT max(transaction_id) FROM $table_ww WHERE user_id = $current_user_id");
		
		//Ok there are some issues wehere if you don't have a balance it blows up SQL
		//Transid should be 1 or greater if its exist if not.
		if ($last_trans_id > 1 ) { 
			
			//fire it and only do this wpdb if it legel. Otherwise just return
			$old_balance = $wpdb->get_var( "SELECT sum(balance) FROM $table_ww WHERE user_id = $current_user_id AND transaction_id = $last_trans_id");
			
		} else {
			
			//Well if they don't have a transaction ID they they don't have a balane
			//Note. OCD to make it a string 0f 0.00 which shouldn't matter because is a display and never used for calculations
			$old_balance = "0.00";
		
		}
		
		//This seems to be just built in to the plugin. I should check to see if its installed.
		$icon_url = WOO_WALLET_ICON;
				
		$balance_return = ''; //Note, I am changing this from the original points orion used. Eventually it will all be this. Descriptive variables. No exceptions!
		$walletURL = get_site_url() . '/my-account/woo-wallet/'; //I'm going to take a big assumption that this url will not change. What could go wrong?
			
		//Formatted for page and every day use. Doesn't look good in menus. Note the <br> and the placement of hte </a>
		$balance_return = "<a href=\"$walletURL\"><img src=\"$icon_url\" width=\"16\" hight=\"16\" title=\"My Wallet\"></a> \$$old_balance<br>"; //I forgot that WooWallet needs to be always hard coded.
		
		return $balance_return;
			
	} else {
		
		return; //No need to show you are not logged in error again and again
		
	}
}
	
/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-balance-ww', 'bc_ww_func');

/* I thought I could get away with attributes, but it seems that formatting overrides so making two shortcodes. One of the menus and want for the page. */
/* Only use the menu if you are hacking shortcodes into your menus */

function bc_ww_menu_func() {
	
	/* Should check to see if user is logged in */
	/* Or at least it shouldn't show anything */

	//I need to add shortcodes to check for if it's in a menu
	
	if ( is_user_logged_in() ) {
		
		global $wpdb;
		$table_ww = $wpdb->prefix . 'woo_wallet_transactions';
		$current_user_id = get_current_user_id();
		
		//Poke the WW table and get balance. Stolen from my own code in VYPS_ww
		$last_trans_id = $wpdb->get_var( "SELECT max(transaction_id) FROM $table_ww WHERE user_id = $current_user_id");
		
		//Ok there are some issues wehere if you don't have a balance it blows up SQL
		//Transid should be 1 or greater if its exist if not.
		if ($last_trans_id > 1 ) { 
			
			//fire it and only do this wpdb if it legel. Otherwise just return
			$old_balance = $wpdb->get_var( "SELECT sum(balance) FROM $table_ww WHERE user_id = $current_user_id AND transaction_id = $last_trans_id");
			
		} else {
			
			//Well if they don't have a transaction ID they they don't have a balane
			$old_balance = "0.00";
		
		}
		
		//This seems to be just built in to the plugin. I should check to see if its installed.
		$icon_url = WOO_WALLET_ICON;
		
		$balance_return = ''; //Note, I am changing this from the original points orion used. Eventually it will all be this. Descriptive variables. No exceptions!
		$walletURL = get_site_url() . '/my-account/woo-wallet/'; //I'm going to take a big assumption that this url will not change. What could go wrong?
			
		//URL is formatted differently for menus.
		$balance_return = "<a href=\"$walletURL\"><img src=\"$icon_url\" width=\"16\" hight=\"16\" title=\"My Wallet\"> \$$old_balance</a>";
				
		return $balance_return;
			
	} else {
		
		return; //No need to show you are not logged in error again and again
		
	}
}
	
/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-balance-ww-menu', 'bc_ww_menu_func');

