<?php
/*
  Plugin Name: VYPS Weighted Raffle Plugin Addon
  Description: Lets user have an weighted raffle to bet VYPS points
  Version: 0.0.06
  Author: VidYen, LLC
  Author URI: https://vidyen.com/
  License: GPLv2 or later
 */


 
register_activation_hook(__FILE__, 'vyps_wr_install');

/* vypswr does need its own table. It will still need to call the vyps_point_log and have a 
*  Some deisgn philosophy discussion. Coin Flip was an interesting plugin but only worked in
*  small amounts where as a weighted pool raffle would be more interesting. The way I envision
*  it is that you have 5 tickets. You can bet up to 4 times 1000 each. Or just once or twice
*  depending on how you feel. We probaly neeed an echo table for this. Say... The game...
*  Who is playing and how much they bet. Since I'm going to assume 5000 points. The table
*  will have 1 row per bet per game. Honestly, we could do this with a log. Meh. Bit compliated
*  the more I think about it. Maybe just a table with list of current players and how much they
*  bet total. How many bets to go before game is completed. The issue is figuring out history.
*  that could be solved with a log just for the wr. Why not just do that. Have a pure log.
*  a row that says points to go before raffle runs. Then a line that says winner. Sounds
*  simple enough. What could go wrong?
*/

/* 
* Installing the Weighted Raffle table below
*/
 
register_activation_hook(__FILE__, 'vyps_wr_install');

function vyps_wr_install() {
    global $wpdb;
	
	$message = ''; //yeah should set that somewhere

    $table_name_wr = $wpdb->prefix . 'vyps_wr'; //btw if you hadn't notice I always name my tables variables visually

    $charset_collate = $wpdb->get_charset_collate();
	
	/* So this is a bit more complicated than the coin flip. 
	* Need points remaining before game results caluclation.
	*/

    $sql = "CREATE TABLE {$table_name_wr} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		gameID mediumint(9) NOT NULL,
		betsRemain mediumint(9) NOT NULL,
		playerID tinytext NOT NULL,
		betID mediumint(9) NOT NULL,
		outcome mediumint(9) NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";
	    
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);

	/* 
	*  Originally, I did not feel I had to make default values, but because of troubleshooting
	*  issues, I need to test default states. Also made me create an uninstall file
	*/
	
	
	/* I actually don't think we need to create a row if we can check bestsRemain = ""
	$action_state = '0';
	$startPoint = 0;
	$table_name_wr = $wpdb->prefix . 'vyps_wr';
	
	$data = [
		'actionState' => $action_state,
		'pointRemaining' => $startPoint,
	];
	$data_id = $wpdb->insert($table_name_wr, $data);
	*/
}

add_action('admin_menu', 'vyps_wr_submenu', 19 );

/* Creates the CoinFlip submenu on the main VYPS plugin */

function vyps_wr_submenu() {
	
	$parent_menu_slug = 'vyps_points';
	$page_title = "VYPS Weighted Raffle Game";
    $menu_title = 'VYPS Weighted Raffle Game';
	$capability = 'manage_options';
    $menu_slug = 'vyps_wr_page';
    $function = 'vyps_wr_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* Below is the functions for the shortcode */

function vyps_wr_sub_menu_page() { 
	/* Actually I don't think I need to do calls on this page */
    
	echo
	"<h1>Welcome to VYPS Weighted Raffle Plugin</h1>
	<p>This plugin needs both VYPS and WooWallet to function. The intention is to allow a quick and easy bridge to use points for users to buy things with points on WooCommerce from their monetization activities.</p>
	<h2>Shortcodes Syntax</h2>
	<p><b>[vyps-wr bet=1000 pid=1]</b></p>
	<p>Function debits points from the VYPS system and credits it to the WooWallet system. Do not use quotes aroudn the nubmers.</p>
	<p>The pid is the pointID number seen on the points list page. This shortcode always requires the user to be logged in and will not let you use set the user id as you do not want other users messing with the balances.</p>
	<p>The earn attribute is how much currency the user earns in WooWallet. The spend attribute is how many VYPS points is spent.</p>
	<p>All attributes must be set for this to function. There is no interfact and is up to the site admin to add shortcode to a page or button. Future versions will include a better interface.</p>
	<h2>Here is a list of our other addons that go along with this system:</h2>
	<p>Coin Hive addon plugin</p>";
} 



/* I will need two short codes. One for the game result tables and one for the game itself. */

/* Below is the CoinFlip game shortcode itself */

function wr_func( $atts ) {
	
	/* Check to see if user is logged in and boot them out of function if they aren't. */
	
	if ( is_user_logged_in() ) {
		
		//I probaly don't have to have this part of the if
		
	} else {
		
		return "You are not logged in.";
		
	}
	
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_wr = $wpdb->prefix . 'vyps_wr';
	$current_user_id = get_current_user_id();
	
	/* 
	*  I feel like I should just reuse this to have an override
    *  For earn and spend the defaults are 0 if the admin forgets
	*  to specify it in the shortcode
	*  Pot is the shortcode attribute for what the pot is.
	*  Bet is how much per player bets  (this should be even or
	*  there might be rounding problems. To be fair, there should only be
	*  one winner per game.
	*/
	
	$atts = shortcode_atts(
		array(
				'pid' => '0',
				'bet' => '0',
				'pot' => '0',
		), $atts, 'vyps-wr' );
		
	$pointID = $atts['pid'];
	$wr_bet = $atts['bet'];
	$wr_pot = $atts['pot'];


	/* if either earn or spend are 0 it means the admin messed up
	*  the shortcode atts and that you need to return out
	*  Shouldn't this all be set to elseifs? 
	*/
	
	if ( $wr_bet == 0 ) {
		
		return "You did not set bet amount!";
		
	}
	
	/* Oh yeah. Checking to see if no pid was set */
	
	if ( $pointID == 0 ) {
		
		return "You did not set pid!";
		
	}
	
		if ( $wr_pot == 0 ) {
		
		return "You did not set the pot amount!";
		
	}

	
	/* Ok. Now we get balance. If it is not enough for the bet variable, we tell them that and return out. NO EXCEPTIONS
	*  We do not want users to be able to play if they don't have enough points.
	*/
	
	$balance_points = $wpdb->get_var( "SELECT sum(points_amount) FROM $table_name_log WHERE user_id = $current_user_id AND points = $pointID");
		
	if ( $wr_bet >= $balance_points ) {
		
		return "You don't have enought points to bet!";
		
	}
	

	/* Ok. Keeping with the log format. Every action will create a row. 1 to bet,
	*  one to accept challeng, and 1 to show result
	*/
	
	//$current_open = $wpdb->get_var( "SELECT max(id) FROM $table_name_wr WHERE actionState = '$last_open'");
	//$current_complete = $wpdb->get_var( "SELECT max(id) FROM $table_name_wr WHERE actionState = '$last_complete'");
	
	/* Going to do some returns to see where I go into this (should delete above variables, but need for reference
	*  Going to check to see if betsRemain is blank first and then go from there.
	*/
	
	$currentRow = $wpdb->get_var( "SELECT max(id) FROM $table_name_wr"); //I tried doing this in one line but.... Had perosnal issues.
	
	if ($currentRow == ''){ //Well null row check
		
		$betsRemain = 0;
		
	} else {
		
		$betsRemain = $wpdb->get_var( "SELECT betsRemain FROM $table_name_wr WHERE id = '$currentRow'"); //Getting the current bets remain on the last row
		
	}
	
	//If the betsRemain is zero its time to make a bet and do a new game
	
	if ($betsRemain == 0){ //Make a new game!
	
		global $wpdb;
		$table_name_wr = $wpdb->prefix . 'vyps_wr';
		/* BTW for the new game option. One needs to record the current user ID, but for the roll, they only need to get
		*  the winner's id for the row.
		*/
		$current_user_id = get_current_user_id();
		//Get the max gameID and add 1
		$oldGameID = $wpdb->get_var( "SELECT max(gameID) FROM $table_name_wr");
		$newGameID = $oldGame + 1;
		
		//Oh yeah. Need to find out how many bets there are going into this game.
		
		$newBetsRemain = ( $wr_pot / $wr_bet ) - 1; //I have a feeling someone is going to not make this even, but we will burn that bridge when we get to it.
		//Also if you want to know why I subbed 1 because I like to end on zero not 1 remaining.
		$betID = 1; //If this is a new game this will always be 1. It may not be the only one number 1 in that column but you can see the first bet in a game
		$outcome = 0; //Also this is zero because the 0 means no winner yet, but a number means playerID won on that row.
		
		$data = [
			'time' => date('Y-m-d H:i:s'),
			'gameID' => $newGameID,
			'betsRemain' => $newBetsRemain,
			'playerID' => $current_user_id,
			'betID' => $betID,
			'outcome' => $outcome,
			];
		$wpdb->insert($table_name_wr, $data);
		
		/* Ok we need to deduct the bet amount */
		
		$table_log = $wpdb->prefix . 'vyps_points_log';
		$reason = "Weight Raffle Entry";
		$amount = $atts['bet'] * -1; //Seems like this is a bad idea to just multiply it by a negative 1 but hey

		$pointType = $pointID; //Yes, it's up at top and should be good (I think)
		$user_id = $current_user_id;
		$data = [
				'reason' => $reason,
				'points' => $pointType,
				'points_amount' => $amount,
				'user_id' => $user_id,
				'time' => date('Y-m-d H:i:s')
				];
		$wpdb->insert($table_log, $data);
		
		return 'New game opened by UID: ' . $current_user_id . ' Points bet on match: ' . $atts['bet'] . ' On point type: ' . $pointType;
			
	} else {
		
		/* If bets remain are not zero we have to assume that either there is something messed up or that a new row... ie new bet should happen 
		*  Actually it dawned on me that an admin could set the pot and get the same... No clue why and it could break their table, but...
		*  they can just uninstall the plug and reinstall and it will delete table.
		*/
		
		global $wpdb;
		$table_name_wr = $wpdb->prefix . 'vyps_wr';
		
		/* BTW for the new game option. One needs to record the current user ID, but for the roll, they only need to get
		*  the winner's id for the row.
		*/
		
		/* for this else, we know that bets remain is not 0 so we should get max(id) and then whatever the bets remain of that was
		* and then minus one from that.
		*/
		$currentRow = $wpdb->get_var( "SELECT max(id) FROM $table_name_wr"); //Find the last row in the wr table
		//$lastBetsRemaining = $wpdb->get_var( "SELECT max(id) FROM $table_name_wr WHERE actionState = '$last_open'");
		//HERE is where we come back 5.29 and find what the bets remained and subtract 1 from it and then post
		

		$current_user_id = get_current_user_id();
		//Get the max gameID and add 1
		//$oldGameID = $wpdb->get_var( "SELECT max(gameID) FROM $table_name_wr");
		$newGameID = $oldGame + 1;
		
		//Oh yeah. Need to find out how many bets there are going into this game.
		
		$newBetsRemain = ( $wr_pot / $wr_bet ) - 1; //I have a feeling someone is going to not make this even, but we will burn that bridge when we get to it.
		//Also if you want to know why I subbed 1 because I like to end on zero not 1 remaining.
		$betID = 1; //If this is a new game this will always be 1. It may not be the only one number 1 in that column but you can see the first bet in a game
		$outcome = 0; //Also this is zero because the 0 means no winner yet, but a number means playerID won on that row.
		
		$data = [
			'time' => date('Y-m-d H:i:s'),
			'gameID' => $newGameID,
			'betsRemain' => $newBetsRemain,
			'playerID' => $current_user_id,
			'betID' => $betID,
			'outcome' => $outcome,
			];
		
	
		return 'Time for new row';
		
	}
}
		


/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-wr', 'wr_func');	

/* Shortcode for the API call to create a lot entry */
/* There is some debate if this should be a button, but I'm just going to run on the code on page load and the admins can just make a button that runs the smart code if they want */