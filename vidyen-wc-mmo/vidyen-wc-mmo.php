<?php
 /*
Plugin Name:  VYPS WooCommerce MMO Plugin
Plugin URI:   https://wordpress.org/plugins/vidyen-point-system-vyps/
Description:  Adds RPG like currencies to WooCommerce for VYPS
Version:      0.0.15
Author:       VidYen, LLC
Author URI:   https://vidyen.com/
License:      GPLv2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

/*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, version 2 of the License
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* See <http://www.gnu.org/licenses/>.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

register_activation_hook(__FILE__, 'vidyen_wc_mmo_sql_install');

//Install the SQL tables for VYPS.
function vidyen_wc_mmo_sql_install() {

    global $wpdb;

		//I have no clue why this is needed. I should learn, but I wasn't the original author. -Felty
		$charset_collate = $wpdb->get_charset_collate();

		//NOTE: I have the mind to make mediumint to int, but I wonder if you get 8 million log transactios that you should consider another solution than VYPS.

		//vidyen_wc_mmo table creation
    $table_name_wc_mmo = $wpdb->prefix . 'vidyen_wc_mmo';

    $sql = "CREATE TABLE {$table_name_wc_mmo} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		point_id mediumint(9) NOT NULL,
		point_amount mediumint(9) NOT NULL,
		output_id mediumint(9) NOT NULL,
		output_amount mediumint(9) NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";

    require_once (ABSPATH . 'wp-admin/includes/upgrade.php'); //I never did investigate why the original outsource dev used this.

    dbDelta($sql);

		//Default data
		$data_insert = [
				'point_id' => 1,
				'point_amount' => 100,
				'output_id' => 2,
				'output_amount' => 1,
		];

		$wpdb->insert($table_name_wc_mmo, $data_insert);
}

//adding menues
add_action('admin_menu', 'vidyen_wc_mmo_menu');

function vidyen_wc_mmo_menu()
{
    $parent_page_title = "VidYen WC MMO";
    $parent_menu_title = 'VY WC MMO';
    $capability = 'manage_options';
    $parent_menu_slug = 'vidyen_wc_mmo';
    $parent_function = 'vidyen_wc_mmo_menu_page';
    add_menu_page($parent_page_title, $parent_menu_title, $capability, $parent_menu_slug, $parent_function);
}

//The actual menu
function vidyen_wc_mmo_menu_page()
{
	global $wpdb;

	if (isset($_POST['point_id']))
	{
		//As the post is the only thing that edits data, I suppose this is the best place to the noce
		$vyps_nonce_check = $_POST['vypsnoncepost'];
		if ( ! wp_verify_nonce( $vyps_nonce_check, 'vyps-nonce' ) ) {
				// This nonce is not valid.
				die( 'Security check' );
		}

    echo '<br><br>Post was set!<br><br>';

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

	//the $wpdb stuff to find what the current name and icons are
	$table_name_wc_mmo = $wpdb->prefix . 'vidyen_wc_mmo';
	$first_row = 1; //Note sure why I'm setting this.

	//Input ID pull
	$point_id = vyps_mmo_sql_point_id_func();

	//Input Amount
	$point_amount = vyps_mmo_sql_point_amount_func();

	//Ouput Amount
	$output_amount = vyps_mmo_sql_output_amount_func();

  /* I suspect something is goin gon here
	//Just setting to 1 if nothing else I suppose, but should always be something
	if ($point_id == '')
	{
		$point_id = 1;
	}

	//Just setting to 1 if nothing else I suppose, but should always be something
	if ($point_amount == '')
	{
		$point_amount = 1;
	}

	//Just setting to 1 if nothing else I suppose, but should always be something
	if ($output_amount == '')
	{
		$output_amount = 1;
	}

  */

	//It's possible we don't use the VYPS logo since no points.
  $vyps_logo_url = plugins_url( 'includes/images/logo.png', __FILE__ );
	$vidyen_wc_mmo_logo_url = plugins_url( 'includes/images/vyvp-logo.png', __FILE__ );

	//Adding a nonce to the post
	$vyps_nonce_check = wp_create_nonce( 'vyps-nonce' );

  //Save for later //<img src="' . $vidyen_wc_mmo_logo_url . '">

	//Static text for the base plugin
	echo
	'<br><br>
	<h1>VidYen WC MMO Sub-Plugin</h1>
	<p>Exchange Rates</p>
	<table>
		<form method="post">
			<tr>
				<th>VidYen Point ID</th>
				<th>Input Amount</th>
				<th>WooWallet Amount</th>
				<th>Submit</th>
			</tr>
			<tr>
				<td><input type="number" name="point_id" type="number" id="point_id" min="1" step="1" value="' . $point_id .  '" required="true">
				<input type="hidden" name="vypsnoncepost" id="vypsnoncepost" value="'. $vyps_nonce_check . '"/></td>
				<td><input type="number" name="point_amount" type="number" id="point_amount" min="1" max="1000000" step="1" value="' . $point_amount . '" required="true"></td>
				<td><input type="number" name="output_amount" type="number" id="output_amount" min="1" max="1000000" step="1" value="' . $output_amount . '" required="true"></td>
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
}

/*** Includes ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_woowallet_currency.php'); //Custom Currencies to WooCommerce
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_sql_call_func.php'); //Custom Currencies to WooCommerce

/*** Shortcodes ***/
//include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-wc-mmo-point-exchange.php'); //Point Exchange
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-wc-ww-bal.php'); //Point Exchange
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-wc-ajax-bal.php'); //Point Exchange

/*** Menu Includes ***/
//NOTE: Note really needed //include( plugin_dir_path( __FILE__ ) . 'includes/menus/adgate-menu.php'); //Order 450 (residual from the extraction for core VYPS)

/*** AJAX ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ajax/vyps_mmo_bal_ajax.php');
