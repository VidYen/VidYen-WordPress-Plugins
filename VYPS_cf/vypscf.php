<?php
/*
  Plugin Name: VYPS CoinFlip Plugin Addon
  Description: Let's user have an RNG coin flip to bet VYPS points
  Version: 0.0.20
  Author: VidYen, LLC
  Author URI: https://vidyen.com/
  License: GPLv2 or later
 */


 
register_activation_hook(__FILE__, 'vyps_cf_install');

/* vypscf does need its own table. It will still need to call the vyps_point_log and have a 
*  need for an uninstall file. The way I envisionsed this is to have a log similar to the regular log
*  to show who won and who lost and how much was wagered. The table for wins/losses will be a shortcode
*  unto itself, but the game will be A (heads) or B (tales). So... Player A ID and player B ID two
*  seperate columns. The bid amount. I suppose since this isn't crypto we just deduct the points
*  From player A when he starts the toss. Then when player bid bids, either they get 2x points or
*  deducts 1x at that time since they initiate the toss. I feel like I should do a rock paper
*  scissors version but that might lead to player collusion since its less RNGey
*  So lets do it like this. Player runs the shortcode and no one has wagered it creates
*  a row with the current bet amount waiting for challenger/opponent
*  As this is an AB system, there is only one person putting up at a time
*  but its just the player B causes the game results to fire when they accept
*  Hopefully that is simple enough.
*/

/* 
* Installing the CoinFlip table below
*/
 
register_activation_hook(__FILE__, 'vyps_cf_install');

function vyps_cf_install() {
    global $wpdb;
	
	$message = ''; //yeah should set that somewhere

    $table_name_cf = $wpdb->prefix . 'vyps_cf'; //btw if you hadn't notice I always name my tables variables visually

    $charset_collate = $wpdb->get_charset_collate();
	
	/* Some table design thoughts. I only need, id, time, and who was the players and who won.
	*  But, people like to know what was the amount won, so putting that in there as well.
	*  Oh derr... Since one could in theory use different points, need an id for that as well.
	*  Since we aren't using API keys, the amount and point types will be decided by shortcode
	*  attributes by Admin. But have to check to make sure if they run two types that there are
	*  are two games going on at same time rather than having two players playing same game with
	*  different point types.
	*  I am debating whether or not to keep this with an uninstall file but the worse that can happen
	*  is that admin uninstalls and current game is lost with the record, but the main log will show
	*  the history.
	*/

    $sql = "CREATE TABLE {$table_name_cf} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		playerID tinytext NOT NULL,
		gameID tinytext NOT NULL,
		actionState tinytext NOT NULL,
		pointID varchar(11) NOT NULL,
		pointAmount double(64, 0) NOT NULL,
		outcome tinytext NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";
	    
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);

	/* 
	*  Originally, I did not feel I had to make default values, but because of troubleshooting
	*  issues, I need to test default states. Also made me create an uninstall file
	*/
	
	$action_state = 'FINALIZED';
	$outcome = 0;
	$table_name_cf = $wpdb->prefix . 'vyps_cf';
	
	$data = [
		'actionState' => $action_state,
		'outcome' => $outcome,
	];
	$data_id = $wpdb->insert($table_name_cf, $data);

}

add_action('admin_menu', 'vyps_cf_submenu', 14 );

/* Creates the CoinFlip submenu on the main VYPS plugin */

function vyps_cf_submenu() 
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "CoinFlip Game";
    $menu_title = 'CoinFlip Game';
	$capability = 'manage_options';
    $menu_slug = 'vyps_cf_page';
    $function = 'vyps_cf_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* Below is the functions for the shortcode */

function vyps_cf_sub_menu_page() 
{ 
	/* Actually I don't think I need to do calls on this page */
    
	echo
	"<br><br><img src=\"../wp-content/plugins/VYPS_base/logo.png\">
	<h1>Welcome to VYPS WooWallet Shortcode Addon Plugin</h1>
	<p>This plugin needs both VYPS and WooWallet to function. The intention is to allow a quick and easy bridge to use points for users to buy things with points on WooCommerce from their monetization activities.</p>
	<h2>Shortcodes Syntax</h2>
	<p><b>[vyps-cf bet=1000 pid=1]</b></p>
	<p>Function debits points from the VYPS system and opens game for another player to accept to bet.</p>
	<p>The pid is the pointID number seen on the points list page. This shortcode always requires the user to be logged in and will not let you use set the user id as you do not want other users messing with the balances.</p>
	<p>The earn attribute is how much currency the user earns in WooWallet. The spend attribute is how many VYPS points is spent.</p>
	<p>All attributes must be set for this to function. There is no interfact and is up to the site admin to add shortcode to a page or button. Future versions will include a better interface.</p>
	<h2>Here is a list of our other addons that go along with this system:</h2>
	<p>Coin Hive addon plugin</p>";
} 



/* I will need two short codes. One for the game result tables and one for the game itself. */

/* Below is the CoinFlip game shortcode itself */

function cf_func( $atts ) {
	
	/* Check to see if user is logged in and boot them out of function if they aren't. */
	
	if ( is_user_logged_in() ) {
		
		//I probaly don't have to have this part of the if
		
	} else {
		
		return "Well user doesn't seem logged in.";
		
	}
	
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_cf = $wpdb->prefix . 'vyps_cf';
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
				'bet' => '0',
		), $atts, 'vyps-cf' );
		
	$pointID = $atts['pid'];
	$cf_bet = $atts['bet'];


	/* if either earn or spend are 0 it means the admin messed up
	*  the shortcode atts and that you need to return out
	*  Shouldn't this all be set to elseifs? 
	*/
	
	if ( $cf_bet == 0 ) {
		
		return "Bet was 0!";
		
	}
	
	/* Oh yeah. Checking to see if no pid was set */
	
	if ( $pointID == 0 ) {
		
		return "You did not set pid!";
		
	}

	
	/* Ok. Now we get balance. If it is not enough for the bet variable, we tell them that and return out. NO EXCEPTIONS
	*  We do not want users to be able to play if they don't have enough points.
	*/
	
	$balance_points = $wpdb->get_var( "SELECT sum(points_amount) FROM $table_name_log WHERE user_id = $current_user_id AND points = $pointID");
	
	if ( $cf_bet >= $balance_points ) {
		
		return "You don't have enought points to bet!";
		
	}
	

	/* Ok. Keeping with the log format. Every action will create a row. 1 to bet,
	*  one to accept challeng, and 1 to show result
	*/
	
	/* Ok we need to figure out what last game row is whether it's bet, accept, or results.
	*  In my head, you don't really need results as the accept automatically runs the game
	*  so it should be bet or result in that column and outcome should be "TBD" if bet
	*  and a player ID if outcome. let me go back and fix the table
	*  I've called state... action_state and outcome... well outcome in the cf table
	*/
	
	$last_complete = 'FINALIZED'; //games that are done
	$last_open = 'TBD'; //games that are open
	//This finds the last finished game
	
	$current_open = $wpdb->get_var( "SELECT max(id) FROM $table_name_cf WHERE actionState = '$last_open'");
	$current_complete = $wpdb->get_var( "SELECT max(id) FROM $table_name_cf WHERE actionState = '$last_complete'");

	/* Probaly a terrible way to do this, but I'm checking to see if either exists and if not then set to zero.
	*  Would never happen on a table with data.
	*/
	
	if ($current_open == ""){
		$current_open = 0;
	}
	
	if ($current_complete == ""){
		$current_compete = 0;
	}
	
	
	
	/* Ok. If $current_complete > $current_open then we want a new game. ie subtract $cf_bet from gamer and then add row to log.
	*  $new_game = 0; game in progress
	*  $new_game = 1; time to new game execute game
	*/
	
	
	
	if ($current_complete >= $current_open){ //It dawned on me that if they are equal that means no games exist
		
		global $wpdb;
		$table_name_cf = $wpdb->prefix . 'vyps_cf';
		/* BTW for the new game option. One needs to record the current user ID, but for the roll, they only need to get
		*  the winner's id for the row.
		*/
		$current_user_id = get_current_user_id();
		
		//wait. look like $atts will go into an array.
		//Ok my code is a mess right now, but let's make it so it make a new row
		//ActionState should be a variable, but it's always going to be TBD if this if fires, sooo...
		$data = [
			'actionState' => 'TBD',
			//'points' => 1,
			//'pointAmount' => 0,
			'playerID' => $current_user_id,
			//'time' => date('Y-m-d H:i:s')
			];
		$wpdb->insert($table_name_cf, $data);
		//I'm guessing the above live works without re-calling the table? Probaly horribly wrong.
		
		/* Ok. The below god creates an negative entry on the log to put up the wager. I just copied
		*  and pasted from the ww plugin.
		*/
		
		$table_log = $wpdb->prefix . 'vyps_points_log';
		$reason = "CoinFlip Bet";
		$amount = $atts['bet'] * -1; //Seems like this is a bad idea to just multiply it by a negative 1 but hey

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
			
		return 'New game opened by UID: ' . $current_user_id . ' Points bet on match: ' . $atts['bet'];
		
	} else { /* I'm going out on a limb here if FINALIZED exists then we need to make a new game. There might be a scenario where this is not true and could go horribly wrong. */

		/* so if there is a current game running we need to finalize it */
		global $wpdb;
		$table_name_cf = $wpdb->prefix . 'vyps_cf';
		$current_user_id = get_current_user_id();
		
		/* Ok. Eventually you can start multiple games. Actually. It might be worth it to have multiple tables for each game?
		*  We will dig around for a solution down the road. But for not to see if you are the starter so you can't challenge
		*  yourself
		*/
		
		$last_open = 'TBD'; //games that are open
		$current_open = $wpdb->get_var( "SELECT max(id) FROM $table_name_cf WHERE actionState = '$last_open'");
		//I feel like the above and below could be solved in one line with an array get_results query, but I'll go back and fix someday
		$original_player_id = $wpdb->get_var( "SELECT playerID FROM $table_name_cf WHERE id = '$current_open'");
		
		//return 'ok what is happening here? current: ' . get_current_user_id() . ' and then the original: ' . $original_player_id;
		//ok it was because i could not spell original... maybe i should just say orgin player?
		
		if ($original_player_id == get_current_user_id()) {
		
			return 'You cannot challenge yourself!';
		
		}
		
		/* Ok. The below god creates an negative entry on the log (for the chalenger) to put up the wager. I just copied
		*  and pasted from the ww plugin.
		*/
		
		$table_log = $wpdb->prefix . 'vyps_points_log';
		$reason = "CoinFlip Bet";
		$amount = $atts['bet'] * -1; //Seems like this is a bad idea to just multiply it by a negative 1 but hey

		$pointType = $pointID; //Originally this was a table call, but seems easier this way
		$user_id = get_current_user_id();
		$data = [
				'reason' => $reason,
				'points' => $pointType,
				'points_amount' => $amount,
				'user_id' => $user_id,
				'time' => date('Y-m-d H:i:s')
				];
		$wpdb->insert($table_log, $data);
		
		/* Ok might as well put the RNG in now */
		$flip_result = mt_rand(1,2);
		
		/*if $flip_result is 1, then original player won, if 2 then challenger won */
		if ($flip_result == 1) {
			
			global $wpdb;
			$table_name_cf = $wpdb->prefix . 'vyps_cf';
			//Rerun the code to find the last open game and find the player id
			$last_open = 'TBD'; //games that are open
			$current_open = $wpdb->get_var( "SELECT max(id) FROM $table_name_cf WHERE actionState = '$last_open'");
			//I feel like the above and below could be solved in one line with an array get_results query, but I'll go back and fix someday
			$original_player_id = $wpdb->get_var( "SELECT playerID FROM $table_name_cf WHERE id = '$current_open'");
			
			$winner = $original_player_id;
			//return 'original player won and their id is: ' . $original_player_id;
		
		} else {
			
			$winner = get_current_user_id();
			//return 'challenger won and their id is simply current user, do we need to return that in test?';
		
		}	
		
		/* Below is the entry into the cf log */
		$data = [
			'actionState' => 'FINALIZED',
			//'points' => 1,
			//'pointAmount' => 0,
			'playerID' => $winner,
			//'time' => date('Y-m-d H:i:s')
			];
		$wpdb->insert($table_name_cf, $data);
		
		/* Ok. The below god creates an postive entry on the log to give the winning. I just copied
		*  and pasted from the ww plugin.
		*/
		
		$table_log = $wpdb->prefix . 'vyps_points_log';
		$reason = "CoinFlip Win";
		$amount = $atts['bet'] * 2; //Well, this should be positive and multiplied by 2 as well... you know.. they won...

		$pointType = $pointID; //Originally this was a table call, but seems easier this way
		$user_id = $winner; //Yeah this isn't current but who won
		$data = [
				'reason' => $reason,
				'points' => $pointType,
				'points_amount' => $amount,
				'user_id' => $user_id,
				'time' => date('Y-m-d H:i:s')
				];
		$wpdb->insert($table_log, $data);
		
		return 'Game closed. The winning ID of the match was: ' . $winner . ' Points won in match: ' . $amount;
	
	}
	
	/* here we need to start over again tomorrow Friday 25
	* Ok plan of action. Once new gaem is 1 then we need to deduct points and start a new game
	*/
	
	
	$endResult = $max_game_row . ' ere we go'. ' - ' . $last_complete . ' - ' . $table_name_cf . ' -current open: ' . $current_open. ' -current complete: ' . $current_complete;
	return $endResult;
	
	/* All right. If user is still in the function, that means they are logged in and have enough points.
	*  It dawned on me an admin might put in a negative number but that's on them.
	*  Now the danergous part. Deduct points and then add the VYPS log to the WooWallet
	*  I'm just going to reuse the CH code for ads and ducts. Ok back to here...
	
	*/
	
	/* The CH add code to insert in the vyps log */
	
	$table_log = $wpdb->prefix . 'vyps_points_log';
	$reason = "Market Transfer";
	$amount = $cf_spend * -1; //Seems like this is a bad idea to just multiply it by a negative 1 but hey

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
	


	return "It was supposed to have been completed.";
	
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-cf', 'cf_func');	

/* Shortcode for the API call to create a lot entry */
/* There is some debate if this should be a button, but I'm just going to run on the code on page load and the admins can just make a button that runs the smart code if they want */