<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
** I have decided to move this main menu to folder in save top directory.
**  Technically its not includes, but its not that hard to find.
*/

//Adding menus.
add_action('admin_menu', 'vidyen_wc_mmo_sub_menu', 600 );

//Sub menu. Adding it to the VYPS system.

function vidyen_wc_mmo_sub_menu()
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "VidYen WooCommerce MMO";
  $menu_title = 'WooCommerce MMO';
	$capability = 'manage_options';
  $menu_slug = 'vyps_wc_mmo_page';
  $function = 'vidyen_wc_mmo_sub_menu_page';

  add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}

//The actual menu
function vidyen_wc_mmo_sub_menu_page()
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

    $table_name_wc_mmo = $wpdb->prefix . 'vidyen_wc_mmo';

	    $data = [
	        'point_id' => $point_id,
	        'point_amount' => $point_amount,
					'output_amount' => $output_amount,
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


	//It's possible we don't use the VYPS logo since no points.
  $vyps_logo_url = plugins_url( 'includes/images/logo.png', __FILE__ );
	$vidyen_wc_mmo_logo_url = plugins_url( 'includes/images/vyvp-logo.png', __FILE__ );

	//Adding a nonce to the post
	$vyps_nonce_check = wp_create_nonce( 'vyps-mmo-nonce' );

  //Save for later //<img src="' . $vidyen_wc_mmo_logo_url . '">

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
				<th>Submit</th>
			</tr>
			<tr>
				<td><input type="number" name="point_id" type="number" id="point_id" min="1" step="1" value="' . $point_id .  '" required="true">
				<input type="hidden" name="vypsnoncepost" id="vypsnoncepost" value="'. $vyps_nonce_check . '"/></td>
				<td><input type="number" name="point_amount" type="number" id="point_amount" min="1" max="1000000" step="1" value="' . $point_amount . '" required="true"></td>
        <td><input type="number" name="output_id" type="number" id="output_id" min="1" step="1" value="' . $output_id .  '" required="true"></td>
				<td><input type="number" name="output_amount" type="number" id="output_amount" min="0.0000001" max="1000000" step="0.0000001" value="' . $output_amount . '" required="true"></td>
				<td><input type="submit" value="Submit"></td>
			</tr>
		</form>
	</table>
	<h2>Shortcode</h2>
	<p><b>[vidyen-wc-mmo]</b></p>
	<p>Simply put the shortcode <b>[vidyen-wc-mmo]</b> on a page and let it run with the point id from the VidYen point system.</p>
	<p>Point ID is the point ID from the VidYen System. Found in Manage Points section of VYPS</p>
	<p>Max bet is how much you want to let them bet in a single hand. Requires session refresh.</p>
	<p>Win Multi is if you want to increase rewards with 2 for 2x the winnings.</p>
	<p>NOTE: If you change this settings while a game is in play, they must close browser or tab and reload page as is server session based.</p>
	<p>Requires the <a href="https://wordpress.org/plugins/vidyen-point-system-vyps/" target="_blank">VidYen Point System</a> for any point record keeping.</p>
	<br><br><a href="https://wordpress.org/plugins/vidyen-point-system-vyps/" target="_blank"><img src="' . $vyps_logo_url . '"></a>
	';

  echo $vyps_wc_mmo_menu_html_ouput;
}
