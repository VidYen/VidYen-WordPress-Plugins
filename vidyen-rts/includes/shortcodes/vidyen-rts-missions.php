<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//I'm going to do this all in a slew of both the balance if ww or not
//Using Ajax will update autoamtically
//NOTE: I've decided to this this with just one point and the WooWallet output for simplicity. If people want more they can ask.

function vidyen_rts_mission_func()
{
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

	//Buildings
	$village_id = vyps_rts_sql_village_id_func();
	$castle_id = vyps_rts_sql_castle_id_func();
	$village_burning_id = vyps_rts_sql_village_burning_id_func();

	//The icons for loot
	$currency_icon = vyps_point_icon_func($currency_point_id);
	$wood_icon = vyps_point_icon_func($wood_point_id);
	$iron_icon = vyps_point_icon_func($iron_point_id);
	$stone_icon = vyps_point_icon_func($stone_point_id);
	$light_soldier_icon = vyps_point_icon_func($light_solider_point_id);

	//Icons for units
	$light_soldier_large_icon = vidyen_rts_unit_icon_func($light_solider_point_id);

	//Building Icons
	$village_icon = vidyen_rts_building_icon_func($village_id);
	$village_burning_icon = vidyen_rts_building_icon_func($village_burning_id);
	$castle_icon = vidyen_rts_building_icon_func($castle_id);

	//Check if user is logged in and stop the code.
	//NOTE:I moved this here. I wanted people to see some of the game even if they are logged out.
	if (!is_user_logged_in())
	{
		$site_path = '/login' . '/';
		$site_url = get_site_url() . $site_path;

		//Village sack
		$mission_html_output =
			'<table width="100%">
				<tr>
					<th>'.$light_soldier_large_icon.' Please login to play the raiding missions.'.$village_icon.'</th>
				</tr>
				<tr>
					<td><div style="font-size: 21px;"><span style="vertical-align: bottom;">Mission Requirements: </span><span style="vertical-align: top;">'.$light_soldier_icon.'</span> <span id="soldiers_required" style="vertical-align: bottom;">20</span></div></td>
				</tr>
				<tr>
					<td>
						<div align="center">
							<input  class="button" id="sack_button" type="button" value="Login" onclick="location.href=\''.$site_url.'\'" />
						</div>
					</td>
				</tr>
			</table>';

		return $mission_html_output; //You get nothing. Use the LG code.
	}


	//Get the url for the solver
	$rts_ajax_js_url = plugins_url( 'js/raid/rts_missions.js', dirname(__FILE__) );
	$rts_ajax_timer_js_url = plugins_url( 'js/raid/rts_mission_timer.js', dirname(__FILE__) );

	//Should be a global, but have this set multiple plasces
	$mission_id = 'sackvillage03'; //five minute village sack
	$mission_time = 180; //5 minutes
	$reason = 'Sack the village!';
	$vyps_meta_id = ''; //I can't think what to use here.

	$current_mission_time = vidyen_rts_check_mission_time_func($user_id, $mission_id, $mission_time);

	$mission_html_output = ''; //Starter

	//Village Sack code
	$mission_html_output .=
		'<table width="100%">
			<tr>
				<th>'.$light_soldier_large_icon.' Send Soldiers to raid poor peasant village for loot. <span id="village_fine" style="display:block;">'.$village_icon.'</span><span id="village_burning" style="display:none;">'.$village_burning_icon.'</span></th>
			</tr>
			<tr>
				<td><div style="font-size: 21px;"><span style="vertical-align: bottom;">Mission Requirements: </span><span style="vertical-align: top;">'.$light_soldier_icon.'</span> <span id="soldiers_required" style="vertical-align: bottom;">20</span></div></td>
			</tr>
			<tr>
				<td>
					<div align="center">
						<input  class="button" id="sack_button" type="button" value="Attack!" onclick="rts_sack_village()" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div id="mission_output" align="center">
					Your soldiers wait for your command.
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
			<tr>
				<td>
				<div id="raidVillageTimerBar" style="position:relative; width:100%; background-color: grey; ">
          <div id="raidVillageCoolDownTimer" style="width:100%; height: 30px; background-color: #b30b00;">
						<div id="countdown_time_left" style="position: absolute; right:12%; color:white; font-size:2vw;"></div><div style="text-align: right;">'.$light_soldier_icon.'</div>
					</div>
        </div>
				</td>
			</tr>
		</table>';
/*
		//Spacing
		$mission_html_output .= '<br><br>';

		//Castle Siege code
		$mission_html_output .=
			'<table width="100%">
				<tr>
					<th>'.$light_soldier_large_icon.' Send Soldiers to raid nearby Castle. '.$castle_icon.'</th>
				</tr>
				<tr>
					<td><div style="font-size: 21px;"><span style="vertical-align: bottom;">Mission Requirements: </span><span style="vertical-align: top;">'.$light_soldier_icon.'</span> <span id="soldiers_required" style="vertical-align: bottom;">200</span></div></td>
				</tr>
				<tr>
					<td>
						<div align="center">
							<input  class="button" id="sack_button" type="button" value="Attack!" onclick="rts_siege_castle()" />
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div id="castle_mission_output" align="center">
						Your soldiers wait for your command.
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
				<tr>
					<td>
					<div id="siegeCastleTimerBar" style="position:relative; width:100%; background-color: grey; ">
						<div id="siegeCastleCoolDownTimer" style="width:100%; height: 30px; background-color: #b30b00;">
							<div id="countdown_time_left" style="position: absolute; right:12%; color:white;"></div><div style="text-align: right;">'.$light_soldier_icon.'</div>
						</div>
					</div>
					</td>
				</tr>
			</table>';
*/


	$mission_html_output .= '<script src="'.$rts_ajax_js_url.'"></script>';
	$mission_html_output .= '<script src="'.$rts_ajax_timer_js_url.'"></script>';

	//adding the icon urls to the js mix
	$mission_html_output .= "<script>
														var currency_icon = '$currency_icon';
														var	wood_icon = '$wood_icon';
														var iron_icon = '$iron_icon';
														var	stone_icon = '$stone_icon';
														var	light_soldier_icon = '$light_soldier_icon';
														var rts_sack_time_left = $current_mission_time;
														var pillage_timer_check = 0;
														if (rts_sack_time_left > 0)
														{
															sack_village_time_left();
														}
													</script>";

	return $mission_html_output;
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vidyen-rts-missions', 'vidyen_rts_mission_func');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */
