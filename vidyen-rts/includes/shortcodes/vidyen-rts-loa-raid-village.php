<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//I'm going to do this all in a slew of both the balance if ww or not
//Using Ajax will update autoamtically
//NOTE: I've decided to this this with just one point and the WooWallet output for simplicity. If people want more they can ask.

function vidyen_rts_loa_raid_village()
{
	//NOTE: Guess what. We pull for SQL instead of short code. This helps with the AJAX
	$div_id = $point_id.'vyrtsdiv';

	//NOTE: needs to replace  get_current_user_id()
	//Also NOTE game_id is not user id. Dumb $WPDB
	if(isset($_GET['user_id'])) //AND $_GET('get_key')
	{
		$game_id = sanitize_text_field(htmlspecialchars($_GET['user_id']));
		$user_id = 0; //Represents no user

		//$get_key = sanitize_text_field(htmlspecialchars($_GET['get_key']));

		//NOTE: I've changed my mind about the get key security.
		/*
		if($get_key != 'csod6132019')
		{
			return; //small security check. Will fix later. Basically people be playing game for you is the worst could happen.
		}
		*/
	}
	else
	{
		return; //You get nothing. Otherwise, your mining for no reward.
	}

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

	//Get the url for the solver
	$rts_ajax_js_url = plugins_url( 'js/raid/rts_loa_raid_village.js', dirname(__FILE__) );
	$rts_ajax_timer_js_url = plugins_url( 'js/raid/rts_mission_timer.js', dirname(__FILE__) );

	//Should be a global, but have this set multiple plasces
	$mission_id = 'sackvillage03'; //five minute village sack
	$mission_time = 180; //5 minutes
	$reason = 'Sack the village!';
	$vyps_meta_id = ''; //I can't think what to use here.

	$current_mission_time = vidyen_rts_check_mission_time_func($user_id, $mission_id, $mission_time, $game_id);

	$mission_html_output = ''; //Starter

	$mission_html_output .= '
	<!DOCTYPE html>
	<html>
	<head>
	<link href="https://fonts.googleapis.com/css?family=Spectral+SC&display=swap" rel="stylesheet">
	<title>CSoD LoA Balance</title>
	<style>
	html, body {
		font-family: "Spectral SC", serif;
	}
	</style>
	</head>
	';

	//NOTE "Spectral SC" makes in all caps.

	//Village Sack code
	$mission_html_output .=
		'<table width="100%" style="color: yellow;">
			<tr>
				<th>'.$light_soldier_large_icon.' <span id="village_fine" style="display:block;">'.$village_icon.'</span><span id="village_burning" style="display:none;">'.$village_burning_icon.'</span></th>
			</tr>
			<tr>
				<td><div style="font-size: 21px;"><span style="vertical-align: bottom;">Mission Requirements: </span><span style="vertical-align: top;">'.$light_soldier_icon.'</span> <span id="soldiers_required" style="vertical-align: bottom;">20</span></div></td>
			</tr>
			<tr>
				<td>
					<div align="center">
						<input  class="button" id="sack_button" type="button" value="Attack!" onclick="rts_loa_raid_village()" />
					</div>
				</td>
			</tr>
		</table>
		<table width="100%" style="color: black; background-color: white; border-style: solid; border: 1px; border-color: black;">
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
						<div id="countdown_time_left" style="position: absolute; right:12%; color:white;"></div><div style="text-align: right;">'.$light_soldier_icon.'</div>
					</div>
        </div>
				</td>
			</tr>
		</table>';

	$mission_html_output .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>';
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
														var user_id = '$game_id';
														if (rts_sack_time_left > 0)
														{
															sack_village_time_left();
														}
													</script>";

	$mission_html_output .= '<script type="text/javascript">
															var ajaxurl = "' . admin_url('admin-ajax.php') . '";
													</script>';

	//NOTE: Autorun the script and loop every 300 seconds
	$mission_html_output .= '<script>
														rts_loa_raid_village();
														setInterval(rts_loa_raid_village, 186000);
													</script>';

	//closing.
	$mission_html_output .= '</body>
													</html>';

	return $mission_html_output;
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vidyen-rts-loa-raid', 'vidyen_rts_loa_raid_village');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */
