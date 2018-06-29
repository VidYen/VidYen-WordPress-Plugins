<?php
$message = '';
$uploads = wp_upload_dir();
$upload_path = $uploads['url'];

//This pulls the admin.php?page=vyps_points_list&edituserpoints=3 like options in the URL


//My code revision. -Felty
if (isset($_GET['edituserpoints'])){
	
	global $wpdb;
	
	//Standard issue table names. We should make this an include?
	$table_name_points = $wpdb->prefix . 'vyps_points';
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_users = $wpdb->prefix . 'users';

	//Grab the GET
	$user_id = $_GET['edituserpoints'];
	
	//I'm going to reverse the the order
	//Two buttons with a add and subtract buttons with two different post values
	
	//Going to do an OR to get check to see if either button was clicked
	
	if ( isset($_POST['addpoint']) OR isset($_POST['subpoint']) ){
		
		//Coerce amount value into double This could go wrong. Might consider doubleval(post) instead
		$point_amount_post = (double)( $_POST['update_user_point']);

		
		if ( isset($_POST['addpoint']) ){
		
		//Actually checking if addpoint may be unneeded
			
			
		} elseif ( isset($_POST['subpoint']) ) {
			
			//Reverse the polarity
			$point_amount_post = $point_amount_post * -1;
			
		}
		
		//Ok. So we know they clicked a button so let's do something
		//We need to post the point type, amount of points, and the reason
		//Since the points which should be point id. I REALLY WANT TO CHANGE POINTS TO POINT_ID
		//Also points_amount to point_amount. Have to wait till monroe is done and do it simultaneously
		//And make a whole branch to resolve.
		
		$data_insert = [
			'reason' => $_POST['reason'],
			'points' => $_POST['points'],
			'points_amount' => $point_amount_post,
			'user_id' => $user_id,
			'time' => date('Y-m-d H:i:s')
			];
		$wpdb->insert($table_name_log, $data_insert);
		
		//So entry done. Now I guess we just have to log success message
		
		$message = 'Success. Points added to user.';
		
	}
	
	//Now the page output. This runs regardless of there is a POST or not. Without the url GET it's pointless though so should not run.
	
	//user_login so its easy to see who your modifying at top of page
	$user_name_data = $wpdb->get_var( "SELECT user_login FROM $table_name_users WHERE id= '$user_id'" ); //And this is why I didn't call it the entire row by arrow. We are in 4d with multiple tables
	
	//We need the list of the coins. I'm going to move this down the page later.
	$number_of_point_rows = $wpdb->get_var( "SELECT max( id ) FROM $table_name_points" ); //No WHERE needed. All rows. No exceptions
	
	//Have to pull the point list as this needed before post is made
	
	//Ok. I could in theory check to see if they have selected. But admins shouldn't be messign around here without payint attention.
	//$drop_down_list_data = "<option value=''>Select Points</option>"; //Commented out due to potential admin error issues. May find better fix later.
	$drop_down_list_data = '';
	
	//Pull the rest of the list properly.
	for ($x_for_count = $number_of_point_rows; $x_for_count > 0; $x_for_count = $x_for_count -1 ) {
		
		//Table call to get the point names
		$point_type_name = $wpdb->get_var( "SELECT name FROM $table_name_points WHERE id= '$x_for_count'" ); //I changed the variable names so its a bit more readable from PL
		
		$row_output = "<option value='$x_for_count'>$point_type_name</option>";
		
		//Concat in the loop
		$drop_down_list_data = $drop_down_list_data . $row_output;
		
	}

	//$drop_down_list_data now contains all the point types that exist and can be thrown at the form
	
		
	//The table where they enter information
	$manage_points_menu_tbl = "
		<div class=\"wrap\">
			<h1 class=\"wp-heading-inline\">Manage Points for <strong>$user_name_data</strong> - UID #$user_id</h1>
			<form method=\"post\">
				<table class=\"form-table\">
					<tr>
						<th><label for=\"points\">Point Type</label></th>
						<td>
							<select class=\"points\" id=\"points\" name=\"points\">
								$drop_down_list_data
							</select>                
							<span class=\"description\">Chose point type</span>
						</td>
					</tr>
					<tr>
						<th><label for=\"update_user_point\">Adjust Points Manually</label></th>
						<td>
							<input type=\"number\" name=\"update_user_point\" id=\"update_user_point\" step=\"0.000000001\">
						</td>     
						<td>
							Note: Administrators should avoid adjusting points manually and rely on the monetization systems.
						</td>
					</tr>						
					<tr>
						<th><label for=\"reason\">Adjustment Reason</label></th>
						<td>
							<input type=\"text\" name=\"reason\" id=\"reason\" maxlength=\"25\" value=\"Manual Admin Adjustment\" size=\"50\">
						</td>
					</tr>
				</table>
				<p class=\"submit\">
					<input type=\"submit\" name=\"addpoint\" id=\"addpoint\" class=\"button button-primary\" value=\"Add Points\">
					<input type=\"submit\" name=\"subpoint\" id=\"subpoint\" class=\"button button-primary\" value=\"Subtract Points\">
				</p>
			</form>
	";
	
	//Ok the echo ouput for the page. I'm not going to use the old point log but rather the new PL
	echo $manage_points_menu_tbl;
	
	//Ok now for the points log. Meh. I'm kind of in a crunch so I'm going to copy and paste the new PL code
	//I should functionize it or do an include. But I'll kick that can of worms down the road after we burn the bridge when we get to it.
	//For now I'm litteraly just going to to an isset on the user id to make it look nice. Please no WTF in the github comments
	
	if ( isset($user_id) ) {
		
		/* Technically users don't have to be logged in
		* Should litterally be the log the admin sees 
		* I don't care. Tell users to not put personal identificable 
		* information in their user name (referred to PID in the health care industry)
		*/
		
		global $wpdb;
		$table_name_points = $wpdb->prefix . 'vyps_points';
		$table_name_log = $wpdb->prefix . 'vyps_points_log';
		$table_name_users = $wpdb->prefix . 'users';
		
		//Ok. Since this for user instead of the entire log whe need to get get where the user did
		//And that is against the psuedo-blockchain philosophy. //Also it dawned on me I can rewrite the public log here.
		
		$number_of_log_rows = $wpdb->get_var( "SELECT count( id ) FROM $table_name_log"); //We need to go through entire log
		$number_of_point_rows = $wpdb->get_var( "SELECT max( id ) FROM $table_name_points" ); //No where needed. All rows. No exceptions
		
		//echo '<br>'. $number_of_log_rows; //Some debugging
		//echo '<br>'. $number_of_point_rows; //More debugging
		
		$begin_row = 1;
		$end_row = ''; //Eventually will have admin ability to filter how many rows they see as after 1000 may be intensive
		
		/* Although normally against totally going programatic. Since I know I'm going to reuse this for the public log I'm going to put the headers into variables */
		/* For public log the user_name should be display name and no need to see the UID and PID */
		/* BTW since we already saw user name before display name is fine here since user ID is always the user ID */
		$date_label = "Date";
		$display_name_label = "Display Name";
		$user_id_label = "UID";
		$point_type_label = "Point Type";
		$point_id_label = "PID";
		$amount_label = "Amount";
		$reason_label = "Adjustment Reason";


		//Header output is also footer output if you have not noticed.
		//Also isn't it nice you can edit the format directly instead it all in the array?
		$header_output = "
				<tr>
					<th>$date_label</th>
					<th>$display_name_label</th>
					<th>$point_type_label</th>
					<th>$amount_label</th>
					<th>$reason_label</th>
				</tr>	
		";


		
		
		//Because the shorcode version won't have this
		$page_header_text = "
			<h1 class=\"wp-heading-inline\">All Point Adjustments</h1>        
			<h2>Point Log</h2>
		";
		
		//this is what it's goint to be called
		$table_output = "";
		
		for ($x_for_count = $number_of_log_rows; $x_for_count > 0; $x_for_count = $x_for_count -1 ) { //I'm counting backwards. Also look what I did. Also also, there should never be a 0 id or less than 1
		
			$date_data = $wpdb->get_var( "SELECT time FROM $table_name_log WHERE id= '$x_for_count' AND user_id = '$user_id'" ); //Straight up going to brute force this un-programatically not via entire row
			$user_id_data = $wpdb->get_var( "SELECT user_id FROM $table_name_log WHERE id= '$x_for_count' AND user_id = '$user_id'" ); //We already know which one we are looking form but.
			$display_name_data = $wpdb->get_var( "SELECT display_name FROM $table_name_users WHERE id= '$user_id_data'" ); //And this is why I didn't call it the entire row by arrow. We are in 4d with multiple tables
			$point_id_data = $wpdb->get_var( "SELECT points FROM $table_name_log WHERE id= '$x_for_count' AND user_id = '$user_id'" ); //Yeah this is why I want to call points something else in this table, but its the PID if you can't tell
			$point_type_data = $wpdb->get_var( "SELECT name FROM $table_name_points WHERE id= '$point_id_data'" ); //And now we are calling a total of 3 tables in this operation
			$amount_data = $wpdb->get_var( "SELECT points_amount FROM $table_name_log WHERE id= '$x_for_count' AND user_id = '$user_id'" );
			$reason_data = $wpdb->get_var( "SELECT reason FROM $table_name_log WHERE id= '$x_for_count' AND user_id = '$user_id'" );
			
			//Did I got through the entire log row by row? Yes I did. Yes its way resource inefficient, but it's better to have code readability than efficiency. Also admins don't look at this as often as the public log.
			//Though this would be how I would make it so users can look at their own logs... Eventually. Honestly, I think I will just have the users download the CSV file at this rate.
			//On second thought. I am going to make copy and paste it into a current user log and make it a function.
			//$amount_data = number_format($amount_data); //Adds commas but leaving it out here to be raw and when make [vyps-pl-tbl] will have formatting and color attributes. Also icons.
			
			//That said, we do not want a bunch of blank rows
			
			if ($amount_data == ''){ //In theory if amount data is blank, then no useful data exists on the row. If its broken, you should be looking at the entire log at that point
			
				//Nothing should happen
				
			} else {
				
				//Run the table creation as normal
				$current_row_output = "
					<tr>
						<td>$date_data</td>
						<td>$display_name_data</td>
						<td>$point_type_data</td>
						<td>$amount_data</td>
						<td>$reason_data</td>
					</tr>
						";
						
				//Compile into row output.
				$table_output = $table_output . $current_row_output; //I like my way that is more reasonable instead of .=
			}
			

			//Next for
		} 
		
		//The page output
		echo "
			<div class=\"wrap\">
				$page_header_text
				<table class=\"wp-list-table widefat fixed striped users\">
					$header_output
					$table_output
					$header_output
				</table>			
			</div>
		";
		
		
	}

	//eoif
}

/**** EDIT PAGE VIEW ****/

elseif ( isset($_GET['edit_vyps'])) {
	
	//usual init
	global $wpdb;
	$point_id = $_GET['edit_vyps'];
	
	
	//the $wpdb stuff to find what the current name and icons are
	$table_name_points = $wpdb->prefix . 'vyps_points';
	$point_name = $wpdb->get_var( "SELECT name FROM $table_name_points WHERE id= '$point_id'" ); //Grabbing the icon
	$icon_url = $wpdb->get_var( "SELECT icon FROM $table_name_points WHERE id= '$point_id'" ); //Grabbing the icon
	
	//So after we see if there is an edit_vyps get we check to see if there is an update post (aka user pressed the update point button.
	if (isset($_POST['update_point'])) {
		
		//Obviously we need the name of the point if they updated it.
		$point_name = $_POST['point_name'];
		
		//Ok we seeing if there was an upload for the point icon etc
		if (!empty($_FILES['point_icon_url']['name'])) {
				
				$point_icon_url = media_handle_upload('point_icon_url', 0);
				$icon = wp_get_attachment_url($point_icon_url);
				
        } else {
			
			//Ok we just make the $icon_url the $icon if there wasn't anything there in the first place. Might be redudant.
			$icon = $icon_url;
        }
		
		//I think this was something the old devs left in.
		//$point = $_POST['point']; //not actual point but starting point which was a stupid idea. daily rewards only
		//'points' => $point, // this was from table data
        //$table = $wpdb->prefix . 'vyps_points'; //Some of stuff. When I see it I try to rename to the new convention, which only I know now I think abouit as it hasn't been documented.
		
        $data_insert = [
            'name' => $point_name,
            'icon' => $icon,
            'time' => date('Y-m-d H:i:s')
        ];
		
		//$wpdb call to update row
		
        $wpdb->update($table_name_points, $data_insert, ['id' => $point_id]);

        $message = "Updated successfully.";
		
	}
	
	//Ok. The above was the post when you hit the update point button.
	//Below is the echo to show you the page. I suppose the above has to come first
	//Due to you need to see results of the update point post if you did click it.
	
	//Ye old message output //I just pulled a Benard though since what I wrote originally comes after this
	if (!empty($message)){
		$message_output = "
		
			<div id=\"message\" class=\"updated notice is-dismissible\">
				<p><strong>$message.</strong></p>
				<button type=\"button\" class=\"notice-dismiss\"><span class=\"screen-reader-text\">Dismiss this notice.</span></button>
			</div>	
		";
	} else {
		
		//If no message then the output should be blank.
		$message_output = '';
		
	}
	
	//The page HTML since no list it should need no loop
	//BTW I didn't write the HTML so I may want to go back someday if I am looking at this and try to improve -Felty
	//Also I think they used the creatureuser id and class. Which I guess works, but not what I would have called it.
	
	$update_point_view = "
	
		<div class=\"wrap\">
        <h1 id=\"add-new-user\">Update Point</h1>
			$message_output
        <p>Update this point.</p>
        <form method=\"post\" name=\"createuser\" id=\"createuser\" class=\"validate\" novalidate=\"novalidate\" enctype=\"multipart/form-data\">
            <table class=\"form-table\">
                <tbody>
                    <tr class=\"form-field form-required\">
                        <th scope=\"row\">
                            <label for=\"point_name\">Point Name<span class=\"description\">(required)</span></label>
                        </th>
                        <td>
                            <input name=\"point_name\" type=\"text\" id=\"point_name\" value=\"$point_name\" aria-required=\"true\" autocapitalize=\"none\" autocorrect=\"off\" maxlength=\"60\" >
                        </td>
                    </tr>        
                    <tr class=\"form-field form-required\">
                        <th scope=\"row\">
                            <label for=\"point_icon_url\">Point Icon url<span class=\"description\">(required)</span></label>
                        </th>
                        <td>
                            <img src=\"$icon_url\" class=\"img-responsive\" width=\"50px\">
                            <br>
                            <input name=\"point_icon_url\" type=\"file\" id=\"point_icon_url\" value=\"$icon_url\" aria-required=\"true\" autocapitalize=\"none\" autocorrect=\"off\">
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class=\"submit\">
                <input type=\"submit\" name=\"update_point\" id=\"update_point\" class=\"button button-primary\" value=\"Update Point\">
            </p>
        </form>
    </div>
	";

	//Echo out the table
	
	echo $update_point_view;
	
	//I feel like that could be more efficienct but its 400% better than the original way.
	
}

/**** JUST THE LIST ****/

else {
	
	//I'm going out on a big assumption taht if not &edituserpoints that we should show something

	//Here is where the main manage points goes. Reference old file to reconstruct.
	//It actually wansn't an error. There just wasn't any html to display

	global $wpdb;
	
	//Only need the poitns list.
	$table_name_points = $wpdb->prefix . 'vyps_points';
	
	//and numbers of rows (i feel maybe this should be outside rather than called twice, but what if  sometimes the if doesn't need to call either?
	$number_of_point_rows = $wpdb->get_var( "SELECT max( id ) FROM $table_name_points" ); //No WHERE needed. All rows. No exceptions
	
	//Init for $table_output
	$table_output = '';
	
	for ($x_for_count = $number_of_point_rows; $x_for_count > 0; $x_for_count = $x_for_count -1 ) { 
		
		$point_type_data = $wpdb->get_var( "SELECT name FROM $table_name_points WHERE id= '$x_for_count'" ); // is the $x_for_count for the id. There should never be one out of place unless was being naughty on the SQL
		$point_icon_data = $wpdb->get_var( "SELECT icon FROM $table_name_points WHERE id= '$x_for_count'" ); //Grabbing the icon
		$point_id_data = $wpdb->get_var( "SELECT id FROM $table_name_points WHERE id= '$x_for_count'" ); //You know I don't think we have to get the id since its in the count, but I'm doing it for buggin reasons.
		
		//Need the siteurl and such
		$edit_rename_url = site_url() . '/wp-admin/admin.php?page=vyps_points_list&edit_vyps=' . $point_id_data;
		
		$current_row_output = "
			<tr>
				<td>$point_type_data</td>
				<td><img src=\"$point_icon_data\" width=\"32\" hight=\"32\"></td>
				<td>$point_id_data</td>
				<td class=\"column-primary\"><a href=\"$edit_rename_url\">Edit</a> | <a onclick=\"return confirm('Are you sure want to do this ?');\" href=\"$edit_rename_url\">Rename</a></td>
			</tr>
				";
		
		//Compile into row output.
		$table_output = $table_output . $current_row_output; //I like my way that is more reasonable instead of .=
	}

	//Feels like the message should be handled better.
	if (!empty($message)){
		
		$message_output = "
		
			<div id=\"message\" class=\"updated notice is-dismissible\">
				<p><strong>$message.</strong></p>
				<button type=\"button\" class=\"notice-dismiss\"><span class=\"screen-reader-text\">Dismiss this notice.</span></button>
			</div>	
		";
	} else {
		
		//Need to just set to blank since PHP needs something if its called
		$message_output = '';
		
	}

	$page_url = site_url() . '/wp-admin/admin.php?page=vyps_points_add'; //Most likley not required but I feel like if I need to manipulate site_url() somehow best to had a variable.
	
	//Ok the header
	$vyps_list_header_output = "

		
			<h1 class=\"wp-heading-inline\">Manage Points</h1>
				$message_output
			<a href=\"$page_url\" class=\"page-title-action\">Add New</a>
			<hr class=\"wp-header-end\">
	";
	
	//Output for table header and footer.
	$vyps_table_header_footer_output = "
		<tr>
			<th scope=\"col\" id=\"name\" class=\"manage-column column-name column-primary sortable desc\">
				<a href=\"#\">
					<span>Point Name</span>
					<span class=\"sorting-indicator\"></span>
				</a>
			</th>
			<th scope=\"col\" id=\"icon\" class=\"manage-column column-icon\">Icon</th>
			<th scope=\"col\" id=\"pointid\" class=\"manage-column column-pointid\">Point ID</th>                    
			<th scope=\"col\" id=\"posts\" class=\"manage-column column-posts num\">Action</th>	
		</tr>	
	";
	
	$vyps_list_output = "
		<div class=\"wrap\">
			$vyps_list_header_output
			<form method=\"get\">   
				<h2 class=\"screen-reader-text\">Points list</h2>
				<table class=\"wp-list-table widefat fixed striped users\">
					<thead>
						$vyps_table_header_footer_output
					</thead>
					<tbody>
						$table_output
					</tbody>
					<tfoot>
						$vyps_table_header_footer_output
					</tfoot>
				</table>
			</form>
			<br class=\"clear\">
		</div>
		";
	
	
	//End result is echo output to manage points.
	echo $vyps_list_output; 

}