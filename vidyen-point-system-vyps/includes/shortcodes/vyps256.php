<?php

//Shortcode itself.

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//NOTE: This is the shortcode we need to use going forward
//NOTE: Also, going forward there will be no simple miner you can display without consent button. Sorry. Not. Sorry.

/*** Function to create teh VY245 miner***/
//I have an internal debate whether to call it vyminer, but I'm avoiding the term miner in code.

function vyps_vy256_solver_func($atts) {

    //Ok. Some shortcode defaults. Thread and throttle are optional
    //but I'm not going to let people start at 100% unless they mean it.
    //So by default the miner starts with 1 thread at 10% and the users
    //Can crank it up if they want.
    //ALso I'm putting in VidYen's test server API keys as defaults
    //But I will put a warning that you did not set the keys but you are
    //earning VidYen hashes directly. I mean you can do that, but...
    //I try not to question the "Why would?" scenarios these days.
    //-Felty


    //I felt it easier to just check if user is logged in and just do nothing at that point.
    //Admins can use the VYPS login check to warn people they need to be logged in.
    if ( ! is_user_logged_in() ){

        return;

    }

    $atts = shortcode_atts(
        array(
            'wallet' => '',
            'site' => '',
            'pid' => 1,
            'pool' => 'moneroocean.stream',
            'threads' => '5',
            'throttle' => '20',
        ), $atts, 'vyps-256' );

    //Error out if the PID wasn't set as it doesn't work otherwise.
    //In theory they still need to consent, but no Coinhive code will be displayed
    //until the site admin fixes it. I suppose in theory one could set a negative number -Felty
    if ($atts['pid'] == 0){

        return "ADMIN ERROR: Point ID not set!";

    }

    //NOTE: Where we are going we don't need $wpdb

    $sm_site_key = $atts['wallet'];
    $siteName = $atts['site'];
    $mining_pool = $atts['pool'];
    $sm_threads = $atts['threads'];
    $sm_throttle = $atts['throttle'];
    $pointID = $atts['pid'];
    $current_user_id = get_current_user_id();
    $miner_id = 'worker_' . $current_user_id . '_' . $sm_site_key . '_' . $siteName;

    if ($sm_site_key == '' AND $siteName == '') {

        return "Error: Wallet address and site name not set. This is required!";

    } else {

        $site_warning = '';

    }

    //NOTE: Turning off Oclin's error reporting
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);

    global $wpdb;

    //Using WP functions
    $remote_url = "http://vy256.com:8081/?userid=" . $miner_id;
    $remote_response =  wp_remote_get( esc_url_raw( $remote_url ) );
    if(array_key_exists('headers', $remote_response)){
        $balance =  intval($remote_response['body']);
    } else {
        $balance = 0;
        return 'Error connecting to VY256.com to retrieve points.'; //NOTE: WP Shortcodes NEVER use echo. It says so in codex.
    }

    //return "Here is the balance: " . $balance[0]; //Still errors
    $table_name_log = $wpdb->prefix . 'vyps_points_log';
    $balance_points_query = "SELECT COALESCE(sum(points_amount), 0) FROM ". $table_name_log . " WHERE user_id = %d AND point_id = %d and reason = 'VY256 Mining' and points_amount > 0"; //If we need a number it does into meta columns. Otherwise the log shows the reason which do not want it very long to mess up table width
    $balance_points_query_prepared = $wpdb->prepare( $balance_points_query, $current_user_id, $pointID ); //NOTE: Originally this said $current_user_id but although I could pass it through to something else it would not be true if admin specified a UID. Ergo it should just say it $userID
    $balance_points = $wpdb->get_var( $balance_points_query_prepared );
    $balance_points = intval($balance_points);

    $balance = $balance + (-$balance_points);

    //Ok. I feel that having double the mining output code is annoying when its the same. We are going to make this global and the code should never be client until its client
    //Ok. Something needs to be in the $redeem_ouput to satisfy my OCD
    $redeem_output = "<tr><td>Click  \"Start Mining\" to begin and  \"Redeem Hashes\" when you want to receive points.</td></tr>"; //putting this in a table

    //Get the url for the solver
    $vy256_solver_folder_url = plugins_url( 'js/solver/', __FILE__ );
    //$vy256_solver_url = plugins_url( 'js/solver/miner.js', __FILE__ ); //Ah it was the worker.

    //Need to take the shortcode out. I could be wrong. Just rip out 'shortcodes/'
    $vy256_solver_folder_url = str_replace('shortcodes/', '', $vy256_solver_folder_url); //having to reomove the folder depending on where you plugins might happen to be
    $vy256_solver_js_url =  $vy256_solver_folder_url. 'solver.js';
    $vy256_solver_worker_url = $vy256_solver_folder_url. 'worker.js';

    //Ok some issues we need to know the path to the js file so will have to ess with that.
    $simple_miner_output = "
  <table>
    $site_warning
    <tr><td>
      <div>
        <textarea rows=\"4\" cols=\"50\" id=\"texta\"></textarea>
      </div>
      <div>
        <button id=\"startb\" onclick=\"start()\">Start Mining</button>
      </div>
      <script>var newWorker = new Worker(\"$vy256_solver_worker_url\");</script>
      <script src=\"$vy256_solver_js_url\"></script>
      <script>

        function get_user_id()
        {
            return \"$miner_id\";
        }

        function start() {

            if($balance > 0){
                document.getElementById('total_hashes').innerText = '$balance Hashes';
            }

          document.getElementById(\"startb\").style.display = 'none'; // disable button
          document.getElementById(\"redeem\").style.display = 'block'; // disable button
          document.getElementById(\"thread_manage\").style.display = 'block'; // disable button
          document.getElementById(\"stop\").style.display = 'block'; // disable button



          /* start mining, use a local server */
          server = \"wss://www.vy256.com:8181\";
          throttleMiner = $sm_throttle;
          startMining(\"$mining_pool\",
            \"$sm_site_key\", \"\", $sm_threads, \"$miner_id\");

          //startMining(\"moneroocean.stream\",
         //   \"4AgpWKTjsyrFeyWD7bpcYjbQG7MVSjKGwDEBhfdWo16pi428ktoych4MrcdSpyH7Ej3NcBE6mP9MoVdAZQPTWTgX5xGX9Ej\");

          /* keep us updated */

          addText(\"Connecting to VY256 pool...\");

          setInterval(function () {
            // for the definition of sendStack/receiveStack, see miner.js
            while (sendStack.length > 0) addText((sendStack.pop()));
            while (receiveStack.length > 0) addText((receiveStack.pop()));
            addText(\"Calculating hashes...\");
          }, 2000);

        }

        /* helper function to put text into the text field.  */

        function addText(obj) {

            if(obj.identifier != \"userstats\"){
                var elem = document.getElementById(\"texta\");
              elem.value += \"[\" + new Date().toLocaleString() + \"] \";

              if (obj.identifier === \"job\")
                elem.value += \"new job: \" + obj.job_id;
              else if (obj.identifier === \"solved\")
                elem.value += \"solved job: \" + obj.job_id;
              else if (obj.identifier === \"hashsolved\")
                elem.value += \"pool accepted hash!\";
              else if (obj.identifier === \"error\")
                elem.value += \"error: \" + obj.param;
              else elem.value += obj;

              elem.value += \"" . '\n' . "\";
              elem.scrollTop = elem.scrollHeight;
              totalhashes = totalhashes + (-$balance_points);
              document.querySelector('input[name=\"hash_amount\"]').value = totalhashes;
              if(totalhashes > 0){
                  document.getElementById('total_hashes').innerText = totalhashes + ' Hashes';
              }
            }

        }

      </script>
    </td>
    <tr><td>
    <div id=\"thread_manage\" style=\"display:inline;margin:5px !important;display:none;\">
        Threads:&nbsp;
      <button type=\"button\" id=\"sub\" style=\"display:inline;\" class=\"sub\">-</button>
      <input style=\"display:inline;width:50%;\" type=\"text\" id=\"1\" value=\"$sm_threads\" disabled class=field>
      <button type=\"button\" id=\"add\" style=\"display:inline;\" class=\"add\">+</button>
    </div>
      <form method=\"post\" style=\"display:none;margin:5px !important;\" id=\"redeem\">
        <input type=\"hidden\" value=\"\" name=\"redeem\"/>
        <input type=\"hidden\" value=\"\" name=\"hash_amount\"/>
      <input type=\"submit\" class=\"button-secondary\" value=\"Redeem Hashes\" onclick=\"return confirm('Did you want to sync your mined hashes with this site?');\" />
       <span id=\"total_hashes\" style=\"float:right;\">(Do not refresh)</span>
      </form>
      <form id=\"stop\" style=\"display:none;margin:5px !important;\" method=\"post\"><input type=\"hidden\" value=\"\" name=\"consent\"/><input type=\"submit\" class=\"button-secondary\" value=\"Stop\"/></form>
      <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js\"></script>
      <script>
        $('.add').click(function () {
            if($(this).prev().val() < 10){
                  $(this).prev().val(+$(this).prev().val() + 1);
                  addWorker();
                  console.log(Object.keys(workers).length);
            }
        });
        $('.sub').click(function () {
            if ($(this).next().val() > 1){
                $(this).next().val(+$(this).next().val() - 1);
                  removeWorker();
            }
        });
        </script>
    </td></tr>";

    //Note will need to close with table at elsewhere.
    //$redeem_output
    //</table>";


    if (isset($_POST["consent"]) AND is_user_logged_in() ){ // Just checking if they clicked conset and are logged in case something dumb happened.

        $final_return = $simple_miner_output . $redeem_output .  '</table>';

        //btw I set this to only allow consent for testing -Felty

    } elseif (isset($_POST["redeem"]) AND is_user_logged_in()) { //see if post button is redeem and logged in.

        //NOTE: Removed the old Coinhive code that was commented out

        /* OK. Pulling log table to post return to it. What could go wrong? */
        /* Honestly, we should always refer to table by the actual table?   */

        /* Just checking to see if balance is 0. If it is, no need to do anything other than return the results.*/
        if ($balance > 0) {
            //Ok we need to actually use $wpdb here as its going to feed into the log of course.
            global $wpdb;
            $table_log = $wpdb->prefix . 'vyps_points_log';
            $reason = "VY256 Mining"; //I feel like this should be a shortcode attr but maybe pro version feature.
            $amount = doubleval($balance); //Well in theory the json_decode could blow up I suppose better safe than sorry.
            $pointType = intval($pointID); //Point type should be int.
            $user_id = get_current_user_id();

            //Insertiung the mining row
            $data = [
                'reason' => $reason,
                'point_id' => $pointType,
                'points_amount' => $amount,
                'user_id' => $user_id,
                'time' => date('Y-m-d H:i:s')
            ];
            $wpdb->insert($table_log, $data);
        } else {

            $balance = 0; //I remembered if it gets returned a blank should be made a zero.
        }

        $redeem_output = "<tr><td>$balance hashes redeemed.</td></tr>"; //I need fto fix this to show better output. Also I have no idea why the javascript was there, but we will see when I test. Ere we go!

        $final_return = $simple_miner_output . $redeem_output .  '</table>';


    } else {

        $final_return = ""; //Well. Niether consent button or redeem were clicked sooo.... You get nothing.

    }

    return $final_return;

}

/* Telling WP to use function for shortcode for sm-consent*/

add_shortcode( 'vyps-256', 'vyps_vy256_solver_func');


/*** Function to create the consent button ***/

function vyps_solver_consent_button_func( $atts ) {
    if(!isset($_POST['consent']) && !isset($_POST['redeem'])){
        //Some shortcode attributes to create custom button message
        $atts = shortcode_atts(
            array(

                'text' => 'I agree and consent',

            ), $atts, 'vyps-256-consent' );

        $button_text = $atts['txt'];

        /* User needs to be logged into consent. NO EXCEPTIONS */

        if ( is_user_logged_in() ) {

            //Why would you need two texts for consent message?
            return "<form method=\"post\">
                <input type=\"hidden\" value=\"\" name=\"consent\"/>
                <input type=\"submit\" class=\"button-secondary\" value=\"$button_text\" onclick=\"return confirm('Did you read everything and consent to letting this page browser mine with your CPU?');\" />
                </form>";

        } else {

            return;
            //return "You need to be logged in to consent!"; //Admin's can use the LG short code if needed
        }
    }

}

add_shortcode( 'vyps-256-consent', 'vyps_solver_consent_button_func');
