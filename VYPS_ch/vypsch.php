<?php
/*
  Plugin Name: VYPS Coinhive Addon
  Description: Adds Coinhive API to the VYPS so you can award points based on hashes mined to your users
  Version: 0.0.20
  Author: VidYen, LLC
  Author URI: https://vidyen.com/
  License: GPLv2 or later
 */

 /* 
* Note: Have renamed all tables to the right tables... ie. tables_ch, table_points
* Need to make every variable have specific context so can simply glance at something to know what it does
*
*
 */
 
register_activation_hook(__FILE__, 'vyps_ch_install');

function vyps_ch_install() {
    global $wpdb;
	
	$message = ''; //yeah should set that somewhere

    $table_name = $wpdb->prefix . 'vyps_ch';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		siteKey tinytext NOT NULL,
		secretKey tinytext NOT NULL,
		siteUID tinytext NOT NULL,
		threads mediumint(9) NOT NULL,
		throttle mediumint(9) NOT NULL,
		pointID varchar(11) NOT NULL,
		pointName tinytext NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";
	    
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);
	
	/* Setting the defaults here for the API's to blank and my personal feelins on what should be default
	*  I set my test site Site Key becasue I got really tired of copy and pasting it in
	*  Also I have put my private site key in but honestly it's only for my test page and not production
	*  The threads and throttle set to only 1 thread and 10% CPU usage by default.
	*  Reason: Who wants to test with 100% CPU usage? Also, you should be kind to your users.
	*/
	$site_key = "5y8ys1vO4guiyggOblimkt46sAOWDc8z";
	$secret_key = "A6YSYjxSpS0NY6sZiBbtV6qdx4006Ypw";
	$site_UID = "VidYenTest";
	$sm_threads = "1";
	$sm_throttle = "90";
	$ch_point_name = "Select Points";
	$table_ch = $wpdb->prefix . 'vyps_ch';
	//For some reason the table call is not redudant and removing it causes things to not feed the default values
	$data = [
		'siteKey' => $site_key,
		'secretKey' => $secret_key,
		'siteUID' => $site_UID,
		'threads' => $sm_threads,
		'throttle' => $sm_throttle,
		'pointName' => $ch_point_name,
	];
	$data_id = $wpdb->insert($table_ch, $data);
}

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
		
		/* what does the 2 lines below do? I can't remember. -Felty */
		$data_id = $wpdb->update($table_ch, $data, array( 'id' => 1 ) );
		
		$site_key_field = $wpdb->get_var( "SELECT * FROM $table_ch", 1, 0 );		
		    
		$message = "Updated successfully.";

	}

	/* this next bit pulls form the vyps_points table to get list */
	$query = "select * from " . $wpdb->prefix . 'vyps_points';
    $data = $wpdb->get_results($query);

	/* I'm putting the logo at top because I can */
	echo '<br><br><img src="' . plugins_url( '../VYPS_base/images/logo.png', __FILE__ ) . '" > ';
		
	?>
	<div class="wrap">
		<h1 id="add-new-user">VYPS Coinhive API Settings</h1>
		 <?php if (!empty($message)): ?>
            <div id="message" class="updated notice is-dismissible">
                <p><strong><?= $message; ?>.</strong></p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            </div>
        <?php endif; ?>
		<p>Put your Coinhive Site API keys below. Refrain from changing these too often as your users may get made at lost balances.</p>
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
						<label for="site_UID">Site UID<span class="description">(Optional: In case your have more than one WP site mining to same site.)</span></label>
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
                    <th><label for="points">Point type to redeem to: <span class="description">(If you delete the point off the main point system you need to reset this field)</span></label></th>
                    <td>                     
                        <select class="points" id="points" name="points">
                            <option value='<?php echo $wpdb->get_var( "SELECT * FROM $table_ch", 6, 0 ); ?>'><?php echo $wpdb->get_var( "SELECT * FROM $table_ch", 7, 0 ); ?></option>
                            <?php if (!empty($data)): ?>
                                <?php foreach ($data as $d): ?>
                                    <option <?php echo ($user_points = (string) $d->id) ? 'selected' : ''; ?> value='<?= $d->id ?>'><?= $d->name; ?></option>
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
		<p><b>[vyps-redeem-ch]</b></p><br><br>
		
	</div>
	
	<?php
	
	/* I may not want advertising, but I suppose putting it here never hurts */
	include( plugin_dir_path( __FILE__ ) . '../VYPS_base/includes/credits.php'); 	
} 

/* Next section for creatiung short code for the simple miner. */

function sm_short_func() {
	
	/* Check to see if user is logged in */
	/* Yes you could get free hashes off people without acknowledging them, but there are other plugins that already do that */
	
	if ( is_user_logged_in() ) {
	
		/* Pulling the WPDB variables*/
		global $wpdb;
		$table_ch = $wpdb->prefix . 'vyps_ch';
		$sm_site_key = $wpdb->get_var( "SELECT * FROM $table_ch", 1, 0 );
		$sm_siteUID = $wpdb->get_var( "SELECT * FROM $table_ch", 3, 0 ); 
		$sm_threads = $wpdb->get_var( "SELECT * FROM $table_ch", 4, 0 );
		$sm_throttle = $wpdb->get_var( "SELECT * FROM $table_ch", 5, 0 );
		$current_user_id = get_current_user_id();
		$sm_user = $sm_siteUID . $current_user_id;
		
		return "
			<script src=\"https://authedmine.com/lib/simple-ui.min.js\" async></script>
			<div class=\"coinhive-miner\" 
				style=\"width: 256px; height: 310px\"
				data-key=\"$sm_site_key\"
				data-threads=\"$sm_threads\"
				data-throttle=\"$sm_throttle\"
				data-user=\"$sm_user\"
				>
				<em>Loading...</em>
				</div>";
	} else {
		echo "You need to be logged in to use Coinhive on this site!";
	}
}

/* Telling WP to use function for shortcode */

add_shortcode( 'vyps-simple-miner', 'sm_short_func');	

/* Shortcode for the API call to create a lot entry */
/* There is some debate if this should be a button, but I'm just going to run on the code on page load and the admins can just make a button that runs the smart code if they want */

function sm_short_redeem_func() {
	
	/* Check to see if user is logged in */
		
	if ( is_user_logged_in() ) {
	
		/* Pulling the WPDB variables*/
		global $wpdb;
		$table_ch = $wpdb->prefix . 'vyps_ch';
		$current_user_id = get_current_user_id();
		$sm_siteUID = $wpdb->get_var( "SELECT * FROM $table_ch", 3, 0 ); 
		$sm_site_key = $wpdb->get_var( "SELECT * FROM $table_ch", 1, 0 );
		$hiveKey = $wpdb->get_var( "SELECT * FROM $table_ch", 2, 0 );
		$hiveUser = $sm_siteUID . $current_user_id;
		
		
		//Copied and pasted from the old VidYen.com code
		// fetch from DB
		//$hiveUser = $user->id;
		//$hiveKey = 'baMweSSSVy93nOaQXOuQ0rKFRQlX0PY1';
		// --------------------
										
		$url = "https://api.coinhive.com/user/balance?name={$hiveUser}&secret={$hiveKey}";
										
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
										
		$jsonData = json_decode($result, true);
		$balance = $jsonData['balance'];
										
		/* echo $balance;
										
		$hostBalance = $unbalance + ($unbalance - $balance);
										
		echo $hostBalance; */
								
		//
		// A very simple PHP example that sends a HTTP POST to a remote site
		//

		$ch = curl_init();
										
		curl_setopt($ch, CURLOPT_URL,"https://api.coinhive.com/user/withdraw");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,
			"name={$hiveUser}&amount={$balance}&secret={$hiveKey}");
										
		// in real life you should use something like:
		// curl_setopt($ch, CURLOPT_POSTFIELDS, 
		//          http_build_query(array('postvar1' => 'value1')));
										
		// receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
										
		$server_output = curl_exec ($ch);
										
		curl_close ($ch);
										
		// further processing ....
		//if ($server_output == "OK") { ... } else { ... }
		
		/* OK. Pulling log table to post return to it. What could go wrong? */
		/* Honestly, we should always refer to table by the actual table?   */
		
		/* Just checking to see if balance is 0. If it is, no need to do anything other than return the results.*/
		if( $balance > 0 )
		{
			global $wpdb;
			
			$table_log = $wpdb->prefix . 'vyps_points_log';
			$reason = "Coin Hive Mining";
			$amount = $balance;

			$pointType = $wpdb->get_var( "SELECT * FROM $table_ch", 6, 0 );
			$user_id = get_current_user_id();
				$data = [
					'reason' => $reason,
					'points' => $pointType,
					'points_amount' => $amount,
					'user_id' => $user_id,
					'time' => date('Y-m-d H:i:s')
				];
			$wpdb->insert($table_log, $data);
		}
		
		/* It dawned on me that text in here only needs a number and let the admin right there response
		*  One could in theory might make an IF statement you have no hashes to redeem, but KISS */
		
		return "$balance";
	} else {
		echo "You need to be logged in to use Coinhive on this site!";
	}

}

add_shortcode( 'vyps-redeem-ch', 'sm_short_redeem_func');
