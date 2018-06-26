<?php
   /*
   Plugin Name: VYPS Leaderboard 
   Description: Leaderboards shortcode for the VYPS plugin system
   Version: 0.0.05
   Author: VidYen, LLC (Contract work by Curtis D. Mimes)
   Author URI: https://vidyen.com/
   License: GPLv2 or later
   */

   /*
   * Contract work by: Curtis D. Mimes
   * On upwork
   */


   /*
	pointId:  from wp_vyps_point
	adjustmentReason: reason on wp_vyps_point_log
	rank:  top, current, bottom
	limit: how many results to include
	abs:  if positive then only include psoitive adjustments, if negative only include negative adjustments.  otherwise nothing
   */

$path = $_SERVER['DOCUMENT_ROOT']; 
include_once $path . '/wp-load.php';

/* Code added later for menu -Felty
*  Checking to see if VYPS is installed and run menus accordingly
*  Technically the shortcodes will still run, but the menus won't the shortcode instructions
*  and tell you VYPS not installed
*  Checking if VYPS active. Note. After looking around , it is better to check to see if function exists rather than plugin. 
*  So we know that if the vyps_points_menu exists, then it's installed. Otherwise, it could be uninstalled or it is, but
*  the admin did something they shouldn't have like rename the menu and broke everything.
*/

if (function_exists('vyps_points_menu')) {
	
	include( plugin_dir_path( __FILE__ ) . '../VYPS_lb/includes/lb_menu.php'); //This include creates the menu in the VYPS submenu

} else {
	
	include( plugin_dir_path( __FILE__ ) . '../VYPS_lb/includes/lb_no_vyps_menu.php'); //This include creates it on top level to inform to install VYPS

}


/*
decided to write a simple query builder for fun.  this is mine, but feel free to use :)
*/
class SelectQueryBuilder
{
    // property declaration
    private $start = 'select ';
    private $end = ";";
    private $whereArray = [];
    private $fromArray = [];
    private $selectArray = [];
    private $groupByArray = [];
    //todo: private $havingArray
    private $orderByArray = [];
    private $isAsc = false;
    private $limit = 99999999; //arbitrarily large number
    private $subQueryAlias = "";
    private $isSubQuery = false;
    private $variableArray = [];

    //for turning arrays of params into a comma seperated string
    //oof, maybe not efficient but we'll live 
    public static function paramsToString($array, $delim){
	    $result = implode(" {$delim} ", $array);
    	return $result;
    }

    // for adding conditions
    public function addWheres($whereArray) {
        foreach($whereArray as $where){
        	array_push($this->whereArray, $where);
        }
    }

    //for adding froms
    public function addFroms($fromArray){
    	foreach($fromArray as $from){
    		array_push($this->fromArray, $from);
    	}
    }

    //for adding groupbys
    public function addGroupBys($groupByArray){
    	foreach($groupByArray as $group){
    		array_push($this->groupByArray, $group);
    	}
    }

	//for adding orderbys
    public function addOrderBys($orderByArray){
    	foreach($orderByArray as $order){
    		array_push($this->orderByArray, $order);
    	}
    }

    //for adding select...things?
    public function addSelects($selectArray){
    	foreach($selectArray as $select){
    		array_push($this->selectArray, $select);
    	}
    }

    //this lets the thing know if it needs to wrap the output in parenthesis
    public function isNowSubquery(){
    	$this->start ='(select ';
    	$this->end=')';
    }

    //this wraps a given query string in a function
    //*NB* ONLY accepts a single param atm. ifyou want it to do more, go wild
    //should also be only for subqueries I think right now.
    public static function wrapQueryInFunction($queryString, $fnName, $param){
    	return "{$fnName}( " . $queryString . ", {$param})";
    }

    //for setting desc/asc
    public function setAscToTrue(){
    	$this->isAsc = true;
    }

    //set limit
    public function setLimit($limit){
    	$this->limit = $limit;
    }

    //add variables
    public function addVariable($varName, $varValue){
    	array_push($this->variableArray, array($varName,$varValue));
    }


    //doesn't work as intended, will fix late
    // public function setSubQueryAlias($alias){
    // 	$this->subQueryAlias = $alias;
    // 	//$this->end = ") as {$alias}";
    // }

    public function getQueryString(){
    	$output = "";

    	//first go ahead and declare anyvariables that are set before the query.
    	if(sizeof($this->variableArray) == 1){
    		$output .= "SET ";
	    	foreach($this->variableArray as $variableKeyPair){
				$output .= " @".$variableKeyPair[0] . " = " . $variableKeyPair[1] . ";"; 
	    	}
    	}elseif(sizeof($this->variableArray) > 1){
    		$output .= "SET ";
    		foreach($this->variableArray as $variableKeyPair){
	    		$output .= " @".$variableKeyPair[0] . " = " . $variableKeyPair[1] . ", "; // a comma instead of semicolon cause we're adding more
	    	}
	    	//add a final semi colon
	    	$output .= "; ";
    	}
    	

    	$output .= $this->start;

    	//add the select first
    	$output .= SelectQueryBuilder::paramsToString($this->selectArray, ",");
    	
    	//then the from
    	$output.= " from " . SelectQueryBuilder::paramsToString($this->fromArray, ",");
    	//then the where
    	if(sizeof($this->whereArray) > 0){
    		$output .= " where " . SelectQueryBuilder::paramsToString($this->whereArray, "and");
    	}
    	//then the group by
    	if(sizeof($this->groupByArray) > 0){
    		$output .=  " group by " . SelectQueryBuilder::paramsToString($this->groupByArray, ",");
    	}
		
    	//then the order
    	if(sizeof($this->orderByArray) > 0){
    		$output .= " order by " .SelectQueryBuilder::paramsToString($this->orderByArray, ",");
    	}
    	//then asc/desc
    	if($this->isAsc && !$this->isSubQuery){
    		$output .= " desc";
    	}else if($this->isAsc && !$this->isSubQuery){
    		$output .= " asc";
    	}

    	$output .= $this->end . (!(empty($this->subQueryAlias)) ? " as " . $this->subQueryAlias : "");

    	return $output;
    }
}

	/**
 * Take a WPDB query result and display it as a table, with headers from data keys.
 * This example only works with ARRAY_A type result from $wpdb query.
 * @param  array                $db_data Result from $wpdb query
 * @return bool                          Success, outputs table HTML
 * @author Tim Kinnane <tim@nestedcode.com>
 * @link   http://nestedcode.com
 */

remove_shortcode("vyps_leaderboard");
function data_table( $db_data ) {
	if ( !is_array( $db_data) || empty( $db_data ) ) return false;
	// Get the table header cells by formatting first row's keys
	$header_vals = array();
	$keys = array_keys( $db_data[0] );
	array_push($header_vals, 'Position');
	foreach ($keys as $row_key) {
		$header_vals[] = ucwords( str_replace( '_', ' ', $row_key ) ); // capitalise and convert underscores to spaces
	}
	$header = "<thead><tr><th>" . join( '</th><th>', $header_vals ) . "</th></tr></thead>";

	// Make the data rows
	$rows = array();
	$positionCounter = 1;
	foreach ( $db_data as $row ) {
		$row_vals = array();

		//add the position value
		array_push($row_vals, $positionCounter);

		//increment the counter to add a 'position' col
		$positionCounter++;
		foreach ($row as $key => $value) {

			// format any date values properly with WP date format
			if ( strpos( $key, 'date' ) !== false || strpos( $key, 'modified' ) !== false ) {
				$date_format = get_option( 'date_format' );
				$value = mysql2date( $date_format, $value );
			}

			//format the amount field
			if(ucwords($key) == 'Total'){
				$value = number_format($value);
			}
			$row_vals[] = $value;
		}
		
		$rows[] = "<tr><td>" . join( '</td><td>', $row_vals ) . "</td></tr>";
	}
	// Put the table together and output
	$result = '<table class="wp-list-table widefat fixed posts">' . $header . '<tbody>' . join( $rows ) . '</tbody></table>';
	return $result;
}

   function vypsSumPointLog($pointId="", $adjustmentReason="", $rank = 'top', $limit=10, $abs =''){

	   	//use wordpress fancy global
	   	global $wpdb;
	   	$pre = $wpdb->prefix;	
	   	//setup the query builder
	   	$builder = new SelectQueryBuilder();
	   	$wheres = [];

	   	//tells us what to add to the sql statement for the value of rank
	   	if($rank == "top"){
	   		$builder->setAscToTrue();
	   	}

	   	//add the pointId to wheres...if it's not just return missing param as string.
	   	if(!empty($pointId)){
	   		array_push($wheres, "{$pre}vyps_points_log.points = {$pointId}");
	   	}else{
	   		return "no point id defined in shortcode. ";
	   	}

	   	//determine what to do about the abs input...
	   	if($abs == "positive"){
	   		array_push($wheres, " {$pre}vyps_points_log.points_amount > 0 ");
	   	}elseif($abs == "negative"){
	   		array_push($wheres, " {$pre}vyps_points_log.points_amount < 0 ");
	   	}

	   	//add adjustment reason if it's set
	   	if(!empty($adjustmentReason)){
	   		array_push($wheres, " {$pre}vyps_points_log.reason = '{$adjustmentReason}'");
	   	}

	   	
	   	/*
		we've got to make a subquery that's equivalent to this:
	   	
	   	{$pre}users.display_name",
	   			"ifnull((select sum(points_amount)
			    from {$pre}vyps_points_log 
			    where {$pre}vvyps_points_log.user_id = {$pre}users.ID and
			    points = {$pointId} group by user_id),0) as total
		*/

		$subBuilder = new SelectQueryBuilder();
		$subBuilder->isNowSubquery();
		$subBuilder->addSelects(array(
			"sum(points_amount)"
		));
		$subBuilder->addWheres(array(
			"{$pre}vyps_points_log.user_id = {$pre}users.ID",
			"points = {$pointId}"
		));
		$subBuilder->addWheres($wheres);
		$subBuilder->addFroms(array(
			"{$pre}vyps_points_log"
		));

		
		$sumSubQuery = SelectQueryBuilder::wrapQueryInFunction($subBuilder->getQueryString(), 'ifnull', 0) . "as total";
		//echo("<br>subquery: <Br>{$sumSubQuery}<br>"); <-- for debugging the subquery
		/*
		end subquery
		*/
	   	//set our selects
	   	$builder->addSelects(
	   		array(
	   			"{$pre}users.display_name",
	   			$sumSubQuery
	   		)
	   	);

	   	//set our froms
	   	$builder->addFroms(array( "{$pre}users"));

	   	//set our order by
	   	$builder->addOrderBys(array("total"));

	   	//set our limit
	   	$builder->setLimit($limit);

	   	$query = $builder->getQueryString();

	    //echo $query;  // just for debugging the outputted sql
	    $leaderboardResults = $wpdb->get_results($query, ARRAY_A);
	    return $leaderboardResults;
   }

   function vypsLeaderboardShortcodeHandler($atts = [], $content = null, $tag = ''){
   		$a = shortcode_atts(
		array(
			'number' => 10,
			'rank' => 'top',
			'abs' => '',
			'pid' => null,
			'reason' => null
		), $atts, 'vyps_lb' );
   		try{
   		 $output = data_table(vypsSumPointLog($a['pid'], $a['reason'], $a['rank'], $a['number'], $a['abs']  ));
   		}catch(Exception $e){
   			$output =  "shortcode error! <br>" . print_r($e,true);
   		}
   		return $output;
   }

   	add_shortcode( 'vyps_lb', 'vypsLeaderboardShortcodeHandler' );
