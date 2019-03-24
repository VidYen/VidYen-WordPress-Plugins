<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//vyps_point_func.php
//These are just functions to call the point name and icon to be called later.
//Simply returns either the point type name or icon url
//Seem to be doing it a lot so might as well be professional about code now instead of copying and pasting every time

/*** POINT NAME FUNCTION ***/
function vyps_point_name_func($point_id) {

  //The usual suspects to get the sql calls up
  global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';

  //Find the name of point id from sql
  $sourceName_query = "SELECT name FROM ". $table_name_points . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
  $sourceName_query_prepared = $wpdb->prepare( $sourceName_query, $point_id );
  $sourceName = $wpdb->get_var( $sourceName_query_prepared );

  //Return it out as a string.
  return $sourceName;
}
