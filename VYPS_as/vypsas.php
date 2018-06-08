<?php
/*
  Plugin Name: VYPS AdScend Plugin Addon
  Description: Earn VYPS points by watching AdScend videos
  Version: 0.0.14
  Author: VidYen, LLC
  Author URI: https://vidyen.com/
  License: GPLv2 or later
 */


 
register_activation_hook(__FILE__, 'vyps_as_install');

/* Will need a table.
*  
*/

/* 
* Installing the AdScend table below
*/
 
register_activation_hook(__FILE__, 'vyps_as_install');

function vyps_as_install() {
    global $wpdb;
	
	$message = ''; //yeah should set that somewhere

    $table_name_as = $wpdb->prefix . 'vyps_as'; //btw if you hadn't notice I always name my tables variables visually

    $charset_collate = $wpdb->get_charset_collate();
	
	/* Some design thoughts. One could use a static table to hold the wall ID or various other static variables like
	*  a custom subid2 etc but that could be all kept in shortcode attributions. To keep WP less table flush (as
	*  given all the other woocomerce junk that will be floating around, we will keep this to a log only. Now why
	*  would you need a log for Adscend while you didn't have to with CoinHive is becasue the peopel who developed
	*  Adscend were not forward thinking and only give you a total. Sooo... The log has to be used as you see if the
	*  user did a post request and then make a log of what their Adscend points were at that time. Then when they
	*  post again see how much had changed between the last before post and now the current and award that difference
	*  It's all terrible and could go horribly wrong for botht he user and the site admin, but again... If AS made
	*  it like Coin Hive we would not have to do this. BTW for my sanities sake, I am going to make this for one
	*  point system only. You could in theory mess it up by having more than one point type with adscend
	*  but why would you? If you wan to, the issues are on you. There will be two shortcodes. 1. for the watching
	*  2. for the post redemption
	*  It dawned on me that the as log should tell you what it thought the last lead was from the pior last row
	*  May not bee completely necessary but it would be nice to know least someone spams the F5 button and somehow
	*  breaks it.
	*/
	
	/* Decided no longer required to make the table as apparently AdScend actually has a API despite the contrary

    $sql = "CREATE TABLE {$table_name_as} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		playerID tinytext NOT NULL,
		pointID varchar(11) NOT NULL,
		leads double(64, 0) NOT NULL,
		priorLeads double(64, 0) NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";
	    
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);
	
	*/

	//Should be no need to have default data
	
}

/* Ok above is the install table creation need to set values
*  And then tie in function. Honestly want to see if function comes in first.
*  Need Adscend shortcode
*/

add_action('admin_menu', 'vyps_as_submenu', 17 );

/* Creates the AdScend submenu on the main VYPS plugin */

function vyps_as_submenu() 
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "VYPS AdScend Addon";
    $menu_title = 'VYPS AdScend Addon';
	$capability = 'manage_options';
    $menu_slug = 'vyps_as_page';
    $function = 'vyps_as_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* Below is the functions for the shortcode */

function vyps_as_sub_menu_page() 
{ 
	/* Actually I don't think I need to do calls on this page */
    
	echo
	"<h1>Welcome to VYPS AdScend Shortcode Addon Plugin</h1>
	<p>This plugin needs VYPS and an Adscend Account to function. The intention is to allow a quick and easy way for you to award user points for Adscend Activity.</p>
	<h2>Shortcodes Syntax</h2>
	<p><b>[vyps-as-watch pub=1000 profile=5000 pid=1]</b></p>
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



/* I will need two short codes. One for the game result tables and one for the game itself. */

/* Below is the AdScend game shortcode itself */

function as_watch_func( $atts ) {
	
	/* Check to see if user is logged in and boot them out of function if they aren't. */
	
	if ( is_user_logged_in() ) {
		
		//I probaly don't have to have this part of the if
		
	} else {
		
		return "You need to be logged in to watch ads for points.";
		
	}
	
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_as = $wpdb->prefix . 'vyps_as';
	$current_user_id = get_current_user_id();
	
	/* 
	*  I feel like I should just reuse this to have an override
    *  For earn and spend the defaults are 0 if the admin forgets
	*  to specify it in the shortcode
	*  
	*/
	
	$atts = shortcode_atts(
		array(
				'pub' => '0',
				'profile' => '0',
				'pid' => '0',
		), $atts, 'vyps-as-watch' );

	/* if either earn or spend are 0 it means the admin messed up
	*  the shortcode atts and that you need to return out
	*  Shouldn't this all be set to elseifs? 
	*/
	
	if ( $atts['pub'] == 0 ) {
		
		return "Publisher was not set!";
		
	}
	
	/* Oh yeah. Check Profile */
	
	if ( $atts['profile'] == 0 ) {
		
		return "You did not a profile!";
		
	}
	
	/* Oh yeah. Checking to see if no pid was set */
	
	if ( $atts['pid'] == 0 ) {
		
		return "You did not set point ID!";
		
	}
    
	//note the subid2 was to satisfy my curiosity about the AS backend.
	
	return '<iframe src="https://asmwall.com/adwall/publisher/' . $atts['pub'] . '/profile/' . $atts['profile'] . '?subid1=' . $current_user_id . '&subid2=' . $atts['pid'] . '" frameborder="0" allowfullscreen="yes" width=800 height=600 ></iframe>';
	
	/* It dawned on me that thie return may not be necessary  and that for this particualr shortcode it was unnecessary to
	*  actually have it post anything to our WP tables as the AS interface doesn't do that until you get a post back.
	*  It also dawned on me I could just call pid sub id etc, but not keeping the names same may confuse.
	*  maybe subid3 could be whatever the user wants?
	*/
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-as-watch', 'as_watch_func');

/* Redemption function shortcode.
*  Should check for current user and all the normal stuff.
*  Input should have a pid just so admin can hardcode what point type payout will be
*  Also payout = 1000 per lead IMO to make people think they got stuff to play with
*  compared to coin hive hashes that is without getting too high up in the places
*  so shortcode should be [vyps-as-redeem pub=1000 profile=5000 pid=1 payout=1000] In theory you can set
*  payout to anything. The pub and profile as variables as one could in theory have more than one site and even
*  more than one wall or whatever on a single site.
*/

function as_redeem_func( $atts ) {
	
	/* Do the logged on check first as I guess it wastes less resources */
	
	if ( is_user_logged_in() ) {
		
		//I probaly don't have to have this part of the if
		
	} else {
		
		return "You need to be logged in to watch ads for points.";
		
	}
	
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_as = $wpdb->prefix . 'vyps_as';
	$current_user_id = get_current_user_id();
	
	$atts = shortcode_atts(
		array(
			'pub' => '0',
			'profile' => '0',
			'api' => 'z',
			'pid' => '0',
			'payout' => '0',
		), $atts, 'vyps-as-redeem');
	
	/* do the normal checks to see if the $atts were set */
	
	if ( $atts['pub'] == 0 ) {
		
	return "Publisher was not set!";
		
	}
	
	/* Oh yeah. Check Profile */
	
	if ( $atts['profile'] == 0 ) {
		
		return "You did not a profile!";
		
	}
	
	/* Oh yeah. Checking to see if no API was set
	*  Yeah I didn't like not putting APIs in shortcode
	*  But Adscend was being a pain. Oh. The API key is on
	*  the integration page on your offer wall under API/SDK
	*  integration. It doesn't even look like its a menu.
	*  It's like that scend in HHG2G trying to get to the form.
	*  It's a shame Adscend didn't copy Coin Hive.
	*/
	
	//return $atts['api']; //return here to see why api key was not working
	
	
	/* API key will never be a single character in theory but I needed something easy to check #lazycoding */
	if ( $atts['api'] == 'z' ) { 
		
		return "You did not set the API Key!";
		
	}
	
	/* Oh yeah. Checking to see if no pid was set */
	
	if ( $atts['pid'] == 0 ) {
		
		return "You did not set point ID!";
		
	}
	
	//In theory one could set their payout to be 0 on purpose, but if you are that kind of person just comment this if out
	
	if ( $atts['payout'] == 0 ) {
		
		return "You did not set payout!";
		
	}

	/* I have a feeling I could check the whole array in one go, but one day I will educate myself better */
	
	/* Ok now we need to post the current leads to the as table and then check to see if there is more than
	*  one row with that user id and then if so caclulate the difference and post that reward to the vyps log
	*  the default leads will be zero so if there wasn't a row to begin with then all the points get awarded
	*/
	
	/* Hrm... The below does generate the correct json but its not pulling for some reason soo... I'm going to use the CH version */
	/* It dawned on me that the ' ' in arrays might be the problem but below is copy and paste from coin hive*/
	
	$pub_id = $atts['pub'];
	$adwall_id = $atts['profile'];
	//$sub_id = 4; //I don't running those ads on my development machine
	$sub_id = $current_user_id; //ok the testing words so lets use another profile
	
	/* The get curl */
	
	$url = "https://adscendmedia.com/adwall/api/publisher/{$pub_id}/profile/{$adwall_id}/user/{$sub_id}/transactions.json";
	
	//Note Api says no https but well I feel it should be so and it seems to work					
	
	$as = curl_init();
	curl_setopt($as, CURLOPT_URL, $url);
	curl_setopt($as, CURLOPT_HEADER, 0);
	curl_setopt($as, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($as);
	curl_close($as);
										
	$jsonData = json_decode($result, true);
	$balance = $jsonData['currency_count'];
	
	//echo " pre foo ";
	//echo $balance;
	//echo " post foo ";
	
	/* the post to deduct the currency */
	
	//
	// A very simple PHP example that sends a HTTP POST to a remote site
	//
	
	$api_key = $atts['api'];
	
	$adj_balance = $balance * -1; //Well. Apparently you can give your viewers more points for no good reason I guess. So we need negative values.
	
	$url = "https://adscendmedia.com/adwall/api/publisher/{$pub_id}/profile/{$adwall_id}/user/{$sub_id}/transactions.json";
	//$url = "https://adscendmedia.com/adwall/api/publisher/{$pub_id}/profile/{$adwall_id}/user/{$sub_id}/transactions.json?api_key={$api_key}&currency_adjustment={$adj_balance}";
	
	
	$as = curl_init();									
	curl_setopt($as, CURLOPT_URL, $url);
	curl_setopt($as, CURLOPT_POST, 1);
	curl_setopt($as, CURLOPT_POSTFIELDS,
		"api_key={$api_key}&currency_adjustment={$adj_balance}");
										
	// in real life you should use something like:
	// curl_setopt($ch, CURLOPT_POSTFIELDS, 
	//          http_build_query(array('postvar1' => 'value1')));
								
	// receive server response ...
	curl_setopt($as, CURLOPT_RETURNTRANSFER, true);
									
	$server_output = curl_exec ($as);
										
	curl_close ($as);
	
	/* OK. Pulling log table to post return to it. What could go wrong? */
	/* Honestly, we should always refer to table by the actual table?   */
		
	/* Just checking to see if balance is 0. If it is, no need to do anything other than return the results.*/
	if( $balance > 0 )
		{
			global $wpdb;
			
			$table_log = $wpdb->prefix . 'vyps_points_log';
			$reason = "Adscend";
			$amount = $balance * $atts['payout']; //

			//$pointType = $wpdb->get_var( "SELECT * FROM $table_ch", 6, 0 ); //it dawned on me I could make the CH not have its own table maybe for 2.0
			$pointType = $atts['pid']; //
			$user_id = get_current_user_id();
				$data = [
					'reason' => $reason,
					'points' => $pointType,
					'points_amount' => $amount,
					'user_id' => $user_id,
					'time' => date('Y-m-d H:i:s')
				];
			$wpdb->insert($table_log, $data);
		} else {
			$amount = 0; //I think this works right. Ere we go!
		}
		
		/* It dawned on me that text in here only needs a number and let the admin right there response
		*  One could in theory might make an IF statement you have no hashes to redeem, but KISS */
	//echo " end balance comes after ";	
	return $amount;		
		
}

add_shortcode( 'vyps-as-redeem', 'as_redeem_func');	

