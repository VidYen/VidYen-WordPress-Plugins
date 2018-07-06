<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

VYPS_check_if_true_admin(); //VYPS internal admin check

/* This is the add_point page obviously */
//As I didn't writ this, I kind of feel maybe this should be a function */

$mesage = '';



if(current_user_can('install_plugins')){

	if (isset($_POST['add_point'])) {


		//As the post is the only thing that edits data, I suppose this is the best place to the noce
		$vyps_nonce_check = $_POST['vypsnoncepost'];
		if ( ! wp_verify_nonce( $vyps_nonce_check, 'vyps-nonce' ) ) {
		    // This nonce is not valid.
		    die( 'Security check' );
		} else {
		    // The nonce was valid.
		    // Do stuff here.
		}

		//There is some debate between me and monroe that this field should check for SQL injection.
		//Although he is right that one should always avoid it, I feel like if you can get to this screen
		//then the person who would inject stuff into the field already can modify your PHP
		//If we do need non-admin level people modifying your point system, then we will build and advance cpanel like system
		//Because you shoulnd't have more than 10 points unless you got some weird ICO exchange going on.

		//Point name. Text value
	    $point_name = sanitize_text_field($_POST['point_name']); //Even though I am in the believe if an admin sql injects himself, we got bigger issues, but this has been sanitized.

		//The icon. I'm suprised this works so well
	    $point_icon_url = media_handle_upload('point_icon_url',0);

		//$point = $_POST['point'];
		//The below comment out was not done by me. The Above was. -Felty
		//$icon=$_FILES['point_icon_url']['name'];

	    $icon = wp_get_attachment_url( $point_icon_url );
	    $table_name_points = $wpdb->prefix . 'vyps_points';

	    $data = [
	        'name' => $point_name,
	        'icon' => $icon,
	        'time' => date('Y-m-d H:i:s')
	    ];
	    $data_id = $wpdb->insert($table_name_points , $data);

	    //'points' => $point,
	    $message = "Added successfully.";

		//Always echo no exceptions!
		echo "<script>window.location.href=\"admin.php?page=vyps_points_list\";</script>";


	}

	//I'm kind of annoyed the the original author didn't do a function, but I'm limited for him so...
	//I'll deal with you later. -Felty

	echo "
		<div class=\"wrap\">
			<h1 id=\"add-new-user\">Add Point</h1>
		";

	//well that is one way to do a message
	if(!empty($message)) {

		echo "<div id=\"message\" class=\"updated notice is-dismissible\">
				<p><strong>$message;.</strong></p>
				<button type=\"button\" class=\"notice-dismiss\"><span class=\"screen-reader-text\">Dismiss this notice.</span></button>
			</div>";
	}

	//Adding a nonce to the post
	$vyps_nonce_check = wp_create_nonce( 'vyps-nonce' );

	echo "

		<div>
				<p>Create a new point type:</p>
				<form method=\"post\" name=\"createuser\" id=\"createuser\" class=\"validate\" novalidate=\"novalidate\" enctype=\"multipart/form-data\">
					<table class=\"form-table\">
						<tbody>
							<tr class=\"form-field form-required\">
								<th scope=\"row\">
									<label for=\"point_name\">Point Name: <span class=\"description\">(required)</span></label>
								</th>
								<td>
									<input name=\"point_name\" type=\"text\" id=\"point_name\" value=\"\" aria-required=\"true\" autocapitalize=\"none\" autocorrect=\"off\" maxlength=\"60\">
								</td>
							</tr>
							<tr class=\"form-field form-required\">
								<th scope=\"row\">
									<label for=\"point_icon_url\">Point Icon url: <span class=\"description\">(required)</span></label>
								</th>
								<td>
									<input name=\"point_icon_url\" type=\"file\" id=\"point_icon_url\" value=\"\" aria-required=\"true\" autocapitalize=\"none\" autocorrect=\"off\">
								</td>
							</tr>
						</tbody>
					</table>
					<p class=\"submit\">
					<input type=\"hidden\" name=\"vypsnoncepost\" id=\"vypsnoncepost\" value=\"$vyps_nonce_check\" />
					<input type=\"submit\" name=\"add_point\" id=\"add_point\" class=\"button button-primary\" value=\"Add New Point\">
				</p>
			</form>
		</div>
	";

}
