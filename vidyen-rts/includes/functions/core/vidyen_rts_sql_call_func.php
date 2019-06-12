<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
** This is where we pull the sql call functions from
** There are no attributes or input since its hard coded.
** Yes, it was a lot of copying and pasting.
*/

/*** Currency ID SQL Call ***/
function vyps_rts_sql_currency_id_func()
{
	if (isset($_SESSION["currency_id"]))
	{
		$point_id = $_SESSION["currency_id"];
		return $point_id;
	}
	else
	{
		global $wpdb;

		//the $wpdb stuff to find what the current name and icons are
		$table_name_rts = $wpdb->prefix . 'vidyen_rts';

		$first_row = 1; //Note sure why I'm setting this.

		//Input ID pull
		$point_id_query = "SELECT currency_id FROM ". $table_name_rts . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
		$point_id = $wpdb->get_var( $point_id_query_prepared );

		$point_id = intval($point_id); //Extra sanitzation
		$_SESSION["currency_id"] = $point_id;
		return $point_id;
	}
}

/************START LIGHT SOLIDER FUNCTIONS********/
/*** LIght Soldier ID SQL Call ***/
function vyps_rts_sql_light_soldier_id_func()
{
	if (isset($_SESSION["light_solider_id"]))
	{
		$point_id = $_SESSION["light_solider_id"];
		return $point_id;
	}
	else
	{
		global $wpdb;

		//the $wpdb stuff to find what the current name and icons are
		$table_name_rts = $wpdb->prefix . 'vidyen_rts';

		$first_row = 1; //Note sure why I'm setting this.

		//Input ID pull
		$point_id_query = "SELECT light_soldier_id FROM ". $table_name_rts . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
		$point_id = $wpdb->get_var( $point_id_query_prepared );
		$_SESSION["light_solider_id"] = $point_id;
		$point_id = intval($point_id); //Extra sanitzation

		return $point_id;
	}
}

/*** LIght Soldier COST SQL Call ***/
function vyps_rts_sql_light_soldier_cost_func()
{
	if (isset($_SESSION["light_solider_cost"]))
	{
		$point_id = $_SESSION["light_solider_cost"];
		return $point_id;
	}
	else
	{
		global $wpdb;

		//the $wpdb stuff to find what the current name and icons are
		$table_name_rts = $wpdb->prefix . 'vidyen_rts';

		$first_row = 1; //Note sure why I'm setting this.

		//Input ID pull
		$point_id_query = "SELECT light_soldier_cost FROM ". $table_name_rts . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
		$point_id = $wpdb->get_var( $point_id_query_prepared );

		$point_id = intval($point_id); //Extra sanitzation
		$_SESSION["light_solider_cost"] = $point_id;
		return $point_id;
	}
}

/*** LIght Soldier Time SQL Call ***/
function vyps_rts_sql_light_soldier_time_func()
{
	if (isset($_SESSION["light_solider_time"]))
	{
		$point_id = $_SESSION["light_solider_time"];
		return $point_id;
	}
	else
	{
		global $wpdb;

		//the $wpdb stuff to find what the current name and icons are
		$table_name_rts = $wpdb->prefix . 'vidyen_rts';

		$first_row = 1; //Note sure why I'm setting this.

		//Input ID pull
		$point_id_query = "SELECT light_soldier_time FROM ". $table_name_rts . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
		$point_id = $wpdb->get_var( $point_id_query_prepared );

		$point_id = intval($point_id); //Extra sanitzation
		$_SESSION["light_solider_time"] = $point_id;
		return $point_id;
	}
}

/************NOTE: END LIGHT SOLIDER FUNCTIONS********/


/************START Laborer FUNCTIONS********/
/*** Laborer ID SQL Call ***/
function vyps_rts_sql_laborer_id_func()
{
	if (isset($_SESSION["laborer_id"]))
	{
		$point_id = $_SESSION["laborer_id"];
		return $point_id;
	}
	else
	{
		global $wpdb;

		//the $wpdb stuff to find what the current name and icons are
		$table_name_rts = $wpdb->prefix . 'vidyen_rts';

		$first_row = 1; //Note sure why I'm setting this.

		//Input ID pull
		$point_id_query = "SELECT laborer_id FROM ". $table_name_rts . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
		$point_id = $wpdb->get_var( $point_id_query_prepared );

		$point_id = intval($point_id); //Extra sanitzation
		$_SESSION["laborer_id"] = $point_id;
		return $point_id;
	}
}

/*** Laborer COST SQL Call ***/
function vyps_rts_sql_laborer_cost_func()
{
	if (isset($_SESSION["laborer_cost"]))
	{
		$point_id = $_SESSION["laborer_cost"];
		return $point_id;
	}
	else
	{
		global $wpdb;

		//the $wpdb stuff to find what the current name and icons are
		$table_name_rts = $wpdb->prefix . 'vidyen_rts';

		$first_row = 1; //Note sure why I'm setting this.

		//Input ID pull
		$point_id_query = "SELECT laborer_cost FROM ". $table_name_rts . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
		$point_id = $wpdb->get_var( $point_id_query_prepared );

		$point_id = intval($point_id); //Extra sanitzation
		$_SESSION["laborer_cost"] = $point_id;
		return $point_id;
	}
}

/*** Laborer Time SQL Call ***/
function vyps_rts_sql_laborer_time_func()
{
	if (isset($_SESSION["laborer_time"]))
	{
		$point_id = $_SESSION["laborer_time"];
		return $point_id;
	}
	else
	{
		global $wpdb;

		//the $wpdb stuff to find what the current name and icons are
		$table_name_rts = $wpdb->prefix . 'vidyen_rts';

		$first_row = 1; //Note sure why I'm setting this.

		//Input ID pull
		$point_id_query = "SELECT laborer_time FROM ". $table_name_rts . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
		$point_id = $wpdb->get_var( $point_id_query_prepared );

		$point_id = intval($point_id); //Extra sanitzation
		$_SESSION["laborer_time"] = $point_id;
		return $point_id;
	}
}

/************NOTE: END Laborer FUNCTIONS********/


/************NOTE: BEGIN Resource functions FUNCTIONS********/

/*** Wood ID SQL Call ***/
function vyps_rts_sql_wood_id_func()
{
	if (isset($_SESSION["wood_id"]))
	{
		$point_id = $_SESSION["wood_id"];
		return $point_id;
	}
	else
	{
		global $wpdb;

		//the $wpdb stuff to find what the current name and icons are
		$table_name_rts = $wpdb->prefix . 'vidyen_rts';

		$first_row = 1; //Note sure why I'm setting this.

		//Input ID pull
		$point_id_query = "SELECT wood_id FROM ". $table_name_rts . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
		$point_id = $wpdb->get_var( $point_id_query_prepared );

		$point_id = intval($point_id); //Extra sanitzation
		$_SESSION["wood_id"] = $point_id;
		return $point_id;
	}
}

/*** Iron ID SQL Call ***/
function vyps_rts_sql_iron_id_func()
{
	if (isset($_SESSION["iron_id"]))
	{
		$point_id = $_SESSION["iron_id"];
		return $point_id;
	}
	else
	{
		global $wpdb;

		//the $wpdb stuff to find what the current name and icons are
		$table_name_rts = $wpdb->prefix . 'vidyen_rts';

		$first_row = 1; //Note sure why I'm setting this.

		//Input ID pull
		$point_id_query = "SELECT iron_id FROM ". $table_name_rts . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
		$point_id = $wpdb->get_var( $point_id_query_prepared );

		$point_id = intval($point_id); //Extra sanitzation
		$_SESSION["iron_id"] = $point_id;
		return $point_id;
	}
}

/*** Iron ID SQL Call ***/
function vyps_rts_sql_stone_id_func()
{
	if (isset($_SESSION["stone_id"]))
	{
		$point_id = $_SESSION["stone_id"];
		return $point_id;
	}
	else
	{
		global $wpdb;

		//the $wpdb stuff to find what the current name and icons are
		$table_name_rts = $wpdb->prefix . 'vidyen_rts';

		$first_row = 1; //Note sure why I'm setting this.

		//Input ID pull
		$point_id_query = "SELECT stone_id FROM ". $table_name_rts . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
		$point_id = $wpdb->get_var( $point_id_query_prepared );

		$point_id = intval($point_id); //Extra sanitzation
		$_SESSION["stone_id"] = $point_id;
		return $point_id;
	}
}

/************NOTE: END Resource functions FUNCTIONS********/


/*** Village ID SQL Call ***/
function vyps_rts_sql_village_id_func()
{
	if (isset($_SESSION["village_id"]))
	{
		$point_id = $_SESSION["village_id"];
		return $point_id;
	}
	else
	{
		global $wpdb;

		//the $wpdb stuff to find what the current name and icons are
		$table_name_rts = $wpdb->prefix . 'vidyen_rts';

		$first_row = 1; //Note sure why I'm setting this.

		//Input ID pull
		$point_id_query = "SELECT village_id FROM ". $table_name_rts . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
		$point_id = $wpdb->get_var( $point_id_query_prepared );

		$point_id = intval($point_id); //Extra sanitzation
		$_SESSION["village_id"] = $point_id;
		return $point_id;
	}
}

/*** village_burning ID SQL Call ***/
function vyps_rts_sql_village_burning_id_func()
{
	if (isset($_SESSION["village_burning_id"]))
	{
		$point_id = $_SESSION["village_burning_id"];
		return $point_id;
	}
	else
	{
		global $wpdb;

		//the $wpdb stuff to find what the current name and icons are
		$table_name_rts = $wpdb->prefix . 'vidyen_rts';

		$first_row = 1; //Note sure why I'm setting this.

		//Input ID pull
		$point_id_query = "SELECT village_burning_id FROM ". $table_name_rts . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
		$point_id = $wpdb->get_var( $point_id_query_prepared );

		$point_id = intval($point_id); //Extra sanitzation
		$_SESSION["village_burning_id"] = $point_id;
		return $point_id;
	}
}

/*** Castle ID SQL Call ***/
function vyps_rts_sql_castle_id_func()
{
	if (isset($_SESSION["castle_id"]))
	{
		$point_id = $_SESSION["castle_id"];
		return $point_id;
	}
	else
	{
		global $wpdb;

		//the $wpdb stuff to find what the current name and icons are
		$table_name_rts = $wpdb->prefix . 'vidyen_rts';

		$first_row = 1; //Note sure why I'm setting this.

		//Input ID pull
		$point_id_query = "SELECT castle_id FROM ". $table_name_rts . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
		$point_id = $wpdb->get_var( $point_id_query_prepared );

		$point_id = intval($point_id); //Extra sanitzation
		$_SESSION["castle_id"] = $point_id;
		return $point_id;
	}
}

/*** Barracks ID SQL Call ***/
function vyps_rts_sql_barracks_id_func()
{
	if (isset($_SESSION["barracks_id"]))
	{
		$point_id = $_SESSION["barracks_id"];
		return $point_id;
	}
	else
	{
		global $wpdb;

		//the $wpdb stuff to find what the current name and icons are
		$table_name_rts = $wpdb->prefix . 'vidyen_rts';

		$first_row = 1; //Note sure why I'm setting this.

		//Input ID pull
		$point_id_query = "SELECT barracks_id FROM ". $table_name_rts . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
		$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
		$point_id = $wpdb->get_var( $point_id_query_prepared );

		$point_id = intval($point_id); //Extra sanitzation
		$_SESSION["barracks_id"] = $point_id;
		return $point_id;
	}
}
