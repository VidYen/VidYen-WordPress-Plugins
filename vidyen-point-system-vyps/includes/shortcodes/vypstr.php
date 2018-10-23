<?php

//VIDYEN Points Threshold Raffle Shortcode


/* I straight up copied vyps_pt_tbl.php and renamed the functions as raffle system should use the table
* The idea of this is to have many people micromine and compete for a big payout via raffle system
* As timed ones never work out since Ironking will buy one ticket and wait, I decided to make it based on
* a threshold and then decide winner when enough people buy it. So time is never a constant here.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Added prepare() to all SQL SELECT calls 7.1.2018 */

function vyps_point_threshold_raffle_func( $atts ) {

	//Check to see if user is logged in and boot them out of function if they aren't.

	if ( is_user_logged_in() ) {

		//I probaly don't have to have this part of the if

	} else {

		return;

	}

	/* The shortcode attributes need to come before the button as they determin the button value */

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
				'mobile' => false,
				'refresh' => false,
		), $atts, 'vyps-tr' );

	$sourcePointID = $atts['spid'];
	$destinationPointID = $atts['dpid'];
	$pt_sAmount = $atts['samount'];
	$pt_dAmount = $atts['damount'];
	$ticket_threshold = $atts['tickets'];

	//NOTE: Used for prevenint F5 reposting, but will break themes. See below.
	$refresh_mode = $atts['refresh'];

	/* Not seeing comma number seperators annoys me */

	$format_pt_sAmount = number_format($pt_sAmount);
	$format_pt_dAmount = number_format($pt_dAmount);

	//Legacy method used for created game id, not the post button.
	$btn_name = "raffle" . $sourcePointID . $destinationPointID . $pt_sAmount . $pt_dAmount . $ticket_threshold;

	//Mobile view variable pass
	$mobile_view = $atts['mobile'];

	/*I don't know if this is some lazy coding but I am going to just return out if they haven't pressed the button
	* Side note: And this is important. The button value should be dynamic to not interfer with other buttons on page
	*
	*/

	//Check to see all the attributes were set

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

	if ( $ticket_threshold == 0 ) {

		return "Admin Error: You did not set ticket amount!";

	}

	/* I tried avoiding calling this before the press, but had to get point names */

	global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_users = $wpdb->prefix . 'users';

	/* Just doing some table calls to get point names and icons. Can you put icons in buttons? Hrm... */

	//Ok below is just the new way we are going to handle prepares. Takes 4 lines to do one get_var now, but just throw more hardware at it.
	//1. Query comment as should be written out if you pasted it into command lines
	//2. the Query pre-pregarded
	//3. the query prepared
	//4. The get_var command. Btw, I would like to avoid calling entire rows if possible as we usually are interested in different tables
	//   And would be harder to read and not really needed.
	//   BTW all table names are hard coded even though they are variables depending on the name of the WP table, but I think
	//   if the prefix was an injection string the entire SQL server would have broke before then -Felty


	//"SELECT name FROM $table_name_points WHERE id= '$sourcePointID'"
	$sourceName_query = "SELECT name FROM ". $table_name_points . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
	$sourceName_query_prepared = $wpdb->prepare( $sourceName_query, $sourcePointID );
	$sourceName = $wpdb->get_var( $sourceName_query_prepared );

	//"SELECT icon FROM $table_name_points WHERE id= '$sourcePointID'"
	$sourceIcon_query = "SELECT icon FROM ". $table_name_points . " WHERE id= %d";
	$sourceIcon_query_prepared = $wpdb->prepare( $sourceIcon_query, $sourcePointID );
	$sourceIcon = $wpdb->get_var( $sourceIcon_query_prepared );

	//SELECT name FROM $table_name_points WHERE id= '$destinationPointID'"
	$destName_query = "SELECT name FROM ". $table_name_points . " WHERE id= %d";
	$destName_query_prepared = $wpdb->prepare( $destName_query, $destinationPointID );
	$destName = $wpdb->get_var( $destName_query_prepared );

	//SELECT icon FROM $table_name_points WHERE id= '$destinationPointID'
	$destIcon_query = "SELECT icon FROM ". $table_name_points . " WHERE id= %d";
	$destIcon_query_prepared = $wpdb->prepare( $destIcon_query, $destinationPointID );
	$destIcon = $wpdb->get_var( $destIcon_query_prepared );

	//Ok we need to check to see if there is a raffle game going on and if it is then keep on going
	//This is just a hunch about the order of santization.
	$btn_name = sanitize_text_field($btn_name);
	$game_id = $btn_name; //see what I did there? raffles will be idenitfied by the same meta as the button name to keep each shortcode unique (unless got two doing the same? which would be dumb)
	//$game_id = sanitize_text_field($game_id); //I believe shortcodes have some sort of santization built in, but easier to do this than research that

	//return $game_id; //debug. What does this show?

	//Going to check to count metaid (which is the game id) to see if any games exist
	//SELECT icon FROM $table_name_points WHERE id= '$destinationPointID'
	$game_id_query = "SELECT count(vyps_meta_id) FROM ". $table_name_log . " WHERE vyps_meta_id = %s";
	$game_id_query_prepared = $wpdb->prepare( $game_id_query, $game_id );
	$game_id_count = $wpdb->get_var( $game_id_query_prepared ); //You know. Is get_var the best for getting counts? Meh. It seems to work.


	//If the game count is zero. Means its not in the database so we need to set meta count to 1;
	if ($game_id_count == 0) {

		$vyps_meta_subid1 = 1; //Starting at one. So shouldn't matter.
		$vyps_meta_subid2 = 1; //Starts at one as well, but there will always be n + 1 in the end for rows for the winning result transaction... Actually. Huh
		$tickets_left = $ticket_threshold; //Ok. This is off by one, because I need this to
		$current_ticket_count = 0; //Zero because we start at 1
		$current_game_count = 0; //No games?

	} else {

		$vyps_meta_data = "rafflebuy";
		//Going to check for the subid (as we know that if there is a game count it put one in)
		$vyps_meta_subid1_max_query = "SELECT max(vyps_meta_subid1) FROM ". $table_name_log . " WHERE vyps_meta_id = %s AND vyps_meta_data = %s";
		$vyps_meta_subid1_max_prepared = $wpdb->prepare( $vyps_meta_subid1_max_query, $game_id, $vyps_meta_data ); //I realized that I can only need to look at rows that are rafflebuys. If rows = threshold, then we done with that game.
		$vyps_meta_subid1_max = $wpdb->get_var( $vyps_meta_subid1_max_prepared );

		//Originally, I was doing min and counting down, but I realized I was being dumb with trying to keep track of two incremental numbers going in opposite directions
		$vyps_meta_subid2_max_query = "SELECT max(vyps_meta_subid2) FROM ". $table_name_log . " WHERE vyps_meta_id = %s AND vyps_meta_subid1 = %d AND vyps_meta_data = %s" ;
		$vyps_meta_subid2_max_prepared = $wpdb->prepare( $vyps_meta_subid2_max_query, $game_id, $vyps_meta_subid1_max, $vyps_meta_data );
		$vyps_meta_subid2_max = $wpdb->get_var( $vyps_meta_subid2_max_prepared );

		//We need to get the tickets left. We can simply get that by seeing what the last game row was and pull it. I'm going to use subid2 for this
		//We don't need to get a count since its a counting one
		$current_game_count = $vyps_meta_subid1_max; //Well its just the last one whatevers of subid1. It doesn't change as much as subid 2 though.
		$current_ticket_count = $vyps_meta_subid2_max;
		$tickets_left = $ticket_threshold - $current_ticket_count; //Better not get an off by one.

		//Ok this would be a good place to check if the subid2 = threshold which means new row next game
		if ( $vyps_meta_subid2_max == $ticket_threshold){

			$current_game_count = $current_game_count + 1; //I will beat this FOBO
			$current_ticket_count = 0; //Zero because we start at 1 Heeee! This better work.
			$tickets_left = $ticket_threshold; //This needs to also be made the same as it has no clue where to look.

		}

	}

	$post_btn_name = "raffle" . $sourcePointID . $destinationPointID . $pt_sAmount . $pt_dAmount . $ticket_threshold;

	//return $game_id_count; //debug

	if (isset($_POST[ $post_btn_name ])){

		//Nothing really happens other than making sure is set.

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

		$results_message = "Press button to buy raffle ticket.";


		//NOTE: Here is the catch. If the post is not pushed. Then this return will fire. Kicks us out. And no operations will happen.
		//If I had to do this over again, I would have used the new PE method with return at the end.
		return "<table id=\"$post_btn_name\">
					<tr>
						<td><div align=\"center\">Ticket Price</div></td>
						<td><div align=\"center\"><img src=\"$sourceIcon\" width=\"16\" hight=\"16\" title=\"$sourceName\"> $format_pt_sAmount</div></td>
						<td>
							<div align=\"center\">
								<b><form method=\"post\">
									<input type=\"hidden\" value=\"\" name=\"$post_btn_name\"/>
									<input type=\"submit\" class=\"button-secondary\" value=\"Buy\" onclick=\"return confirm('You are about to by 1 ticket for $format_pt_sAmount $sourceName for a chance to win $pt_dAmount $destName. Are you sure?');\" />
								</form></b>
							</div>
						</td>
						<td><div align=\"center\"><img src=\"$destIcon\" width=\"16\" hight=\"16\" title=\"$destName\"> $format_pt_dAmount</div></td>
						<td><div align=\"center\">$tickets_left of $ticket_threshold Left</div></td>
					</tr>
					<tr>
						<td colspan = 5><div align=\"center\"><b>$results_message</b></div></td>
					</tr>
				</table>";
					//<br><br>$post_btn_name";	//Debug: I'm curious what it looks like.
	}


	/* These operations are below the post check as no need to waste server CPU if user didn't press button */


	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$current_user_id = get_current_user_id();

	//Ok. Now we get balance. If it is not enough for the spend variable, we tell them that and return out. NO EXCEPTIONS

	//SELECT sum(points_amount) FROM $table_name_log WHERE user_id = $current_user_id AND points = $sourcePointID
	$balance_points_query = "SELECT sum(points_amount) FROM ". $table_name_log . " WHERE user_id = %d AND point_id = %d";
	$balance_points_query_prepared = $wpdb->prepare( $balance_points_query, $current_user_id, $sourcePointID );
	$balance_points = $wpdb->get_var( $balance_points_query_prepared );

	/* I do not ever see the need for a non-formatted need point */
	$need_points = number_format($pt_sAmount - $balance_points);

	if ( $pt_sAmount > $balance_points ) {

		$results_message = "Not enough " . $sourceName . " to buy a ticket! You need " . $need_points . " more.";

		return "<table id=\"$post_btn_name\">
					<tr>
						<td><div align=\"center\">Ticket Price</div></td>
						<td><div align=\"center\"><img src=\"$sourceIcon\" width=\"16\" hight=\"16\" title=\"$sourceName\"> $format_pt_sAmount</div></td>
						<td>
							<div align=\"center\">
								<b><form method=\"post\">
									<input type=\"hidden\" value=\"\" name=\"$post_btn_name\"/>
									<input type=\"submit\" class=\"button-secondary\" value=\"Buy\" onclick=\"return confirm('You are about to by 1 ticket for $format_pt_sAmount $sourceName for a chance to win $pt_dAmount $destName. Are you sure?');\" />
								</form></b>
							</div>
						</td>
						<td><div align=\"center\"><img src=\"$destIcon\" width=\"16\" hight=\"16\" title=\"$destName\"> $format_pt_dAmount</div></td>
						<td><div align=\"center\">$tickets_left of $ticket_threshold Left</div></td>
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
	$reason = "Rafle Ticket Purchase";
	$amount = $pt_sAmount * -1; //Seems like this is a bad idea to just multiply it by a negative 1 but hey

	$PointType = $sourcePointID; //Originally this was a table call, but seems easier this way
	$user_id = $current_user_id;

	/* In my heads points out should happen first and then points destination. */

	//NOTE: Adding the $game_id to the issue. BTW meta_id etc was reserved so prefixed vyps
	//Btw I'm 75% sure putting in VYPS meta count this way will make it constant until the win.
	//Could be terribly wrong though

	$current_ticket_count = $current_ticket_count + 1; //Ah now I know why to start at zero. Actually some of that I don't think we need. Just a few bytes wasted I guess.

	$tickets_left = $ticket_threshold - $current_ticket_count; //Well. Good as place as any to check the tickets left.

	$data = [
				'reason' => $reason,
				'point_id' => $PointType,
				'points_amount' => $amount,
				'user_id' => $user_id,
				'vyps_meta_id' => $game_id,
				'vyps_meta_data' => 'rafflebuy',
				'vyps_meta_subid1' => $current_game_count,
				'vyps_meta_subid2' => $current_ticket_count,
				'time' => date('Y-m-d H:i:s')
				];
		$wpdb->insert($table_log, $data);

		//NOTE: This was a custom request. It got so annoying trying to get it to work with all themes
		//that I told them, if they want ot mess with it, they can add the refresh=true on theirs.
		//Otherwise tell your users not to hit F5 to refresh the post.
		if( $refresh_mode == true ){

			$new_url = add_query_arg( 'success', 1, get_permalink() );
			wp_redirect( $new_url, 303 );

		}

		//The below should only be run if there is a winner. which means we should only fire if the game games left = 1
		//Yeah counting at zero, but this fires it means there was one ticket left so there should be no more as this is exectuting

		$results_message = "Success. Ticket bought at: ". date('Y-m-d H:i:s');

	//Ok I wrapped my head around where I am having the off by one error.
	//So if the post happens when tickets are left is 1. That means that is the last ticket and there needs to be a row made with a 0 entry for subid2?

	if ($current_ticket_count == $ticket_threshold) {

		//the code that runs when there are no more tickets left. which means the person bought the last one.
		//In theory on the buying of the last ticket the winner is found at the same time.

		//Find the winner

		$winning_ticket = mt_rand(1,$ticket_threshold); //In theory the ticket threshold should always be an integer sooo... One of those tickets should hav wonder+


		$vyps_meta_data = "rafflebuy";

		//I'm going to be really surprised if the query works on the first try. Basically we find the user id of the ticket owner that one (it should be already inserted into the table)
		$vyps_winning_user_id_query = "SELECT user_id FROM ". $table_name_log . " WHERE vyps_meta_id = %s AND vyps_meta_subid1 = %d AND vyps_meta_data = %s AND vyps_meta_subid2 = %d" ;
		$vyps_winning_user_id_prepared = $wpdb->prepare( $vyps_winning_user_id_query, $game_id, $vyps_meta_subid1_max, $vyps_meta_data, $winning_ticket );
		$vyps_winning_user_id = $wpdb->get_var( $vyps_winning_user_id_prepared );

		//I want the display_name to make it look nice because of OCD. Will only be used on message
		$display_name_data_query = "SELECT display_name FROM ". $table_name_users . " WHERE id = %d"; //Note: Pulling from WP users table
		$display_name_data_query_prepared = $wpdb->prepare( $display_name_data_query, $vyps_winning_user_id );
		$display_name_data = $wpdb->get_var( $display_name_data_query_prepared );

		/* Ok. Now we put the destination points in. Reason should stay the same */

		$amount = $pt_dAmount; //Destination amount should be positive

		$PointType = $destinationPointID; //Originally this was a table call, but seems easier this way


   //Note sure if it matters witht the tickets left part.

	 $reason = "Raffle Ticket Win";
	 $vyps_meta_data = "rafflewin";

		$data = [
				'reason' => $reason,
				'point_id' => $PointType,
				'points_amount' => $amount,
				'user_id' => $vyps_winning_user_id,
				'vyps_meta_id' => $game_id,
				'vyps_meta_data' => $vyps_meta_data,
				'vyps_meta_subid1' => $current_game_count,
				'vyps_meta_subid2' => $current_ticket_count,
				'vyps_meta_subid3' => $winning_ticket,
				'time' => date('Y-m-d H:i:s')
				];
		$wpdb->insert($table_log, $data);

		$results_message = "The user $display_name_data won with ticket $winning_ticket : ". date('Y-m-d H:i:s');
		//for now i'm just going to see if this works before adding RNG

		//Meh I need a butom button!, but I sort of need to change the table a bit rather than reuse it.
		//Having issues. One day will come back and fix. Damn I need more helpers.

		return "<table id=\"refresh\">
					<tr>
						<td><div align=\"center\">Ticket Price</div></td>
						<td><div align=\"center\"><img src=\"$sourceIcon\" width=\"16\" hight=\"16\" title=\"$sourceName\"> $format_pt_sAmount</div></td>
						<td>
							<div align=\"center\">
								<b><form method=\"post\">
									<input type=\"hidden\" value=\"\" name=\"refresh\"/>
									<input type=\"submit\" class=\"button-secondary\" value=\"Refresh\" onclick=\"return confirm('You are about to by 1 ticket for $format_pt_sAmount $sourceName for a chance to win $pt_dAmount $destName. Are you sure?');\" />
								</form></b>
							</div>
						</td>
						<td><div align=\"center\"><img src=\"$destIcon\" width=\"16\" hight=\"16\" title=\"$destName\"> $format_pt_dAmount</div></td>
						<td><div align=\"center\">0 of $ticket_threshold Left</div></td>
					</tr>
					<tr>
						<td colspan = 5><div align=\"center\"><b>$results_message</b></div></td>
					</tr>
				</table>";

	}

	return "<table id=\"$post_btn_name\">
				<tr>
					<td><div align=\"center\">Ticket Price</div></td>
					<td><div align=\"center\"><img src=\"$sourceIcon\" width=\"16\" hight=\"16\" title=\"$sourceName\"> $format_pt_sAmount</div></td>
					<td>
						<div align=\"center\">
							<b><form method=\"post\">
								<input type=\"hidden\" value=\"\" name=\"$post_btn_name\"/>
								<input type=\"submit\" class=\"button-secondary\" value=\"Buy\" onclick=\"return confirm('You are about to by 1 ticket for $format_pt_sAmount $sourceName for a chance to win $pt_dAmount $destName. Are you sure?');\" />
							</form></b>
						</div>
					</td>
					<td><div align=\"center\"><img src=\"$destIcon\" width=\"16\" hight=\"16\" title=\"$destName\"> $format_pt_dAmount</div></td>
					<td><div align=\"center\">$tickets_left of $ticket_threshold Left</div></td>
				</tr>
				<tr>
					<td colspan = 5><div align=\"center\"><b>$results_message</b></div></td>
				</tr>
			</table>";

			/* since I have the point names I might as well use them. Also I put it below because its annoying to have button move. */
			//<br><br>$post_btn_name"; //Debug stuff


}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-tr', 'vyps_point_threshold_raffle_func');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */

/* WW shortcode was here but moved it out */
