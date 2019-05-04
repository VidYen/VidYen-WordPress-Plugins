<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//I'm going to do this all in a slew of both the balance if ww or not
//Using Ajax will update autoamtically
//NOTE: I've decided to this this with just one point and the WooWallet output for simplicity. If people want more they can ask.

function vidyen_rts_mission_func()
{

	//Check if user is logged in and stop the code.
	//NOTE:I moved this here. I realized, its more likely that 10,000 users are bashing site mem while the admin is almost always logged in.
	if ( !is_user_logged_in() )
	{
		return; //You get nothing. Use the LG code.
	}

	//NOTE: Guess what. We pull for SQL instead of short code. This helps with the AJAX
	$div_id = $point_id.'vyrtsdiv';
	$user_id = get_current_user_id();

	//Currency will be listed as the poitn id
	$currency_point_id = intval(vyps_rts_sql_currency_id_func());

	//Resource IDs
	$currency_point_id = vyps_rts_sql_currency_id_func();
	$wood_point_id = vyps_rts_sql_wood_id_func();
	$iron_point_id = vyps_rts_sql_iron_id_func();
	$stone_point_id = vyps_rts_sql_stone_id_func();

	//Military
	$light_solider_point_id = vyps_rts_sql_light_soldier_id_func();

	//The icons for loot
	$currency_icon = vyps_point_icon_func($currency_point_id);
	$wood_icon = vyps_point_icon_func($wood_point_id);
	$iron_icon = vyps_point_icon_func($iron_point_id);
	$stone_icon = vyps_point_icon_func($stone_point_id);
	$light_soldier_icon = vyps_point_icon_func($light_solider_point_id);

	//Get the url for the solver
	$rts_ajax_js_url = plugins_url( 'js/rts_missions.js', dirname(__FILE__) );

	//Concatenate the values into the output
	$mission_html_output =
		'<table>
			<tr>
				<th>Send Soldiers to raid poor peasant village for loot.</th>
			</tr>
			<tr>
				<td><div style="font-size: 21px;"><span style="vertical-align: bottom;">Soldiers Required:</span><span style="vertical-align: top;">'.$light_soldier_icon.'</span> <span id="soldiers_required" style="vertical-align: bottom;">20</span></div></td>
			</tr>
			<tr>
				<td>
					<div align="center">
						<input  class="button" id="sack_button" type="button" value="Tally hoe!" onclick="rts_sack_village()" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div id="mission_output" align="center">
					Your soliders wait for your command.
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div id="loot_output" align="center">
						You have no loot yet.
					</div>
				</td>
			</tr>
		</table>';


	$mission_html_output .= '<script src="'.$rts_ajax_js_url.'"></script>';

	//adding the icon urls to the js mix
	$mission_html_output .= "<script>
														var currency_icon = '$currency_icon';
														var	wood_icon = '$wood_icon';
														var iron_icon = '$iron_icon';
														var	stone_icon = '$stone_icon';
														var	light_soldier_icon = '$light_soldier_icon';
													</script>";

	return $mission_html_output;
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vidyen-rts-missions', 'vidyen_rts_mission_func');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */
