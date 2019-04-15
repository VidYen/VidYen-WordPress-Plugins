<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** AJAX PHP TO MAKE COOKIE ***/

// register the ajax action for authenticated users
add_action('wp_ajax_vy_vidhash_consent_action', 'vy_vidhash_consent_action');

//register the ajax for non authenticated users
add_action( 'wp_ajax_nopriv_vy_vidhash_consent_action', 'vy_vidhash_consent_action' );

// handle the ajax request
function vy_vidhash_consent_action()
{
  global $wpdb; // this is how you get access to the database

  //We are goign to set a cookie
  $cookie_name = "vidhashconsent";
  $cookie_value = "consented";
  setcookie($cookie_name, $cookie_value, time() + (86400 * 28), "/"); //Set for 28 days Perfect 13 months

  wp_die(); // this is required to terminate immediately and return a proper response
}

/*** Fix for the ajaxurl not found with custom template sites ***/
add_action('wp_head', 'vidyen_vidhash_plugin_ajaxurl');

function vidyen_vidhash_plugin_ajaxurl()
{
   echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}
