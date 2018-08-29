<?php

//VIDYEN Points Exchange Shortcode
//Advanced version with timer

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Added prepare() to all SQL SELECT calls 7.1.2018 */

//Oh. Maybe I should put this elsewhere but I have foudn this nifty code off https://stackoverflow.com/questions/8273804/convert-seconds-into-days-hours-minutes-and-seconds
//So I'm putting it here as a function. Will use elsewhere mayhaps. If so will fix later.
//NOTE: This is the time converstion
function vyps_secondsToTime($seconds) {
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes, and %s seconds');
}

function vyps_point_exchange_func( $atts ) {

	//The shortcode attributes need to come before the button as they determine the button value

	$atts = shortcode_atts(
		array(
				'firstid' => '0',
				'secondid' => '0',
				'outputid' => '0',
				'firstamount' => '0',
				'secondamount' => '0',
				'outputamount' => '0',
				'days' => '0',
				'hours' => '0',
				'minutes' => '0'
		), $atts, 'vyps-pe' );

	/* Save this for later
	$sourcePointID = $atts['spid'];
	$destinationPointID = $atts['dpid'];
	$pt_sAmount = $atts['samount'];
	$pt_dAmount = $atts['damount'];
	*/
	$current_user_id = get_current_user_id(); //This needs to go here as we are checking the time way before then.
	$firstPointID = $atts['firstid'];
	$secondPointID = $atts['secondid'];
	$destinationPointID = $atts['outputid'];
	$pt_fAmount = $atts['firstamount']; //I'm going to be the first to say, I'm am not proud of my naming conventions. Gods know if I ever get amnesia and have to fix my own code, I will be highly displeased. -Felty
	$pt_sAmount = $atts['secondamount']; //f = first and s = second, notice how i reused some of the old variables for new. Not really intentional nor well executed.
	$pt_dAmount = $atts['outputamount'];
	$time_days = $atts['days'];
	$time_hours = $atts['hours'];
	$time_minutes = $atts['minutes'];

	//Now to get the raw seconds. This is important. 24 * 60 * 60 = 86400, 3600 seconds in an hour etc. We are not doing months unless you want to do my 28 day suggestion with 13 months.
	$time_left_seconds = 0; //Just in case there was no transfer inbound to have this variable set.
	$time_seconds = ($time_days * 86400) + ($time_hours * 3600) + ($time_minutes * 60);

	//The usual suspects check to see if admin has set their short codes right.
	//Ok I'm lazy here, but the admins should know which of the three they did not set.
	if ( $pt_dAmount == 0) {

		return "Admin Error: Output amount set to 0. Please set.";

	}

	//Oh yeah. Checking to see if source pid was set

	if ( $firstPointID == 0 OR $destinationPointID == 0) {

		return "Admin Error: A required id was set to 0! Please set all three.";

	}

	if ($time_seconds < 0 ) {

		//I feel like this should be not needed but I feel that it would blow up one of my ifs down below catastrophically
		return "Admin Error: You have a negative time somewhere.";

	}

	//Check if user is logged in and stop the code.
	//NOTE: I am doing this here because I realize that the admin should see rigth away the short code is set incorrectly rather than having to log in to see that it is
	if ( is_user_logged_in() ) {

	//I probaly don't have to have this part of the if

	} else {

		return; //You get nothing. Use the LG code.

	}

	//Not seeing comma number seperators annoys me
	$format_pt_fAmount = number_format($pt_fAmount);
	$format_pt_sAmount = number_format($pt_sAmount);
	$format_pt_dAmount = number_format($pt_dAmount);

	//I am going to redo the process but just use all the variables.
	$btn_name = $firstPointID . $secondPointID . $destinationPointID . $pt_fAmount . $pt_sAmount . $pt_dAmount;

	//I don't know if this is some lazy coding but I am going to just return out if they haven't pressed the button
	// Side note: And this is important. The button value should be dynamic to not interfer with other buttons on page

	//I tried avoiding calling this before the press, but had to get point names

	global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';
	$table_name_log = $wpdb->prefix . 'vyps_points_log';

	//Just doing some table calls to get point names and icons. Can you put icons in buttons? Hrm...

	//Ok below is just the new way we are going to handle prepares. Takes 4 lines to do one get_var now, but just throw more hardware at it.
	//1. Query comment as should be written out if you pasted it into command lines
	//2. the Query pre-pregarded
	//3. the query prepared
	//4. The get_var command. Btw, I would like to avoid calling entire rows if possible as we usually are interested in different tables
	//   And would be harder to read and not really needed.
	//   BTW all table names are hard coded even though they are variables depending on the name of the WP table, but I think
	//   if the prefix was an injection string the entire SQL server would have broke before then -Felty

	//Going to group this up since need to tell which ones are different
	//NOTE: Reused code and just differentated the end variables with f for first and s for second. t will be for third if we do a three variable output


	//NOTE: First source point
	//"SELECT name FROM $table_name_points WHERE id= '$sourcePointID'"
	$sourceName_query = "SELECT name FROM ". $table_name_points . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
	$sourceName_query_prepared = $wpdb->prepare( $sourceName_query, $firstPointID );
	$f_sourceName = $wpdb->get_var( $sourceName_query_prepared );

	//"SELECT icon FROM $table_name_points WHERE id= '$sourcePointID'"
	$sourceIcon_query = "SELECT icon FROM ". $table_name_points . " WHERE id= %d";
	$sourceIcon_query_prepared = $wpdb->prepare( $sourceIcon_query, $firstPointID );
	$f_sourceIcon = $wpdb->get_var( $sourceIcon_query_prepared );

	//NOTE: Second source point. Perhaps I should write something more to differentiate.
	//Only does the the WPDB if the the secondPointID actually exists. I thought but perhaps greater than 0 is best to rule out negative numbers
	if ($secondPointID > 0){

		//"SELECT name FROM $table_name_points WHERE id= '$sourcePointID'"
		$sourceName_query = "SELECT name FROM ". $table_name_points . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$sourceName_query_prepared = $wpdb->prepare( $sourceName_query, $secondPointID );
		$s_sourceName = $wpdb->get_var( $sourceName_query_prepared );

		//"SELECT icon FROM $table_name_points WHERE id= '$sourcePointID'"
		$sourceIcon_query = "SELECT icon FROM ". $table_name_points . " WHERE id= %d";
		$sourceIcon_query_prepared = $wpdb->prepare( $sourceIcon_query, $secondPointID );
		$s_sourceIcon = $wpdb->get_var( $sourceIcon_query_prepared );

	}

	//NOTE: Output point
	//SELECT name FROM $table_name_points WHERE id= '$destinationPointID'"
	$destName_query = "SELECT name FROM ". $table_name_points . " WHERE id= %d";
	$destName_query_prepared = $wpdb->prepare( $destName_query, $destinationPointID );
	$destName = $wpdb->get_var( $destName_query_prepared );

	//SELECT icon FROM $table_name_points WHERE id= '$destinationPointID'
	$destIcon_query = "SELECT icon FROM ". $table_name_points . " WHERE id= %d";
	$destIcon_query_prepared = $wpdb->prepare( $destIcon_query, $destinationPointID );
	$destIcon = $wpdb->get_var( $destIcon_query_prepared );

	//NOTE: I will have two inputs on two different row and the output and transfer button will be spread accross
	//As my coding skills have improved greatly in the past 2 months I am redoing this next bit to be more modernized
	//But I am still strapped for time, so I'm not going back to apply the updates to the old exchanged

	//NOTE: Time check.
	//I'm putting this here as you might want to know you still have time before you can click the button before you click the button
	//Should only check if we have a time check in 'teh' short codes
	//No miliseconds. I'm not sorry.
	if ($time_seconds > 0 ){

		$last_transfer_query = "SELECT max(id) FROM ". $table_name_log . " WHERE user_id = %d AND vyps_meta_id = %s"; //In theory we should check for the pid as well, but it the btn should make it unique
		$last_transfer_query_prepared = $wpdb->prepare( $last_transfer_query, $current_user_id, $btn_name );
		$last_transfer = $wpdb->get_var( $last_transfer_query_prepared ); //Now we know the last id. NOTE: It is possible that there was not a previous transaction.

		//return $last_transfer; //DEBUG I think there is something going on here that I'm not aware of.

		if ($last_transfer == ''){

			//Well nothing should happen. There was no prior entry. Go ahead and check entries


		} else {

			//We now know the id exists so an entry exists so we need ot check its timed
			$last_posted_time_query = "SELECT time FROM ". $table_name_log . " WHERE id = %d";
			$last_posted_time_query_prepared = $wpdb->prepare($last_posted_time_query, $last_transfer ); //The ids should all be unique. In theory and in practice.
			$last_posted_time = $wpdb->get_var( $last_posted_time_query_prepared ); //Now we know time of the last transaction

			//return $last_posted_time; //DEBUG seeing what the time is.

			$last_posted_time = strtotime($last_posted_time); //Note sure why the 'new' but it was how PHP man suggested to do it
			//return $last_posted_time;
			$current_time = date('Y-m-d H:i:s');
			$current_time = strtotime($current_time);
			$time_passed = $current_time - $last_posted_time; //I'm just making a big guess here that this will work.

			$time_left_seconds = $time_seconds - $time_passed; //NOTE: if this number is positive it means they still need to wait.
			$display_time = vyps_secondsToTime($time_left_seconds); //This converts the seconds left into something more human readable. Master race machines and replicants need not use.

		}

	}

	$results_message = "Press button to transfer.";

	if ( $time_left_seconds > 0 ) {

		$results_message = "You have $display_time before another transfer.";

	}

	if (isset($_POST[ $btn_name ])){

		//Code check.
		//First things first. Make sure they have enough damn points to transfer.
		$table_name_log = $wpdb->prefix . 'vyps_points_log';
		$current_user_id = get_current_user_id();

		//Check the first input

		$balance_points_query = "SELECT sum(points_amount) FROM ". $table_name_log . " WHERE user_id = %d AND point_id = %d";
		$balance_points_query_prepared = $wpdb->prepare( $balance_points_query, $current_user_id, $firstPointID );
		$f_balance_points = $wpdb->get_var( $balance_points_query_prepared );

		/* I do not ever see the need for a non-formatted need point */
		$f_need_points = number_format($pt_fAmount - $f_balance_points);

		//Check the second inputs
		if ($secondPointID > 0){

			$balance_points_query = "SELECT sum(points_amount) FROM ". $table_name_log . " WHERE user_id = %d AND point_id = %d";
			$balance_points_query_prepared = $wpdb->prepare( $balance_points_query, $current_user_id, $secondPointID );
			$s_balance_points = $wpdb->get_var( $balance_points_query_prepared );

			/* I do not ever see the need for a non-formatted need point */
			$s_need_points = number_format($pt_sAmount - $s_balance_points);

		} else {

			$s_balance_points = 0; //Need to zero this out if there was no second point
			$s_need_points = 0; //And this as well. Methinks mayhaps I should have thought this through better

		}


		//Four possible scenarios, both are enough, both are not enough, first is not enough, second is not enough
		//NOTE: Now we are adding the other scenario that the time is not enough, but that check will be run above.

		if ( $pt_fAmount > $f_balance_points AND $pt_sAmount > $s_balance_points AND $pt_fAmount != 0 ) {

			$results_message = "You need $f_need_points $f_sourceName and $s_need_points $s_sourceName more to transfer!";

		} elseif ( $pt_fAmount > $f_balance_points AND $pt_fAmount != 0 ) {

			$results_message = "Not enough $f_sourceName! You need $f_need_points more.";

		} elseif ( $pt_sAmount > $s_balance_points AND $pt_sAmount != 0 ) {

			$results_message = "Not enough $s_sourceName! You need $s_need_points more.";

		} elseif ( $time_left_seconds > 0 ) {

			//This means the timer has not go on long enough
			//Need some message.
			$results_message = "You have $display_time before another transfer.";

		} else {

			//NOTE: OK. We can run the transfer

			$table_log = $wpdb->prefix . 'vyps_points_log';
			$reason = "Point Transfer";
			$famount = $pt_fAmount * -1; //Seems like this is a bad idea to just multiply it by a negative 1 but hey
			$samount = $pt_sAmount * -1;

			$fPointType = $firstPointID; //Originally this was a table call, but seems easier this way
			$sPointType = $secondPointID; //WCCW
			$user_id = $current_user_id;


			//Run the first input.
			$data = [
					'reason' => $reason,
					'point_id' => $fPointType,
					'points_amount' => $famount,
					'user_id' => $user_id,
					'time' => date('Y-m-d H:i:s'),
					];
			$wpdb->insert($table_log, $data);

			//NOTE: Only run this is the second amount is greater than zero.
			if ( $pt_sAmount > 0){

				//Run the second input
				$data = [
						'reason' => $reason,
						'point_id' => $sPointType,
						'points_amount' => $samount,
						'user_id' => $user_id,
						'time' => date('Y-m-d H:i:s')
						];
				$wpdb->insert($table_log, $data);
			}

			// Ok. Now we put the destination points in. Reason should stay the same

			$amount = $pt_dAmount; //Destination amount should be positive

			$PointType = $destinationPointID; //Originally this was a table call, but seems easier this way

			//The $btn_name should be unique. But tags the first inserted point
			//I had an internal debate to put this on the input or output, but sometimes the input will have no row if there was no COST
			//So when checking for time, it should check the last meta $btn_name and see how long to go
			$data = [
					'reason' => $reason,
					'point_id' => $PointType,
					'points_amount' => $amount,
					'user_id' => $user_id,
					'time' => date('Y-m-d H:i:s'),
					'vyps_meta_id' => $btn_name
					];
			$wpdb->insert($table_log, $data);

			$results_message = "Success. Exchanged at: ". date('Y-m-d H:i:s');

		}



	} else {

		//This is what shows if button has yet to be pressed

	}

	//Down here is where the end result goes in the returned
	//It really didn't matter where this went so going here.

	//NOTE: Doing a if then, to have two different versions for 1 or 2 inputs

	if ($secondPointID == 0){

		//Well we know we have only point input as no PID 0
		$table_result_ouput = "<table id=\"$btn_name\">
					<tr><!-- First input -->
						<td><div align=\"center\">Spend</div></td>
						<td><div align=\"center\"><img src=\"$f_sourceIcon\" width=\"16\" hight=\"16\" title=\"$f_sourceName\"> $format_pt_fAmount</div></td>
						<td>
							<div align=\"center\">
								<b><form method=\"post\">
									<input type=\"hidden\" value=\"\" name=\"$btn_name\"/>
									<input type=\"submit\" class=\"button-secondary\" value=\"Transfer\" onclick=\"return confirm('You are about to transfer $format_pt_fAmount $f_sourceName for $pt_dAmount $destName. Are you sure?');\" />
								</form></b>
							</div>
						</td>
						<td><div align=\"center\"><img src=\"$destIcon\" width=\"16\" hight=\"16\" title=\"$destName\"> $format_pt_dAmount</div></td>
						<td><div align=\"center\">Receive</div></td>
					</tr>
					";

	} else {

	//Output for when there is two points.
	$table_result_ouput = "<table id=\"$btn_name\">
				<tr><!-- First input -->
					<td rowspan=\"2\"><div align=\"center\">Spend</div></td>
					<td><div align=\"center\"><img src=\"$f_sourceIcon\" width=\"16\" hight=\"16\" title=\"$f_sourceName\"> $format_pt_fAmount</div></td>
					<td rowspan=\"2\">
						<div align=\"center\">
							<b><form method=\"post\">
								<input type=\"hidden\" value=\"\" name=\"$btn_name\"/>
								<input type=\"submit\" class=\"button-secondary\" value=\"Transfer\" onclick=\"return confirm('You are about to transfer $format_pt_fAmount $f_sourceName and $format_pt_sAmount $s_sourceName for $pt_dAmount $destName. Are you sure?');\" />
							</form></b>
						</div>
					</td>
					<td rowspan=\"2\"><div align=\"center\"><img src=\"$destIcon\" width=\"16\" hight=\"16\" title=\"$destName\"> $format_pt_dAmount</div></td>
					<td rowspan=\"2\"><div align=\"center\">Receive</div></td>
				</tr>
				<tr><!-- Second input -->
					<td><div align=\"center\"><img src=\"$s_sourceIcon\" width=\"16\" hight=\"16\" title=\"$s_sourceName\"> $format_pt_sAmount</div></td>
				</tr>";
	}

	//NOTE: I'm ending the table here and the next is ends
	//The only thing that really changes is the last row message. Ergo I can save a lot of code editing by just modifying the below and keeping the above a template.
	//I just have to remember to close the damn table or it all blows up.
	$table_close_output = "
				<tr>
					<td colspan = 5><div align=\"center\"><b>$results_message</b></div></td>
				</tr>
			</table>";
				//<br><br>$btn_name";	//Debug: I'm curious what it looks like.

	//Lets see if it works:
	return $table_result_ouput . $table_close_output;

}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-pe', 'vyps_point_exchange_func');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */

/* WW shortcode was here but moved it out */
