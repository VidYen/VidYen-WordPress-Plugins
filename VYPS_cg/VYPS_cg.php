<?php
/*
  Plugin Name: VidYen Point System Game Plugin
  Description: Spend points by playing games.
  Version: 0.0.10
  Author: VidYen, LLC
  Author URI: https://vidyen.com/
  License: GPLv2 or later
 */

if ( ! defined('ABSPATH' ) ) {
    die();
}

define('VY_ABSPATH', __DIR__);
if( ! class_exists('VYPS' ) ) {
    class VYPS
    {
        public function __construct()
        {
            global $wpdb;

            $this->includes();

            /** Table Names */

            //tracks equipment available
            $wpdb->vypsg_equipment  = $wpdb->prefix . 'vypsg_equipment';
            //tracks the user's army
            $wpdb->vypsg_tracking    = $wpdb->prefix . 'vypsg_tracking';
            //battle log
            $wpdb->vypsg_battles    = $wpdb->prefix . 'vypsg_battles';
            //pending battles
            $wpdb->vypsg_pending_battles    = $wpdb->prefix . 'vypsg_pending_battles';

            $wpdb->vyps_points = $wpdb->prefix . 'vyps_points';
        }

        //build extra menus
        private function includes()
        {
            include_once VY_ABSPATH . '/includes/menu-page.php';
        }

    }

    $vidyen = new VYPS();
}

/**
 * Creates tables and adds roles
 */
function vypsg_activate()
{
    global $wpdb;

    //add ability for admins to manage vidyen
    $role = get_role( 'administrator' );
    if ( ! $role->has_cap( 'manage_vidyen' ) ) {
        $role->add_cap( 'manage_vidyen' );
    }

    $charset_collate = $wpdb->get_charset_collate();

    $table['vypsg_equipment'] = "CREATE TABLE $wpdb->vypsg_equipment (
      id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
      name VARCHAR(255) NOT NULL,
      description TEXT(400) NOT NULL,
      icon VARCHAR(255) NOT NULL,
      point_type_id INTEGER(10) NOT NULL,
      point_cost DECIMAL(16,8) NOT NULL,
      point_sell DECIMAL(16,8) NOT NULL,
      manpower VARCHAR(255) NOT NULL,
      manpower_use INTEGER(10) NOT NULL,
      speed_modifier INTEGER(10) NOT NULL,
      combat_range INTEGER(10) NOT NULL,
      soft_attack INTEGER(10) NOT NULL DEFAULT 0,
      hard_attack INTEGER(10) NOT NULL DEFAULT 0,
      armor INTEGER(10) NOT NULL DEFAULT '1',
      entrenchment INTEGER(255) NOT NULL DEFAULT '1',
      support INTEGER(1) NOT NULL, /* 0 = no support, 1 = support */
      faction VARCHAR(255) NOT NULL DEFAULT '',
      model_year INTEGER(5) NOT NULL DEFAULT '1970',
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $table['vypsg_tracking'] = "CREATE TABLE $wpdb->vypsg_tracking (
      id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
      item_id VARCHAR(255) NOT NULL,
      username VARCHAR(25) NOT NULL,
      battle_id MEDIUMINT(9), /* what battle it was lost in */
      lost INTEGER(1), /* 0 = lost, 1 = gained */
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $table['vypsg_battles'] = "CREATE TABLE $wpdb->vypsg_battles (
      id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
      winner VARCHAR(255) NOT NULL,
      loser VARCHAR(255) NOT NULL,
      lost_id DECIMAL(16,8) NOT NULL,
      battle_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $table['vypsg_pending_battles'] = "CREATE TABLE $wpdb->vypsg_pending_battles (
      id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
      user_one VARCHAR(255) NOT NULL,
      user_two VARCHAR(255),
      user_one_accept INTEGER(1), /* 0 = declines, 1 = accepts */
      user_two_accept INTEGER(1), /* 0 = declines, 1 = accepts */
      battled INTEGER(1) DEFAULT 0,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $table['vypsg_equipment'] );
    dbDelta( $table['vypsg_tracking'] );
    dbDelta( $table['vypsg_battles'] );
    dbDelta( $table['vypsg_pending_battles'] );
}
register_activation_hook(__FILE__, 'vypsg_activate' );