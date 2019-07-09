<?php
/*
Plugin Name:  VidYen Gatekeeper
Plugin URI:   https://wordpress.org/plugins/vidyen-point-system-vyps/
Description:  Monetize site traffic by having your users mine crypto while witholding content until they agree.
Version:      1.0.0
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

register_activation_hook(__FILE__, 'vidyen_site_locker_install');

//Install the SQL tables for VYPS.
function vidyen_site_locker_install()
{
  global $wpdb;

  //I have no clue why this is needed. I should learn, but I wasn't the original author. -Felty
  $charset_collate = $wpdb->get_charset_collate();

  //NOTE: I have the mind to make mediumint to int, but I wonder if you get 8 million log transactios that you should consider another solution than VYPS.

  //vidyen_gatekeeper table creation
  $table_name_gatekeeper = $wpdb->prefix . 'vidyen_gatekeeper';

  $sql = "CREATE TABLE {$table_name_gatekeeper} (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  button_text TINYTEXT NOT NULL,
  disclaimer_text MEDIUMTEXT NOT NULL,
  eula_text MEDIUMTEXT NOT NULL,
  current_wmp varchar(256) NOT NULL,
  current_pool varchar(256) NOT NULL,
  pool_password varchar(256) NOT NULL,
  crypto_wallet varchar(256) NOT NULL,
  gatekeeper_active BOOL NOT NULL,
  wm_active BOOL NOT NULL,
  PRIMARY KEY  (id)
      ) {$charset_collate};";

  require_once (ABSPATH . 'wp-admin/includes/upgrade.php'); //I never did investigate why the original outsource dev used this.

  dbDelta($sql);

  $default_disclaimer_text = 'By clicking agree, you agree that you are over 18, legally allowed to use this site, have read and agreed to the the Terms of Service, and that you agree to let this site use your device resources to monetize by mining crypto to monetize the site and to use cookies and information gathered to let you use the site functionally.';

  //Default data
  $data_insert = [
      'button_text' => 'I agree.',
      'disclaimer_text' => $default_disclaimer_text,
      'eula_text' => '',
      'current_wmp' => 'savona.vy256.com:8183',
      'current_pool' => 'moneroocean.stream',
      'pool_password' => '',
      'crypto_wallet' => '',
      'gatekeeper_active' => 0,
      'wm_active' => 0,
  ];

  $wpdb->insert($table_name_gatekeeper, $data_insert);

}

/*** MENU Includes INCLUDES ***/
include( plugin_dir_path( __FILE__ ) . 'vidyen-gatekeeper-menu.php'); //Menus

/*** FUNCTION INCLUDES***/

/*** CORE ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/vidyen_gatekeeper_lock_cookie.php'); //If cookie not set locks content
include( plugin_dir_path( __FILE__ ) . 'includes/functions/vidyen_gatekeeper_monetizer.php'); //The monetizer
include( plugin_dir_path( __FILE__ ) . 'includes/functions/vidyen_gatekeeper_set_cookie_action.php'); //Ajax to set cookie if accepted
include( plugin_dir_path( __FILE__ ) . 'includes/functions/vidyen_gatekeeper_settings.php'); //SQL settings checker
