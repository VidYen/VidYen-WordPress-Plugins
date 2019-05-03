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
	$page_title = "VidYen MMO";
  $menu_title = 'MMO Menu';
	$capability = 'manage_options';
  $menu_slug = 'vyps_wc_mmo_page';
  $function = 'vidyen_rts_sub_menu_page';

  add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}

//The actual menu
function vidyen_rts_sub_menu_page()
{
	global $wpdb;

	if (isset($_POST['point_id']))
	{
		//As the post is the only thing that edits data, I suppose this is the best place to the noce
		$vyps_nonce_check = $_POST['vypsnoncepost'];
		if ( ! wp_verify_nonce( $vyps_nonce_check, 'vyps-mmo-nonce' ) )
    {
				// This nonce is not valid.
				die( 'Security check' );
		}

		//ID Text value
		$point_id = abs(intval($_POST['point_id'])); //Even though I am in the believe if an admin sql injects himself, we got bigger issues, but this has been sanitized.

		//The icon. I'm suprised this works so well
		$point_amount = abs(intval($_POST['point_amount']));

		//The icon. I'm suprised this works so well
		$output_amount = abs(floatval($_POST['output_amount']));

		if($_POST['api_key'] == '' OR !isset($_POST['api_key']))
		{
			$api_key = sanitize_text_field(str_replace('-', '', implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6))));
		}
		else
		{
				$api_key = sanitize_text_field(($_POST['api_key']));
		}

    $table_name_wc_mmo = $wpdb->prefix . 'vidyen_rts';

	    $data = [
	        'point_id' => $point_id,
	        'point_amount' => $point_amount,
					'output_amount' => $output_amount,
					'api_key' => $api_key,
	    ];

			$wpdb->update($table_name_wc_mmo, $data, ['id' => 1]);
	    //$data_id = $wpdb->update($table_name_wc_mmo , $data);

	    //I forget thow this works
	    $message = "Added successfully.";
	}

  //Repulls from SQL
	//Input ID pull
	$point_id = intval(vyps_mmo_sql_point_id_func());

	//Input Amount
	$point_amount = intval(vyps_mmo_sql_point_amount_func());

  //Ouput id
  $output_id = intval(vyps_mmo_sql_output_id_func());

	//Ouput Amount
	$output_amount = floatval(vyps_mmo_sql_output_amount_func());

	$api_key = sanitize_text_field(vyps_mmo_sql_api_key_func());


	//It's possible we don't use the VYPS logo since no points.
  $vyps_logo_url = plugins_url( 'includes/images/logo.png', __FILE__ );
	$vidyen_rts_logo_url = plugins_url( 'includes/images/vyvp-logo.png', __FILE__ );

	//Adding a nonce to the post
	$vyps_nonce_check = wp_create_nonce( 'vyps-mmo-nonce' );

  //Save for later //<img src="' . $vidyen_rts_logo_url . '">

	//Static text for the base plugin
	$vyps_wc_mmo_menu_html_ouput ='<br><br>
	<h1>VidYen WC MMO Sub-Plugin</h1>
	<p>Exchange Rates</p>
	<table>
		<form method="post">
			<tr>
				<th>VidYen Point ID</th>
				<th>Input Amount</th>
        <th>WooWallet Icon</th>
				<th>WooWallet Amount</th>
				<th>API Key (To Reset Leave Blank)</th>
				<th>Submit</th>
			</tr>
			<tr>
				<td><input type="number" name="point_id" type="number" id="point_id" min="1" step="1" value="' . $point_id .  '" required="true">
				<input type="hidden" name="vypsnoncepost" id="vypsnoncepost" value="'. $vyps_nonce_check . '"/></td>
				<td><input type="number" name="point_amount" type="number" id="point_amount" min="1" max="1000000" step="1" value="' . $point_amount . '" required="true"></td>
        <td><input type="number" name="output_id" type="number" id="output_id" min="1" step="1" value="' . $output_id .  '" required="true"></td>
				<td><input type="number" name="output_amount" type="number" id="output_amount" min="0.0000001" max="1000000" step="0.0000001" value="' . $output_amount . '" required="true"></td>
				<td><input type="text" name="api_key"  id="api_key" value="' . $api_key . '"></td>
				<td><input type="submit" value="Submit"></td>
			</tr>
		</form>
	</table>
	<h2>API Key Copy and Paste</h2>
	<p>'.$api_key.'</p>
	<h2>Shortcode</h2>
	<p><b>[vidyen-rts-bal]</b> for live balance.</p>
	<p><b>[vyps-mmo-pe]</b> for live point exchange.</p>
	<p><b>[vidyen-rts-deduct point_id=2 apikey=(set here on in MMO menu)]</b> This is a postback page. If you do not remember how to do the post back page watch the Wannads tutorial video in full.</p>
	<p><b>[vidyen-rts-credit point_id=2 apikey=(set here on in MMO menu)]</b> Same as above but does the credit when you want to talk currency off server and into site.</p>
	<p><b>[vidyen-rts-api-bal mode=GET gui=TRUE point_id=7]</b> This is a postback page for external curls. If you do not remember how to do the post back page watch the Wannads tutorial video in full.</p>
	<p><b>[vidyen-rts-register apikey=test]</b> This is for your registration curl. Only enter your apikey where test is written.</p>
	<p><b>[vidyen-loa-id]</b> This is for your LoA userid box. It shows each user their currently stored LoA userid and lets them clear it if its incorrect. If edit=TRUE you are able to edit this directly from the website.</p>
	<p>Simply put the shortcodes on a page and let it run with the point id from the VidYen point system.</p>
	<p>Point ID is the point ID from the VidYen System. Found in Manage Points section of VYPS</p>
	<p>NOTE: If you change this settings while a game is in play, they must close browser or tab and reload page as is server session based.</p>
	<p>Requires the <a href="https://wordpress.org/plugins/vidyen-point-system-vyps/" target="_blank">VidYen Point System</a></p>
	<br><br><a href="https://wordpress.org/plugins/vidyen-point-system-vyps/" target="_blank"><img src="' . $vyps_logo_url . '"></a>
	';

  echo $vyps_wc_mmo_menu_html_ouput;
}
