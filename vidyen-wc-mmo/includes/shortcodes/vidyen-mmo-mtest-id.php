<?php

//This is for the player id that players must set on the website

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This should need no shortcode attributes
function vidyen_mmo_mtest_id_func($atts)
{

  //Simple attribue to see if you can edit. But default will be off.
  $atts = shortcode_atts(
    array(
        'edit' => FALSE
    ), $atts, 'vyps-mtest-query' );

  //Boot user out if not logged in. BTW my code is evolving. -Felty
  if ( ! is_user_logged_in() )
  {
    return; //Not logged in. You see nothing.
  }

  //Get current user Id obviously and email
  $user_id = get_current_user_id();
  $user_info = get_userdata($user_id);
  $user_email =  $user_info->user_email;
  $user_registration_link = '/registerid '. $user_email;
  //$mtest_link_generate_url = plugins_url( 'js/', dirname(__FILE__) ) . 'mtest-id-generate.js'; //Not needed I think.

  //This is hardcoded, but the label we are going to cram into the usermeta table
  $key = 'vidyen_mmo_mtest_id';

  //NO WIRE ARRAYS! Only one value at all time. Unless someone messed something up somewhere.
  $single = TRUE;

  //I'm only going to run this is you edit. Otherwise its just a clear
  if ($atts['edit'] == TRUE)
  {
    if (isset($_POST['update_mtest_id']))
    {
      //As the post is the only thing that edits data, I suppose this is the best place to the noce
      $vyps_nonce_check = $_POST['vypsnoncepost'];

      //Make sure no one is being tricked into updating their Wallet Address
      if ( ! wp_verify_nonce( $vyps_nonce_check, 'vyps-nonce' ) )
      {
          // This nonce is not valid.
          die( 'Security check' );
      }
      else
      {
          // The nonce was valid.
          //Carry on.
          //Do the dew.
          //Do we even need a else here?
      }

      //Getting the mtest_id and things.
      $mtest_id = sanitize_text_field($_POST['mtest_id']); //Sanitize it! From orbit!

      //Ok now we update in hell! (Hell = usermeta table) I wonder if anyone reads these coments. -Felty
      update_user_meta( $user_id, $key, $mtest_id );
    }

    //NOTE: This should come after the post so it will update before the refresh or otherwise the mtest_id may be out of date. Remind me to check.
    //And we have the current mtest_id. This may not exist or be blank. Also I'm 99.99999999% sure I can't tell if its actually a real XMR address
    $mtest_id = get_user_meta( $user_id, $key, $single );

    //Adding a nonce to the post
    $vyps_nonce_check = wp_create_nonce( 'vyps-nonce' );

    //displaying mtest_id if EXISTS
    if (strlen($mtest_id) > 1)
    {
      $display_mtest_id = "Current mtest ID:<br>" . $mtest_id; //I'm really guessing here as just assuming that if they put in more than one. Probaly should do validation somewhere down road
    }
    else
    {
      $display_mtest_id = "No mtest ID set:"; //Just some text
    }

    $form_result_ouput = "
      <div>
          <p>$display_mtest_id</p>
          <form method=\"post\" name=\"createuser\" id=\"createuser\" class=\"validate\" novalidate=\"novalidate\" enctype=\"multipart/form-data\">
            <table class=\"form-table\">
              <tbody>
                <tr class=\"form-field form-required\">
                  <th scope=\"row\">
                    <label for=\"mtest_id\">Update mtest ID:</label>
                  </th>
                  <td>
                    <input name=\"mtest_id\" type=\"text\" id=\"mtest_id\" value=\"$mtest_id\" aria-required=\"true\" autocapitalize=\"none\" autocorrect=\"off\" maxlength=\"256\">
                  </td>
                </tr>
              </tbody>
            </table>
            <p class=\"submit\">
            <input type=\"hidden\" name=\"vypsnoncepost\" id=\"vypsnoncepost\" value=\"$vyps_nonce_check\" />
            <input type=\"submit\" name=\"update_mtest_id\" id=\"update_mtest_id\" class=\"button button-primary\" value=\"Update\">
          </p>
        </form>
      </div>
      ";
    }
    else
    {
      //This is all other cases... ALl you can do is clear.
      if (isset($_POST['change_mtest_id_action']))
      {
        //As the post is the only thing that edits data, I suppose this is the best place to the noce
        $vyps_nonce_check = $_POST['vypsnoncepost'];

        //Make sure no one is being tricked into updating their Wallet Address
        if ( ! wp_verify_nonce( $vyps_nonce_check, 'vyps-nonce' ) )
        {
            // This nonce is not valid.
            die( 'Security check' );
        }

        //Getting the mtest_id and things.
        //$mtest_id = sanitize_text_field($_POST['mtest_id']); //Sanitize it! From orbit!
        if (isset($_POST['mtest_id']))
        {
          $mtest_id = sanitize_text_field($_POST['mtest_id']);
        }
        else
        {
          $mtest_id = ''; //Tis blank if no post!
        }
        //Ok now we update in hell! (Hell = usermeta table) I wonder if anyone reads these coments. 300 was a good movie. -Felty
        update_user_meta( $user_id, $key, $mtest_id );
      }

      //Adding a nonce to the post
      $vyps_nonce_check = wp_create_nonce( 'vyps-nonce' );

      $mtest_id = get_user_meta($user_id, $key, $single); //I forgot this. Shoudl work now.

      //displaying mtest_id if EXISTS
      if (strlen($mtest_id) > 1)
      {
        $display_mtest_id =  $mtest_id; //I'm really guessing here as just assuming that if they put in more than one. Probaly should do validation somewhere down road
      }
      else
      {
        $display_mtest_id = "No Mine Test ID set!"; //Just some text
      }

      $form_result_ouput =
      "<div>
          <form method=\"post\" name=\"createuser\" id=\"createuser\" class=\"validate\" novalidate=\"novalidate\" enctype=\"multipart/form-data\">
            <table class=\"form-table\">
              <tbody>
                <tr class=\"form-field form-required\">
                  <th scope=\"row\">
                    <label for=\"mtest_id\">Mine Test ID:</label>
                  </th>
                  <td>
                    <input name=\"mtest_id\" type=\"text\" id=\"mtest_id\" value=\"$display_mtest_id\" autocapitalize=\"none\" autocorrect=\"off\" maxlength=\"256\">
                  </td>
                </tr>
              </tbody>
            </table>
            <p class=\"submit\">
            <input type=\"hidden\" name=\"vypsnoncepost\" id=\"vypsnoncepost\" value=\"$vyps_nonce_check\" />
            <input type=\"hidden\" name=\"change_mtest_id_action\" id=\"change_mtest_id_action\" value=\"change_mtest_id_action\" />
            <input type=\"submit\" name=\"set_mtest_id\" id=\"set_mtest_id\" class=\"button button-primary\" value=\"Clear ID\" onclick=\"return confirm('Are you sure you want to change your ID?');\">
          </p>
        </form>
      </div>
        ";
    }

  //Remember kids. Always return shortcodes. Never echo or you are going to have a bad time.
  return $form_result_ouput;
}

//Adding the shortcode.
add_shortcode( 'vidyen-mtest-id', 'vidyen_mmo_mtest_id_func');

//Some debuging going on here.
function vidyen_load_mtest_id_shortcode_func($atts)
{

  $atts = shortcode_atts(
		array(
				'mtest_id' => 'empty'
		), $atts, 'vyps-mtest-query' );

    $mtest_user_id = $atts['mtest_id'];

    return vidyen_mmo_mtest_user_query_func($mtest_user_id);
}

add_shortcode( 'vyps-mtest-query', 'vidyen_load_mtest_id_shortcode_func');
