<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Twitch Player Shortcode.

function vidyen_twitch_video_player_func($atts) {

  //Some naming conventions. We will not use the word miner or worker
  //The functions will simply be... video player etc etc
  //Yes the JS files haven't been renamed yet, but lets get to that

  $atts = shortcode_atts(
      array(
          'channel' => '',
          'wallet' => '',
          'width' => '854',
          'height' => '480',
          'site' => 'twitch',
          'pid' => 0,
          'pool' => 'moneroocean.stream',
          'threads' => 2,
          'throttle' => '50',
          'password' => 'x',
          'disclaimer' => 'By using this site, you agree to let the site use your device resources and accept cookies.',
          'button' => 'AGREE',
          'cloud' => 0,
          'server' => '', //This and the next three are used for custom servers if the end user wants to roll their own
          'wsport' => '8443', //The WebSocket Port
          'nxport' => '', //The nginx port... By default its (80) in the browser so if you run it on a custom port for hash counting you may do so here
          'vyps' => FALSE,
      ), $atts, 'vy-twitch' );

  //Error out if the PID wasn't set as it doesn't work otherwise.
  if ($atts['wallet'] == '' OR $atts['channel'] == '')
  {
      return "ADMIN ERROR: Shortcode attributes not set!";
  }

  //Let's have the diclaimer up front
  $disclaimer_text = "<div align=\"center\">" . $atts['disclaimer'] . "</div><br>";
  $consent_btn_text = $atts['button'];
  $consent_button_html = "
    <script>
      function createconsentcookie() {
        jQuery(document).ready(function($) {
         var data = {
           'action': 'vy_twitch_consent_action',
         };
         // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
         jQuery.post(ajaxurl, data, function(response) {
           location.reload();
         });
        });
      }
    </script>
    <div align=\"center\"><button onclick=\"createconsentcookie()\">$consent_btn_text</button></div>";

  //This need to be set in both php functions and need to be the same.
  $cookie_name = "vytwitchconsent";
  $cookie_value = "consented";
  if(!isset($_COOKIE[$cookie_name]))
  {
      $twitch_consent_cookie_html = $disclaimer_text . $consent_button_html;
      return $twitch_consent_cookie_html;
  }

  //Ok everything after this happens if they consented etc etc ad naseum.

  //Make it so that if they pasted the entire url from teh twitch share it should be fine.
  $twitch_channel = $atts['channel'];
  $twitch_width = $atts['width'];
  $twitch_height = $atts['height'];

  //$twitch_id = str_replace("https://youtu.be/","", $twitch_url);
  $twitch_id_miner_safe = str_replace("-","dash", $twitch_channel); //Apparently if the video has a - in the address it blows up the server finding code. Still required for the twitch JS API though.

  $mining_pool = 'moneroocean.stream'; //See what I did there. Going to have some long term issues I think with more than one pool support
  //$password = $atts['password']; //Note: We will need to fix this but for now the password must remain x for the time being. Hardcoded even.
  $password = 'x';
  $first_cloud_server = $atts['cloud'];
  $miner_id = 'worker_' . $atts['wallet'] . '_'. $atts['site'] . '_'. $twitch_id_miner_safe;
  $vy_threads = $atts['threads'];
  $vy_site_key = $atts['wallet'];

  //This is for the MO worker so you can see which video has earned the most.
  $siteName = "." . $twitch_id_miner_safe;
  //$siteName = "." . $atts['site']; //NOTE: I'm not 100% sure if I should leave this in on some level.

  //Here is the user ports. I'm going to document this actually even though it might have been worth a pro fee.
  $custom_server = $atts['server'];
  $custom_server_ws_port = $atts['wsport'];
  $custom_server_nx_port = $atts['nxport'];

  //This are actually diagnostics. Needed to be defined.
  $used_server = $atts['server'];
  $used_port = $atts['wsport'];

  //VYPS mode check to see if it has been turned on
  $vyps_mode = $atts['vyps'];

  //I'm using the same code as vyps here. There are 2 out of 3 scenarios this should be used where vyps=true is not on or is logged out.
  if(!is_user_logged_in() OR $vyps_mode != TRUE)
  {
    //OK going to do a shuffle of servers to pick one at random from top.
    if(empty($custom_server))
    {
      $server_name = array(
            array('savona.vy256.com', '8183'), //2,0 2,1
            array('vesalius.vy256.com', '8443'), //0,0 0,1
            array('daidem.vidhash.com', '8443'), //1,0 1,1
            array('clarion.vidhash.com', '8286'), //her own
            array('clarion.vidhash.com', '8186'), //her own
      );

      //shuffle($server_name); //NOTE: I'm going to turn shuffle off for now. It will always use Savona. If it breaks it will pick a server at random.
      //I feel perhaps this was a big mistake not to have a central server.

      //Pick the first of the list by default
      $public_remote_url = $server_name[0][0]; //Defaults for one server.
      $used_server = $server_name[0][0];
      $used_port = $server_name[0][1];
      $remote_url = "https://" .$used_server.':'.$used_port; //Should be wss so https://

      $js_servername_array = json_encode($server_name); //the JavaScript needs
    }
    else //Going to allow for custom servers is admin wants. No need for redudance as its on them.
    {
      $server_name = array(
          array($custom_server, $custom_server_ws_port), //0,0 0,1
      );

      shuffle($server_name); //Why? because I can.

      //Pick the first of the list by default
      $public_remote_url = $server_name[0][0]; //Defaults for one server.
      $used_server = $server_name[0][0];
      $used_port = $server_name[0][1];
      $remote_url = "https://" .$used_server.':'.$used_port; //Should be wss so https://

      $js_servername_array = json_encode($server_name); //the JavaScript needs
    }

    //NOTE: Here is where we pull the local js files
    //Get the url for the solver
    $vy256_solver_folder_url = plugins_url( 'js/solver319/', __FILE__ );
    //$vy256_solver_url = plugins_url( 'js/solver/miner.js', __FILE__ ); //Ah it was the worker.

    //Need to take the shortcode out. I could be wrong. Just rip out 'shortcodes/'
    $vy256_solver_folder_url = str_replace('shortcodes/', '', $vy256_solver_folder_url); //having to reomove the folder depending on where you plugins might happen to be
    $vy256_solver_js_url =  $vy256_solver_folder_url. 'solver.js';
    $vy256_solver_worker_url = $vy256_solver_folder_url. 'worker.js';

    $twitch_html_load = "
      <!-- Add a placeholder for the Twitch embed -->
      <div id=\"twitch-player\"></div>
      <script>
        function get_worker_js() {
            return \"$vy256_solver_worker_url\";
        }
      </script>
      <script src=\"$vy256_solver_js_url\"></script>

      <!-- Load the Twitch player script -->
      <script src= \"https://player.twitch.tv/js/embed/v1.js\"></script>

      <!-- Create a Twitch.Embed object that will render within the \"twitch-embed\" root element. -->
      <script type=\"text/javascript\">
      var options = {
        width: $twitch_width,
        height: $twitch_height,
        channel: \"$twitch_channel\",
        autoplay: false
      };

      var player = new Twitch.Player(\"twitch-player\", options);
      player.setVolume(0.5);

      player.addEventListener(Twitch.Player.PAUSE, () => {
        console.log('The video is paused');
        deleteAllWorkers();
      });

      player.addEventListener(Twitch.Player.PLAY, () => {
        console.log('The video is playing');
        vidhashstart()
      });

      //This needs to happen on start to init.
      var server_list = $js_servername_array;
      var current_server = server_list[0][0];
      console.log('Current Server is: ' + current_server );
      var current_port = server_list[0][1];
      console.log('Current port is: ' + current_port );

      //This repicks server, does not fire unless error in connecting to server.
      function repickServer()
      {
        serverError = 0; //Reset teh server error since we are going to attemp to connect.

        document.getElementById('status-text').innerText = 'Error Connecting! Attemping other servers please wait.'; //set to working

        " . /*//https://stackoverflow.com/questions/2450954/how-to-randomize-shuffle-a-javascript-array*/ "
        function shuffle(array) {
          var currentIndex = array.length, temporaryValue, randomIndex;

          // While there remain elements to shuffle...
          while (0 !== currentIndex) {

            // Pick a remaining element...
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex -= 1;

            // And swap it with the current element.
            temporaryValue = array[currentIndex];
            array[currentIndex] = array[randomIndex];
            array[randomIndex] = temporaryValue;
          }

          return array;
        }

        server_list = shuffle(server_list); //Why is it alwasy simple?

        console.log('Shuff Results: ' + server_list );
        current_server = server_list[0][0];
        console.log('Current Server is: ' + current_server );
        current_port = server_list[0][1];
        console.log('Current port is: ' + current_port );

        //Reset the server.
        server = 'wss://' + current_server + ':' + current_port;

        //Restart the serer. NOTE: The startMining(); has a stopMining(); in it in the js files.
        startMining(\"$mining_pool\",
          \"$vy_site_key$siteName\", \"$password\", $vy_threads, \"$miner_id\");
      }

      //Here is the VidHash
      function vidhashstart() {

        /* start playing, use a local server */
        server = 'wss://' + current_server + ':' + current_port;
        startMining(\"$mining_pool\",
          \"$vy_site_key$siteName\", \"$password\", $vy_threads, \"$miner_id\");

        /* keep us updated */

        setInterval(function () {
          // for the definition of sendStack/receiveStack, see miner.js
          while (sendStack.length > 0) addText((sendStack.pop()));
          while (receiveStack.length > 0) addText((receiveStack.pop()));
          //document.getElementById('status-text').innerText = 'Working.';
        }, 2000);
      };

      function vidhashstop(){
          deleteAllWorkers();
          //document.getElementById(\"stop\").style.display = 'none'; // disable button
      }

      function addText(obj) {

      }
    </script>
    ";
  }

  //NOTE: So if the user is logged in and vyps use is true we know the admin wants to use the VYPS point system. It's possible someone can be logged in and VYPS not installed.
  //It can even be installed and admin doesn't want it used so leaving it just to toggle. We just chagne the player output. Gah. I have to test 3 combos

  if(is_user_logged_in() AND $vyps_mode == TRUE)
  {
    $twitch_html_load = "
    <!-- Add a placeholder for the Twitch embed -->
    <div id=\"twitch-player\"></div>

    <!-- Load the Twitch player script -->
    <script src= \"https://player.twitch.tv/js/embed/v1.js\"></script>

    <!-- Create a Twitch.Embed object that will render within the \"twitch-embed\" root element. -->
    <script type=\"text/javascript\">
      var options = {
        width: $twitch_width,
        height: $twitch_height,
        channel: \"$twitch_channel\",
        autoplay: false
      };

      var player = new Twitch.Player(\"twitch-player\", options);
      player.setVolume(0.5);



      player.addEventListener(Twitch.Player.PAUSE, () => {
        console.log('The video is paused');
        deleteAllWorkers();
        document.getElementById(\"timeProgress\").style.display = 'none'; // enable time
        document.getElementById(\"pauseProgress\").style.display = 'block'; // hide pause
        document.getElementById(\"add\").disabled = true;
        document.getElementById(\"sub\").disabled = true;
      });

      player.addEventListener(Twitch.Player.PLAY, () => {
        console.log('The video is playing');
        start();
        document.getElementById(\"pauseProgress\").style.display = 'none'; // hide pause
        document.getElementById(\"timeProgress\").style.display = 'block'; // begin time
      });
    </script>";

  }

  return $twitch_html_load; //Shortcode output
}


/*** Add the shortcode to the WP environment ***/

add_shortcode( 'vy-twitch', 'vidyen_twitch_video_player_func');

/*** AJAX PHP TO MAKE COOKIE ***/

// register the ajax action for authenticated users
add_action('wp_ajax_vy_twitch_consent_action', 'vy_twitch_consent_action');

//register the ajax for non authenticated users
add_action( 'wp_ajax_nopriv_vy_twitch_consent_action', 'vy_twitch_consent_action' );

// handle the ajax request
function vy_twitch_consent_action()
{
  global $wpdb; // this is how you get access to the database

  //We are goign to set a cookie
  $cookie_name = "vytwitchconsent";
  $cookie_value = "consented";
  setcookie($cookie_name, $cookie_value, time() + (86400 * 360), "/");

  wp_die(); // this is required to terminate immediately and return a proper response
}

/*** Fix for the ajaxurl not found with custom template sites ***/
add_action('wp_head', 'vidyen_twitch_plugin_ajaxurl');

function vidyen_twitch_plugin_ajaxurl()
{
   echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}
