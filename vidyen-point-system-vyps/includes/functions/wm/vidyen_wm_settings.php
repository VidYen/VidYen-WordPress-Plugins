<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This is specifically designed to just read the SQL settings once to minimize the SQL hits.
function vidyen_vy_wm_settings()
{
  global $wpdb;

  //grab table name
  $table_name_wm = $wpdb->prefix . 'vidyen_wm_settings';

  $index = 1; //Well first row

  //SQL query of current row on settings table
  $wm_data_query = "SELECT * FROM ". $table_name_wm . " WHERE id = %d";
  $wm_data_query_prepared = $wpdb->prepare( $wm_data_query, $index );
  $wm_data = $wpdb->get_results($wm_data_query_prepared);

  foreach ($wm_data as $result)
  {
    $button_text = $result->button_text;
    $disclaimer_text = $result->disclaimer_text;
    $eula_text = $result->eula_text;
    $login_text = $result->login_text;
    $login_url = $result->login_url;
    $current_wmp = $result->current_wmp;
    $current_pool = $result->current_pool;
    $site_name = $result->site_name;
    $crypto_wallet = $result->crypto_wallet;
    $hash_per_point = $result->hash_per_point;
    $point_id = $result->point_id;
    $graphic_selection = $result->graphic_selection;
    $wm_pro_active = $result->wm_pro_active;
    $wm_woo_active = $result->wm_woo_active;
    $wm_threads = $result->wm_threads;
    $wm_cpu = $result->wm_cpu;
    $discord_webhook = $result->discord_webhook;
    $discord_text = $result->discord_text;
    $youtube_url = $result->youtube_url;
    $custom_wmp = $result->custom_wmp;
    $wm_threads_low = $result->wm_threads_low;
    $wm_cpu_low = $result->wm_cpu_low;
    $wm_threads_medium = $result->wm_threads_medium;
    $wm_cpu_medium = $result->wm_cpu_medium;
    $wm_threads_high = $result->wm_threads_high;
    $wm_cpu_high = $result->wm_cpu_high;
    //Array parsing to cram it into multi dimensional row
    //TODO: Add index names and not numbers for second part!
    $vy_wm_parsed_array[$index]['button_text'] = $button_text;
    $vy_wm_parsed_array[$index]['disclaimer_text'] = $disclaimer_text;
    $vy_wm_parsed_array[$index]['eula_text'] = $eula_text;
    $vy_wm_parsed_array[$index]['login_text'] = $login_text;
    $vy_wm_parsed_array[$index]['login_url'] = $login_url;
    $vy_wm_parsed_array[$index]['current_wmp'] = $current_wmp;
    $vy_wm_parsed_array[$index]['current_pool'] = $current_pool;
    $vy_wm_parsed_array[$index]['site_name'] = $site_name;
    $vy_wm_parsed_array[$index]['crypto_wallet'] = $crypto_wallet;
    $vy_wm_parsed_array[$index]['hash_per_point'] = $hash_per_point;
    $vy_wm_parsed_array[$index]['point_id'] = $point_id;
    $vy_wm_parsed_array[$index]['graphic_selection'] = $graphic_selection;
  	$vy_wm_parsed_array[$index]['wm_pro_active'] = $wm_pro_active;
    $vy_wm_parsed_array[$index]['wm_woo_active'] = $wm_woo_active;
    $vy_wm_parsed_array[$index]['wm_threads'] = $wm_threads;
    $vy_wm_parsed_array[$index]['wm_cpu'] = $wm_cpu;
    $vy_wm_parsed_array[$index]['discord_webhook'] = $discord_webhook;
    $vy_wm_parsed_array[$index]['discord_text'] = $discord_text;
    $vy_wm_parsed_array[$index]['youtube_url'] = $youtube_url;
    $vy_wm_parsed_array[$index]['custom_wmp'] = $custom_wmp;
    $vy_wm_parsed_array[$index]['wm_threads_low'] = $wm_threads_low;
    $vy_wm_parsed_array[$index]['wm_cpu_low'] = $wm_cpu_low;
    $vy_wm_parsed_array[$index]['wm_threads_medium'] = $wm_threads_medium;
    $vy_wm_parsed_array[$index]['wm_cpu_medium'] = $wm_cpu_medium;
    $vy_wm_parsed_array[$index]['wm_threads_high'] = $wm_threads_high;
    $vy_wm_parsed_array[$index]['wm_cpu_high'] = $wm_cpu_high;

    $index++; //Technically it should be only one row unless I screwed up.
  }
  return $vy_wm_parsed_array;
}
