<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//NOTE: I feel somewhat motivated to get this to work now that I can get referrals transfered according to Adscend.

//AS shortcode functons.
//Need less disclaimers here as average people believe that mining is evil. From my point of view, advertising is evil. -Felty
//Yeah that is a prequel reference.

// Below is the Adscend watching shortcode itself
//Needs no SQL injection checking or nonce as Adscend does the ad tracking.
//The worst someone could do is watch ads for you I guess.
function vyps_adscend_func( $atts ) {

	/* Check to see if user is logged in and boot them out of function if they aren't. */

	if (!is_user_logged_in())
	{
    return; //Admins should put the login shortcode for uniformity.
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

		return "You did not set a profile!";

	}

	/* Oh yeah. Checking to see if no pid was set */

	if ( $atts['pid'] == 0 ) {

		return "You did not set point ID!";

	}

	//note the subid2 was to satisfy my curiosity about the AS backend.

	return '<iframe src="https://asmwall.com/adwall/publisher/' . $atts['pub'] . '/profile/' . $atts['profile'] . '?subid1=' . $current_user_id . '&subid2=' . $atts['pid'] . '" frameborder="0" allowfullscreen="yes" width=800 height=600 ></iframe>';

	/* It dawned on me that the return may not be necessary  and that for this particualr shortcode it was unnecessary to
	*  actually have it post anything to our WP tables as the AS interface doesn't do that until you get a post back.
	*  It also dawned on me I could just call pid sub id etc, but not keeping the names same may confuse.
	*  maybe subid3 could be whatever the user wants?
	*/
}

/* Telling WP to use function for shortcode */
add_shortcode( 'vyps-as-watch', 'vyps_adscend_func');


//The redeem will need some cleansing but not much. -Felty
function vyps_adscend_redeem_func( $atts ) {

	/* Do the logged on check first as I guess it wastes less resources */

	if ( is_user_logged_in() ) {

		//I probaly don't have to have this part of the if

	} else {

    return; //Use the login function please.
		//return "You need to be logged in to watch ads for points.";

	}

	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$current_user_id = get_current_user_id();

	$atts = shortcode_atts(
		array(
			'pub' => '0',
			'profile' => '0',
			'api' => 'z',
			'pid' => '0',
			'payout' => '0',
			'pro' => '',
		), $atts, 'vyps-as-redeem');

	/* do the normal checks to see if the $atts were set */

	if ( $atts['pub'] == 0 ) {

	return "Publisher was not set!";

	}

	/* Oh yeah. Check Profile */

	if ( $atts['profile'] == 0 ) {

		return "You did not set a profile!";

	}

	/* Oh yeah. Checking to see if no API was set
	*  Yeah I didn't like not putting APIs in shortcode
	*  But Adscend was being a pain. Oh. The API key is on
	*  the integration page on your offer wall under API/SDK
	*  integration. It doesn't even look like its a menu.
	*  It's like that scend in HHG2G trying to get to the form.
	*  It's a shame Adscend didn't copy CH
	*/

	//return $atts['api']; //return here to see why api key was not working


	/* API key will never be a single character in theory but I needed something easy to check #lazycoding */
	if ( $atts['api'] == 'z' )
	{
		return "You did not set the API Key!";
	}

	/* Oh yeah. Checking to see if no pid was set */

	if ( $atts['pid'] == 0 )
	{
		return "You did not set point ID!";
	}

	//In theory one could set their payout to be 0 on purpose, but if you are that kind of person just comment this if out

	if ( $atts['payout'] == 0 )
	{
		return "You did not set payout!";
	}

	/* Ok. This might be lazy coding and Grey Goose but I figured we can just see if the button has been clicked */

	$VYPS_power_url = plugins_url( 'images/', dirname(__FILE__) ) . 'powered_by_vyps.png'; //Well it should work out.
	$VYPS_power_row = "<br>Powered by <a href=\"https://wordpress.org/plugins/vidyen-point-system-vyps/\" target=\"_blank\"><img src=\"$VYPS_power_url\"></a>";

	//Procheck here. Do not forget the ==
	if (vyps_procheck_func($atts) == 1)
	{
		$VYPS_power_row = ''; //No branding if procheck is correct.
	}

	if (isset($_POST["redeem"]))
	{

	}
	else
	{
		/* Just show them button if button has not been clicked. Its a requirement not a suggestion. */
		return "<form method=\"post\">
                <input type=\"hidden\" value=\"\" name=\"redeem\"/>
                <input type=\"submit\" class=\"button-secondary\" value=\"Redeem Adscend\" onclick=\"return confirm('You are about to sync your Adscend point with this site. Are you sure?');\" />
                </form>
								$VYPS_power_row";
	}

	/* I have a feeling I could check the whole array in one go, but one day I will educate myself better */

	/* Ok now we need to post the current leads to the as table and then check to see if there is more than
	*  one row with that user id and then if so caclulate the difference and post that reward to the vyps log
	*  the default leads will be zero so if there wasn't a row to begin with then all the points get awarded
	*/

	/* Hrm... The below does generate the correct json but its not pulling for some reason soo... I'm going to use the CH version */
	/* It dawned on me that the ' ' in arrays might be the problem but below is copy and paste from CH */

	$pub_id = $atts['pub'];
	$adwall_id = $atts['profile'];
	//$sub_id = 4; //I don't running those ads on my development machine
	$sub_id = $current_user_id; //ok the testing words so lets use another profile

	/* The get curl */

	$url = "https://adscendmedia.com/adwall/api/publisher/{$pub_id}/profile/{$adwall_id}/user/{$sub_id}/transactions.json";

	/* //Working on something. Will delete later. I'm thinking the postback is the best method for getting referrals.

	$site_url = esc_url(site_url());

	$vyps_url ='https://www.vidyen.com/adscend-tracking/?type=adscend&site=' . $site_url;

	*/
	//Note Api says no https but well I feel it should be so and it seems to work

	$as = curl_init();
	curl_setopt($as, CURLOPT_URL, $url);
	curl_setopt($as, CURLOPT_HEADER, 0);
	curl_setopt($as, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($as);
	curl_close($as);

	$jsonData = json_decode($result, true);
	$balance = $jsonData['currency_count'];

  //Here goes the cleansing. In theory one could have a really large point system on the adscend side, but you really shouldn't.
  $balance = intval($balance);

	//echo " pre foo ";
	//echo $balance;
	//echo " post foo ";

	/* the post to deduct the currency */

	//
	// A very simple PHP example that sends a HTTP POST to a remote site
	//

	$api_key = $atts['api'];

	$adj_balance = abs($balance) * -1; //Well. Apparently you can give your viewers more points for no good reason I guess. So we need negative values. Added abs() in cases where there are point refunds.

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

			$table_name_log = $wpdb->prefix . 'vyps_points_log';
			$reason = "Adscend"; //Felty's NOTE: I feel this could be user set, but there could be issues letting the admins have that much power.
      $payout_clean = intval($atts['payout']); //Actually, the * should keep it from being text, but then it could just blow up everything so better to be safe.
			$amount = $balance * $payout_clean;
      $amount = intval($amount); //In theory both $balance and $payout_clean have been intval() but who knows.


      $pointID = $atts['pid']; //Yeah I'm doing some cleanings and renamed some variables.
      $pointID = intval($pointID); //was originally named $pointType
			$user_id = get_current_user_id();

      //Insert into the vyps_points_log table. My OCD is getting upset at the plural. But I don't think most users will even know.
      $data = [
					'reason' => $reason,
					'point_id' => $pointID,
					'points_amount' => $amount,
					'user_id' => $user_id,
					'time' => date('Y-m-d H:i:s')
				];
			$wpdb->insert($table_name_log, $data);
		} else {
			$amount = 0; //I think this works right. Ere we go!
		}

		/* It dawned on me that text in here only needs a number and let the admin right there response
		*  One could in theory might make an IF statement you have no hashes to redeem, but KISS */
	//echo " end balance comes after ";

	/* Developers note. I try to put working into site admins mouth but this only way to show button and the redeemed points in a way that I liked.
	*  In theory if admin doesn't like it, they edit the below wording or just use the old shortcodes which I left in. -Felty
	*/
	return "<b>" . $amount . " points redeemed.</b><br><br><form method=\"post\">
                <input type=\"hidden\" value=\"\" name=\"redeem\"/>
                <input type=\"submit\" class=\"button-secondary\" value=\"Redeem Adscend\" onclick=\"return confirm('You are about to synce your Adscend point with this site. Are you sure?');\" />
                </form>
								$VYPS_power_row";

}

add_shortcode( 'vyps-as-redeem', 'vyps_adscend_redeem_func');
