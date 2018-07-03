<?php
if (! defined('WP_UNINSTALL_PLUGIN')) {
    die();
}

/**
 * Delete plugin table when uninstalled
 */
function plugin_uninstalled()
{
    global $wpdb;
    $table_names = array( 'vysystem', 'vytype', 'vybalance', 'vylog' );
    if (sizeof($table_names) > 0) {
        foreach ($table_names as $table_name) {
            $table = $wpdb->prefix . $table_name;
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }
    }
}
plugin_uninstalled();
