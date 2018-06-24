<?php
if(!defined('ABSPATH')){
    die();
}

/**
 * Adds menu links to the admin toolbar.
 */
function vy_register_menu_page()
{
    add_menu_page('VYPS Game', 'VYPS Game', 'manage_vidyen', 'VYPS_cg/pages/manage-equipment.php' );

    add_submenu_page( 'VYPS_cg/pages/manage-equipment.php', 'Create equipment', 'Create equipment',
        'manage_vidyen', 'VYPS_cg/pages/manage-equipment.php' );
}
add_action( 'admin_menu', 'vy_register_menu_page');

/**
 * Adds page for users play game
 */
function my_users_menu()
{
    add_menu_page('My Equipment', 'VYPS Game', 'read', 'my-equipment', 'my_equipment_page', 'dashicons-smiley' );

    add_submenu_page( 'my-equipment', 'Buy Equipment', 'Buy Equipment',
        'read', 'buy-equipment', 'buy_equipment' );

    add_submenu_page( 'my-equipment', 'Battle Log', 'Battle Log',
        'read', 'battle-log', 'battle_log' );

    add_submenu_page( 'my-equipment', 'Battle', 'Battle',
        'read', 'battle', 'battle' );

}
add_action( 'admin_menu', 'my_users_menu' );

/**
 * Displays HTML for equipment page.
 */
function my_equipment_page()
{
    include __DIR__ . '/../pages/my-equipment.php';
}

/**
 * Displays HTML for buy equipment page.
 */
function buy_equipment()
{
    include __DIR__ . '/../pages/buy-equipment.php';
}

/**
 * Displays HTML for buy battle log.
 */
function battle_log()
{
    include __DIR__ . '/../pages/battle-log.php';
}

/**
 * Displays HTML for buy battle log.
 */
function battle()
{
    include __DIR__ . '/../pages/battle.php';
}