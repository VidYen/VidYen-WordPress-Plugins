<?php

//VIDYEN Points Exchange Shortcode
//Advanced version with timer

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function vyps_reward_timer_func( $atts )
{

	//The shortcode attributes need to come before the button as they determine the button value

	$atts = shortcode_atts(
		array(
				'outputid' => 0,
				'outputamount' => 0,
				'days' => '0',
				'hours' => '0',
				'minutes' => '0',
        'comment' => '',
        'mobile' => TRUE,
        'reason' => 'Reward Button',
        'vyps_meta_id' => '',
		), $atts, 'vidyen-reward-timer' );

  //Check if user is logged in and stop the code.
	//NOTE:I moved this here. I realized, its more likely that 10,000 users are bashing site mem while the admin is almost always logged in.
	if ( !is_user_logged_in() )
  {
		return; //You get nothing. Use the LG code.
	}

	$user_id = get_current_user_id(); //This needs to go here as we are checking the time way before then.
	$point_id = $atts['outputid'];
	$point_amount = $atts['outputamount']; //desintation amount.
  $reason = $atts['reason']; //PE reason TODO fix later

  //Time variables
	$time_days = $atts['days'];
	$time_hours = $atts['hours'];
	$time_minutes = $atts['minutes'];

  //Now to get the raw seconds. This is important. 24 * 60 * 60 = 86400, 3600 seconds in an hour etc. We are not doing months unless you want to do my 28 day suggestion with 13 months.
  $time_left_seconds = 0; //Just in case there was no transfer inbound to have this variable set.
  $time_seconds = ($time_days * 86400) + ($time_hours * 3600) + ($time_minutes * 60);

  if ( $point_id == 0 OR $point_amount== 0 )
  {
    return 'Warning outputid and outputamount needs to be set in shortcode.';
  }

	if ($time_seconds < 1 )
  {
		//I feel like this should be not needed but I feel that it would blow up one of my ifs down below catastrophically
		return "Admin Error: You need a time greater than 0!";
	}

	//Not seeing comma number seperators annoys me
	$formatted_ouput_amount = number_format($point_amount);

  $vyps_meta_id = $point_id . $point_amount . $time_seconds; //This string should be unique enough.

  //NOTE: I'm replacing these SQL calls with functions that also do SQL calls

	//First source point name and Icon
  $output_name = vyps_point_name_func($point_id);  //I'm 80% sure that the $atts will be the same. From vyps_point_func.php
  $output_icon = vyps_point_icon_func($point_id);  //I'm 80% sure that the $atts will be the same. From vyps_point_func.php

  $last_posted_time = vyps_point_check_last_transaction_time($user_id, $point_id, $vyps_meta_id); //check the db for the seconds that have passed since last transaction

  //NOTE: I feel that this could also be functionalized, but I put the db calls mostly in the functions these days
  //return $last_posted_time;
  $current_time = date('Y-m-d H:i:s');
  $current_time = strtotime($current_time);
  $time_passed = $current_time - $last_posted_time; //I'm just making a big guess here that this will work.

  $time_left_seconds = $time_seconds - $time_passed; //NOTE: if this number is positive it means they still need to wait.
  $display_time = vyps_secondsToTime($time_left_seconds); //This converts the seconds left into something more human readable. Master race machines and replicants need not use.

  //If we know the last posted time was 0 then there wasn't a transaction found so go ahead and reward them
  //I realized I could save a case and resue this if the last posted time was greater than 0 and time left was < 1 second... its a millisecond who the hell cares!
  if ($last_posted_time == 0 OR ($last_posted_time > 0 AND $time_left_seconds < 1) )
  {
    //First things first, if the post is set for the meta id we just add the points.
    //We know its been done
    if (isset($_POST[ $vyps_meta_id ]))
    {
      $credit_result = vyps_point_credit_func( $point_id, $point_amount, $user_id, $reason, $vyps_meta_id );

      if ($credit_result == 1)
      {
        $results_message = "Success. Rewarded at: ". date('Y-m-d H:i:s');
      }
    }
    else
    {
      $results_message = "Press button to get your reward!";
    }

    //Some terrible logic behind this. Either there is the first transaction or the first initial reward.
    $reward_timer_html_ouput =
    '<table id="'.$vyps_meta_id.'">
        <tr><!-- Second row -->
          <td><div align="center">Receive</div></td>
          <td><div align="center">'.$output_icon.' '.$formatted_ouput_amount.'</div></td>
        </tr>
        <tr><!-- Button row -->
          <td colspan = 2>
            <div align="center">
              <b><form method="post">
                <input type="hidden" value="" name="'.$vyps_meta_id.'"/>
                <input type="submit" class="button-secondary" value="Get Reward" onclick="return confirm(\'You are about to receive '.$formatted_ouput_amount.' '.$output_name.'. Are you sure?\');" />
              </form></b>
            </div>
          </td>
        </tr>
        <tr>
					<td colspan = 2><div align="center"><b>'.$results_message.'</b></div></td>
				</tr>
			</table>
    ';
  }
  elseif ($time_left_seconds > 0) //So we know there was a transaction. So we are going to catch if the time left is greater than 0 which means they cannot update.
  {
    $results_message = "You have $display_time before another reward."; //Simple enough.

    //Some terrible logic behind this. Either there is the first transaction or the first initial reward.
    $reward_timer_html_ouput =
    '<table id="'.$vyps_meta_id.'">
        <tr><!-- Second row -->
          <td><div align="center">Receive</div></td>
          <td><div align="center">'.$output_icon.' '.$formatted_ouput_amount.'</div></td>
        </tr>
        <tr><!-- Button row -->
          <td colspan = 2>
            <div align="center">
              <b><form method="post">
                <input type="hidden" value="" name="'.$vyps_meta_id.'"/>
                <input type="submit" class="button-secondary" value="Get Reward" onclick="return confirm(\'You are about to receive'.$formatted_ouput_amount.' '.$output_name.'. Are you sure?\');" />
              </form></b>
            </div>
          </td>
        </tr>
        <tr>
          <td colspan = 2><div align="center"><b>'.$results_message.'</b></div></td>
        </tr>
      </table>';
  }

  return $reward_timer_html_ouput; //And that's that.

}

/* Telling WP to use function for shortcode */

add_shortcode( 'vidyen-reward-timer', 'vyps_reward_timer_func');
