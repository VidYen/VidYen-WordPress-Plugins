<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Copy of the rts-recruit but although I was going to not use loa it dawned on me this will only work with loa so there.
//You might be able to mod it tho? *shrugs* -Signed Felty

function vidyen_rts_loa_recruit_func()
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
	$laborer_point_id = vyps_rts_sql_laborer_id_func();

	//Buildings
	$village_id = vyps_rts_sql_village_id_func();
	$castle_id = vyps_rts_sql_castle_id_func();

	//The icons for loot
	$currency_icon = vyps_point_icon_func($currency_point_id);
	$wood_icon = vyps_point_icon_func($wood_point_id);
	$iron_icon = vyps_point_icon_func($iron_point_id);
	$stone_icon = vyps_point_icon_func($stone_point_id);
	$laborer_icon = vyps_point_icon_func($laborer_point_id);

	//Icons for units
	$laborer_large_icon = vidyen_rts_unit_icon_func($laborer_point_id);

	//Building Icons
	$village_icon = vidyen_rts_building_icon_func($village_id);
	$castle_icon = vidyen_rts_building_icon_func($castle_id);

	//Get the url for the solver
	$rts_ajax_js_url = plugins_url( 'js/recruit/rts_recruit.js', dirname(__FILE__) );
	$rts_ajax_timer_js_url = plugins_url( 'js/recruit/rts_recruit_timer.js', dirname(__FILE__) );

	//Should be a global, but have this set multiple plasces
	$mission_id = 'recruitLaborers05'; //five minute village sack
	$mission_time = 300; //5 minutes
	$reason = 'Hire laborers.';
	$vyps_meta_id = ''; //I can't think what to use here.

	$current_mission_time = vidyen_rts_check_mission_time_func($user_id, $mission_id, $mission_time, $game_id);

	$mission_html_output = ''; //Starter

	//Village Sack code. Since we aren't using the WP jquery we have to import it first
	$mission_html_output .=
		'<table width="100%">
			<tr>
				<th>'.$laborer_large_icon.' Recruit laborers from local village. '.$village_icon.'</th>
			</tr>
			<tr>
				<td><div style="font-size: 21px;"><span style="vertical-align: bottom;">Mission Requirements: </span><span style="vertical-align: top;">'.$currency_icon.'</span> <span id="money_required" style="vertical-align: bottom;">1000</span></div></td>
			</tr>
			<tr>
				<td>
					<div align="center">
						<input  class="button" id="recruit_laborers_button" type="button" value="Speak to village elder!" onclick="rts_recruit_laborers()" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div id="mission_output" align="center">
					You seek out the village elder searching for those who wish to work.
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div id="recruit_output" align="center">
						You have not made your offer yet.
					</div>
				</td>
			</tr>
			<tr>
				<td>
				<div id="recruitLaborersTimerBar" style="position:relative; width:100%; background-color: grey; ">
          <div id="recruitLaborersCoolDownTimer" style="width:100%; height: 30px; background-color: #b30b00;">
						<div id="recruit_laborers_countdown_time_left" style="position: absolute; right:12%; color:white; font-size:1.25vw;"></div><div style="text-align: right;">'.$laborer_icon.'</div>
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
					<th>'.$laborer_large_icon.' Send Soldiers to raid nearby Castle. '.$castle_icon.'</th>
				</tr>
				<tr>
					<td><div style="font-size: 21px;"><span style="vertical-align: bottom;">Mission Requirements: </span><span style="vertical-align: top;">'.$laborer_icon.'</span> <span id="soldiers_required" style="vertical-align: bottom;">200</span></div></td>
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
							<div id="countdown_time_left" style="position: absolute; right:12%; color:white;"></div><div style="text-align: right;">'.$laborer_icon.'</div>
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
														var	laborer_icon = '$laborer_icon';
														var rts_recruit_time_left = $current_mission_time;
														var recruit_laborers_timer_check = 0;
														var user_id = '$game_id';
														if (rts_recruit_time_left > 0)
														{
															recruit_laborers_time_left();
														}
													</script>";

		$mission_html_output .= '<script type="text/javascript">
																var ajaxurl = "' . admin_url('admin-ajax.php') . '";
														</script>';

	return $mission_html_output;
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vidyen-rts-loa-recruit', 'vidyen_rts_loa_recruit_func');

/* Ok after much deliberation, I decided I want the WW plugin to go into the pt since it has become the exchange */
/* If you don't have WW, it won't kill anything if you don't call it */
