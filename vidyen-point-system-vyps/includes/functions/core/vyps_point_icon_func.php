<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Moved the point icon here so easier for me to find. I need to find the point icon

/*** POINT ICON FUNCTION ***/
function vyps_point_icon_func($point_id)
{
  //The usual suspects to get the sql calls up
  global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';

  //Find the name of point id from sql. NOTE: I did it twice here because if get the icon I need name as well for OCD reasons.
  $sourceName_query = "SELECT name FROM ". $table_name_points . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
  $sourceName_query_prepared = $wpdb->prepare( $sourceName_query, $point_id );
  $sourceName = $wpdb->get_var( $sourceName_query_prepared );

  //Get the source icon url
  $sourceIcon_query = "SELECT icon FROM ". $table_name_points . " WHERE id= %d";
  $sourceIcon_query_prepared = $wpdb->prepare( $sourceIcon_query, $point_id );
  $sourceIcon = $wpdb->get_var( $sourceIcon_query_prepared );

  //since 99.9999% of time I need to format it, I'm going to put it in here as well.
  //It's not like I couldn't just return the above and not the below in its own function later
  $icon_html = "<img src=\"$sourceIcon\" width=\"16\" hight=\"16\" title=\"$sourceName\">";

  return $icon_html;
}
