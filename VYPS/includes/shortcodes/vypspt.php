<?php
/*
  Plugin Name: VYPS Point Transfer Addon
  Description: Allows users to transfer VYPS point type to another at different rates.
  Version: 0.0.05
  Author: VidYen, LLC
  Author URI: https://vidyen.com/
  License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Added prepare() to all SQL SELECT calls 7.1.2018 */

/*** Shortcode without button ***/

function vyps_point_transfer_func( $atts ) {

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
	//Luckily this was the same variable names as the tbl version so could just copy and paste.

	//SELECT sum(points_amount) FROM $table_name_log WHERE user_id = $current_user_id AND points = $sourcePointID
	$balance_points_query = "SELECT sum(points_amount) FROM ". $table_name_log . " WHERE user_id = %d AND points = %d";
	$balance_points_query_prepared = $wpdb->prepare( $balance_points_query, $current_user_id, $sourcePointID );
	$balance_points = $wpdb->get_var( $balance_points_query_prepared );

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

add_shortcode( 'vyps-pt', 'vyps_point_transfer_func');


/*** Shortcode with button ***/

function vyps_point_transfer_btn_func( $atts ) {

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

	//"SELECT name FROM $table_name_points WHERE id= '$sourcePointID'"
	$sourceName_query = "SELECT name FROM ". $table_name_points . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
	$sourceName_query_prepared = $wpdb->prepare( $sourceName_query, $sourcePointID );
	$sourceName = $wpdb->get_var( $sourceName_query_prepared );

	//SELECT name FROM $table_name_points WHERE id= '$destinationPointID'"
	$destName_query = "SELECT name FROM ". $table_name_points . " WHERE id= %d";
	$destName_query_prepared = $wpdb->prepare( $destName_query, $destinationPointID );
	$destName = $wpdb->get_var( $destName_query_prepared );


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

	//SELECT sum(points_amount) FROM $table_name_log WHERE user_id = $current_user_id AND points = $sourcePointID
	$balance_points_query = "SELECT sum(points_amount) FROM ". $table_name_log . " WHERE user_id = %d AND points = %d";
	$balance_points_query_prepared = $wpdb->prepare( $balance_points_query, $current_user_id, $sourcePointID );
	$balance_points = $wpdb->get_var( $balance_points_query_prepared );

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

add_shortcode( 'vyps-pt-btn', 'vyps_point_transfer_btn_func');

/* Ok. After much deliberation and anguish at messing with modals, I have remembered that I am a system designed not a UI designer and should let users handle that
*  That said, before I make the shortcodes just out raw data, I want a table system that does just that. Behold. The table system. -Felty
*/

/* WW and PT_tbl shortcode was here but moved it out to own files */
