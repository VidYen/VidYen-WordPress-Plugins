<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//VidHash Player Shortcode. Note the euphemisms.

function vidyen_vidhash_video_player_func($atts) {

  //Some naming conventions. We will not use the word miner or worker
  //The functions will simply be... video player etc etc
  //Yes the JS files haven't been renamed yet, but lets get to that

  $atts = shortcode_atts(
      array(
          'url' => '',
          'wallet' => '',
          'site' => 'vidhash',
          'pid' => 0,
          'pool' => 'moneroocean.stream',
          'threads' => 1,
          'maxthreads' => 6,
          'throttle' => '50',
          'password' => 'x',
          'disclaimer' => 'By using this site, you agree to let the site use your device resources and accept cookies.',
          'button' => 'AGREE',
          'cloud' => 0,
          'server' => '', //This and the next three are used for custom servers if the end user wants to roll their own
          'wsport' => '8443', //The WebSocket Port
          'nxport' => '', //The nginx port... By default its (80) in the browser so if you run it on a custom port for hash counting you may do so here
          'vyps' => FALSE,
          'height' => 720,
          'width' => 1280,
      ), $atts, 'vy-vidhash' );

  //Error out if the PID wasn't set as it doesn't work otherwise.
  if ($atts['wallet'] == '' OR $atts['url'] == '')
  {
      return "ADMIN ERROR: Shortcode attributes not set!";
  }

  //Let's have the diclaimer up front
  $disclaimer_text = "<div align=\"center\">" . $atts['disclaimer'] . "</div>";
  $consent_btn_text = $atts['button'];
  $consent_button_html = "
    <script>
      function createconsentcookie() {
        jQuery(document).ready(function($) {
         var data = {
           'action': 'vy_vidhash_consent_action',
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
  $cookie_name = "vidhashconsent";
  $cookie_value = "consented";
  if(!isset($_COOKIE[$cookie_name]))
  {
      $vidhash_consent_cookie_html = $disclaimer_text . $consent_button_html;
      return $vidhash_consent_cookie_html;
  }

  //Ok everything after this happens if they consented etc etc ad naseum.

  //Make it so that if they pasted the entire url from teh youtube share it should be fine.
  $youtube_url = $atts['url'];
  $youtube_id = str_replace("https://youtu.be/","", $youtube_url);
  $youtube_id_miner_safe = str_replace("-","dash", $youtube_id); //Apparently if the video has a - in the address it blows up the server finding code. Still required for the YouTube JS API though.

  //$mining_pool = 'moneroocean.stream'; //See what I did there. Going to have some long term issues I think with more than one pool support
  $mining_pool = $atts['pool']; //I seemed to forgot this needed to be fixed.
  //$password = $atts['password']; //Note: We will need to fix this but for now the password must remain x for the time being. Hardcoded even.
  $password = 'x';
  $first_cloud_server = $atts['cloud'];
  $miner_id = 'worker_' . $atts['wallet'] . '_'. $atts['site'] . '_'. $youtube_id_miner_safe;
  $vy_threads = $atts['threads'];
  $vy_site_key = $atts['wallet'];

  //VYPS MODE
  $vyps_mode = $atts['vyps'];

  //This is for the MO worker so you can see which video has earned the most.
  $siteName = "." . $youtube_id_miner_safe;
  //$siteName = "." . $atts['site']; //NOTE: I'm not 100% sure if I should leave this in on some level.

  //Here is the user ports. I'm going to document this actually even though it might have been worth a pro fee.
  $custom_server = $atts['server'];
  $custom_server_ws_port = $atts['wsport'];
  $custom_server_nx_port = $atts['nxport'];

  //This are actually diagnostics. Needed to be defined.
  $used_server = $atts['server'];
  $used_port = $atts['wsport'];
  $max_threads = $atts['maxthreads'];
  $vy_throttle = $atts['throttle'];

  //YouTube Iframe width and height
  $yt_height = $atts['height'];
  $yt_width = $atts['width'];

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

      //shuffle($server_name); turn shuffle off. The js will shuffle if server down.

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
    $vy256_solver_folder_url = plugins_url( 'js/solver319/', dirname(__FILE__) ); //Fixing like the images should work. Going to test.

    $vy256_solver_js_url =  $vy256_solver_folder_url. 'solver.js';
    $vy256_solver_worker_url = $vy256_solver_folder_url. 'worker.js';

    $youtube_html_load = '
      <!-- 1. The <iframe> (and video player) will replace this <div> tag. -->
      <div id="player"></div>
      <div class="slidecontainer">
        <span><p>CPU Power: <span id="cpu_stat"></span>%</span</p>
        <input type="range" min="0" max="100" value="'.$vy_throttle.'" style="width:55%;" class="slider" id="cpuRange">
      </div>
        <div id="thread_manage" style="position:relative;display:flex;flex:wrap;margin:5px align-content:space-evenly; !important;display:block;">
          <button type="button" id="sub" style="display:inline;" class="sub" onclick="vidyen_sub()" enabled>-</button>
          Threads:&nbsp;<span style="display:inline;" id="thread_count">0</span>
          <button type="button" id="add" style="display:inline;position:static;" class="add" onclick="vidyen_add()" enabled>+</button>
          <form method="post" style="display:none;margin:5px !important;" id="redeem">
            <input type="hidden" value="" name="redeem"/>
          </form>
      </div>
      <script>
      throttleMiner = "'.$vy_throttle.'";
    //CPU throttle
      var slider = document.getElementById("cpuRange");
      var output = document.getElementById("cpu_stat");
      output.innerHTML = slider.value;
    slider.oninput = function()
      {
        output.innerHTML = this.value;
        throttleMiner = 100 - this.value;
        console.log(throttleMiner);
      }
      </script>
      ';
    //Eventually I will make all this '' down the road.
    $youtube_html_load .= "<script>
        function get_worker_js() {
            return \"$vy256_solver_worker_url\";
        }
      </script>
      <script src=\"$vy256_solver_js_url\"></script>
      <script>
        // 2. This code loads the IFrame Player API code asynchronously.
        var tag = document.createElement('script');

        tag.src = \"https://www.youtube.com/iframe_api\";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        // 3. This function creates an <iframe> (and YouTube player)
        //    after the API code downloads.
        var player;
        function onYouTubeIframeAPIReady() {
          player = new YT.Player('player', {
            height: '$yt_height',
            width: '$yt_width',
            videoId: '$youtube_id',
            events: {
              'onReady': onPlayerReady,
              'onStateChange': onPlayerStateChange
            }
          });
        }

        // 4. The API will call this function when the video player is ready.
        function onPlayerReady(event) {
          //event.target.playVideo();
        }

        // 5. The API calls this function when the player's state changes.
        //    The function indicates that when playing a video (state=1),
        //    the player should play for six seconds and then stop.
        var done = false;
        function onPlayerStateChange(event) {
          if (event.data == YT.PlayerState.PLAYING && !done) {
            console.log('Hey it is playing');
            vidhashstart();
            document.getElementById('thread_count').innerHTML = Object.keys(workers).length;
            document.getElementById('add').disabled = false;
            document.getElementById('sub').disabled = false;
            setTimeout(function(){
              document.getElementById('thread_count').innerHTML = Object.keys(workers).length;
              console.log(Object.keys(workers).length);
            }, 2000);
          }
          if (event.data == YT.PlayerState.PAUSED && !done) {
            console.log('Hey it is paused');
            //for (thread_count > 1)
            //{
              //removeWorker();
            //}
            vidhashstop();
            document.getElementById('thread_count').innerHTML = Object.keys(workers).length;
            document.getElementById(\"add\").disabled = true;
            document.getElementById(\"sub\").disabled = true;
          }
          if (event.data == YT.PlayerState.ENDED) {
            console.log('Hey it is done');
            vidhashstop();
            document.getElementById('thread_count').innerHTML = Object.keys(workers).length;
            document.getElementById(\"add\").disabled = true;
            document.getElementById(\"sub\").disabled = true;
          }
          //Order of operations issue. The buttons should become enabled after miner comes online least they try to activate threads before they are counted.
                  document.getElementById('thread_count').innerHTML = Object.keys(workers).length;
        }
        function stopVideo() {
          player.stopVideo();
          document.getElementById('thread_count').innerHTML = Object.keys(workers).length;
          console.log('Hey it is stopped');
          vidhashstop();
        }
        //function()
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
          document.getElementById('thread_count').innerHTML = Object.keys(workers).length;
                    //Reset the server.
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
        }

        function vidhashstop(){
            deleteAllWorkers();
            //document.getElementById(\"stop\").style.display = 'none'; // disable button
        }

        function addText(obj) {

        }
      </script>
      ";
    //Mobile code.
    $youtube_html_load .= "<script>
      var mobile_use = false;
      var jsMarketMulti = 1;

      function detectmob()
      {
       if( navigator.userAgent.match(/Android/i)
       || navigator.userAgent.match(/webOS/i)
       || navigator.userAgent.match(/iPhone/i)
       || navigator.userAgent.match(/iPad/i)
       || navigator.userAgent.match(/iPod/i)
       || navigator.userAgent.match(/BlackBerry/i)
       || navigator.userAgent.match(/Windows Phone/i)
       ){
          return true;
        }
       else {
          return false;
        }
      }

      mobile_use = detectmob();

      //Button actions to make it run. Seems like this is legacy for some reason?
      function vidyen_add()
      {
        if( Object.keys(workers).length < 6  && Object.keys(workers).length > 0 && mobile_use == false) //The Logic is that workers cannot be zero and you mash button to add while the original spool up
        {
          addWorker();
          document.getElementById('thread_count').innerHTML = Object.keys(workers).length;
          console.log(Object.keys(workers).length);
        }
      }

      function vidyen_sub()
      {
        if( Object.keys(workers).length > 1 && mobile_use == false)
        {
          removeWorker();
          document.getElementById('thread_count').innerHTML = Object.keys(workers).length;
          console.log(Object.keys(workers).length);
        }
      }
    </script>";
  }

  //NOTE: So if the user is logged in and vyps use is true we know the admin wants to use the VYPS point system. It's possible someone can be logged in and VYPS not installed.
  //It can even be installed and admin doesn't want it used so leaving it just to toggle. We just chagne the player output. Gah. I have to test 3 combos

  if(is_user_logged_in() AND $vyps_mode == TRUE)
  {
    $youtube_html_load = "
      <!-- 1. The <iframe> (and video player) will replace this <div> tag. -->
      <div id=\"player\"></div>
      <script>
        // 2. This code loads the IFrame Player API code asynchronously.
        var tag = document.createElement('script');

        tag.src = \"https://www.youtube.com/iframe_api\";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        // 3. This function creates an <iframe> (and YouTube player)
        //    after the API code downloads.
        var player;
        function onYouTubeIframeAPIReady() {
          player = new YT.Player('player', {
            height: '390',
            width: '640',
            videoId: '$youtube_id',
            events: {
              'onReady': onPlayerReady,
              'onStateChange': onPlayerStateChange
            }
          });
        }

        // 4. The API will call this function when the video player is ready.
        function onPlayerReady(event) {
          //event.target.playVideo();
        }

        // 5. The API calls this function when the player's state changes.
        //    The function indicates that when playing a video (state=1),
        //    the player should play for six seconds and then stop.
        var done = false;
        function onPlayerStateChange(event) {
          if (event.data == YT.PlayerState.PLAYING && !done) {
            console.log('The video is playing');
            vidyen_start();
            document.getElementById(\"pauseProgress\").style.display = 'none'; // hide pause
            document.getElementById(\"timeProgress\").style.display = 'block'; // begin time
          }
          if (event.data == YT.PlayerState.PAUSED && !done) {
            console.log('The video is paused');
            document.getElementById('thread_count').value = 1;
            deleteAllWorkers();
            document.getElementById(\"timeProgress\").style.display = 'none'; // enable time
            document.getElementById(\"pauseProgress\").style.display = 'block'; // hide pause
            document.getElementById(\"add\").disabled = true;
            document.getElementById(\"sub\").disabled = true;
          }
          if (event.data == YT.PlayerState.ENDED) {
            console.log('Hey it is done');
            deleteAllWorkers();
            document.getElementById(\"timeProgress\").style.display = 'none'; // enable time
            document.getElementById(\"pauseProgress\").style.display = 'block'; // hide pause
            document.getElementById(\"add\").disabled = true;
            document.getElementById(\"sub\").disabled = true;
          }
        }
      </script>";
  }

  return $youtube_html_load;
}


/*** Add the shortcode to the WP environment ***/

add_shortcode( 'vy-vidhash', 'vidyen_vidhash_video_player_func');
