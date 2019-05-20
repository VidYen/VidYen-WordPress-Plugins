<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
** I have decided to move this main menu to folder in save top directory.
**  Technically its not includes, but its not that hard to find.
*/

//Adding menus.
add_action('admin_menu', 'vidyen_rts_sub_menu', 640 );

//Sub menu. Adding it to the VYPS system.

function vidyen_rts_sub_menu()
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "VidYen RTS";
  $menu_title = 'RTS Menu';
	$capability = 'manage_options';
  $menu_slug = 'vyps_rts_page';
  $function = 'vidyen_rts_sub_menu_page';

  add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}

//The actual menu
function vidyen_rts_sub_menu_page()
{
	global $wpdb;

	if (isset($_POST['currency_id']))
	{
		//As the post is the only thing that edits data, I suppose this is the best place to the noce
		$vyps_nonce_check = $_POST['vypsnoncepost'];
		if ( ! wp_verify_nonce( $vyps_nonce_check, 'vyps-rts-nonce' ) )
    {
				// This nonce is not valid.
				die( 'Security check' );
		}

		//IDs in
		$currency_id = abs(intval($_POST['currency_id'])); //Even though I am in the believe if an admin sql injects himself, we got bigger issues, but this has been sanitized.
		$wood_id = abs(intval($_POST['wood_id']));
		$iron_id = abs(intval($_POST['iron_id']));
		$stone_id = abs(intval($_POST['stone_id']));
		$light_soldier_id = abs(intval($_POST['light_soldier_id']));
		$village_id = abs(intval($_POST['village_id']));
		$castle_id = abs(intval($_POST['castle_id']));

    $table_name_rts = $wpdb->prefix . 'vidyen_rts';

	    $data = [
	        'currency_id' => $currency_id,
	        'wood_id' => $wood_id,
					'iron_id' => $iron_id,
					'stone_id' => $stone_id,
					'light_soldier_id' => $light_soldier_id,
					'castle_id' => $castle_id,
	    ];

			$wpdb->update($table_name_rts, $data, ['id' => 1]);
	    //$data_id = $wpdb->update($table_name_rts , $data);

	    //I forget thow this works
	    $message = "Added successfully.";
	}

  //Repulls from SQL
	//Currency ID
	$currency_id = intval(vyps_rts_sql_currency_id_func());

	//Wood Id
	$wood_id = intval(vyps_rts_sql_wood_id_func());

  //Iron Id
  $iron_id = intval(vyps_rts_sql_iron_id_func());

	//Stone ID
	$stone_id = intval(vyps_rts_sql_stone_id_func());

	//Light Soldier Id (yes I don't have knights yet but plannged so I keep variables ready)
	$light_soldier_id = intval(vyps_rts_sql_light_soldier_id_func());

	//Village ID
	$village_id = intval(vyps_rts_sql_village_id_func());

	//Castle ID
	$castle_id = intval(vyps_rts_sql_castle_id_func());


	//It's possible we don't use the VYPS logo since no points.
  $vyps_logo_url = plugins_url( 'includes/images/logo.png', __FILE__ );
	$vidyen_rts_logo_url = plugins_url( 'includes/images/vyvp-logo.png', __FILE__ );

	//Adding a nonce to the post
	$vyps_nonce_check = wp_create_nonce( 'vyps-rts-nonce' );

  //Save for later //<img src="' . $vidyen_rts_logo_url . '">

	//Static text for the base plugin
	$vyps_rts_menu_html_ouput ='<br><br>
	<h1>VidYen WC RTS Sub-Plugin</h1>
	<p>Point IDs to Game Values</p>
	<table>
		<form method="post">
			<tr>
				<td>Currency Point ID</td>
				<td><input type="number" name="currency_id" type="number" id="currency_id" min="1" step="1" value="'. $currency_id.'" required="true">
				<input type="hidden" name="vypsnoncepost" id="vypsnoncepost" value="'.$vyps_nonce_check.'"/></td>
				<td>'.vyps_point_icon_func($currency_id).'</td>
			</tr>
			<tr>
				<td>Wood Point ID</td>
				<td><input type="number" name="wood_id" type="number" id="wood_id" min="1" step="1" value="'.$wood_id.'" required="true"></td>
				<td>'.vyps_point_icon_func($wood_id).'</td>
			</tr>
			<tr>
				<td>Iron Point ID</td>
				<td><input type="number" name="iron_id" type="number" id="iron_id" min="1" step="1" value="'.$iron_id.'" required="true"></td>
				<td>'.vyps_point_icon_func($iron_id).'</td>
			</tr>
			<tr>
				<td>Stone Point ID</td>
				<td><input type="number" name="stone_id" type="number" id="stone_id" min="1" step="1" value="'.$stone_id.'" required="true"></td>
				<td>'.vyps_point_icon_func($stone_id).'</td>
			</tr>
			<tr>
				<td>Light Soldier ID</td>
				<td><input type="number" name="light_soldier_id" type="number" id="light_soldier_id" min="1" step="1" value="'.$light_soldier_id.'" required="true"></td>
				<td>'.vyps_point_icon_func($light_soldier_id).'</td>
			</tr>
			<tr>
				<td>Village Point ID</td>
				<td><input type="number" name="village_id" type="number" id="village_id" min="1" step="1" value="'.$village_id.'" required="true"></td>
				<td>'.vyps_point_icon_func($village_id).'</td>
			</tr>
			<tr>
				<td>Castle ID</td>
				<td><input type="number" name="castle_id" type="number" id="castle_id" min="1" step="1" value="'.$castle_id.'" required="true"></td>
				<td>'.vyps_point_icon_func($castle_id).'</td>
			</tr>
			<tr>
				<td colspan="3"><input type="submit" value="Submit"></td>
			</tr>
		</form>
	</table>';
	/*
	<h2>Shortcode</h2>
	<p><b>[vidyen-rts-bal]</b> for live balance.</p>
	<p><b>[vyps-rts-pe]</b> for live point exchange.</p>
	<p><b>[vidyen-rts-deduct currency_id=2 apikey=(set here on in RTS menu)]</b> This is a postback page. If you do not remember how to do the post back page watch the Wannads tutorial video in full.</p>
	<p><b>[vidyen-rts-credit currency_id=2 apikey=(set here on in RTS menu)]</b> Same as above but does the credit when you want to talk currency off server and into site.</p>
	<p><b>[vidyen-rts-api-bal mode=GET gui=TRUE currency_id=7]</b> This is a postback page for external curls. If you do not remember how to do the post back page watch the Wannads tutorial video in full.</p>
	<p><b>[vidyen-rts-register apikey=test]</b> This is for your registration curl. Only enter your apikey where test is written.</p>
	<p><b>[vidyen-loa-id]</b> This is for your LoA userid box. It shows each user their currently stored LoA userid and lets them clear it if its incorrect. If edit=TRUE you are able to edit this directly from the website.</p>
	<p>Simply put the shortcodes on a page and let it run with the point id from the VidYen point system.</p>
	<p>Point ID is the point ID from the VidYen System. Found in Manage Points section of VYPS</p>
	<p>NOTE: If you change this settings while a game is in play, they must close browser or tab and reload page as is server session based.</p>
	<p>Requires the <a href="https://wordpress.org/plugins/vidyen-point-system-vyps/" target="_blank">VidYen Point System</a></p>
	<br><br><a href="https://wordpress.org/plugins/vidyen-point-system-vyps/" target="_blank"><img src="' . $vyps_logo_url . '"></a>
	';*/

  echo $vyps_rts_menu_html_ouput;
}
