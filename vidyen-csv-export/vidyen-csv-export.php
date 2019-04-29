<?php
/**
 * CSV Exporter bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           vyps csv export
 *
 * @wordpress-plugin
 * Plugin Name:       VidYen CSV Export
 * Plugin URI:        https://github.com/VidYen/VidYen-WordPress-Plugins
 * Description:       Exports the VidYen Point Log to CSV
 * Version:           1.0.0
 * Author:            VidYen, LLC
 * Author URI:        http://vidyen.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       csv-export
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Added prepare() to all SQL SELECT calls 7.1.2018 */

/* Main Public Log shortcode function */

function vyps_csv()
{

	/* Technically users don't have to be logged in
	* Should litterally be the log the admin sees
	* I don't care. Tell users to not put personal identificable
	* information in their user name (referred to PID in the health care industry)
	*/

	//Shortcode stuff
	//I'm going to eventually have site admins set logs for activities like reason etc and the meta fields, but for now.
	$atts =
		array(
				'pid' => '0',
				'reason' => '0',
				'rows' => 10000000,
				'bootstrap' => 'no',
				'userid' => 0,
				'uid' => FALSE,
				'admin' => FALSE,
				'current' => FALSE,
				'pages' => 10, //How many pages will have
	 );

	$point_id = $atts['pid'];
	$reason = $atts['reason'];
	$table_row_limit = $atts['rows']; //50 by default
	$user_id = $atts['userid'];
	$uid_on = $atts['uid'];
	$boostrap_on = $atts['bootstrap'];
	$admin_on = $atts['admin'];
	$current_user_state = $atts['current'];
	$max_pages = $atts['pages'];
	$max_pages_middle = intval($max_pages/2); //The middle in theory. I guess?

	//So users can see their own transcations, I'm putting this shortcode hoook in.
	if ($current_user_state == TRUE)
	{
		$user_id = get_current_user_id(); //Over riding the current userid to show just the current user. I have no idea if this actually works as may have not set it up correctly.
	}

	global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_users = $wpdb->prefix . 'users';

	function insert( $table, $reason_data, $format = null ) {
    return $this->_insert_replace_helper( $table, $reason_data, $format, 'INSERT' );
}
	//BTW the number of IDs should always match the number of rows, NO EXCEPTIONS. If it doesn't it means the admin deleted a row
	//And that is against the psuedo-blockchain philosophy. //Also it dawned on me I can rewrite the public log here.

	//$number_of_log_rows = $wpdb->get_var( "SELECT max( id ) FROM $table_name_log" ); //No WHERE needed. All rows. No exceptions
  $number_of_log_rows_query = "SELECT max( id ) FROM ". $table_name_log;  //I'm wondering if a prepare is even needed, but throw it all in.
  $number_of_log_rows = $wpdb->get_var( $number_of_log_rows_query ); //Ok. I realized that not only prepare() doesn't work it, there is no varialbes needed to sanitize as the table name is actually hard coded.

	$amount_of_pages = ceil($number_of_log_rows / $table_row_limit); //So we know how many rows and we divide it by whatever it is and round up if not even as means maybe like one extra item over?

	//$number_of_point_rows = $wpdb->get_var( "SELECT max( id ) FROM $table_name_points" ); //No WHERE needed. All rows. No exceptions
  $number_of_point_rows_query = "SELECT max( id ) FROM ". $table_name_points;  //I'm wondering if a prepare is even needed, but throw it all in.
  $number_of_point_rows = $wpdb->get_var( $number_of_point_rows_query ); //Same issue as line 33. No real user input involved. Just server variables.

	//This will be set by the rows atts above eventually
	$begin_row = 2;
	$end_row = ''; //Eventually will have admin ability to filter how many rows they see as after 1000 may be intensive

	/* Although normally against totally going programatic. Since I know I'm going to reuse this for the public log I'm going to put the headers into variables */
	/* For public log the user_name should be display name and no need to see the UID and PID */
	$transaction_id = "Transaction ID";
	$date_label = "Date";
	$display_name_label = "Display Name";
	$user_id_label = "UID";
	$point_type_label = '"Point Type"';
	$point_id_label = "PID";
	$amount_label = "Amount";
	$reason_label = "Adjustment Reason";

	//this code below checks the gets and determines the page nation
	if (isset($_GET['action']))
	{
		$page_number = intval(htmlspecialchars($_GET['action']));
	}
	else
	{
		$page_number = 1; //Well... Always first.
	}

	//Adding the UID option to show in the admin panel of if the admin wants to turn on the public log for some reason.
	//NOTE: have decided to use the function log as the log itself.
	if ( $uid_on == TRUE )
	{
		$uid_label_row = "<th>$user_id_label</th>";
	}
	else
	{
		$uid_label_row = ""; //Defined and set to blank to if need to display.
	}

	//this is what it's goint to be called
	$table_output = "";

	//Ok I got logic here that I think will work. the > will always be $table_range_stop = $number_of_log_rows - ($number_of_log_rows - $table_row_limit ) or $current_rows_output number.
	//OLD: for ($x_for_count = $number_of_log_rows; $x_for_count > 0; $x_for_count = $x_for_count -1 ) {
	$table_range_start = $number_of_log_rows -( $table_row_limit * ( $page_number - 1 )); //Hrm... This doesn't seem like it will work.
	$table_range_stop = $number_of_log_rows - ($table_row_limit * $page_number); //I'm thinking oddly here but this should be higher.

	//Ok a catch stop for pages with more than 0 items
	if ( $table_range_stop < 1 ){

				$table_range_stop = 1; //If we go below 1, then just hard floor it at 1 as no 0 or negative transaction numbers exists.
	}

	//Setting a check if its not the current user check via short code, then we just run the following code as normal.
	if ($current_user_state != TRUE)
	{
		//The number of log rows will always but correct but its the starting point and end points that will change.
		for ($x_for_count = $table_range_start; $x_for_count >= $table_range_stop; $x_for_count = $x_for_count - 1 ) //I'm counting backwards. Also look what I did. Also also, there should never be a 0 id or less than 1
		{
			//$date_data = $wpdb->get_var( "SELECT time FROM $table_name_log WHERE id= '$x_for_count'" ); //Straight up going to brute force this un-programatically not via entire row
			$date_data_query = "SELECT time FROM ". $table_name_log . " WHERE id = %d";
			$date_data_query_prepared = $wpdb->prepare( $date_data_query, $x_for_count );
			$date_data = $wpdb->get_var( $date_data_query_prepared );

			//$user_id_data = $wpdb->get_var( "SELECT user_id FROM $table_name_log WHERE id= '$x_for_count'" );
			$user_id_data_query = "SELECT user_id FROM ". $table_name_log . " WHERE id = %d";
			$user_id_data_query_prepared = $wpdb->prepare( $user_id_data_query, $x_for_count );
			$user_id_data = $wpdb->get_var( $user_id_data_query_prepared );
			$user_id_validated = intval($user_id_data); //I added this extra line to make the return an int as it wasn't being compared correctly as was coming out as a string not a number.

			//$display_name_data = $wpdb->get_var( "SELECT display_name FROM $table_name_users WHERE id= '$user_id_data'" ); //And this is why I didn't call it the entire row by arrow. We are in 4d with multiple tables
			$display_name_data_query = "SELECT display_name FROM ". $table_name_users . " WHERE id = %d"; //Note: Pulling from WP users table
			$display_name_data_query_prepared = $wpdb->prepare( $display_name_data_query, $user_id_data );
			$display_name_data = $wpdb->get_var( $display_name_data_query_prepared );

			//$point_id_data = $wpdb->get_var( "SELECT point_id FROM $table_name_log WHERE id= '$x_for_count'" );
			$point_id_data_query = "SELECT point_id FROM ". $table_name_log . " WHERE id = %d";
			$point_id_data_query_prepared = $wpdb->prepare( $point_id_data_query, $x_for_count );
			$point_id_data = $wpdb->get_var( $point_id_data_query_prepared );

			//$point_type_data = $wpdb->get_var( "SELECT name FROM $table_name_points WHERE id= '$point_id_data'" );
			$point_type_data_query = "SELECT name FROM ". $table_name_points . " WHERE id = %d";
			$point_type_data_query_prepared = $wpdb->prepare( $point_type_data_query, $point_id_data );
			$point_type_data = $wpdb->get_var( $point_type_data_query_prepared );

			//$amount_data = $wpdb->get_var( "SELECT points_amount FROM $table_name_log WHERE id= '$x_for_count'" );
	    $amount_data_query = "SELECT points_amount FROM ". $table_name_log . " WHERE id = %d";
	    $amount_data_query_prepared = $wpdb->prepare( $amount_data_query, $x_for_count );
	    $amount_data = $wpdb->get_var( $amount_data_query_prepared );

			//$reason_data = $wpdb->get_var( "SELECT reason FROM $table_name_log WHERE id= '$x_for_count'" );
	    $reason_data_query = "SELECT reason FROM ". $table_name_log . " WHERE id = %d";
	    $reason_data_query_prepared = $wpdb->prepare( $reason_data_query, $x_for_count );
	    $reason_data = $wpdb->get_var( $reason_data_query_prepared );

			//If statement to pop in the UID if There
			//Just popping in to the table if there. Hopefully it doesn't blow up the existing table
		$uid_data_row = $user_id_data;

			$current_row_output = ("\"$x_for_count\", \"$reason_data\", \"$uid_data_row\", \"$date_data\", \"$point_type_data\", \"$amount_data\", \"$display_name_data\" \n");

			//Code inserted to see if a user id was specified. If so, we are creating a table just for that user_id.
			if( $user_id == 0)
			{
				//Compile into row output.
				$table_output = $table_output . $current_row_output; //I like my way that is more reasonable instead of .=
			}
			elseif ( $user_id_validated == $user_id AND $user_id > 0)
			{
				//The idea above is to see if the query for the sql UID pull (validated) $user_id is the same as the query AND is greater than zero.
				//In theory, you could put a negative number in, but not sure why, but never trust your users not to try.
				//I believe there should be either it is 0 or above zero and equals but never anything else so we should be good.
				$table_output = $table_output . $current_row_output; //I like my way that is more reasonable instead of .=
			}
		}

		if (isset($_GET[ "download_file" ]))
		{
	 	return	file_put_contents("file.txt", $table_output);
		}
		return $table_output;
		//NOTE: I'm Normally against this but this warrants two outputs
		//The page output
		/*return "
			<div class=\"wrap\">
				<table class=\"wp-list-table widefat fixed striped users\">
					$table_output
				</table>
				<b><form method=\"post\">
					<input type=\"hidden\" value=\"\" name=\"download_file\">
					<input type=\"submit\" class=\"button-secondary\" value=\"Get Reward\">
				</form></b>
			</div>
		";*/

	} //End of if its not current user
}
class CSVExport {

  /**
   * Constructor
   */
  public function __construct() {
    if (isset($_GET['report'])) {

      $csv = $this->generate_csv();
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: private", false);
      header("Content-Type: application/octet-stream");
      header("Content-Disposition: attachment; filename=\"report.csv\";");
      header("Content-Transfer-Encoding: binary");

      echo $csv;
      exit;
    }

// Add extra menu items for admins
    add_action('admin_menu', array($this, 'admin_menu'));

// Create end-points
    add_filter('query_vars', array($this, 'query_vars'));
    add_action('parse_request', array($this, 'parse_request'));
  }

  /**
   * Add extra menu items for admins
   */
  public function admin_menu() {
    add_submenu_page('vyps_points', 'Download Report', 'Download Report', 'manage_options', 'download_report', array($this, 'download_report'));
  }
  /**
   * Allow for custom query variables
   */
  public function query_vars($query_vars) {
    $query_vars[] = 'download_report';
    return $query_vars;
  }

  /**
   * Parse the request
   */
  public function parse_request(&$wp) {
    if (array_key_exists('download_report', $wp->query_vars)) {
      $this->download_report();
      exit;
    }
  }

  /**
   * Download report
   */
  public function download_report() {
    echo '<div class="wrap">';
    echo '<div id="icon-tools" class="icon32">
</div>';
    echo '<h2>Download Report</h2>';
    echo '<p><a href="?page=download_report&report=users">Export to CSV</a></p>';
  }

  /**
   * Converting data to CSV
   */
  public function generate_csv() {
    $csv_output = vyps_csv();
    return $csv_output;
  }

}

// Instantiate a singleton of this plugin
$csvExport = new CSVExport($table_output);
