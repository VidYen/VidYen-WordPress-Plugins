<?php
 /*
Plugin Name:  VidYen MMO Plugin
Plugin URI:   https://wordpress.org/plugins/vidyen-point-system-vyps/
Description:  MMO Support Plugin
Version:      0.1.00
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
function vidyen_wc_mmo_sql_install()
{
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
	output_amount decimal(32,8) NOT NULL,
  api_key varchar(128) NOT NULL,
	PRIMARY KEY  (id)
      ) {$charset_collate};";

  require_once (ABSPATH . 'wp-admin/includes/upgrade.php'); //I never did investigate why the original outsource dev used this.

  dbDelta($sql);

  //create random api_key. Shall be santizied.

  $key = sanitize_text_field(str_replace('-', '', implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6))));

	//Default data
	$data_insert = [
			'point_id' => 1,
			'point_amount' => 100,
			'output_id' => 2,
			'output_amount' => 1,
      'api_key' => $key,
	];

	$wpdb->insert($table_name_wc_mmo, $data_insert);
}

/*** Includes ***/
/*** Functions ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_woowallet_currency.php'); //Custom Currencies to WooCommerce
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_sql_call_func.php'); //SQL Call functions
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_mmo_wc_ww_bal_func.php'); //Custom WooWallet balance function for this purpose


/*** Shortcodes ***/
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-wc-ajax-bal.php'); //Ajax Balance
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-wc-mmo-point-exchange.php'); //Ajax Point Exchange
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-mmo-postback.php'); //Post back for game transfers
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-mmo-api-bal.php'); //Post back for game transfers

/*** Menu Includes ***/
include( plugin_dir_path( __FILE__ ) . 'vidyen-wc-mmo-menu.php'); //Order 600

/*** AJAX ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ajax/vyps_mmo_bal_ajax.php');
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ajax/vyps_mmo_exchange_ajax.php');

/*** Templater ***/
include( plugin_dir_path( __FILE__ ) . 'vidyen-wc-mmo-template-function.php'); //Order 600
