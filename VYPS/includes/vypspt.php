<?php
/*
  Plugin Name: VYPS Point Transfer Addon
  Description: Allows users to transfer VYPS point type to another at different rates.
  Version: 0.0.05
  Author: VidYen, LLC
  Author URI: https://vidyen.com/
  License: GPLv2 or later
 */


 
//register_activation_hook(__FILE__, 'vyps_pt_install'); //Base move

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



//add_action('admin_menu', 'vyps_pt_submenu', 440 ); //base move

/* Creates the PT submenu on the main VYPS plugin to show instructions and that its installed */

/* Base move.
function vyps_pt_submenu() 
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "Point Transfer Addon Plugin";
    $menu_title = 'Point Transfer';
	$capability = 'manage_options';
    $menu_slug = 'vyps_pt_page';
    $function = 'vyps_pt_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}
*/


/* Below is the functions for the shortcode */

/* Base move
function vyps_pt_sub_menu_page() 
{ 

	//Logo from base. If a plugin is installed not on the menu they can't see it not showing.
	echo '<br><br><img src="' . plugins_url( '../VYPS_base/images/logo.png', __FILE__ ) . '" > ';
    
	//Instructions on page
	echo
	"<h1>VYPS Transfer Shortcode Plugin</h1>
	<p>This plugin needs VYPS Base and two point types to function. The intention is to allow a quick and easy way for users to transfer one type of point to another at varrying rates</p>
	<h2>Shortcodes Syntax</h2>
	<p><b>[vyps-pt spid=1 dpid=2 samount=100000 damount=100]</b></p>
	<p>Function debits points from one point type to another with in being how many points used to transfer and out as how many points they get in the new point type</p>
	<p>The spid is the source pointID and the dpid is the destination seen on the points list page. This shortcode always requires the user to be logged in and will not let you use set the user id as you do not want other users messing with the balances.</p>
	<p>The earn attribute is how much currency the user gets in the other poitn type. The spend attribute is how many VYPS points is spent.</p>
	<p>All attributes must be set for this to function. There is no interfact and is up to the site admin to add shortcode to a page or button. Future versions will include a better interface.</p>
	";
	
	//Credits include
	include( plugin_dir_path( __FILE__ ) . '../VYPS_base/includes/credits.php'); 
}

*/

/*** Shortcode without button ***/

function pt_func( $atts ) {
	
	/* Check to see if user is logged in and boot them out of function if they aren't. */
	
	if ( is_user_logged_in() ) {
		
		//I probaly don't have to have this part of the if
		
	} else {
		
		return "You are not logged in.";
		
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


/*** Shortcode with button ***/

function pt_btn_func( $atts ) {
	
	/* Check to see if user is logged in and boot them out of function if they aren't. */
	
	if ( is_user_logged_in() ) {
		
	//I probaly don't have to have this part of the if
		
	} else {
		
		return "You are not logged in.";
		
	}
	
	/* The shortcode attributes need to come before the button as they determin the button value */
	
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
	
	$btn_name = $sourcePointID . $destinationPointID . $pt_sAmount . $pt_dAmount;
	
	/*I don't know if this is some lazy coding but I am going to just return out if they haven't pressed the button
	* Side note: And this is important. The button value should be dynamic to not interfer with other buttons on page
	*/
	
	/* I tried avoiding calling this before the press, but had to get point names */
	
	global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	
	/* Just doing some table calls to get point names. Can you put icons in buttons? Hrm... */
	
	$sourceName = $wpdb->get_var( "SELECT name FROM $table_name_points WHERE id= '$sourcePointID'" );
	$destName = $wpdb->get_var( "SELECT name FROM $table_name_points WHERE id= '$destinationPointID'" );
	
	if (isset($_POST[ $btn_name ])){ 
	
		/* Nothing should happen */
	
	} else {
		
		/* Ok. I'm creating a semi-unique name by just concatinating all the shortcode attributes.
		*  In theory one could have two buttons with the same shortcode attributes, but why would you do that?
		*  What should happen is that the function only runs when the unique name of the button is posted.
		*  What could go wrong?
		*/
		
		/* Just show them button if button has not been clicked. Its a requirement not a suggestion. */
		
		/* In future version I'm going to make the points say the numerical values that about to be transfered. Maybe. */
		/* I added ability to have point names but for now. Just have the button say transfer and the warning give how much */
		
		return "<form method=\"post\">
                <input type=\"hidden\" value=\"\" name=\"$btn_name\"/>
                <input type=\"submit\" class=\"button-secondary\" value=\"Transfer\" onclick=\"return confirm('You are about to transfer points $pt_sAmount $sourceName for $pt_dAmount $destName. Are you sure?');\" />
                </form>";
				//<br><br>$btn_name";	//Debug: I'm curious what it looks like.	
	}
	
	
	/* These operations are below the post check as no need to wast server CPU if user didn't press button */
	
	
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$current_user_id = get_current_user_id();
	
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
	
	return "<form method=\"post\">
                <input type=\"hidden\" value=\"\" name=\"$btn_name\"/>
                <input type=\"submit\" class=\"button-secondary\" value=\"Transfer\" onclick=\"return confirm('You are about to transfer $pt_sAmount $sourceName for $pt_dAmount $destName. Are you sure?');\" />
            </form><br><br>
			$pt_sAmount $sourceName spent for $pt_dAmount $destName earned";
			
			/* since I have the point names I might as well use them. Also I put it below because its annoying to have button move. */
			//<br><br>$btn_name"; //Debug stuff
	
	
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-pt-btn', 'pt_btn_func');

/* Ok. After much deliberation and anguish at messing with modals, I have remembered that I am a system designed not a UI designer and should let users handle that
*  That said, before I make the shortcodes just out raw data, I want a table system that does just that. Behold. The table system. -Felty
*/

function pt_tbl_func( $atts ) {
	
	/* Check to see if user is logged in and boot them out of function if they aren't. */
	
	if ( is_user_logged_in() ) {
		
	//I probaly don't have to have this part of the if
		
	} else {
		
		return "You are not logged in.";
		
	}
	
	/* The shortcode attributes need to come before the button as they determin the button value */
	
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
	
	/* Not seeing comma number seperators annoys me */
	
	$format_pt_sAmount = number_format($pt_sAmount);
	$format_pt_dAmount = number_format($pt_dAmount);
	
	$btn_name = $sourcePointID . $destinationPointID . $pt_sAmount . $pt_dAmount;
	
	/*I don't know if this is some lazy coding but I am going to just return out if they haven't pressed the button
	* Side note: And this is important. The button value should be dynamic to not interfer with other buttons on page
	*/
	
	/* I tried avoiding calling this before the press, but had to get point names */
	
	global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	
	/* Just doing some table calls to get point names and icons. Can you put icons in buttons? Hrm... */
	
	$sourceName = $wpdb->get_var( "SELECT name FROM $table_name_points WHERE id= '$sourcePointID'" );
	$sourceIcon = $wpdb->get_var( "SELECT icon FROM $table_name_points WHERE id= '$sourcePointID'" );
	
	$destName = $wpdb->get_var( "SELECT name FROM $table_name_points WHERE id= '$destinationPointID'" );
	$destIcon = $wpdb->get_var( "SELECT icon FROM $table_name_points WHERE id= '$destinationPointID'" );
	
	if (isset($_POST[ $btn_name ])){ 
	
		/* Nothing should happen */
	
	} else {
		
		/* Ok. I'm creating a semi-unique name by just concatinating all the shortcode attributes.
		*  In theory one could have two buttons with the same shortcode attributes, but why would you do that?
		*  What should happen is that the function only runs when the unique name of the button is posted.
		*  What could go wrong?
		*/
		
		/* Just show them button if button has not been clicked. Its a requirement not a suggestion. */
		
		/* In future version I'm going to make the points say the numerical values that about to be transfered. Maybe. */
		/* I added ability to have point names but for now. Just have the button say transfer and the warning give how much */
		/* BTW it's only 1 column, 2 rows for each button. One for the output at top and one at bottom for button. */
		/* Reason I put button at bottom is that don't want mouse on the text bothering user */
		
		$results_message = "Press button to transfer points.";
		
		return "<table id=\"$btn_name\">
					<tr>
						<td><div align=\"center\">Spend</div></td>
						<td><div align=\"center\"><img src=\"$sourceIcon\" width=\"16\" hight=\"16\" title=\"$sourceName\"> $format_pt_sAmount</div></td>
						<td>
							<div align=\"center\">
								<b><form method=\"post\">
									<input type=\"hidden\" value=\"\" name=\"$btn_name\"/>
									<input type=\"submit\" class=\"button-secondary\" value=\"Transfer\" onclick=\"return confirm('You are about to transfer points $format_pt_sAmount $sourceName for $pt_dAmount $destName. Are you sure?');\" />
								</form></b>
							</div>
						</td>
						<td><div align=\"center\"><img src=\"$destIcon\" width=\"16\" hight=\"16\" title=\"$destName\"> $format_pt_dAmount</div></td>
						<td><div align=\"center\">Receive</div></td>
					</tr>
					<tr>
						<td colspan = 5><div align=\"center\"><b>$results_message</b></div></td>
					</tr>
				</table>";
					//<br><br>$btn_name";	//Debug: I'm curious what it looks like.	
	}
	
	
	/* These operations are below the post check as no need to wast server CPU if user didn't press button */
	
	
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$current_user_id = get_current_user_id();
	
	/* if either earn or spend are 0 it means the admin messed up
	*  the shortcode atts and that you need to return out
	*  Shouldn't this all be set to elseifs? 
	*/
	
	if ( $pt_sAmount == 0 ) {
		
		return "Admin Error: Source amount was 0!";
		
	}
	
	if ( $pt_dAmount == 0 ) {
		
		return "Admin Error: Destination amount was 0!";
		
	}
	
	/* Oh yeah. Checking to see if source pid was set */
	
	if ( $sourcePointID == 0 ) {
		
		return "Admin Error: You did not set source pid!";
		
	}
	
	/* And the destination pid */
	
	if ( $destinationPointID == 0 ) {
		
		return "Admin Error: You did not set destination pid!";
		
	}

	
	//Ok. Now we get balance. If it is not enough for the spend variable, we tell them that and return out. NO EXCEPTIONS
	
	$balance_points = $wpdb->get_var( "SELECT sum(points_amount) FROM $table_name_log WHERE user_id = $current_user_id AND points = $sourcePointID");
	
	/* I do not ever see the need for a non-formatted need point */
	
	$need_points = number_format($pt_sAmount - $balance_points);
	
	if ( $pt_sAmount > $balance_points ) {
		
		$results_message = "Not enough " . $sourceName . " to transfer! You need " . $need_points . " more.";
		
		return "<table id=\"$btn_name\">
					<tr>
						<td><div align=\"center\">Spend</div></td>
						<td><div align=\"center\"><img src=\"$sourceIcon\" width=\"16\" hight=\"16\" title=\"$sourceName\"> $format_pt_sAmount</div></td>
						<td>
							<div align=\"center\">
								<b><form method=\"post\">
									<input type=\"hidden\" value=\"\" name=\"$btn_name\"/>
									<input type=\"submit\" class=\"button-secondary\" value=\"Transfer\" onclick=\"return confirm('You are about to transfer points $format_pt_sAmount $sourceName for $pt_dAmount $destName. Are you sure?');\" />
								</form></b>
							</div>
						</td>
						<td><div align=\"center\"><img src=\"$destIcon\" width=\"16\" hight=\"16\" title=\"$destName\"> $format_pt_dAmount</div></td>
						<td><div align=\"center\">Receive</div></td>
					</tr>
					<tr>
						<td colspan = 5><div align=\"center\"><b>$results_message</b></div></td>
					</tr>
				</table>";
		
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
	
	$results_message = "Success. Points exchanged at: ". date('Y-m-d H:i:s');
	
	return "<table id=\"$btn_name\">
					<tr>
						<td><div align=\"center\">Spend</div></td>
						<td><div align=\"center\"><img src=\"$sourceIcon\" width=\"16\" hight=\"16\" title=\"$sourceName\"> $format_pt_sAmount</div></td>
						<td>
							<div align=\"center\">
								<b><form method=\"post\">
									<input type=\"hidden\" value=\"\" name=\"$btn_name\"/>
									<input type=\"submit\" class=\"button-secondary\" value=\"Transfer\" onclick=\"return confirm('You are about to transfer points $format_pt_sAmount $sourceName for $pt_dAmount $destName. Are you sure?');\" />
								</form></b>
							</div>
						</td>
						<td><div align=\"center\"><img src=\"$destIcon\" width=\"16\" hight=\"16\" title=\"$destName\"> $format_pt_dAmount</div></td>
						<td><div align=\"center\">Receive</div></td>
					</tr>
					<tr>
						<td colspan = 5><div align=\"center\"><b>$results_message</b></div></td>
					</tr>
				</table>";
			
			/* since I have the point names I might as well use them. Also I put it below because its annoying to have button move. */
			//<br><br>$btn_name"; //Debug stuff
	
	
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-pt-tbl', 'pt_tbl_func');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */

function pt_ww_func( $atts ) {
	
	/* Check to see if user is logged in and boot them out of function if they aren't. */
	
	if ( is_user_logged_in() ) {
		
		//I probaly don't have to have this part of the if
		
	} else {
		
		//Moving forward table shortcodes will not be the ones giving error needs to be logged in. Will be a dedicated SC for that
		
		return;
		
	}
	
	/* The shortcode attributes need to come before the button as they determin the button value */
	/* for WW, there is no dpid as we only send to WW and not in reverse. I could but why would you want to? */
	/* I guess if you want RMT, but then it violates the RNG principal... Legally */
		
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
	
	
	/* Not seeing comma number seperators annoys me */
	
	$format_pt_sAmount = number_format($pt_sAmount);
	
	setlocale(LC_MONETARY, 'en_US.UTF-8');
	$format_pt_dAmount = money_format('%.2n', $pt_dAmount); //Did you catch that? WooWallet should always be money. Not number.
	
	
	
	/*I don't know if this is some lazy coding but I am going to just return out if they haven't pressed the button
	* Side note: And this is important. The button value should be dynamic to not interfer with other buttons on page
	*/
	
	/* I tried avoiding calling this before the press, but had to get point names */
	
	global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	
	/* Just doing some table calls to get point names and icons. Can you put icons in buttons? Hrm... */
	
	$sourceName = $wpdb->get_var( "SELECT name FROM $table_name_points WHERE id= '$sourcePointID'" );
	$sourceIcon = $wpdb->get_var( "SELECT icon FROM $table_name_points WHERE id= '$sourcePointID'" );
	
	$destName = "My Wallet"; //I'm debating to call this WooWallet but usres may not know what it is and it says My Wallet in the interface
	$destIcon = WOO_WALLET_ICON; //I didn't think that would work, but as long as WooWallet is installed it should work.
	
	$btn_friendly_dAmount = $pt_dAmount * 100000; //Multiplying by this number should make anything below 0.009 a whole number while retaining the accuracy of site admin rather than chopping it off with an int conversion
	$btn_name = $sourcePointID . $pt_sAmount . $btn_friendly_dAmount;
	
	if (isset($_POST[ $btn_name ])){ 
	
		/* Nothing should happen */
	
	} else {
		
		/* Ok. I'm creating a semi-unique name by just concatinating all the shortcode attributes.
		*  In theory one could have two buttons with the same shortcode attributes, but why would you do that?
		*  What should happen is that the function only runs when the unique name of the button is posted.
		*  What could go wrong?
		*/
		
		/* Just show them button if button has not been clicked. Its a requirement not a suggestion. */
		
		/* In future version I'm going to make the points say the numerical values that about to be transfered. Maybe. */
		/* I added ability to have point names but for now. Just have the button say transfer and the warning give how much */
		/* BTW it's only 1 column, 2 rows for each button. One for the output at top and one at bottom for button. */
		/* Reason I put button at bottom is that don't want mouse on the text bothering user */
		
		$results_message = "Press button to transfer points.";
		
		return "<table id=\"$btn_name\">
					<tr>
						<td><div align=\"center\">Spend</div></td>
						<td><div align=\"center\"><img src=\"$sourceIcon\" width=\"16\" hight=\"16\" title=\"$sourceName\"> $format_pt_sAmount</div></td>
						<td>
							<div align=\"center\">
								<b><form method=\"post\">
									<input type=\"hidden\" value=\"\" name=\"$btn_name\"/>
									<input type=\"submit\" class=\"button-secondary\" value=\"Transfer\" onclick=\"return confirm('You are about to transfer points $format_pt_sAmount $sourceName for $pt_dAmount $destName. Are you sure?');\" />
								</form></b>
							</div>
						</td>
						<td><div align=\"center\"><img src=\"$destIcon\" width=\"16\" hight=\"16\" title=\"$destName\"> $format_pt_dAmount</div></td>
						<td><div align=\"center\">Receive</div></td>
					</tr>
					<tr>
						<td colspan = 5><div align=\"center\"><b>$results_message</b></div></td>
					</tr>
				</table>";
					//<br><br>$btn_name";	//Debug: I'm curious what it looks like.	
	}
	
	
	/* These operations are below the post check as no need to wast server CPU if user didn't press button */
	
	
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$current_user_id = get_current_user_id();
	
	/* if either earn or spend are 0 it means the admin messed up
	*  the shortcode atts and that you need to return out
	*  Shouldn't this all be set to elseifs? 
	*/
	
	if ( $pt_sAmount == 0 ) {
		
		return "Admin Error: Source amount was 0!";
		
	}
	
	//Technically we need a destination amount even though we know where its going
	
	if ( $pt_dAmount == 0 ) {
		
		return "Admin Error: Destination amount was 0!";
		
	}
	
	
	/* Oh yeah. Checking to see if source pid was set */
	
	if ( $sourcePointID == 0 ) {
		
		return "Admin Error: You did not set source pid!";
		
	}
	
	/* And the destination pid */
	
	//Don't need for WW as its going out of VYPS to WW
	/*
	if ( $destinationPointID == 0 ) {
		
		return "Admin Error: You did not set destination pid!";
		
	}
	*/
	
	//Ok. Now we get balance. If it is not enough for the spend variable, we tell them that and return out. NO EXCEPTIONS
	
	$balance_points = $wpdb->get_var( "SELECT sum(points_amount) FROM $table_name_log WHERE user_id = $current_user_id AND points = $sourcePointID");
	
	/* I do not ever see the need for a non-formatted need point */
	
	$need_points = number_format($pt_sAmount - $balance_points);
	
	if ( $pt_sAmount > $balance_points ) {
		
		$results_message = "Not enough " . $sourceName . " to transfer! You need " . $need_points . " more.";
		
		return "<table id=\"$btn_name\">
					<tr>
						<td><div align=\"center\">Spend</div></td>
						<td><div align=\"center\"><img src=\"$sourceIcon\" width=\"16\" hight=\"16\" title=\"$sourceName\"> $format_pt_sAmount</div></td>
						<td>
							<div align=\"center\">
								<b><form method=\"post\">
									<input type=\"hidden\" value=\"\" name=\"$btn_name\"/>
									<input type=\"submit\" class=\"button-secondary\" value=\"Transfer\" onclick=\"return confirm('You are about to transfer points $format_pt_sAmount $sourceName for $pt_dAmount $destName. Are you sure?');\" />
								</form></b>
							</div>
						</td>
						<td><div align=\"center\"><img src=\"$destIcon\" width=\"16\" hight=\"16\" title=\"$destName\"> $format_pt_dAmount</div></td>
						<td><div align=\"center\">Receive</div></td>
					</tr>
					<tr>
						<td colspan = 5><div align=\"center\"><b>$results_message</b></div></td>
					</tr>
				</table>";
		
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
	/* Ok commenting the second vyps point and putting the WW action in */
	/*
	$data = [
			'reason' => $reason,
			'points' => $PointType,
			'points_amount' => $amount,
			'user_id' => $user_id,
			'time' => date('Y-m-d H:i:s')
			];
	$wpdb->insert($table_log, $data);
	
	*/
	
	/* Below is code copied from the old WW sub plugin 
	* Honestly, I don't think we need to worry as it just touches the WW table *
	* Oh snap. I used the same variable names for once. Thank the gods.
	* I do need to define $ww_earn which is the $dpoinamount.
	* My only concern is that the WooWallet in the menu does not update after the first
	* refresh so it doesn't look like anything happened on my site.
	* so will just add an option in bc, to show WW balance. Annoying but well. 
	* Actually. I could just make a coin balance that simply pulls current WW balance every time
	* which is also annoying. -Felty */
	
	$ww_earn = $pt_dAmount;
	
	$table_ww = $wpdb->prefix . 'woo_wallet_transactions';
	/* I feel like if WooWallet coder realized balances were bad and logs were good, I wouldn't have to do the following */
	/* I'm pulling the max transaction_id for the user and then creating a new one with the balance + earn to get the new balance on new row */
	$last_trans_id = $wpdb->get_var( "SELECT max(transaction_id) FROM $table_ww WHERE user_id = $current_user_id");
	//return $last_trans_id; //this was 7
	//$new_trans_id = $last_trans_id + 1; //Not needed as i think its auto increment //$current_user_id
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
	
	$results_message = "Success. Points exchanged at: ". date('Y-m-d H:i:s');
	
	return "<table id=\"$btn_name\">
					<tr>
						<td><div align=\"center\">Spend</div></td>
						<td><div align=\"center\"><img src=\"$sourceIcon\" width=\"16\" hight=\"16\" title=\"$sourceName\"> $format_pt_sAmount</div></td>
						<td>
							<div align=\"center\">
								<b><form method=\"post\">
									<input type=\"hidden\" value=\"\" name=\"$btn_name\"/>
									<input type=\"submit\" class=\"button-secondary\" value=\"Transfer\" onclick=\"return confirm('You are about to transfer points $format_pt_sAmount $sourceName for $pt_dAmount $destName. Are you sure?');\" />
								</form></b>
							</div>
						</td>
						<td><div align=\"center\"><img src=\"$destIcon\" width=\"16\" hight=\"16\" title=\"$destName\"> $format_pt_dAmount</div></td>
						<td><div align=\"center\">Receive</div></td>
					</tr>
					<tr>
						<td colspan = 5><div align=\"center\"><b>$results_message</b></div></td>
					</tr>
				</table>";
			
			/* since I have the point names I might as well use them. Also I put it below because its annoying to have button move. */
			//<br><br>$btn_name"; //Debug stuff
	
	
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-pt-ww', 'pt_ww_func');		