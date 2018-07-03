<?php
if (!defined('ABSPATH')) {
    die();
}

/**
 * Adds menu links to the admin toolbar.
 */
function vy_register_menu_page()
{
    add_menu_page('VYPS Game', 'VYPS Game', 'manage_vidyen', 'VYPS_cg/pages/manage-equipment.php');

    add_submenu_page(
        'VYPS_cg/pages/manage-equipment.php',
        'Create equipment',
        'Create equipment',
        'manage_vidyen',
        'VYPS_cg/pages/manage-equipment.php'
    );
}
add_action('admin_menu', 'vy_register_menu_page');
