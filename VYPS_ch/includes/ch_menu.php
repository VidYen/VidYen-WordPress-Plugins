<?php
/*
  Leaderboard menu to show shortcodes and other things.
    
 */
 
 add_action('admin_menu', 'vyps_ch_submenu', 430 );

/* Creates the Coinhive submenu on the main VYPS plugin */


function vyps_ch_submenu() 
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "Manage Coinhive API";
    $menu_title = 'Coinhive Settings';
	$capability = 'manage_options';
    $menu_slug = 'vyps_ch_page';
    $function = 'vyps_ch_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* this next function creates the page on the Coinhive submenu */

function vyps_ch_sub_menu_page() 
{ 

	/* Getting the plugin root path. I'm calling VYPS_root but not to be confused with the root in the folder */
	$VYPS_root_path = plugin_dir_path(__FILE__);
	$path_find = "VYPS_ch/includes/";
	$path_remove = '';
	$VYPS_root_path = str_replace( $path_find, $path_remove, $VYPS_root_path);
	
	$VYPS_logo_url = plugins_url() . '/VYPS/images/logo.png'; //I should make this a function.
		
	echo '<br><br><img src="' . $VYPS_logo_url . '" > ';
    global $wpdb;
	$table_ch = $wpdb->prefix . 'vyps_ch';
	$table_points = $wpdb->prefix . 'vyps_points';
	
    if (isset($_POST['save_settings'])) {

		$site_key = $_POST['site_key'];
		$secret_key = $_POST['secret_key'];
		$site_UID = $_POST['site_UID'];
		$sm_threads = $_POST['sm_threads'];
		$sm_throttle = $_POST['sm_throttle'];
		//I realized the below point_id variable is named poorly, will fix eventually
		$point_id = $_POST['points'];
		//BTW the below get_var seems to be the right way on this server without the quotes around name etc?
		$ch_point_name =  $wpdb->get_var( "SELECT name FROM $table_points WHERE id = $point_id" );
		$table_ch = $wpdb->prefix . 'vyps_ch'; //I feel like this call is reduntant but will have to check later
		$data = [
			'siteKey' => $site_key,
			'secretKey' => $secret_key,
			'siteUID' => $site_UID,
			'threads' => $sm_threads,
			'throttle' => $sm_throttle,
			'pointID' => $point_id,
			'pointName' => $ch_point_name,
		];
		
		/* what does the 2 lines below do? I should find out someday. -Felty */
		$data_id = $wpdb->update($table_ch, $data, array( 'id' => 1 ) );
		
		$site_key_field = $wpdb->get_var( "SELECT * FROM $table_ch", 1, 0 );		
		    
		$message = "Updated successfully.";

	}

	/* this next bit pulls form the vyps_points table to get list */
	$query = "select * from " . $wpdb->prefix . 'vyps_points';
    $data = $wpdb->get_results($query);

	/* old logo removed */
	//echo '<br><br><img src="' . plugins_url( '../VYPS/images/logo.png', __FILE__ ) . '" > ';
	
	/* Yeah I know. No-echo code. Even though CH was written by me this was copy and paste from old system revised
	*  That said... I'm not going to rewrite this as I'm just going to go pure shortcode and get rid of the CH table.
	*/
		
	?>
	<div class="wrap">
		<h1 id="add-new-user">VYPS Coinhive API Settings</h1>
		 <?php if (!empty($message)): ?>
            <div id="message" class="updated notice is-dismissible">
                <p><strong><?= $message; ?>.</strong></p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            </div>
        <?php endif; ?>
		<p>Put your Coinhive Site API keys below. Refrain from changing these too often as your users may get angry at lost hashes. NOTE: Current API keys are for VidYen. Replace with yours to get credit!</p>
		<form method="post" name="createuser" id="createuser" class="validate" novalidate="novalidate" enctype="multipart/form-data">
			<table class="form-table">
			<tbody>
				<!-- Site Key -->
				<tr class="form-field form-required">
					<th scope="row">
						<label for="site_key">Site Key<span class="description">(Required: Found on Coinhive Settings>Site Page)</span></label>
					</th>
					<td>
						<input name="site_key" type="text" id="site_key" value="<?php echo $wpdb->get_var( "SELECT * FROM $table_ch", 1, 0 ); ?>" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="120">
					</td>
				</tr>
				<!-- Secret Key -->				
				<tr class="form-field form-required">
					<th scope="row">
						<label for="secret_key">Secret Key<span class="description">(Reuired: Found on Coinhive Settings>Site Page)</span></label>
					</th>
					<td>
						<input name="secret_key" type="text" id="secret_key" value="<?php echo $wpdb->get_var( "SELECT * FROM $table_ch", 2, 0 ); ?>" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="120">
					</td>
				</tr>
				<!-- Site UID -->	
				<tr class="form-field form-required">
					<th scope="row">
						<label for="site_UID">Site UID<span class="description">(Optional: In case your have more than one WP site mining to same site, you can set site name here.)</span></label>
					</th>
					<td>
						<input name="site_UID" type="text" id="site_UID" value="<?php echo $wpdb->get_var( "SELECT * FROM $table_ch", 3, 0 ); ?>" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="120">
					</td>
				</tr>
				<!-- Threads -->	
				<tr class="form-field form-required">
					<th scope="row">
						<label for="sm_threads">Thread Default<span class="description">(Optional: How many threads you want users to start with.)</span></label>
					</th>
					<td>
						<input name="sm_threads" type="number" id="sm_threads" value="<?php echo $wpdb->get_var( "SELECT * FROM $table_ch", 4, 0 ); ?>" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="2" min="1" max="256">
					</td>
				</tr>
				<!-- Throttle -->	
				<tr class="form-field form-required">
					<th scope="row">
						<label for="sm_throttle">CPU Throttle<span class="description">(Optional: Range is 0 to 90, how much of the CPU do you not want to use by default. Recommended 90.)</span></label>
					</th>
					<td>
						<input name="sm_throttle" type="number" id="sm_throttle" value="<?php echo $wpdb->get_var( "SELECT * FROM $table_ch", 5, 0 ); ?>" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="3" min="0" max="90">
					</td>
				</tr>
				<!-- Point Type -->
				<tr>
                    <th><label for="points">Point type to redeem to: <span class="description">Set this or will give SQL error on redemption.</span></label></th>
                    <td>                     
                        <select class="points" id="points" name="points">
                            <option value="<?php echo $wpdb->get_var( "SELECT * FROM $table_ch", 6, 0 ); ?>" selected><?php echo $wpdb->get_var( "SELECT * FROM $table_ch", 7, 0 ); ?></option>
                            <?php if (!empty($data)): ?>
                                <?php foreach ($data as $d): ?>
                                    <option <?php /* echo ($user_points = (string) $d->id) ? 'selected' : ''; */ ?> value="<?= $d->id ?>"><?= $d->name; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>                
                    </td>
                </tr>
			</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="save_settings" id="save_settings" class="button button-primary" value="Save Settings">
			</p>
		</form>
		<h1>Shortcodes:</h1>
		<p>Display the simple miner for users on a page.</p>
		<p><b>[vyps-simple-miner]</b></p>
		<p>Call the Coinhive POST/GET API to redeem to the VidYen point system. Will return the number of hashes acknolwedged from CoinHive that is added to the VYPS database.</p>
		<p><b>[vyps-redeem-ch]</b></p><br>
		<p>Creates a consent button that only allows the Simple Miner to load on consent with second shortcode on page.</p>
		<p><b>[vyps-ch-consent]</b></p><br>
		<p>Loads simple miner on same page when consent button is clicked and agreed to. Note: Put this and previous shortcode on same page. Includes a redemption function button in shortcode.</p>
		<p><b>[vyps-ch-sm-consent]</b></p><br>
		
	</div>
	
	<?php
	
	/* I may not want advertising, but I suppose putting it here never hurts */
	$credits_include = $VYPS_root_path . 'VYPS/includes/credits.php';
	include( $credits_include ); 
	
}