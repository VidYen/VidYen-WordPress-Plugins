<?php

//This is for the XMR wallet in the user meta shortcode.

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//I swore I'd never use the usermeta table, but here i am.
//Use case is that I just need every user to have an XMR if they are in the worker share point system.

//This should need no shortcode attributes
function vyps_xmr_wallet_func() {

  //Boot user out if not logged in. BTW my code is evolving. -Felty
  if ( ! is_user_logged_in() ) {

    return; //Not logged in. You see nothing.

  }

  //Get current user Id obviously
  $user_id = get_current_user_id();

  //This is hardcoded, but the label we are going to cram into the usermeta table
  $key = 'vyps_xmr_wallet';

  //NO WIRE ARRAYS! Only one value at all time. Unless someone messed something up somewhere.
  $single = TRUE;

  if (isset($_POST['update_wallet'])) {

    //As the post is the only thing that edits data, I suppose this is the best place to the noce
    $vyps_nonce_check = $_POST['vypsnoncepost'];

    //Make sure no one is being tricked into updating their Wallet Address
    if ( ! wp_verify_nonce( $vyps_nonce_check, 'vyps-nonce' ) ) {

        // This nonce is not valid.
        die( 'Security check' );

    } else {

        // The nonce was valid.
        //Carry on.
        //Do the dew.
        //Do we even need a else here?
    }

    //Getting the wallet and things.
    $xmr_wallet = sanitize_text_field($_POST['xmr_wallet']); //Sanitize it! From orbit!

    //Ok now we update in hell! (Hell = usermeta table) I wonder if anyone reads these coments. -Felty
    update_user_meta( $user_id, $key, $xmr_wallet );

  }

  //NOTE: This should come after the post so it will update before the refresh or otherwise the wallet may be out of date. Remind me to check.
  //And we have the current wallet. This may not exist or be blank. Also I'm 99.99999999% sure I can't tell if its actually a real XMR address
  $xmr_wallet = get_user_meta( $user_id, $key, $single );

  //Adding a nonce to the post
  $vyps_nonce_check = wp_create_nonce( 'vyps-nonce' );

  //displaying wallet if EXISTS
  if (strlen($xmr_wallet) > 1){

    $display_wallet = "Current XMR Address:<br>" . $xmr_wallet; //I'm really guessing here as just assuming that if they put in more than one. Probaly should do validation somewhere down road

  } else {

    $display_wallet = "Add your XMR Payout Wallet:"; //Just some text

  }

  $form_result_ouput = "
    <div>
        <p>$display_wallet</p>
        <form method=\"post\" name=\"createuser\" id=\"createuser\" class=\"validate\" novalidate=\"novalidate\" enctype=\"multipart/form-data\">
          <table class=\"form-table\">
            <tbody>
              <tr class=\"form-field form-required\">
                <th scope=\"row\">
                  <label for=\"xmr_wallet\">Update XMR Wallet Address:</label>
                </th>
                <td>
                  <input name=\"xmr_wallet\" type=\"text\" id=\"xmr_wallet\" value=\"$xmr_wallet\" aria-required=\"true\" autocapitalize=\"none\" autocorrect=\"off\" maxlength=\"256\">
                </td>
              </tr>
            </tbody>
          </table>
          <p class=\"submit\">
          <input type=\"hidden\" name=\"vypsnoncepost\" id=\"vypsnoncepost\" value=\"$vyps_nonce_check\" />
          <input type=\"submit\" name=\"update_wallet\" id=\"update_wallet\" class=\"button button-primary\" value=\"Update\">
        </p>
      </form>
    </div>
    ";

  //Remember kids. Always return shortcodes. Never echo or you are going to have a bad time.
  return $form_result_ouput;

}

//Adding the shortcode.
add_shortcode( 'vyps-xmr-wallet', 'vyps_xmr_wallet_func');
