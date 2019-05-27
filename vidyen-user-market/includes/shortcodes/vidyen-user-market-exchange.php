<?php

//This is a copy of the threshold user_exchange. I need to remove the table stuff from Here
//And then add two user items

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function vidyen_user_market_func($atts)
{
	//Check to see if user is logged in and boot them out of function if they aren't.
	if(!is_user_logged_in())
	{
		return; //They see nothing. Let the other things handle it.
	}

	/* The shortcode attributes need to come before the button as they determin the button value */

	/*
	* sell_id=destination point id //goddamned smart that id made two poitnts here
	* buy_id=source point ID
	* sell_amount=the cost of buying a user_exchange ticket
	* buy_amount=the amount they get from selling.
	* uid = unique idefentifier in case more than one on a page
	* tickets? there will be only 2 members to the party. no more no less.
	* thats pretty much it
	*/

	$atts = shortcode_atts(
		array(
				'sell_id' => 0,
				'sell_amount' => 0,
				'buy_id' => 0,
				'buy_amount' => 0,
				'uid' => '',
		), $atts, 'vidyen-user-market' );

	$sell_id = $atts['sell_id'];
	$sell_amount = $atts['sell_amount'];
	$buy_id = $atts['buy_id'];
	$buy_amount = $atts['buy_amount'];

	$unique_id = $atts['uid'];

	$exchange_threshold = 2; //I'm hardcoding this

	//NOTE: Used for prevenint F5 reposting, but will break themes. See below.
	$refresh_mode = $atts['refresh'];

	/* Not seeing comma number seperators annoys me */

	$format_sell_amount = number_format($sell_amount);
	$format_buy_amount = number_format($buy_amount);

	//Legacy method used for created game id, not the post button.
	$vyps_meta_id = "user_exchange" . $sell_id . $buy_id . $sell_amount . $buy_amount . $unique_id;

	//Mobile view variable pass
	$mobile_view = $atts['mobile'];

	//Check to see all the attributes were set
	if ( $sell_amount == 0 )
	{
		return "Admin Error: Sell amount was 0!";
	}

	if ( $buy_amount == 0 )
	{
		return "Admin Error: Buy amount was 0!";
	}

	/* Oh yeah. Checking to see if source pid was set */
	if ( $sell_id == 0 )
	{
		return "Admin Error: You did not set sell point id!";
	}

	/* And the destination pid */

	if ( $buy_id == 0 )
	{
		return "Admin Error: You did not set buy point id!";
	}

	if ( $unique_id == '' )
	{
		return "Admin Error: You did not set unique identifier!";
	}

	//We may no longer need this. Can replace with a lot of generic functions. Go me.

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

	$sell_id_name = vyps_point_name_func($sell_id);
	$sell_id_icon = vyps_point_icon_func($sell_id);

	$buy_id_name = vyps_point_name_func($buy_id);
	$buy_id_icon = vyps_point_icon_func($buy_id);

	//Ok, the next has to remain.
	//This is just a hunch about the order of santization.
	$vyps_meta_id = sanitize_text_field($vyps_meta_id);
	$game_id = $vyps_meta_id; //see what I did there? user_exchanges will be idenitfied by the same meta as the button name to keep each shortcode unique (unless got two doing the same? which would be dumb)

	//Going to check to count metaid (which is the game id) to see if any games exist
	//SELECT icon FROM $table_name_points WHERE id= '$buy_id'
	$game_id_query = "SELECT count(vyps_meta_id) FROM ". $table_name_log . " WHERE vyps_meta_id = %s";
	$game_id_query_prepared = $wpdb->prepare( $game_id_query, $game_id );
	$game_id_count = $wpdb->get_var( $game_id_query_prepared ); //You know. Is get_var the best for getting counts? Meh. It seems to work.

	//If the game count is zero. Means its not in the database so we need to set meta count to 1;
	if ($game_id_count == 0)
	{
		$vyps_meta_subid1 = 1; //Starting at one. So shouldn't matter.
		$vyps_meta_subid2 = 1; //Starts at one as well, but there will always be n + 1 in the end for rows for the winning result transaction... Actually. Huh
		$tickets_left = $exchange_threshold; //Ok. This is off by one, because I need this to
		$current_ticket_count = 0; //Zero because we start at 1
		$current_game_count = 0; //No games?
	}
	else
	{
		$vyps_meta_data = "user_exchange";
		//Going to check for the subid (as we know that if there is a game count it put one in)
		$vyps_meta_subid1_max_query = "SELECT max(vyps_meta_subid1) FROM ". $table_name_log . " WHERE vyps_meta_id = %s AND vyps_meta_data = %s";
		$vyps_meta_subid1_max_prepared = $wpdb->prepare( $vyps_meta_subid1_max_query, $game_id, $vyps_meta_data ); //I realized that I can only need to look at rows that are user_exchanges. If rows = threshold, then we done with that game.
		$vyps_meta_subid1_max = $wpdb->get_var( $vyps_meta_subid1_max_prepared );

		//Originally, I was doing min and counting down, but I realized I was being dumb with trying to keep track of two incremental numbers going in opposite directions
		$vyps_meta_subid2_max_query = "SELECT max(vyps_meta_subid2) FROM ". $table_name_log . " WHERE vyps_meta_id = %s AND vyps_meta_subid1 = %d AND vyps_meta_data = %s" ;
		$vyps_meta_subid2_max_prepared = $wpdb->prepare( $vyps_meta_subid2_max_query, $game_id, $vyps_meta_subid1_max, $vyps_meta_data );
		$vyps_meta_subid2_max = $wpdb->get_var( $vyps_meta_subid2_max_prepared );

		//We need to get the tickets left. We can simply get that by seeing what the last game row was and pull it. I'm going to use subid2 for this
		//We don't need to get a count since its a counting one
		$current_game_count = $vyps_meta_subid1_max; //Well its just the last one whatevers of subid1. It doesn't change as much as subid 2 though.
		$current_ticket_count = $vyps_meta_subid2_max;
		$tickets_left = $exchange_threshold - $current_ticket_count; //Better not get an off by one.

		//Ok this would be a good place to check if the subid2 = threshold which means new row next game
		if ( $vyps_meta_subid2_max == $exchange_threshold)
		{
			$current_game_count = $current_game_count + 1; //I will beat this FOBO
			$current_ticket_count = 0; //Zero because we start at 1 Heeee! This better work.
			$tickets_left = $exchange_threshold; //This needs to also be made the same as it has no clue where to look.
		}
	}

	$post_vyps_meta_id = "user_exchange" . $sell_id . $buy_id . $sell_amount . $buy_amount . $unique_id;

	//return $game_id_count; //debug

	if (!isset($_POST[ $post_vyps_meta_id ]))
	{
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

		$results_message = "Press button to buy user_exchange ticket.";


		//NOTE: Here is the catch. If the post is not pushed. Then this return will fire. Kicks us out. And no operations will happen.
		//If I had to do this over again, I would have used the new PE method with return at the end.
		return "<table id=\"$post_vyps_meta_id\">
					<tr>
						<td><div align=\"center\">Ticket Price</div></td>
						<td><div align=\"center\"><img src=\"$sell_id_icon\" width=\"16\" hight=\"16\" title=\"$sell_id_name\"> $format_sell_amount</div></td>
						<td>
							<div align=\"center\">
								<b><form method=\"post\">
									<input type=\"hidden\" value=\"\" name=\"$post_vyps_meta_id\"/>
									<input type=\"submit\" class=\"button-secondary\" value=\"Buy\" onclick=\"return confirm('You are about to by 1 ticket for $format_sell_amount $sell_id_name for a chance to win $buy_amount $buy_id_name. Are you sure?');\" />
								</form></b>
							</div>
						</td>
						<td><div align=\"center\"><img src=\"$buy_id_icon\" width=\"16\" hight=\"16\" title=\"$buy_id_name\"> $format_buy_amount</div></td>
						<td><div align=\"center\">$tickets_left of $exchange_threshold Left</div></td>
					</tr>
					<tr>
						<td colspan = 5><div align=\"center\"><b>$results_message</b></div></td>
					</tr>
				</table>";
					//<br><br>$post_vyps_meta_id";	//Debug: I'm curious what it looks like.
	}


	/* These operations are below the post check as no need to waste server CPU if user didn't press button */


	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$current_user_id = get_current_user_id();

	//Ok. Now we get balance. If it is not enough for the spend variable, we tell them that and return out. NO EXCEPTIONS

	//SELECT sum(points_amount) FROM $table_name_log WHERE user_id = $current_user_id AND points = $sell_id
	$balance_points_query = "SELECT sum(points_amount) FROM ". $table_name_log . " WHERE user_id = %d AND point_id = %d";
	$balance_points_query_prepared = $wpdb->prepare( $balance_points_query, $current_user_id, $sell_id );
	$balance_points = $wpdb->get_var( $balance_points_query_prepared );

	/* I do not ever see the need for a non-formatted need point */
	$need_points = number_format($sell_amount - $balance_points);

	if ( $sell_amount > $balance_points )
	{
		$results_message = "Not enough " . $sell_id_name . " to buy a ticket! You need " . $need_points . " more.";

		return "<table id=\"$post_vyps_meta_id\">
					<tr>
						<td><div align=\"center\">Ticket Price</div></td>
						<td><div align=\"center\"><img src=\"$sell_id_icon\" width=\"16\" hight=\"16\" title=\"$sell_id_name\"> $format_sell_amount</div></td>
						<td>
							<div align=\"center\">
								<b><form method=\"post\">
									<input type=\"hidden\" value=\"\" name=\"$post_vyps_meta_id\"/>
									<input type=\"submit\" class=\"button-secondary\" value=\"Buy\" onclick=\"return confirm('You are about to by 1 ticket for $format_sell_amount $sell_id_name for a chance to win $buy_amount $buy_id_name. Are you sure?');\" />
								</form></b>
							</div>
						</td>
						<td><div align=\"center\"><img src=\"$buy_id_icon\" width=\"16\" hight=\"16\" title=\"$buy_id_name\"> $format_buy_amount</div></td>
						<td><div align=\"center\">$tickets_left of $exchange_threshold Left</div></td>
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
	$amount = $sell_amount * -1; //Seems like this is a bad idea to just multiply it by a negative 1 but hey

	$PointType = $sell_id; //Originally this was a table call, but seems easier this way
	$user_id = $current_user_id;

	/* In my heads points out should happen first and then points destination. */

	//NOTE: Adding the $game_id to the issue. BTW meta_id etc was reserved so prefixed vyps
	//Btw I'm 75% sure putting in VYPS meta count this way will make it constant until the win.
	//Could be terribly wrong though

	$current_ticket_count = $current_ticket_count + 1; //Ah now I know why to start at zero. Actually some of that I don't think we need. Just a few bytes wasted I guess.

	$tickets_left = $exchange_threshold - $current_ticket_count; //Well. Good as place as any to check the tickets left.

	$data = [
				'reason' => $reason,
				'point_id' => $PointType,
				'points_amount' => $amount,
				'user_id' => $user_id,
				'vyps_meta_id' => $game_id,
				'vyps_meta_data' => 'user_exchange',
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

	if ($current_ticket_count == $exchange_threshold)
	{
		//Ok with exchange there are always points given to both parties.
		//So the winners are just ticket 1 and 2

		//Did i start coutning at 0?
		$seller_ticket = 1;
		$buyer_ticket = 2;

		//$winning_ticket = mt_rand(1,$exchange_threshold); //In theory the ticket threshold should always be an integer sooo... One of those tickets should hav wonder+


		$vyps_meta_data = "user_exchange";

		//Seller
		//I'm going to be really surprised if the query works on the first try. Basically we find the user id of the ticket owner that one (it should be already inserted into the table)
		$vidyen_exchange_buyer_user_id_query = "SELECT user_id FROM ". $table_name_log . " WHERE vyps_meta_id = %s AND vyps_meta_subid1 = %d AND vyps_meta_data = %s AND vyps_meta_subid2 = %d" ;
		$vidyen_exchange_buyer_user_id_prepared = $wpdb->prepare( $vidyen_exchange_buyer_user_id_query, $game_id, $vyps_meta_subid1_max, $vyps_meta_data, $buyer_ticket );
		$vidyen_exchange_buyer_user_id = $wpdb->get_var( $vidyen_exchange_buyer_user_id_prepared );

		//I want the display_name to make it look nice because of OCD. Will only be used on message
		$display_name_data_query = "SELECT display_name FROM ". $table_name_users . " WHERE id = %d"; //Note: Pulling from WP users table
		$display_name_data_query_prepared = $wpdb->prepare( $display_name_data_query, $vidyen_exchange_buyer_user_id );
		$display_name_data = $wpdb->get_var( $display_name_data_query_prepared );

		//Buyer
		//I'm going to be really surprised if the query works on the first try. Basically we find the user id of the ticket owner that one (it should be already inserted into the table)
		$vidyen_exchange_seller_user_id_query = "SELECT user_id FROM ". $table_name_log . " WHERE vyps_meta_id = %s AND vyps_meta_subid1 = %d AND vyps_meta_data = %s AND vyps_meta_subid2 = %d" ;
		$vidyen_exchange_seller_user_id_prepared = $wpdb->prepare( $vidyen_exchange_seller_user_id_query, $game_id, $vyps_meta_subid1_max, $vyps_meta_data, $buyer_ticket );
		$vidyen_exchange_seller_user_id = $wpdb->get_var( $vidyen_exchange_seller_user_id_prepared );

		//I want the display_name to make it look nice because of OCD. Will only be used on message
		$display_name_data_query = "SELECT display_name FROM ". $table_name_users . " WHERE id = %d"; //Note: Pulling from WP users table
		$display_name_data_query_prepared = $wpdb->prepare( $display_name_data_query, $vidyen_exchange_seller_user_id );
		$display_name_data = $wpdb->get_var( $display_name_data_query_prepared );


		/* Ok. Now we put the destination points in. Reason should stay the same */

		$amount = $buy_amount; //Destination amount should be positive

		$PointType = $buy_id; //Originally this was a table call, but seems easier this way

		//NOTE: I"m curious if given them both the meta will cause issues? shouldn't. WCCW
		$reason = "user_exchange";
		$vyps_meta_data = "user_exchange";

		//This way will be easier for me to keep track of:
		$vyps_meta_id = $game_id;
		$vyps_meta_data = $vyps_meta_data;
		$vyps_meta_subid1 = $current_game_count;
		$vyps_meta_subid2 = $current_ticket_count;
		//$vyps_meta_subid3 = $winning_ticket;

		//NOTE: These are ontogically reversed as the seller gets the buyer and the buyer gets the seller.
		//Credit Seller
		$credit_seller_result = vyps_point_credit_func($buy_id, $buy_amount, $vidyen_exchange_seller_user_id, $reason, $vyps_meta_id, $vyps_meta_data, $vyps_meta_subid1, $vyps_meta_subid2, $seller_ticket);

		//Credit Buyer
		$credit_buyer_result = vyps_point_credit_func($sell_id, $sell_amount, $vidyen_exchange_buyer_user_id, $reason, $vyps_meta_id, $vyps_meta_data, $vyps_meta_subid1, $vyps_meta_subid2, $buyer_ticket);

		$results_message = "The user $display_name_data won with ticket $winning_ticket : ". date('Y-m-d H:i:s');
		//for now i'm just going to see if this works before adding RNG

		//Meh I need a butom button!, but I sort of need to change the table a bit rather than reuse it.
		//Having issues. One day will come back and fix. Damn I need more helpers.

		return "<table id=\"refresh\">
					<tr>
						<td><div align=\"center\">Ticket Price</div></td>
						<td><div align=\"center\"><img src=\"$sell_id_icon\" width=\"16\" hight=\"16\" title=\"$sell_id_name\"> $format_sell_amount</div></td>
						<td>
							<div align=\"center\">
								<b><form method=\"post\">
									<input type=\"hidden\" value=\"\" name=\"refresh\"/>
									<input type=\"submit\" class=\"button-secondary\" value=\"Refresh\" onclick=\"return confirm('You are about to by 1 ticket for $format_sell_amount $sell_id_name for a chance to win $buy_amount $buy_id_name. Are you sure?');\" />
								</form></b>
							</div>
						</td>
						<td><div align=\"center\"><img src=\"$buy_id_icon\" width=\"16\" hight=\"16\" title=\"$buy_id_name\"> $format_buy_amount</div></td>
						<td><div align=\"center\">0 of $exchange_threshold Left</div></td>
					</tr>
					<tr>
						<td colspan = 5><div align=\"center\"><b>$results_message</b></div></td>
					</tr>
				</table>";

	}

	return "<table id=\"$post_vyps_meta_id\">
				<tr>
					<td><div align=\"center\">Ticket Price</div></td>
					<td><div align=\"center\"><img src=\"$sell_id_icon\" width=\"16\" hight=\"16\" title=\"$sell_id_name\"> $format_sell_amount</div></td>
					<td>
						<div align=\"center\">
							<b><form method=\"post\">
								<input type=\"hidden\" value=\"\" name=\"$post_vyps_meta_id\"/>
								<input type=\"submit\" class=\"button-secondary\" value=\"Buy\" onclick=\"return confirm('You are about to by 1 ticket for $format_sell_amount $sell_id_name for a chance to win $buy_amount $buy_id_name. Are you sure?');\" />
							</form></b>
						</div>
					</td>
					<td><div align=\"center\"><img src=\"$buy_id_icon\" width=\"16\" hight=\"16\" title=\"$buy_id_name\"> $format_buy_amount</div></td>
					<td><div align=\"center\">$tickets_left of $exchange_threshold Left</div></td>
				</tr>
				<tr>
					<td colspan = 5><div align=\"center\"><b>$results_message</b></div></td>
				</tr>
			</table>";

			/* since I have the point names I might as well use them. Also I put it below because its annoying to have button move. */
			//<br><br>$post_vyps_meta_id"; //Debug stuff


}

/* Telling WP to use function for shortcode */

add_shortcode( 'vidyen-user-market', 'vidyen_user_market_func');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */

/* WW shortcode was here but moved it out */
