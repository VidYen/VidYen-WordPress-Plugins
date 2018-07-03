<?php
/*
 * Removing Plugin data using uninstall.php
 * the below function clears the database table on uninstall
 * only loads this file when uninstalling a plugin.
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * exit uninstall if not called by WP
 */
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

//There was some uninstall tables but we getting rid of the ch tables now
//Where we're going we won't need tables...
