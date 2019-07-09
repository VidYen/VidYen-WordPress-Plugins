<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** AJAX PHP TO MAKE COOKIE ***/

// register the ajax action for authenticated users
add_action('wp_ajax_vidyen_gatekeeper_set_cookie_action', 'vidyen_gatekeeper_set_cookie_action');

//register the ajax for non authenticated users
add_action( 'wp_ajax_nopriv_vidyen_gatekeeper_set_cookie_action', 'vidyen_gatekeeper_set_cookie_action' );

// handle the ajax request
function vidyen_gatekeeper_set_cookie_action()
{
  global $wpdb; // this is how you get access to the database

  //We are goign to set a cookie
  $cookie_name = "vidyengatekeeperconsent";
  $cookie_value = "consented";
  setcookie($cookie_name, $cookie_value, time() + (86400), "/"); //Cookie good for 24 hours. Perhaps this can be a global setting?

  wp_die(); // this is required to terminate immediately and return a proper response
}

/*** Fix for the ajaxurl not found with custom template sites ***/
add_action('wp_head', 'vidyen_gatekeeper_set_ajaxurl');

function vidyen_gatekeeper_set_ajaxurl()
{
   echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}
