<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Added prepare() to all SQL SELECT calls 7.1.2018 */

//Oh. Maybe I should put this elsewhere but I have foudn this nifty code off https://stackoverflow.com/questions/8273804/convert-seconds-into-days-hours-minutes-and-seconds
//So I'm putting it here as a function. Will use elsewhere mayhaps. If so will fix later.
//NOTE: This is the time converstion

/* Vyps should already be installed so this should already exist
function vyps_secondsToTime($seconds) {
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes, and %s seconds');
}

*/

function vyps_wc_mmo_point_exchange_func( $atts )
{

	//The shortcode attributes need to come before the button as they determine the button value

	$atts = shortcode_atts(
		array(
				'firstid' => '0',
				'secondid' => '0',
				'outputid' => '0',
				'firstamount' => '0',
				'secondamount' => '0',
				'outputamount' => '0',
        'refer' => 0,
				'days' => '0',
				'hours' => '0',
				'minutes' => '0',
        'symbol' => '',
        'amount' => 0,
        'from_user_id' => 0,
        'to_user_id' => 0,
        'fee' => 0,
        'comment' => '',
        'skip_confirm' => true,
        'mobile' => TRUE,
        'woowallet' => false,
        'mycred' => false,
        'mycred_reason' => 'VYPS Transfer',
        'gamipress' => false,
        'gamipress_reason' => 'VYPS Transfer',
        'transfer' => false,
        'vyps_meta_id' => '',
        'reason' => 'Point Exchange',
        'auto' => FALSE,
		), $atts, 'vyps-pe' );

  //Check if user is logged in and stop the code.
	//NOTE:I moved this here. I realized, its more likely that 10,000 users are bashing site mem while the admin is almost always logged in.
	if ( !is_user_logged_in() )
  {
		return; //You get nothing. Use the LG code.
	}

	$user_id = get_current_user_id(); //This needs to go here as we are checking the time way before then.
	$firstPointID = $atts['firstid'];
	$secondPointID = $atts['secondid'];
	$destinationPointID = $atts['outputid'];
	$pt_fAmount = $atts['firstamount']; //I'm going to be the first to say, I'm am not proud of my naming conventions. Gods know if I ever get amnesia and have to fix my own code, I will be highly displeased. -Felty
	$pt_sAmount = $atts['secondamount']; //f = first and s = second, notice how i reused some of the old variables for new. Not really intentional nor well executed.
	$pt_dAmount = $atts['outputamount']; //desintation amount.
  $vyps_reason = $atts['reason']; //PE reason TODO fix later
  $refer_rate = intval($atts['refer']); //Yeah I intvaled it immediatly. No wire decimals!
  $user_transfer_state = $atts['transfer']; //NOTE: I was going to rewrite whole system to do user to user tranfer and it occurred to me could just put it here

  //WooWallet Check
  $woowallet_mode = $atts['woowallet'];

  $destIcon_output = $destIcon;

	$vyps_mmo_ajax = '<script src="'.$vy256_solver_js_url.'"></script>';

	$mmo_wc_table_html_output = '
		<tr><!-- First input -->
			<td><div align="center">Spend</div></td>
			<td><div align="center">'.$f_sourceIcon.' '.$format_pt_fAmount.'</div></td>
			<td>
				<div align="center">
					<b><form method="post">
						<input type="hidden" value="" name="'.$vyps_meta_id.'"/>
						<input type="submit" class="button-secondary" value="Transfer" onclick="return confirm(\'You are about to transfer '.$format_pt_fAmount.' '.$f_sourceName. ' for '.$format_pt_dAmount.' '.$destName.'. Are you sure?\');" />
					</form></b>
				</div>
			</td>
			<td><div align="center">'.$destIcon_output.' '.$format_pt_dAmount.'</div></td>
			<td><div align=\"center\">Receive</div></td>
		</tr>
		';

    $table_close_output_html = '
          <tr>
            <td colspan = 2><div align="center"><b>.'$results_message.'</b></div></td>
          </tr>';
  } //End of mobile view check.
	//Lets see if it works:

	$mmo_pe_html_output = $vyps_mmo_ajax . '<table id="'.$vyps_meta_id.'">' . $table_result_ouput . $table_close_output_html . '</table>';
	return $mmo_pe_html_output;
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-wc-mmo', 'vyps_wc_mmo_point_exchange_func');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */
