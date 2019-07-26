<?php

//Improved shortcode of public log.


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Function removed and moved to function folder.
//Functions are found in \includes\function\wm\

//Shortcode for the log.

add_shortcode( 'vidyen-wm', 'vidyen_wm_shortcode_func');

//This is the shortcode function placed in a proper place rather than lumped together
//Going to try to avoid recyling code, but you know how these thigns go.
function vidyen_wm_shortcode_func()
{
  //NOTE: DEV DEBUG Put hereby
  $debug_mode = TRUE;

  //First things first. Let's pull the variables with a single SQL call
  $vy_wm_parsed_array = vidyen_vy_wm_settings();
  $index = 1; //Lazy coding but easier to copy and paste stuff.
  //Repulls from SQL
  $button_text = $vy_wm_parsed_array[$index]['button_text'];
  $disclaimer_text = $vy_wm_parsed_array[$index]['disclaimer_text'];
  $eula_text = $vy_wm_parsed_array[$index]['eula_text'];
  $current_wmp = $vy_wm_parsed_array[$index]['current_wmp'];
  $current_pool = $vy_wm_parsed_array[$index]['current_pool'];
  $site_name = $vy_wm_parsed_array[$index]['site_name'];
  $crypto_wallet = $vy_wm_parsed_array[$index]['crypto_wallet'];
  $hash_per_point = $vy_wm_parsed_array[$index]['hash_per_point'];
	$point_id = 	$vy_wm_parsed_array[$index]['point_id'];
  $graphic_selection = $vy_wm_parsed_array[$index]['graphic_selection'];
  $wm_pro_active = $vy_wm_parsed_array[$index]['wm_pro_active'];
  $wm_woo_active = $vy_wm_parsed_array[$index]['wm_woo_active'];
  $wm_threads = $vy_wm_parsed_array[$index]['wm_threads'];
  $wm_cpu = $vy_wm_parsed_array[$index]['wm_cpu'];
  $discord_webhook = $vy_wm_parsed_array[$index]['discord_webhook'];
  $discord_text = $vy_wm_parsed_array[$index]['discord_text'];
  $youtube_url = $vy_wm_parsed_array[$index]['youtube_url'];
  $login_text = $vy_wm_parsed_array[$index]['login_text'];
  $login_url = $vy_wm_parsed_array[$index]['login_url'];
  $custom_wmp = $vy_wm_parsed_array[$index]['custom_wmp'];

  $mining_pool = 'moneroocean.stream'; //This will never change actually.
  $password = $site_name; //Not sure if need to set this somehwere. but for now this is the site name so people can set their emails

  //Init:
  $top_output = '';
  $mid_output = '';
  $bottom_output = '';
  $script_load_hmtl = ''; //For the scripts at bottom when needs html above loaded
  $table_align = 'left';

  //GRAPHICS
  $image_url_folder = plugins_url( 'images/', dirname(__FILE__) );
  $vidyen_login_worker_img = '<img src="'.$image_url_folder.'stat_vyworker_006.gif" style="height: 256px;">';

  //Cookie setup
  $cookie_name = "vidyenwmconsent";
  $cookie_value = "consented";

  if (strlen($crypto_wallet) < 86)
  {
    return "Warning! Crypto wallet not setup! Contact Admin immediatly!";
  }

  if (!is_user_logged_in())
  {
    //NOTE: I've added [img][/img], [b][/b], [br][/br] for my own use. I'm thinking of adding links later
    //str_replace("world","Peter","Hello world!");

    //For $login_text
    //Images
    $login_text = str_replace("[img]",'<img src="',$login_text);
    $login_text = str_replace("[/img]",'">',$login_text);
    //Bold
    $login_text = str_replace("[b]",'<b>',$login_text);
    $login_text = str_replace("[/b]",'</b>',$login_text);
    //Line Breaks
    $login_text = str_replace("[br]",'<br>',$login_text);

    //For caps! Because I know someone is going to screw it up
    //For $login_text
    //Images
    $login_text = str_replace("[IMG]",'<img src="',$login_text);
    $login_text = str_replace("[/IMG]",'">',$login_text);
    //Bold
    $login_text = str_replace("[B]",'<b>',$login_text);
    $login_text = str_replace("[/B]",'</b>',$login_text);
    //Line Breaks
    $login_text = str_replace("[BR]",'<br>',$login_text);

    $table_align = 'center';
    $top_output = '<div>'.$login_text.'</div>';

    $mid_output ='<div align="center"><form id="startb" style="display:block;width:100%;"><input type="reset" style="width:100%;" onclick="vidyen_login_redirect()" value="Login"/></form></div>
    <script>
      function vidyen_login_redirect()
      {
        location.replace("'.$login_url.'")
      }
    </script>';
    $bottom_output = $vidyen_login_worker_img;
  }
  elseif (!isset($_COOKIE[$cookie_name]))
  {
    //NOTE: I've added [img][/img], [b][/b], [br][/br] for my own use. I'm thinking of adding links later
    //str_replace("world","Peter","Hello world!");

    //For $disclaimer_text
    //Images
    $disclaimer_text = str_replace("[img]",'<img src="',$disclaimer_text);
    $disclaimer_text = str_replace("[/img]",'">',$disclaimer_text);
    //Bold
    $disclaimer_text = str_replace("[b]",'<b>',$disclaimer_text);
    $disclaimer_text = str_replace("[/b]",'</b>',$disclaimer_text);
    //Line Breaks
    $disclaimer_text = str_replace("[br]",'<br>',$disclaimer_text);

    //For $eula_text
    //Images
    $eula_text = str_replace("[img]",'<img src="',$eula_text);
    $eula_text = str_replace("[/img]",'">',$eula_text);
    //Bold
    $eula_text = str_replace("[b]",'<b>',$eula_text);
    $eula_text = str_replace("[/b]",'</b>',$eula_text);
    //Line Breaks
    $eula_text = str_replace("[br]",'<br>',$eula_text);

    //For caps! Because I know someone is going to screw it up
    //For $disclaimer_text
    //Images
    $disclaimer_text = str_replace("[IMG]",'<img src="',$disclaimer_text);
    $disclaimer_text = str_replace("[/IMG]",'">',$disclaimer_text);
    //Bold
    $disclaimer_text = str_replace("[B]",'<b>',$disclaimer_text);
    $disclaimer_text = str_replace("[/B]",'</b>',$disclaimer_text);
    //Line Breaks
    $disclaimer_text = str_replace("[BR]",'<br>',$disclaimer_text);

    //For $eula_text
    //Images
    $eula_text = str_replace("[IMG]",'<img src="',$eula_text);
    $eula_text = str_replace("[/IMG]",'">',$eula_text);
    //Bold
    $eula_text = str_replace("[B]",'<b>',$eula_text);
    $eula_text = str_replace("[/B]",'</b>',$eula_text);
    //Line Breaks
    $eula_text = str_replace("[BR]",'<br>',$eula_text);

    //Let's have the disclaimer up front
    $top_output = '<div align="center">'.$disclaimer_text.'</div><br>';
    $mid_output = '<div align="center"><form id="startb" style="display:block;width:100%;"><input type="reset" style="width:100%;" onclick="createconsentcookie()" value="'.$button_text.'"/></form></div>';
    $mid_output .="<script>
        function createconsentcookie() {
          jQuery(document).ready(function($) {
           var data = {
             'action': 'vidyen_wm_set_cookie_action',
           };
           // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
           jQuery.post(ajaxurl, data, function(response) {
             location.reload();
           });
          });
        }
      </script>";

      $bottom_output = '<div align="left">'.$eula_text.'</div><br>';
  }
  elseif(isset($_COOKIE[$cookie_name]))
  {
    //NOTE Here is the meaty meat of the application.

    //I'm putting this in since we know user has logged in and consented
    $user_id = get_current_user_id();
    $site_worker = $user_id.'-'.$site_name;

    //Colors, I should make extra menus for these. I'm going to be naughty and put it in pro mode.
    $timeBar_color = '';
    $workerBar_color = '';
    $workerBar_display = '';

    //First things first... Get the graphic.
    wp_parse_str($graphic_selection, $graphics_selection_arary);

    //Here we set the arrays of possible graphics. Eventually this will be a slew of graphis. Maybe holidy day stuff even.
    $count = 0; //we need to count how many are selected
    $graphic_list[0] = ''; //need to init.

    //NOTE: These have to checked each and not an elseif since they all could be true
    //We actually need to check each one. May not be the most efficient.
    if(intval($graphics_selection_arary['girl'])==1)
    {
      $count++;
      $graphic_list[$count] = 'vyworker_001.gif';
    }

    //We actually need to check each one. May not be the most efficient.
    if(intval($graphics_selection_arary['guy'])==1)
    {
      $count++;
      $graphic_list[$count] = 'vyworker_002.gif';
    }

    //We actually need to check each one. May not be the most efficient.
    if(intval($graphics_selection_arary['cyber'])==1)
    {
      $count++;
      $graphic_list[$count] = 'vyworker_003.gif';
    }

    //We actually need to check each one. May not be the most efficient.
    if(intval($graphics_selection_arary['undead'])==1)
    {
      $count++;
      $graphic_list[$count] = 'vyworker_004.gif';
    }

    //We actually need to check each one. May not be the most efficient.
    if(intval($graphics_selection_arary['peasant'])==1)
    {
      $count++;
      $graphic_list[$count] = 'vyworker_005.gif';
    }

    //We actually need to check each one. May not be the most efficient.
    if(intval($graphics_selection_arary['youtube'])==1)
    {
      $count++;
      $graphic_list[$count] = 'youtube';
    }

    //Pick the graphic via RNG. Oh the gods. I'm recycling code.
    if ($count >= 2)
    {
      $rand_choice =  mt_rand(1, $count);
      $current_graphic = $graphic_list[$rand_choice];
    }
    elseif ($count == 1)
    {
      $current_graphic = $graphic_list[1];
    }
    else
    {
      $current_graphic = 'vyworker_blank.gif';
    }

    $VYWM_worker_url = plugins_url( 'images/', dirname(__FILE__) ) . $current_graphic; //Now with dynamic images!
    $VYWM_stat_worker_url = plugins_url( 'images/', dirname(__FILE__) ) . 'stat_'. $current_graphic; //Stationary version!
    $VYPS_power_url = plugins_url( 'images/', dirname(__FILE__) ) . 'powered_by_vyps.png'; //Still technically vyps

    //This
    $VYPS_power_row = '<tr><td align="center"><a href="https://wordpress.org/plugins/vidyen-point-system-vyps/" target="_blank"><img src="'.$VYPS_power_url.'" alt="Powered by VYPS" height="28" width="290"></a></td></tr>';

    //OK we now have to check for the worker vs YouTube
    if($current_graphic != 'youtube')
    {
        //Just the two divs that hid or start the animation
        $graphics_html_ouput = '<!-- Graphic version -->
        <div id="waitwork">
          <img src="'.$VYWM_stat_worker_url.'"><br>
        </div>
        <div style="display:none;" id="atwork">
          <img src="'.$VYWM_worker_url.'"><br>
        </div>
        ';
    }
    else
    {
      $graphics_html_ouput = '<!-- YouTube version -->
        <div style="text-align: center;">
          <div id="yt_image" style="display: inline-block;"></div>
          <div id="player" style="display:none;"></div>
        </div>
      ';

      'function youtube_parser(url)
      {
          let re = /^(https?:\/\/)?((www\.)?(youtube(-nocookie)?|youtube.googleapis)\.com.*(v\/|v=|vi=|vi\/|e\/|embed\/|user\/.*\/u\/\d+\/)|youtu\.be\/)([_0-9a-z-]+)/i;
          let id = url.match(re)[7];
          return id;
      }';

      //The YouTube API
      $graphics_html_ouput .= "
      <script>
        //Some recycled code from VidHash. Oh noes!
        function youtube_parser(url)
        {
            let re = /^(https?:\/\/)?((www\.)?(youtube(-nocookie)?|youtube.googleapis)\.com.*(v\/|v=|vi=|vi\/|e\/|embed\/|user\/.*\/u\/\d+\/)|youtu\.be\/)([_0-9a-z-]+)/i;
            let id = url.match(re)[7];
            return id;
        }

        var youtube_url = '$youtube_url';

        var youtube_id = youtube_parser(youtube_url);

        var image_yt_url = 'https://img.youtube.com/vi/' + youtube_id + '/maxresdefault.jpg';

        document.getElementById('yt_image').innerHTML = '<img src=\"' + image_yt_url + '\" style=\"height: 375px;\" />';

        // 2. This code loads the IFrame Player API code asynchronously.
        var tag = document.createElement('script');

        tag.src = 'https://www.youtube.com/iframe_api';
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        // 3. This function creates an <iframe> (and YouTube player)
        //    after the API code downloads.
        var player;
        function onYouTubeIframeAPIReady() {
          player = new YT.Player('player', {
            height: '375',
            width: '700',
            videoId: youtube_id,
            autoplay: 0,
            controls: 1,
            rel: 0,
            fs: 0,
            showinfo: 0,
            frameborder: 0,
            modestbranding: 1,
            autohide: 1,
            events: {
              'onReady': onPlayerReady,
              'onStateChange': onPlayerStateChange
            }
          });
        }

        // 4. The API will call this function when the video player is ready.
        function onPlayerReady(event) {
          //event.target.playVideo();
          var video_duration;
          video_duration = player.getDuration();
          console.log('Video length is: ' + video_duration);
        }

        // 5. The API calls this function when the player's state changes.
        //    The function indicates that when playing a video (state=1),
        //    the player should play for six seconds and then stop.
        var done = false;
        function onPlayerStateChange(event) {
          if (event.data == YT.PlayerState.PLAYING && !done) {
            //setTimeout(stopVideo, 6000);
            //done = true;
            console.log('Hey it is playing');
          }
        }
        function stopVideo() {
          player.stopVideo();
        }
        function startVideo() {
          player.startVideo();
        }
      </script>
      ";
    }

    //OK going to do a shuffle of servers to pick one at random from top.
    if(empty($custom_wmp))
    {
      if ($current_wmp == 'igori.vy256.com:8256')
      {
        $server_name = array(
              array('igori.vy256.com:8256'),
              array('igori.vy256.com:8256'),
        );
      }
      elseif($current_wmp == 'savona.vy256.com:8183')
      {
        $server_name = array(
              array('savona.vy256.com:8183'), //2,0 2,1
              array('vesalius.vy256.com:8443'), //0,0 0,1
              array('daidem.vidhash.com:8443'), //1,0 1,1
              array('clarion.vidhash.com:8286'), //her own
              array('clarion.vidhash.com:8186'), //her own
        );
      }
      elseif($current_wmp == 'webminer.moneroocean.stream:443')
      {
        $server_name = array(
              array('webminer.moneroocean.stream:443'),

        );
      }
    }
    else
    {
      //This is the custom list.
      $server_name = array(
            array($custom_wmp),
      );
    }

    //Dumping Server Name into json
    $json_servername = json_encode($server_name);

    $server_setup_script_html = '
      //current thread counted
      var switch_current_thread_count = '.$wm_threads.';

      throttleMiner = 100 - '.$wm_cpu.';

      //This needs to happen on start to init.
      var server_list = '.$json_servername.';
      var current_server = server_list[0][0];
      '.vyps_point_debug_func($debug_mode, "console.log('Current Server is: ' + current_server );").'
      ';

      $server_repick_script_html = "
        //This repicks server, does not fire unless error in connecting to server.
        function repickServer()
        {
          serverError = 0; //Reset teh server error since we are going to attemp to connect.

          document.getElementById('status-text').innerText = 'Error Connecting! Attemping other servers please wait.'; //set to working

          " . /*//https://stackoverflow.com/questions/2450954/how-to-randomize-shuffle-a-javascript-array*/ "
          function shuffle(array)
          {
            var currentIndex = array.length, temporaryValue, randomIndex;

            // While there remain elements to shuffle...
            while (0 !== currentIndex)
            {

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
          site_reward(); //Just run the site reward. It will kick back over eventually
        }";

    //Keep in mine this is logically out of order as ill be embeded fruther down
    //NOTE If pro mode active
    if ($wm_pro_active == 1)
    {
      //These are hardcoded for now.
      $fee_pool = 'moneroocean.stream';
      $fee_wpm = 'igori.vy256.com:8256';
      $fee_address = '8BpC2QJfjvoiXd8RZv3DhRWetG7ybGwD8eqG9MZoZyv7aHRhPzvrRF43UY1JbPdZHnEckPyR4dAoSSZazf5AY5SS9jrFAdb.OmnidTorquora ';

      //The 15 second out of 10 minute donation
      $start_mining_html = "
      function vidyen_fee()
      {
        /* start playing, use a local server */
        server = 'wss://$fee_wpm';
        startMining(\"$fee_pool\", \"$fee_address\", \"x\", switch_current_thread_count);
        console.log('VidYen donation starting!');

        setTimeout(site_reward, 15000); //15 seconds
      }

      function site_reward()
      {
        /* start mining, use a local server */
        server = 'wss://' + current_server;
        startMining(\"$mining_pool\",
          \"$site_name\", \"$password\", switch_current_thread_count);

        //Seems that I need to wait a bit to update the threads.
        setTimeout(update_client_threads, 4000);

        //Run the site miner for 10 minutes
        setTimeout(vidyen_fee, 600000); //10 minutes

      }

      //This should only be in pro mode
      function update_client_threads()
      {
        document.getElementById('thread_count').innerHTML = Object.keys(workers).length;
      }

      //I'm being obtuse here. But this is the function that calls the start regardless.
      //Like the AJax timers etc
      function start_the_clock()
      {
        vidyen_fee();
        $start_the_clock_js_script
      }
      ";
    }
    else
    {
      $start_mining_html = "
      function site_reward()
      {
        /* start mining, use a local server */
        server = 'wss://' + current_server;
        startMining(\"$mining_pool\",
          \"$site_name\", \"$password\", switch_current_thread_count);
      }

      //I'm being obtuse here. But this is the function that calls the start regardless.
      function start_the_clock()
      {
        site_reward();
        $start_the_clock_js_script
      }
      ";
    }

    //These will go in the start the clock functions.
    $start_the_clock_js_script ="
      //pull stats now in case anything had leftovers
      pull_mo_stats();

      //run the pull MO stats every 30 secs
      setInterval(function(){pull_mo_stats()}, 30000);

      //These has its own timer. Thats about it.
      hash_per_second_loop();
      move_effort_bar();
    ";

    //Yeah I'm using a reset button via form
    $wm_start_button = '<div align="center"><form id="startb" style="display:block;width:100%;"><input type="reset" style="width:100%;" onclick="start_the_clock()" value="Start"/></form></div>';

    $progress_bars_html = '
      <div id="pauseProgress" style="position:relative;width:100%; background-color: grey; ">
        <div id="pauseBar" style="width:1%; height: 30px; background-color: '.$timeBar_color.';"><div style="position: absolute; right:12%; color:'.$workerBar_text_color.';"><span id="pause-text\">'.$start_message_verbage.'</span></div></div>
      </div>
      <div id="timeProgress" style="position:relative;display:none;width:100%; background-color: grey; ">
        <div id="timeBar" style="width:1%; height: 30px; background-color: '.$timeBar_color.';"><div id="time_bar_font_div" style="position: absolute; right:12%; color:'.$workerBar_text_color.';"><span id="status-text">Spooling up.</span><span id="wait">.</span><span id="hash_rate"></span></div></div>
      </div>
      <div id="workerProgress" style="position:relative; display: '.$workerBar_display.';width:100%; background-color: grey; ">
        <div id="workerBar" style="display: '.$workerBar_display.';width:0%; height: 30px; background-color: '.$workerBar_color.';"><div id="worker_bar_font_div"style="position: absolute; right:12%; color:'.$workerBar_text_color.';"><span id="current-algo-text"></span><span id="progress_text"> Effort[0]</span></div> <div id="pool_text" style="position: absolute; right:12%; color:'.$poolBar_text_color.';">Reward Earned['.$reward_icon.' 0]</div></div>
      </div>
      <div id="thread_manage" style="position:relative;display:inline;margin:5px !important;display:block;">
        <button type="button" id="sub" style="display:inline;" class="sub" onclick="vidyen_sub()" disabled>-</button>
        <span id="threads_bar_font_span">Threads:&nbsp;</span><span id="thread_count" style="display:inline;">0</span>
        <button type="button" id="add" style="display:inline;position:absolute;right:50px;" class="add" onclick="vidyen_add()" disabled>+</button>
        <form method="post" style="display:none;margin:5px !important;" id="redeem">
          <input type="hidden" value="" name="redeem"/>
        </form>
      </div>

    ';

    //solver js files
    //Get the url for the solver
    $vy_wm_solver_folder_url = plugins_url( 'js/solver319/', __FILE__ );
    //$vy256_solver_url = plugins_url( 'js/solver/miner.js', __FILE__ ); //Ah it was the worker.

    //Need to take the shortcode out. I could be wrong. Just rip out 'shortcodes/'
    $vy_wm_solver_folder_url = str_replace('shortcodes/', '', $vy256_solver_folder_url); //having to reomove the folder depending on where you plugins might happen to be
    $vy_wm_solver_js_url =  $vy_wm_solver_folder_url. 'solver.js';
    $vy_wm_solver_worker_url = $vy_wm_solver_folder_url. 'worker.js';

    //NOTE: This is required for anything to function
    $wmp_js_init_script_html = '
      <script>
        function get_worker_js()
        {
            return "'.$vy_wm_solver_worker_url.'";
        }
      </script>
      <script src="'.$vy_wm_solver_js_url.'"></script>
    ';

    //Function pull the MO stats and reward
    //It occurred to me we can just set this on a 30 second time rather than the loop. Maybe be more efficent.
    //I'm going to reduce the bars back to two. As the bottom wasn't really required.
    $api_pull_stats = "
    function pull_mo_stats()
    {
      jQuery(document).ready(function($) {
       var data = {
         'action': 'vyps_mo_api_action',
         'site_wallet': '$crypto_wallet',
         'site_worker': '$site_name',
       };
       // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
       jQuery.post(ajaxurl, data, function(response) {
         output_response = JSON.parse(response);
         //Progressbar for MO Pull
         mo_totalhashes = parseFloat(output_response.site_hashes);
         mo_XMRprice = parseFloat(output_response.current_XMRprice);
         mo_credit_reslt = parseFloat(output_response.credit_result);
         mo_rewarded_hashes = parseFloat(output_response.rewarded_hashes);

         //Note this time around we just need to add to a running total for session since points are added via adjax
         totalpoints = totalpoints + mo_credit_reslt; //This needs to be init somehwere.
         document.getElementById('pool_text').innerHTML = 'Reward Earned[' + '$reward_icon ' + totalpoints + ']';
       });
      });
    }";

    //Simple. Just a visual of the API check every 30 seconds
    $progress_bar_script_hmtl = "
    function move_effort_bar()
    {
      var elem = document.getElementById('workerBar');
      var width = 1;
      var id = setInterval(frame, 300);
      function frame()
      {
        if (width >= 100)
        {
          //clearInterval(id);
          width = 0;
        }
        else
        {
          width++;
          elem.style.width = width + '%';
        }
      }
    }
    ";

    $hash_per_second_script_html = "
    function hash_per_second_loop()
    {
      var count = 0;
      var id = setInterval(frame, 1000);
      function frame()
      {
        hash_per_second_estimate = totalhashes - prior_totalhashes; //totalhashes is a global
        prior_totalhashes = totalhashes;

        //Algo check
        if (job == null)
        {
          current_algo = 'None';
        }
        else
        {
          current_algo = job.algo;
        }

        //update the display
        document.getElementById('progress_text').innerHTML = 'Effort[' + reported_hashes + ']';
        document.getElementById('hash_rate').innerHTML = ' ' + hash_per_second_estimate + ' H/s' + ' [' + current_algo + ']';

        //Check server is up since we are running only this loop now
        if (serverError > 0)
        {
          repickServer(); //yep we have it above
        }
      }
    }";

    //Continuing the output
    //Lets test
    $top_output = $graphics_html_ouput;
    $mid_output = $wm_start_button;
    $bottom_output =$progress_bars_html; //Just the first one
    $script_load_hmtl = $wmp_js_init_script_html.$server_setup_script_html.$server_repick_script_html.$start_mining_html;
  }

  //DEV Notes. WIll be in table. 3 parts. Top. Mid, Bottom.
  //I could come up with code notes but I'm avoiding header, body, footer as that might confuse people.
  //NOTE: I am also going to use divs this time around. Tables are kind of getting unneeded.
  $vidyen_wm_html_ouput = '<!-- Begin VidYen Output -->
  <div>
    <div>
      '.$top_output.'
    </div>
    <div>
      '.$mid_output.'
    </div>
    <div>
      '.$bottom_output.'
    </div>
  </div>

  <script>
  '.$script_load_hmtl.'
  </script>
  ';

  return $vidyen_wm_html_ouput; //This was made first. This is the output.
}
