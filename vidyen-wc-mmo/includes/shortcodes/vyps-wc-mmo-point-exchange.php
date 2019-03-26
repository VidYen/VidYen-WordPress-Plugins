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

function vyps_wc_mmo_point_exchange_func()
{
  //Check if user is logged in and stop the code.
	//NOTE:I moved this here. I realized, its more likely that 10,000 users are bashing site mem while the admin is almost always logged in.
	if ( !is_user_logged_in() )
  {
		return; //You get nothing. Use the LG code.
	}

	$point_id = intval(vyps_mmo_sql_point_id_func());
	$point_amount = intval(vyps_mmo_sql_point_amount_func());
	$output_id = intval(vyps_mmo_sql_output_id_func());
	$output_amount = intval(vyps_mmo_sql_output_id_func());
	$div_id = $point_id.'vymmodiv';
	$user_id = get_current_user_id();
	$vyps_points_output = number_format(vyps_point_balance_func($point_id, $user_id), 0);
	$source_icon = vyps_point_icon_func($point_id);
	$point_id = $output_id;
	$output_icon = vyps_point_icon_func($point_id);

	//Get the url for the js
	$mmo_ajax_js_url = plugins_url( 'js/mmo_exchange.js', dirname(__FILE__) );

	$mmo_wc_exchange_table_html_output = '<table>';
	$mmo_wc_exchange_table_html_output .=
	'<tr><!-- Input -->
			<td><div align="center">Spend:</div></td>
			<td><div align="center">'.$source_icon.' '.$point_amount.'</div></td>
		</tr>
		<tr><!-- Output -->
			<td><div align="center">Receive:</div></td>
			<td><div align="center">'.$output_icon.' '.$output_amount.'</div></td>
		</tr>
		<tr>
			<td colspan = 2>
				<div align="center">
					<form style="display:block;width:100%;"><input type="reset" style="width:100%;" onclick="vidyen_mmo_exchange()" value="Exchange"/></form>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan = 2><div id="exchange_results" align="center">Press Exchange to Trade</div></td>
  	</tr>';
		$mmo_wc_exchange_table_html_output .= '</table>';
		$mmo_wc_exchange_table_html_output .= '<script src="'.$mmo_ajax_js_url.'"></script>';

	return $mmo_wc_exchange_table_html_output;
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-wc-mmo', 'vyps_wc_mmo_point_exchange_func');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */
