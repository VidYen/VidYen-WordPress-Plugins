<?php

//VIDYEN Points Threshold Raffle Shortcode


/* I straight up copied vyps_pt_tbl.php and renamed the functions as raffle system should use the table
* The idea of this is to have many people micromine and compete for a big payout via raffle system
* As timed ones never work out since Ironking will buy one ticket and wait, I decided to make it based on
* a threshold and then decide winner when enough people buy it. So time is never a constant here.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Added prepare() to all SQL SELECT calls 7.1.2018 */

function vyps_point_threshold_raffle_log_func( $atts ) {

	//Check to see if user is logged in and boot them out of function if they aren't.

	if ( is_user_logged_in() ) {

	//I probaly don't have to have this part of the if

	} else {

		return;

	}

	//Note I just recycled the vypstr file as I can just use the shortcodes to determine logs if needed.
	//The atts aren't required but can be useful if they are wanted.

	/*
	* spid=source point ID
	* dpid=destination point id //yeah not needed by maybe the raffle wants to pay out to a different point type
	* samount=the cost of buying a raffle ticket
	* tickets=number of tickets... how many tickets can be bought before raffle is decided
	* damount=the pot payout amount //i could just take the number of tickets and times the samount, but maybe the admin wants something weird
	* thats pretty much it
	*/

	$atts = shortcode_atts(
		array(
				'spid' => '0',
				'dpid' => '0',
				'samount' => '0',
				'tickets' => '0',
				'damount' => '0',
				'rows' => '20',
		), $atts, 'vyps-tr-log' );

	$sourcePointID = $atts['spid'];
	$destinationPointID = $atts['dpid'];
	$pt_sAmount = $atts['samount'];
	$pt_dAmount = $atts['damount'];
	$ticket_threshold = $atts['tickets'];
	$table_rows = $atts['rows']; //this is new so admin can set rows.
	/* Not seeing comma number seperators annoys me */

	$format_pt_sAmount = number_format($pt_sAmount);
	$format_pt_dAmount = number_format($pt_dAmount);

	//NOTE: the button name will be used for the log of that button to see who bought tickets.
	$btn_name = "raffle" . $sourcePointID . $destinationPointID . $pt_sAmount . $pt_dAmount . $ticket_threshold;


	//IMO the log doesn't have to show any particular game, but it can if you want.
	//For now it will show all the games that have been played and are still open.

	// I tried avoiding calling this before the press, but had to get point names

	global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_users = $wpdb->prefix . 'users';

	// Just doing some table calls to get point names and icons. Can you put icons in buttons? Hrm...

	$buttons_output = "
		<ul class=\"pagination\">
		  <li><a href=\"?action=first\">First</a></li>
		  <li><a href=\"?action=prev\">Prev</a></li>
		  <li><a href=\"?action=next\">Next</a></li>
		  <li><a href=\"?action=last\">Last</a></li>
		</ul>";

		if (isset($_GET['action'])){

			$button_action = htmlspecialchars($_GET['action']);

		} else{

		$button_action = "";

	}

	//Time to generate the table. We can borrow the public log code somewhat.
	//if I hate more time, I'd covert the PL in to a better general use function, but technically this uses the meta fields
	//So won't be able to 1 for 1 it.

	//BTW the number of IDs should always match the number of rows, NO EXCEPTIONS. If it doesn't it means the admin deleted a row
	//And that is against the psuedo-blockchain philosophy. //Also it dawned on me I can rewrite the public log here.

	//I'm 99.99% sure I need to find the max of the current game id (meta_id) and then max of the

	//$number_of_log_rows = $wpdb->get_var( "SELECT max( id ) FROM $table_name_log" ); //No WHERE needed. All rows. No exceptions
	$number_of_log_rows_query = "SELECT max( vyps_meta_subid1 ) FROM ". $table_name_log. " WHERE vyps_meta_id = %s";  //Technically we don' tneed to count as subid 2 tells us how many.
	$number_of_log_rows_query_prepared = $wpdb->prepare( $number_of_log_rows_query, $btn_name ); //btn name should tell us
	$number_of_log_rows = $wpdb->get_var( $number_of_log_rows_query_prepared );
	$current_game_id = $btn_name . $number_of_log_rows; //Maybe I should name it better, but shouldn't matter. I feel like I should name number_of_log_rows something else but it has two different uses.

	//So at this point we know vyps_meta_id which is the btn_name and now we just need to know how many tickets.
	$number_of_ticket_rows_query = "SELECT max( vyps_meta_subid2 ) FROM ". $table_name_log. " WHERE vyps_meta_id = %s AND vyps_meta_subid1 = %d";  //Technically we don' tneed to count as subid 2 tells us how many.
	$number_of_ticket_rows_query_prepared = $wpdb->prepare( $number_of_ticket_rows_query, $btn_name, $number_of_log_rows ); //btn name should tell us
	$number_of_ticket_rows = $wpdb->get_var( $number_of_ticket_rows_query_prepared ); //This tell us how many tickets have been bought

	$begin_row = 1; //Not sure if I will have an off by one.
	$end_row = ''; //Eventually will have admin ability to filter how many rows they see as after 1000 may be intensive

	// Although normally against totally going programatic. Since I know I'm going to reuse this for the public log I'm going to put the headers into variables
	// For public log the user_name should be display name and no need to see the UID and PID
	$date_label = "Date";
	$display_name_label = "Name";
	$ticket_number_label = "Ticket Number";

	//Header output is also footer output if you have not noticed.
	//Also isn't it nice you can edit the format directly instead it all in the array?
	$header_output = "
			<tr>
				<th>$date_label</th>
				<th>$display_name_label</th>
				<th>$ticket_number_label</th>
			</tr>
	";




	//Because the shorcode version won't have this
	$page_header_text = "
		<h1 class=\"wp-heading-inline\">Current Game Ticket Purchases</h1>
		";

	//this is what it's goint to be called
	$table_output = "";
	$vyps_meta_data_check = "rafflebuy";

	for ($x_for_count = $number_of_ticket_rows; $x_for_count > 0; $x_for_count = $x_for_count -1 ) { //I'm counting backwards. Also look what I did. Also also, there should never be a 0 id or less than 1

		//$date_data = $wpdb->get_var( "SELECT time FROM $table_name_log WHERE id= '$x_for_count'" ); //Straight up going to brute force this un-programatically not via entire row
		$date_data_query = "SELECT time FROM ". $table_name_log . " WHERE vyps_meta_id = %s AND vyps_meta_subid1 = %d AND vyps_meta_subid2 = %d AND vyps_meta_data = %s";
		$date_data_query_prepared = $wpdb->prepare( $date_data_query, $btn_name, $number_of_log_rows, $x_for_count, $vyps_meta_data_check ); //Yeah this is a 4 dimensional query. Also rafflebuy is hardcoded which might be questionable
		$date_data = $wpdb->get_var( $date_data_query_prepared );

		//$date_data = $wpdb->get_var( "SELECT time FROM $table_name_log WHERE id= '$x_for_count'" ); //Straight up going to brute force this un-programatically not via entire row
		$user_id_data_query = "SELECT user_id FROM ". $table_name_log . " WHERE vyps_meta_id = %s AND vyps_meta_subid1 = %d AND vyps_meta_subid2 = %d AND vyps_meta_data = %s";
		$user_id_data_query_prepared = $wpdb->prepare( $user_id_data_query, $btn_name, $number_of_log_rows, $x_for_count, $vyps_meta_data_check ); //Yeah this is a 4 dimensional query. Also rafflebuy is hardcoded which might be questionable
		$user_id_data = $wpdb->get_var( $user_id_data_query_prepared );

		//NOTE: the one below didn't have to be changed becasue we know the user_id above and now just look at the $wpdb user table.
		//$display_name_data = $wpdb->get_var( "SELECT display_name FROM $table_name_users WHERE id= '$user_id_data'" ); //And this is why I didn't call it the entire row by arrow. We are in 4d with multiple tables
		$display_name_data_query = "SELECT display_name FROM ". $table_name_users . " WHERE id = %d"; //Note: Pulling from WP users table
		$display_name_data_query_prepared = $wpdb->prepare( $display_name_data_query, $user_id_data );
		$display_name_data = $wpdb->get_var( $display_name_data_query_prepared );

		//I cut down the need for the data because really all you need is the date, name of person, and the ticket number. You should already know the damn price.
		//Also we already know the ticket number as it's the x for count (unless I go some FOBO thing going on)

		$current_row_output = "
			<tr>
				<td>$date_data</td>
				<td>$display_name_data</td>
				<td>$x_for_count</td>
			</tr>
				";


			//We can identify the winner if they exist. So is threshold is number of tickets
			if ($ticket_threshold == $number_of_ticket_rows ) {

				$vyps_meta_data_win = 'rafflewin';
				//we know there is a winner in the list so we check each time
				$winner_query = "SELECT vyps_meta_subid3 FROM ". $table_name_log . " WHERE vyps_meta_id = %s AND vyps_meta_subid1 = %d AND vyps_meta_data = %s";
				$winner_query_prepared = $wpdb->prepare( $winner_query, $btn_name, $number_of_log_rows, $vyps_meta_data_win ); //Yeah this is a 4 dimensional query. Also rafflebuy is hardcoded which might be questionable
				$winner = $wpdb->get_var( $winner_query_prepared );

				//So we have the id. So...

				if ($x_for_count == $winner ) {

					//We just make everything bold. Note. We had to call table ticket something else least the loop get mad at us for getting turned into a string.

					$current_row_output = "
						<tr>
							<td><b>$date_data</b></td>
							<td><b>$display_name_data</b></td>
							<td><b>$x_for_count (winning ticket)</b></td>
						</tr>
							";

						//I needed a spot to overwrite the header in case we had a finalized game. I probaly should change the wording.
						$page_header_text = "
							<h1 class=\"wp-heading-inline\">Last Game Ticket Purchases</h1>
								";

				}
			//Second if close.
			}






	//Compile into row output.
	$table_output = $table_output . $current_row_output; //I like my way that is more reasonable instead of .=

}
	//The page output
	return "
		<div class=\"wrap\">
			$page_header_text
			<table class=\"wp-list-table widefat fixed striped users\">
				$header_output
				$table_output
				$header_output
			</table>
		</div>
	";


	//return $buttons_output . "<br><br>" . $button_action;

}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-tr-log', 'vyps_point_threshold_raffle_log_func');
