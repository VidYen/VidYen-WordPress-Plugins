<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This is specifically designed to just read the SQL settings once to minimize the SQL hits.
function vidyen_woocommerce_currencies_settings()
{
  global $wpdb;

  //grab table name
  $table_name_woocommerce_currencies = $wpdb->prefix . 'vidyen_woocommerce_currencies';

  $index = 1; //Well first row

  //SQL query of current row on settings table
  $woocommerce_currencies_data_query = "SELECT * FROM ". $table_name_woocommerce_currencies . " WHERE id = %d";
  $woocommerce_currencies_data_query_prepared = $wpdb->prepare( $woocommerce_currencies_data_query, $index );
  $woocommerce_currencies_data = $wpdb->get_results($woocommerce_currencies_data_query_prepared);

  foreach ($woocommerce_currencies_data as $result)
  {
    $currency_name = $result->currency_name;
    $currency_symbol = $result->currency_symbol;

    //Array parsing to cram it into multi dimensional row
    //TODO: Add index names and not numbers for second part!
    $woocommerce_currencies_parsed_array[$index]['currency_name'] = $currency_name;
    $woocommerce_currencies_parsed_array[$index]['currency_symbol'] = $currency_symbol;

    $index++; //Technically it should be only one row unless I screwed up.
  }
  return $woocommerce_currencies_parsed_array;
}
