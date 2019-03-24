<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
** This is where we pull the sql call functions from
** There are no attributes or input since its hard coded.
** I am keeping in same file since they are rather small calls. Will break apart if decide to do something dumb like exapnd on them. -Felty
*/

/*** POINT ID SQL Call ***/
function vyps_mmo_sql_point_id_func()
{
	global $wpdb;

	//the $wpdb stuff to find what the current name and icons are
	$table_name_wc_mmo = $wpdb->prefix . 'vidyen_wc_mmo';

	$first_row = 1; //Note sure why I'm setting this.

	//Input ID pull
	$point_id_query = "SELECT point_id FROM ". $table_name_wc_mmo . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
	$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
	$point_id = $wpdb->get_var( $point_id_query_prepared );

	$point_id = intval($point_id); //Extra sanitzation

	return $point_id;
}

/*** INPUT AMOUNT SQL Call ***/
function vyps_mmo_sql_input_amount_func()
{
	global $wpdb;

	//the $wpdb stuff to find what the current name and icons are
	$table_name_wc_mmo = $wpdb->prefix . 'vidyen_wc_mmo';

	$first_row = 1; //Note sure why I'm setting this.

	//Input Amount
	$input_amount_query = "SELECT input_amount FROM ". $table_name_wc_mmo . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
	$input_amount_query_prepared = $wpdb->prepare( $input_amount_query, $first_row );
	$input_amount = $wpdb->get_var( $input_amount_query_prepared );


	$input_amount = intval($input_amount); //Extra sanitzation

	return $input_amount;
}

/*** OUTPUT ID SQL Call ***/
function vyps_mmo_sql_ouput_id_func()
{
	global $wpdb;

	//the $wpdb stuff to find what the current name and icons are
	$table_name_wc_mmo = $wpdb->prefix . 'vidyen_wc_mmo';

	$first_row = 1; //Note sure why I'm setting this.

	//Ouput Amount NOTE: For now this is just for WooWallet
	$ouput_id_query = "SELECT ouput_id FROM ". $table_name_wc_mmo . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
	$ouput_id_query_prepared = $wpdb->prepare( $ouput_id_query, $first_row );
	$ouput_id = $wpdb->get_var( $ouput_id_query_prepared );


	$ouput_id = intval($ouput_id); //Extra sanitzation

	return $ouput_id;
}

/*** OUTPUT AMOUNT SQL Call ***/
function vyps_mmo_sql_output_amount_func()
{
	global $wpdb;

	//the $wpdb stuff to find what the current name and icons are
	$table_name_wc_mmo = $wpdb->prefix . 'vidyen_wc_mmo';

	$first_row = 1; //Note sure why I'm setting this.

	//Ouput Amount NOTE: For now this is just for WooWallet
	$output_amount_query = "SELECT output_amount FROM ". $table_name_wc_mmo . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
	$output_amount_query_prepared = $wpdb->prepare( $output_amount_query, $first_row );
	$output_amount = $wpdb->get_var( $output_amount_query_prepared );


	$output_amount = intval($output_amount); //Extra sanitzation

	return $output_amount;
}
