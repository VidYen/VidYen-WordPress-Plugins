<?php

//This is for the XMR wallet in the user meta shortcode.

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//OK. We are having a gui for stanard egnlish and the three break downs afterwards
function vyps_refer_gui_short_func() {

  //Boot user out if not logged in. BTW my code is evolving. -Felty
  if ( ! is_user_logged_in() ) {

    return; //Not logged in. You see nothing.

  }
  //Set variables
  //Get current user Id obviously
  $current_user_id = get_current_user_id();

  //These two should be blank if none found.
  $display_refer = '';
  $current_refer_name = '';
  $message_output = '';

  //WE functionized this. This should output encode64
  $user_id = $current_user_id;
  $user_refer = vyps_create_refer_func($user_id);

  //This is hardcoded, but the label we are going to cram into the usermeta table
  $key = 'vyps_current_refer';

  //NO WIRE ARRAYS! Only one value at all time. Unless someone messed something up somewhere.
  $single = TRUE;

  //I realize I should post before I pull. Since requires refresh.

  if (isset($_POST['update_refer'])) {

    //As the post is the only thing that edits data, I suppose this is the best place to the noce
    $vyps_nonce_check = $_POST['vypsnoncepost'];

    //Make sure no one is being tricked into updating their Wallet Address
    if ( ! wp_verify_nonce( $vyps_nonce_check, 'vyps-nonce' ) ) {

        // This nonce is not valid.
        die( 'Security check! Nonce check failed!' );

    } else {

        // The nonce was valid.
        //Carry on.
        //Do the dew.
        //Do we even need a else here?
    }

    //Getting the wallet and things.
    $current_refer = sanitize_text_field($_POST['refer_post']); //Sanitize it! From orbit!

    //Ok we just check via the function to see if it actually works. Otherwise, it does not update.
    //I'm on the fence whether or not we actually tell the user when its wrong or when they have nothing.
    if (vyps_is_refer_func($current_refer) > 0){

      //Ok now we update in hell! (Hell = usermeta table) I wonder if anyone reads these coments. -Felty
      update_user_meta( $current_user_id, $key, $current_refer );

    } else {

      $message_output = "<p><b>$current_refer not found!</p>";

    }

  }

  //Ok now we can get it from the meta post post.
  $current_refer = get_user_meta( $current_user_id, $key, $single ); //the user_meta should be most up to date now.
  $current_refer_user_id = vyps_current_refer_func($current_user_id); //Basically we


  //Checking to see if function returned and ID.
  if ( $current_refer_user_id != 0 ) {

    //Get that data!
    $current_refer_data = get_userdata( $current_refer_user_id );

    //This following data call should work. In theory.
    $current_refer_name = $current_refer_data->display_name; //Just the display name. I know I hate -> but its having issues.

  } else {

    //If not found, then well
    $current_refer_name = 'No referral!';
    $display_refer = '';

  }

  //Now we pull what the users current refer was
  $current_refer = get_user_meta( $current_user_id, $key, $single );

  //The output
  $display_refer = $current_refer; //I'm really guessing here as just assuming that if they put in more than one. Probaly should do validation somewhere down road

  //Adding a nonce to the post
  $vyps_nonce_check = wp_create_nonce( 'vyps-nonce' );

  $form_result_ouput = "
    <div>
        <h2>Your referral code to give to others:</h2>
        <p>$user_refer</p>
        <h2>Your current referral:</h2>
        <p>$display_refer</p>
        <p>Belongs to:</p>
        <p>$current_refer_name</p>
        $message_output
        <form method=\"post\" name=\"createuser\" id=\"createuser\" class=\"validate\" novalidate=\"novalidate\" enctype=\"multipart/form-data\">
          <table class=\"form-table\">
            <tbody>
              <tr class=\"form-field form-required\">
                <th scope=\"row\">
                  <label for=\"refer_post\">Update Referral Code:</label>
                </th>
                <td>
                  <input name=\"refer_post\" type=\"text\" id=\"refer_post\" value=\"$current_refer\" aria-required=\"true\" autocapitalize=\"none\" autocorrect=\"off\" maxlength=\"256\">
                </td>
              </tr>
            </tbody>
          </table>
        <p class=\"submit\">
          <input type=\"hidden\" name=\"vypsnoncepost\" id=\"vypsnoncepost\" value=\"$vyps_nonce_check\" />
          <input type=\"submit\" name=\"update_refer\" id=\"update_refer\" class=\"button button-primary\" value=\"Update\">
        </p>
      </form>
    </div>
    ";

  //Remember kids. Always return shortcodes. Never echo or you are going to have a bad time.
  return $form_result_ouput;

}

//Adding the shortcode.
add_shortcode( 'vyps-refer-gui', 'vyps_refer_gui_short_func');

//Ok here are the 3 breakdown shortcodes for localization
//1. Your referal code
//2. Who you are referred to
//3. The input field
//The rest the admins have to put in with their own text


/*** REFER CODE ONLY ***/
function vyps_refer_code_short_func() {

  //Boot user out if not logged in. BTW my code is evolving. -Felty
  if ( ! is_user_logged_in() ) {

    return; //Not logged in. You see nothing.

  }
  //Set variables
  //Get current user Id obviously
  $current_user_id = get_current_user_id();

  //WE functionized this. This should output encode64
  $user_id = $current_user_id;
  $user_refer = vyps_create_refer_func($user_id);

  return $user_refer; //Yep you just see you code

}

//Adding the shortcode.
add_shortcode( 'vyps-refer-code', 'vyps_refer_code_short_func');

/*** The code of the person you are set to ***/
function vyps_current_refer_short_func() {

  //Boot user out if not logged in. BTW my code is evolving. -Felty
  if ( ! is_user_logged_in() ) {

    return; //Not logged in. You see nothing.

  }

  $current_user_id = get_current_user_id();

  //WE functionized this. This should output encode64
  $user_id = $current_user_id;
  $user_refer = vyps_create_refer_func($user_id);

  //This is hardcoded, but the label we are going to cram into the usermeta table
  $key = 'vyps_current_refer';

  //NO WIRE ARRAYS! Only one value at all time. Unless someone messed something up somewhere.
  $single = TRUE;

  //Now we pull what the users current refer was
  $current_refer = get_user_meta( $current_user_id, $key, $single ); //I'm guessing if it doesn't exist, that it will show a blank? WCCW

  return $current;

}

//Adding the shortcode.
add_shortcode( 'vyps-refer-current', 'vyps_current_refer_short_func');
