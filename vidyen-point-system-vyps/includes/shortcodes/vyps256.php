<?php

//Shortcode itself.

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//NOTE: This is the shortcode we need to use going forward
//NOTE: Also, going forward there will be no simple miner you can display without consent button. Sorry. Not. Sorry.

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
            'pid' => 0,
            'pool' => 'moneroocean.stream',
            'threads' => '2',
            'throttle' => '50',
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
    //$sm_user = $sm_siteUID . $current_user_id; //not needed since not using CH API
    //$hiveUser = $sm_siteUID . $current_user_id; //not needed since not using CH API

    if ($sm_site_key == '' AND $siteName == '') {

        return "Error: Wallet address and site name not set. This is required!";

    } else {

        $site_warning = '';

    }

    //NOTE: Debugging turned off
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);

    if (isset($_POST["consent"]) AND is_user_logged_in() ){ // Just checking if they clicked conset and are logged in case something dumb happened.

      global $wpdb;

      //loading the graphic url
      $VYPS_worker_url = plugins_url() . '/vidyen-point-system-vyps/images/vyworker_large.gif'; //Small version

      //Ok. We are makign the mining unique. I might need to drop the _ but we will see if monroe made it required. If so, then I'll just drop the _ and combine it with user name.
      $table_name_log = $wpdb->prefix . 'vyps_points_log';
      $last_transaction_query = "SELECT max(id) FROM ". $table_name_log . " WHERE user_id = %d AND reason = %s"; //Ok we find the id of the last VY256 mining
      $last_transaction_query_prepared = $wpdb->prepare( $last_transaction_query, $current_user_id, "VY256 Mining" ); //NOTE: Originally this said $current_user_id but although I could pass it through to something else it would not be true if admin specified a UID. Ergo it should just say it $userID
      $last_transaction_id = $wpdb->get_var( $last_transaction_query_prepared );

      $miner_id = 'worker_' . $current_user_id . '_' . $sm_site_key . '_' . $siteName . $last_transaction_id;

      //Using WP functions
      $remote_url = "http://vy256.com:8081/?userid=" . $miner_id;
      $remote_response =  wp_remote_get( esc_url_raw( $remote_url ) );
      if(array_key_exists('headers', $remote_response)){
          $balance =  intval($remote_response['body']);
      } else {
          $balance = 0;
          return 'Error connecting to VY256.com! Server maybe offline? Contact VidYen.com admin!'; //NOTE: WP Shortcodes NEVER use echo. It says so in codex.
      }

      if ($balance > 0) {
          //Ok we need to actually use $wpdb here as its going to feed into the log of course.
          global $wpdb;
          $table_log = $wpdb->prefix . 'vyps_points_log';
          $reason = "VY256 Mining"; //I feel like this should be a shortcode attr but maybe pro version feature.
          $amount = doubleval($balance); //Well in theory the json_decode could blow up I suppose better safe than sorry.
          $pointType = intval($pointID); //Point type should be int.
          $user_id = get_current_user_id();

          //Inserting Coin Hive row.
          $data = [
              'reason' => $reason,
              'point_id' => $pointType,
              'points_amount' => $amount,
              'user_id' => $user_id,
              'time' => date('Y-m-d H:i:s')
          ];
          $wpdb->insert($table_log, $data);

          //Yeah a bit heavy on the SQL calls but need to check a second time if redeeming on load
          $table_name_log = $wpdb->prefix . 'vyps_points_log';
          $last_transaction_query = "SELECT max(id) FROM ". $table_name_log . " WHERE user_id = %d AND reason = %s"; //Ok we find the id of the last VY256 mining
          $last_transaction_query_prepared = $wpdb->prepare( $last_transaction_query, $current_user_id, "VY256 Mining" ); //NOTE: Originally this said $current_user_id but although I could pass it through to something else it would not be true if admin specified a UID. Ergo it should just say it $userID
          $last_transaction_id = $wpdb->get_var( $last_transaction_query_prepared );

          //Now redoing with new miner id. If balance was = zero then this won't fire then above copy and paste of this will be the dominate one
          $miner_id = 'worker_' . $current_user_id . '_' . $sm_site_key . '_' . $siteName . $last_transaction_id;

          $redeem_output = "<tr><td>$balance hashes redeemed.</td></tr>"; //if there is any blance is gets redeemed.

      } else {

          $balance = 0; //I remembered if it gets returned a blank should be made a zero.
          //This is first time happenings. Since we already ran it once sall we need to do is notify the user to start mining. Order of operations.
          $redeem_output = "<tr><td>Click  \"Start Mining\" to begin and  \"Stop\" when you want to stop the miner and credit for points.</td></tr>";

      }

      //Get the url for the solver
      $vy256_solver_folder_url = plugins_url( 'js/solver/', __FILE__ );
      //$vy256_solver_url = plugins_url( 'js/solver/miner.js', __FILE__ ); //Ah it was the worker.

      //Need to take the shortcode out. I could be wrong. Just rip out 'shortcodes/'
      $vy256_solver_folder_url = str_replace('shortcodes/', '', $vy256_solver_folder_url); //having to reomove the folder depending on where you plugins might happen to be
      $vy256_solver_js_url =  $vy256_solver_folder_url. 'solver.js';
      $vy256_solver_worker_url = $vy256_solver_folder_url. 'worker.js';

      //Ok some issues we need to know the path to the js file so will have to ess with that.
      $simple_miner_output = "<!-- $remote_url -->
      <table>
        $site_warning
        <tr><td>
          <div>
          <img src=\"$VYPS_worker_url\"><br>
          </div>

          <script>
                  function get_worker_js()
            {
                return \"$vy256_solver_worker_url\";
            }

            </script>
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
              document.getElementById(\"mining\").style.display = 'block'; // disable button



              /* start mining, use a local server */
              server = \"wss://www.vy256.com:8181\";
              startMining(\"$mining_pool\",
                \"$sm_site_key\", \"\", $sm_threads, \"$miner_id\");

              //startMining(\"moneroocean.stream\",
             //   \"4AgpWKTjsyrFeyWD7bpcYjbQG7MVSjKGwDEBhfdWo16pi428ktoych4MrcdSpyH7Ej3NcBE6mP9MoVdAZQPTWTgX5xGX9Ej\");

              /* keep us updated */


              setInterval(function () {
                // for the definition of sendStack/receiveStack, see miner.js
                while (sendStack.length > 0) addText((sendStack.pop()));
                while (receiveStack.length > 0) addText((receiveStack.pop()));
                document.getElementById('status-text').innerText = 'Calculating hashes';
              }, 2000);

            }

            function stop(){
                deleteAllWorkers();
                document.getElementById(\"stop\").style.display = 'none'; // disable button
            }

            /* helper function to put text into the text field.  */

            function addText(obj) {

                if(obj.identifier != \"userstats\"){
                  totalhashes = totalhashes;
                  document.querySelector('input[name=\"hash_amount\"]').value = totalhashes;
                  if(totalhashes > 0){
                      document.getElementById('total_hashes').innerText = totalhashes + ' Hashes';
                  }
                }

            }

          </script>

            <style>
            .loader {
                border: 16px solid #f3f3f3; /* Light grey */
                border-top: 16px solid #555;
                border-radius: 50%;
                width: 120px;
                height: 120px;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            </style>
            <center id=\"mining\" style=\"display:none;\"><div class=\"loader\"></div>
            <div style=\"margin-top:10px !important;\"><span id=\"status-text\">Connecting to server</span><span id=\"wait\">.</span></div>

    <script>
    var dots = window.setInterval( function() {
        var wait = document.getElementById(\"wait\");
        if ( wait.innerHTML.length > 3 )
            wait.innerHTML = \".\";
        else
            wait.innerHTML += \".\";
        }, 500);
    </script>
    </center>
    </td></tr>
    <tr>
       <td>
         <div>
           <button id=\"startb\" onclick=\"start()\">Start Mining</button>
         </div>
        <div id=\"thread_manage\" style=\"display:inline;margin:5px !important;display:none;\">
            Power:&nbsp;
          <button type=\"button\" id=\"sub\" style=\"display:inline;\" class=\"sub\">-</button>
          <input style=\"display:inline;width:50%;\" type=\"text\" id=\"1\" value=\"$sm_threads\" disabled class=field>
          <button type=\"button\" id=\"add\" style=\"display:inline;\" class=\"add\">+</button>
        </div>
          <form method=\"post\" style=\"display:none;margin:5px !important;\" id=\"redeem\">
            <input type=\"hidden\" value=\"\" name=\"redeem\"/>
            <input type=\"hidden\" value=\"\" name=\"hash_amount\"/>
          <!--<input type=\"submit\" class=\"button-secondary\" value=\"Redeem Hashes\" onclick=\"return confirm('Did you want to sync your mined hashes with this site?');\" />-->
           <span id=\"total_hashes\" style=\"float:right;\">(Do not refresh)</span>
          </form>
          <form id=\"stop\" style=\"display:none;margin:5px !important;\" method=\"post\"><input type=\"hidden\" value=\"\" name=\"consent\"/><input type=\"submit\" class=\"button - secondary\" value=\"Redeem Hashes\"/></form>
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
                if ($(this).next().val() > 0){
                    $(this).next().val(+$(this).next().val() - 1);
                      removeWorker();
                }
            });
            </script>
        </td></tr>";

      $final_return = $simple_miner_output . $redeem_output .  '</table>';


    } else {

        $final_return = ""; //Well. Niether consent button or redeem were clicked sooo.... You get nothing.

    }

    return $final_return;

}

/* Telling WP to use function for shortcode for sm-consent*/

add_shortcode( 'vyps-256', 'vyps_vy256_solver_func');



/* Shortcode for the API call to create a lot entry */
/* There is some debate if this should be a button, but I'm just going to run on the code on page load and the admins can just make a button that runs the smart code if they want */

function vyps_solver_consent_button_func( $atts ) {
    if(!isset($_POST['consent']) && !isset($_POST['redeem'])){
        //Some shortcode attributes to create custom button message
        $atts = shortcode_atts(
            array(

                'txt' => 'I agree and consent',

            ), $atts, 'vyps-ch-consent' );

        $button_text = $atts['txt'];

        /* User needs to be logged into consent. NO EXCEPTIONS */

        if ( is_user_logged_in() ) {

            return "Please consent to mining. <form method=\"post\">
                <input type=\"hidden\" value=\"\" name=\"consent\"/>
                <input type=\"submit\" class=\"button-secondary\" value=\"$button_text\" onclick=\"return confirm('Did you read everything and consent to letting this page browser mine with your CPU?');\" />
                </form>";

        } else {

            return "You need to be logged in to consent!"; //I feel like admin an use a
        }
    }

}

add_shortcode( 'vyps-256-consent', 'vyps_solver_consent_button_func');
