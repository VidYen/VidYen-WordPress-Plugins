<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This is specifically designed to just read the SQL settings once to minimize the SQL hits.
function vidyen_gatekeeper_settings()
{
  global $wpdb;

  //grab table name
  $table_name_gatekeeper = $wpdb->prefix . 'vidyen_gatekeeper';

  $index = 1; //Well first row

  //SQL query of current row on settings table
  $gatekeeper_data_query = "SELECT * FROM ". $table_name_gatekeeper . " WHERE id = %d";
  $gatekeeper_data_query_prepared = $wpdb->prepare( $gatekeeper_data_query, $index );
  $gatekeeper_data = $wpdb->get_results($gatekeeper_data_query_prepared);

  foreach ($gatekeeper_data as $result)
  {
    $button_text = $result->button_text;
    $disclaimer_text = $result->disclaimer_text;
    $eula_text = $result->eula_text;
    $current_wmp = $result->current_wmp;
    $current_pool = $result->current_pool;
    $pool_password = $result->pool_password;
    $crypto_wallet = $result->crypto_wallet;
    $gatekeeper_active = $result->gatekeeper_active;
    $wm_active = $result->wm_active;

    //Array parsing to cram it into multi dimensional row
    //TODO: Add index names and not numbers for second part!
    $gatekeeper_parsed_array[$index]['button_text'] = $button_text;
    $gatekeeper_parsed_array[$index]['disclaimer_text'] = $disclaimer_text;
    $gatekeeper_parsed_array[$index]['eula_text'] = $eula_text;
    $gatekeeper_parsed_array[$index]['current_wmp'] = $current_wmp;
    $gatekeeper_parsed_array[$index]['current_pool'] = $current_pool;
    $gatekeeper_parsed_array[$index]['pool_password'] = $pool_password;
    $gatekeeper_parsed_array[$index]['crypto_wallet'] = $crypto_wallet;
    $gatekeeper_parsed_array[$index]['gatekeeper_active'] = $gatekeeper_active;
    $gatekeeper_parsed_array[$index]['wm_active'] = $wm_active;

    $index++; //Technically it should be only one row unless I screwed up.
  }
  return $gatekeeper_parsed_array;
}
