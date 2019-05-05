<?php
 /*
Plugin Name:  VidYen RTS Plugin
Plugin URI:   https://wordpress.org/plugins/vidyen-point-system-vyps/
Description:  Adds RTS Game to VidYen Point System
Version:      0.0.67
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

register_activation_hook(__FILE__, 'vidyen_rts_sql_install');

//Install the SQL tables for VYPS.
function vidyen_rts_sql_install()
{
  global $wpdb;

	//I have no clue why this is needed. I should learn, but I wasn't the original author. -Felty
	$charset_collate = $wpdb->get_charset_collate();

	//NOTE: I have the mind to make mediumint to int, but I wonder if you get 8 million log transactios that you should consider another solution than VYPS.

	//vidyen_rts table creation
  $table_name_rts = $wpdb->prefix . 'vidyen_rts';

  $sql = "CREATE TABLE {$table_name_rts} (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
  currency_id mediumint(9) NOT NULL,
  wood_id mediumint(9) NOT NULL,
  iron_id mediumint(9) NOT NULL,
  stone_id mediumint(9) NOT NULL,
	light_soldier_id mediumint(9) NOT NULL,
	light_soldier_cost mediumint(9) NOT NULL,
  light_soldier_time mediumint(9) NOT NULL,
	light_ship_id mediumint(9) NOT NULL,
	light_ship_cost mediumint(9) NOT NULL,
  light_ship_time mediumint(9) NOT NULL,
	PRIMARY KEY  (id)
      ) {$charset_collate};";

  //vidyen_rts_mission table creation
  //This is mostly a place holder for now until later
  /* NOTE: Don't use this yet
  $table_name_rts_mission = $wpdb->prefix . 'vidyen_rts_mission';

  $sql .= "CREATE TABLE {$table_name_rts_mission} (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
  currency_id mediumint(9) NOT NULL,
  wood_id mediumint(9) NOT NULL,
  iron_id mediumint(9) NOT NULL,
  stone_id mediumint(9) NOT NULL,
	light_soldier_id mediumint(9) NOT NULL,
	light_soldier_cost mediumint(9) NOT NULL,
  light_soldier_time mediumint(9) NOT NULL,
	light_ship_id mediumint(9) NOT NULL,
	light_ship_cost mediumint(9) NOT NULL,
  light_ship_time mediumint(9) NOT NULL,
	PRIMARY KEY  (id)
      ) {$charset_collate};";
  */

  //vidyen_rts_mission_log. keeps track of the mission timers
  //Not really sucess but rather just to keep track of users
  //A design philosophy note: I don't see users running more than one type of a mission at a time.
  //A gate as you will
	$table_name_rts_mission_log = $wpdb->prefix . 'vidyen_rts_mission_log';

  $sql .= "CREATE TABLE {$table_name_rts_mission_log} (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
  reason varchar(128) NOT NULL,
  user_id mediumint(9) NOT NULL,
	time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  mission_id varchar(32) NOT NULL,
  mission_time mediumint(9) NOT NULL,
  adjustment varchar(100) NOT NULL,
	mission_meta_id varchar(64) NOT NULL,
	mission_meta_data varchar(128) NOT NULL,
	mission_meta_amount double(64,0) NOT NULL,
	mission_meta_subid1 mediumint(9) NOT NULL,
	mission_meta_subid2 mediumint(9) NOT NULL,
	mission_meta_subid3 mediumint(9) NOT NULL,
	PRIMARY KEY  (id)
      ) {$charset_collate};";

  require_once (ABSPATH . 'wp-admin/includes/upgrade.php'); //I never did investigate why the original outsource dev used this.

  dbDelta($sql);

	//Default data
	$data_insert = [
      'currency_id' => 1,
      'wood_id' => 4,
      'iron_id' => 6,
      'stone_id' => 5,
			'light_soldier_id' => 7,
			'light_soldier_cost' => 10,
      'light_soldier_time' => 1,
			'light_ship_id' => 8,
			'light_ship_cost' => 100,
      'light_ship_time' => 100,
	];

  //There will be no default data for the mission_log table

	$wpdb->insert($table_name_rts, $data_insert);
}

/*** Includes ***/
/*** CORE Functions ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vidyen_rts_sql_call_func.php'); //SQL Call functions
//include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_mmo_wc_ww_bal_func.php'); //Custom WooWallet balance function for this purpose
//include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vidyen_mmo_loa_user_query_func.php'); //Adds meta check

/*** Balance AJAX ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ajax/vidyen_rts_bal_ajax.php');

/*** MISSION FUNCTIONS ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/missions/vidyen_rts_sack_village_ajax.php');
include( plugin_dir_path( __FILE__ ) . 'includes/functions/missions/vidyen_rts_add_mission_func.php');
include( plugin_dir_path( __FILE__ ) . 'includes/functions/missions/vidyen_rts_check_mission_time_func.php');
include( plugin_dir_path( __FILE__ ) . 'includes/functions/missions/vidyen_rts_sack_village_timer_ajax.php');

/*** Shortcodes ***/
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-rts-missions.php'); //Ajax Balance
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-rts-bal.php'); //Post back for game transfers
//include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-rts-point-exchange.php'); //Ajax Point Exchange
//include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-rts-credit-postback.php'); //Post back for game credit transfers
//include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-rts-deduct-postback.php'); //Post back for game deduct transfers
//include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-rts-api-bal.php'); //Post back for game transfers
//include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-rts-loa-id.php'); //Stores the LOA Player ID
//include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-rts-register.php'); //Registers the User ID

/*** Menu Includes ***/
include( plugin_dir_path( __FILE__ ) . 'vidyen-rts-menu.php'); //Order 600


/*** Templater ***/
include( plugin_dir_path( __FILE__ ) . 'vidyen-rts-template-function.php'); //Order 600
