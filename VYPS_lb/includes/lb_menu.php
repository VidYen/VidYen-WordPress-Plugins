<?php
/*
  Leaderboard menu to show shortcodes and other things.
    
 */
 


add_action('admin_menu', 'vyps_lb_submenu', 460 ); //Takeing the WW place at 460

/* Creates the LB information submenu on the main VYPS plugin */

function vyps_lb_submenu() 
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "Leaderboard Instructions";
    $menu_title = 'Leaderboard';
	$capability = 'manage_options';
    $menu_slug = 'vyps_lb_page';
    $function = 'vyps_lb_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* this next function creates the page on the Coinhive submenu */

function vyps_lb_sub_menu_page() 
{ 
	/* Getting the plugin root path. I'm calling VYPS_root but not to be confused with the root in the folder */
	$VYPS_root_path = plugin_dir_path(__FILE__);
	$path_find = "VYPS_lb/includes/";
	$path_remove = '';
	$VYPS_root_path = str_replace( $path_find, $path_remove, $VYPS_root_path);
	
	$VYPS_logo_url = plugins_url() . '/VYPS/images/logo.png'; //I should make this a function.
		
	echo '<br><br><img src="' . $VYPS_logo_url . '" > ';
	
	/* The shortcode instructions are in teh shortcode file. Might be unneeded but I prefer it this way */
	$lb_sc_instruct_include = $VYPS_root_path . 'VYPS_lb/includes/lb_sc_instruct.php';
	include( $lb_sc_instruct_include ); 
	
	
	/* I may not want advertising, but I suppose putting it here never hurts */
	
	$credits_include = $VYPS_root_path . 'VYPS/includes/credits.php';
	include( $credits_include ); 
	
} 


