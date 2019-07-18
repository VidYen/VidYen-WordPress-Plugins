<?php
 /*
Plugin Name:  VidYen WooCommerce Currencies
Plugin URI:   https://wordpress.org/plugins/vidyen-point-system-vyps/
Description:  Adds Custom Currency to WooCommerce
Version:      0.0.17
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

register_activation_hook(__FILE__, 'vidyen_woocommerce_currencies_sql_install');

//Install the SQL tables for VYPS.
function vidyen_woocommerce_currencies_sql_install()
{
  global $wpdb;

	//I have no clue why this is needed. I should learn, but I wasn't the original author. -Felty
	$charset_collate = $wpdb->get_charset_collate();

	//NOTE: I have the mind to make mediumint to int, but I wonder if you get 8 million log transactios that you should consider another solution than VYPS.

	//vidyen_woocommerce_currencies table creation
  $table_name_woocommerce_currencies = $wpdb->prefix . 'vidyen_woocommerce_currencies';

  $sql = "CREATE TABLE {$table_name_woocommerce_currencies} (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
  currency_name varchar(128) NOT NULL,
  currency_symbol varchar(128) NOT NULL,
	PRIMARY KEY  (id)
      ) {$charset_collate};";

  require_once (ABSPATH . 'wp-admin/includes/upgrade.php'); //I never did investigate why the original outsource dev used this.

  dbDelta($sql);

  $pull_currency_name = 'VidYen';
  $pull_currency_symbol = 'V¥';

	//Default data
	$data_insert = [
			'currency_name' => $pull_currency_name,
			'currency_symbol' => $pull_currency_symbol
	];

	$wpdb->insert($table_name_woocommerce_currencies, $data_insert);
}

/*** MENU Includes INCLUDES ***/
include( plugin_dir_path( __FILE__ ) . 'vidyen-woocommerce-currencies-menu.php'); //Menus

/*** Includes ***/
/*** Functions ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/vidyen_woocommerce_check.php'); //Checks to see if WooCommerce installed
include( plugin_dir_path( __FILE__ ) . 'includes/functions/vidyen_woocommerce_currencies_func.php'); //Checks to see if WooCommerce installed, run first
include( plugin_dir_path( __FILE__ ) . 'includes/functions/vidyen_woocommerce_settings.php'); //Checks to see if WooCommerce installed, run first
