<?php
/*
  Plugin Name: VYPS Coinhive Addon
  Plugin URI: http://vyps.org
  Description: Adds Coinhive API to the VYPS so you can award points based on hashes mined to your users
  Version: 0.0.26
  Author: VidYen, LLC
  Author URI: https://vidyen.com/
  License: GPLv2 or later
 */

 /* 
* Note: Have renamed all tables to the right tables... ie. tables_ch, table_points
* Need to make every variable have specific context so can simply glance at something to know what it does
*
*
 */
 
register_activation_hook(__FILE__, 'vyps_ch_install');

function vyps_ch_install() {
    global $wpdb;
	
	$message = ''; //yeah should set that somewhere

    $table_name = $wpdb->prefix . 'vyps_ch';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		siteKey tinytext NOT NULL,
		secretKey tinytext NOT NULL,
		siteUID tinytext NOT NULL,
		threads mediumint(9) NOT NULL,
		throttle mediumint(9) NOT NULL,
		pointID varchar(11) NOT NULL,
		pointName tinytext NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";
	    
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);
	
	/* Setting the defaults here for the API's to blank and my personal feelins on what should be default
	*  I set my test site Site Key becasue I got really tired of copy and pasting it in
	*  Also I have put my private site key in but honestly it's only for my test page and not production
	*  The threads and throttle set to only 1 thread and 10% CPU usage by default.
	*  Reason: Who wants to test with 100% CPU usage? Also, you should be kind to your users.
	*/
	$site_key = "5y8ys1vO4guiyggOblimkt46sAOWDc8z";
	$secret_key = "A6YSYjxSpS0NY6sZiBbtV6qdx4006Ypw";
	$site_UID = "VYPS";
	$sm_threads = "1";
	$sm_throttle = "90";
	$ch_point_name = "Select Points";
	$table_ch = $wpdb->prefix . 'vyps_ch';
	//For some reason the table call is not redudant and removing it causes things to not feed the default values
	$data = [
		'siteKey' => $site_key,
		'secretKey' => $secret_key,
		'siteUID' => $site_UID,
		'threads' => $sm_threads,
		'throttle' => $sm_throttle,
		'pointName' => $ch_point_name,
	];
	$data_id = $wpdb->insert($table_ch, $data);
}

/* Check to see if VYPS installed function  and run menus accordingly */

if (function_exists('vyps_points_menu')) {
	
	//I would love to make this like it's own thing but then we'd have to assume they installed the warning plug in. lol.
	include( plugin_dir_path( __FILE__ ) . '../VYPS_ch/includes/ch_menu.php'); //This include creates the menu in the VYPS submenu

} else {
	
	//I would love to make this like it's own thing but then we'd have to assume they installed the warning plug in. lol.
	include( plugin_dir_path( __FILE__ ) . '../VYPS_ch/includes/no_vyps_menu.php'); //This include creates it on top level to inform to install VYPS

}

/* Next section for creatiung short code for the simple miner. */
/* Going to move this to a shortcodes include eventually */

function sm_short_func() {
	
	/* Check to see if user is logged in */
	/* Yes you could get free hashes off people without acknowledging them, but there are other plugins that already do that */
	
	if ( is_user_logged_in() ) {
	
		/* Pulling the WPDB variables*/
		global $wpdb;
		$table_ch = $wpdb->prefix . 'vyps_ch';
		$sm_site_key = $wpdb->get_var( "SELECT * FROM $table_ch", 1, 0 );
		$sm_siteUID = $wpdb->get_var( "SELECT * FROM $table_ch", 3, 0 ); 
		$sm_threads = $wpdb->get_var( "SELECT * FROM $table_ch", 4, 0 );
		$sm_throttle = $wpdb->get_var( "SELECT * FROM $table_ch", 5, 0 );
		$current_user_id = get_current_user_id();
		$sm_user = $sm_siteUID . $current_user_id;
		
		return "
			<script src=\"https://authedmine.com/lib/simple-ui.min.js\" async></script>
			<div class=\"coinhive-miner\" 
				style=\"width: 256px; height: 310px\"
				data-key=\"$sm_site_key\"
				data-threads=\"$sm_threads\"
				data-throttle=\"$sm_throttle\"
				data-user=\"$sm_user\"
				>
				<em>Loading...</em>
				</div>";
	} else {
		echo "You need to be logged in to use Coinhive on this site!";
	}
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-simple-miner', 'sm_short_func');	


/* Ok. This is the consent version simple miner shortcode. Only works when user consents with the other button.
*  I have half a mine to make it required, but only if I come across admins abusing it.
*/

function sm_short_consent_func() {
	
	if (isset($_POST["consent"])){ // button name
		
		/* Check to see if user is logged in */
		if ( is_user_logged_in() ) {
	
			/* Pulling the WPDB variables*/
			global $wpdb;
			$table_ch = $wpdb->prefix . 'vyps_ch';
			$sm_site_key = $wpdb->get_var( "SELECT * FROM $table_ch", 1, 0 );
			$sm_siteUID = $wpdb->get_var( "SELECT * FROM $table_ch", 3, 0 ); 
			$sm_threads = $wpdb->get_var( "SELECT * FROM $table_ch", 4, 0 );
			$sm_throttle = $wpdb->get_var( "SELECT * FROM $table_ch", 5, 0 );
			$current_user_id = get_current_user_id();
			$sm_user = $sm_siteUID . $current_user_id;
			
			return "
				<script src=\"https://authedmine.com/lib/simple-ui.min.js\" async></script>
				<div class=\"coinhive-miner\" 
					style=\"width: 256px; height: 310px\"
					data-key=\"$sm_site_key\"
					data-threads=\"$sm_threads\"
					data-throttle=\"$sm_throttle\"
					data-user=\"$sm_user\"
					>
					<em>Loading...</em>
					</div>
					<br>
					<form method=\"post\">
						<input type=\"hidden\" value=\"\" name=\"redeem\"/>
					<input type=\"submit\" class=\"button-secondary\" value=\"Redeem Hashes\" onclick=\"return confirm('Did you want to sync your mined hashes with this site?');\" />
					</form>";
		} else {
			echo "You need to be logged in to use Coinhive on this site!";
		}
		
	} elseif (isset($_POST["redeem"])) { //see if post button is redeem and run that
		
			if ( is_user_logged_in() ) {
	
				/* Pulling the WPDB variables*/
				global $wpdb;
				$table_ch = $wpdb->prefix . 'vyps_ch';
				$current_user_id = get_current_user_id();
				$sm_siteUID = $wpdb->get_var( "SELECT * FROM $table_ch", 3, 0 ); 
				$sm_site_key = $wpdb->get_var( "SELECT * FROM $table_ch", 1, 0 );
				$hiveKey = $wpdb->get_var( "SELECT * FROM $table_ch", 2, 0 );
				$hiveUser = $sm_siteUID . $current_user_id;
				
				
				//Copied and pasted from the old VidYen.com code
				// fetch from DB
				//$hiveUser = $user->id;
				//$hiveKey = 'baMweSSSVy93nOaQXOuQ0rKFRQlX0PY1';
				// --------------------
												
				$url = "https://api.coinhive.com/user/balance?name={$hiveUser}&secret={$hiveKey}";
												
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($ch);
				curl_close($ch);
												
				$jsonData = json_decode($result, true);
				$balance = $jsonData['balance'];
												
				/* echo $balance;
												
				$hostBalance = $unbalance + ($unbalance - $balance);
												
				echo $hostBalance; */
										
				//
				// A very simple PHP example that sends a HTTP POST to a remote site
				//

				$ch = curl_init();
												
				curl_setopt($ch, CURLOPT_URL,"https://api.coinhive.com/user/withdraw");
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,
					"name={$hiveUser}&amount={$balance}&secret={$hiveKey}");
												
				// in real life you should use something like:
				// curl_setopt($ch, CURLOPT_POSTFIELDS, 
				//          http_build_query(array('postvar1' => 'value1')));
												
				// receive server response ...
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
												
				$server_output = curl_exec ($ch);
												
				curl_close ($ch);
												
				// further processing ....
				//if ($server_output == "OK") { ... } else { ... }
				
				/* OK. Pulling log table to post return to it. What could go wrong? */
				/* Honestly, we should always refer to table by the actual table?   */
				
				/* Just checking to see if balance is 0. If it is, no need to do anything other than return the results.*/
				if( $balance > 0 )
				{
					global $wpdb;
					
					$table_log = $wpdb->prefix . 'vyps_points_log';
					$reason = "Coinhive Mining";
					$amount = $balance;

					$pointType = $wpdb->get_var( "SELECT * FROM $table_ch", 6, 0 );
					$user_id = get_current_user_id();
						$data = [
							'reason' => $reason,
							'points' => $pointType,
							'points_amount' => $amount,
							'user_id' => $user_id,
							'time' => date('Y-m-d H:i:s')
						];
					$wpdb->insert($table_log, $data);
				}
				
				/* It dawned on me that text in here only needs a number and let the admin right there response
				*  One could in theory might make an IF statement you have no hashes to redeem, but KISS */
				
				return "$balance points redeemed.<br><br>
					<form method=\"post\">
						<input type=\"hidden\" value=\"\" name=\"consent\"/>
						<input type=\"submit\" class=\"button-secondary\" value=\"Go back to mining.\" onclick=\"return confirm('Click OK to go back to mining.');\" />
					</form>
				";
			} else {
				echo "You need to be logged in to use Coinhive on this site!";
			}
		
		
	} else {
		//return; //if post is not ran than do nothing. I could check to see if logged in first, but then I guess you couldn't see consent button.
	}

}

/* Telling WP to use function for shortcode for sm-consent*/

add_shortcode( 'vyps-ch-sm-consent', 'sm_short_consent_func');	



/* Shortcode for the API call to create a lot entry */
/* There is some debate if this should be a button, but I'm just going to run on the code on page load and the admins can just make a button that runs the smart code if they want */

function sm_short_redeem_func() {
	
	/* Check to see if user is logged in */
	/* Actually redeem does not need consent as user never sees coinhive's servers and therefore will not run client code */
		
	if ( is_user_logged_in() ) {
		

	
		/* Pulling the WPDB variables*/
		global $wpdb;
		$table_ch = $wpdb->prefix . 'vyps_ch';
		$current_user_id = get_current_user_id();
		$sm_siteUID = $wpdb->get_var( "SELECT * FROM $table_ch", 3, 0 ); 
		$sm_site_key = $wpdb->get_var( "SELECT * FROM $table_ch", 1, 0 );
		$hiveKey = $wpdb->get_var( "SELECT * FROM $table_ch", 2, 0 );
		$hiveUser = $sm_siteUID . $current_user_id;
		
		
		//Copied and pasted from the old VidYen.com code
		// fetch from DB
		//$hiveUser = $user->id;
		//$hiveKey = 'baMweSSSVy93nOaQXOuQ0rKFRQlX0PY1';
		// --------------------
										
		$url = "https://api.coinhive.com/user/balance?name={$hiveUser}&secret={$hiveKey}";
										
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
										
		$jsonData = json_decode($result, true);
		$balance = $jsonData['balance'];
										
		/* echo $balance;
										
		$hostBalance = $unbalance + ($unbalance - $balance);
										
		echo $hostBalance; */
								
		//
		// A very simple PHP example that sends a HTTP POST to a remote site
		//

		$ch = curl_init();
										
		curl_setopt($ch, CURLOPT_URL,"https://api.coinhive.com/user/withdraw");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,
			"name={$hiveUser}&amount={$balance}&secret={$hiveKey}");
										
		// in real life you should use something like:
		// curl_setopt($ch, CURLOPT_POSTFIELDS, 
		//          http_build_query(array('postvar1' => 'value1')));
										
		// receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
										
		$server_output = curl_exec ($ch);
										
		curl_close ($ch);
										
		// further processing ....
		//if ($server_output == "OK") { ... } else { ... }
		
		/* OK. Pulling log table to post return to it. What could go wrong? */
		/* Honestly, we should always refer to table by the actual table?   */
		
		/* Just checking to see if balance is 0. If it is, no need to do anything other than return the results.*/
		if( $balance > 0 )
		{
			global $wpdb;
			
			$table_log = $wpdb->prefix . 'vyps_points_log';
			$reason = "Coinhive Mining";
			$amount = $balance;

			$pointType = $wpdb->get_var( "SELECT * FROM $table_ch", 6, 0 );
			$user_id = get_current_user_id();
				$data = [
					'reason' => $reason,
					'points' => $pointType,
					'points_amount' => $amount,
					'user_id' => $user_id,
					'time' => date('Y-m-d H:i:s')
				];
			$wpdb->insert($table_log, $data);
		}
		
		/* It dawned on me that text in here only needs a number and let the admin right there response
		*  One could in theory might make an IF statement you have no hashes to redeem, but KISS */
		
		return "$balance";
	} else {
		echo "You need to be logged in to use Coinhive on this site!";
	}

}

add_shortcode( 'vyps-redeem-ch', 'sm_short_redeem_func');

/* Ok. I got annoyed with WordPress and relative links so I thought I might as well add a button to opt in.
*  This gist is that user will have an opt in button. Admin can put anything on the page they want at bottom.
*  And user had to click. I consent. It ads a 0 entry log in for that user for the point id with reason "consent"
*  Page reloads and then the if will let the shortcode run the coinhive simple miner. I really want it to not touch
*  the authmine server until the consent. Least someone freaks out by randomly exploring. Since it does not do that
*  if you are not logged in, I'm going to assume it's just another if logged in user was logged in and consented
*  in the log. One in theory could give points for consented, but that might be too much effort. -Felty
*/

function sm_short_click_consent_func() {
	
	/* User needs to be logged into consent. NO EXCEPTIONS */
	
	if ( is_user_logged_in() ) {
		
		//echo "<b>e</b> Where does this go?";
		//return "<b>r</b> where does this go?";
		
		return "<form method=\"post\">
                <input type=\"hidden\" value=\"\" name=\"consent\"/>
                <input type=\"submit\" class=\"button-secondary\" value=\"I agree and consent\" onclick=\"return confirm('Did you read everything and consent to letting this page browser mine with your CPU?');\" />
                </form>";
		
		/*
		if(isset($_POST['consent'])){ // button name
			//do_stuff();
			echo "hooo";
		} else {
			//do_other_stuff();
			echo "booo";
		}
		*/
		
	} else {
		
		return "You need to be logged in to consent!";
	}
	
}

add_shortcode( 'vyps-ch-consent', 'sm_short_click_consent_func');
