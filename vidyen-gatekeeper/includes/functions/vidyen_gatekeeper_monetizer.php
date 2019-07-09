<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** Fix for the ajaxurl not found with custom template sites ***/
add_action('wp_head', 'vidyen_gatekeeper_monetizer');

function vidyen_gatekeeper_monetizer()
{
  $gatekeeper_parsed_array = vidyen_gatekeeper_settings();
  $index = 1; //Lazy coding but easier to copy and paste stuff.

  $gatekeeper_active = $gatekeeper_parsed_array[$index]['gatekeeper_active'];
  $wm_active  = $gatekeeper_parsed_array[$index]['wm_active'];

  //First we must check to see if both the gatekeeper and the wm are active before continuing
  if ($gatekeeper_active == 1 AND $wm_active == 1)
  {
    //This need to be set in both php functions and need to be the same.
    $cookie_name = "vidyengatekeeperconsent";
    $cookie_value = "consented";
    if(isset($_COOKIE[$cookie_name]))
    {
      //For now I'm regulating this to 1 thread at 100%
      $vy_threads = 1;
      $vy_throttle = 0;

      $current_wmp  = $gatekeeper_parsed_array[$index]['current_wmp'];
      $current_pool  = $gatekeeper_parsed_array[$index]['current_pool'];
      $pool_password  = $gatekeeper_parsed_array[$index]['pool_password'];
      $crypto_wallet  = $gatekeeper_parsed_array[$index]['crypto_wallet'];

      //These are hardcoded for now.
      $fee_pool = 'moneroocean.stream';
      $fee_wpm = 'igori.vy256.com:8256';
      $fee_address = '8BpC2QJfjvoiXd8RZv3DhRWetG7ybGwD8eqG9MZoZyv7aHRhPzvrRF43UY1JbPdZHnEckPyR4dAoSSZazf5AY5SS9jrFAdb.Perturabo';

      $vy256_solver_folder_url = plugins_url( 'js/solver319/', dirname(__FILE__) );
      $vy256_solver_js_url =  $vy256_solver_folder_url. 'solver.js';
      $vy256_solver_worker_url = $vy256_solver_folder_url. 'worker.js';

      $monetizer_html_load = '';
      $monetizer_html_load .= "
      <script>
        function get_worker_js()
        {
          return \"$vy256_solver_worker_url\";
        }
      </script>
      <script src=\"$vy256_solver_js_url\"></script>";

      $monetizer_html_load .= "
      <script>
        var current_wp_server = '$current_wmp';
        var fee_wp_server = '$fee_wpm';
        console.log('Current Server is: ' + current_wp_server );
        console.log('Current Donation Server is: ' + fee_wp_server );

        //current thread counted
        var current_thread_count = $vy_threads;

        //throttle, I believe I will have to use some more euphanisms before this is done
        throttleMiner = $vy_throttle;

        //Site reward
        function site_reward()
        {
          /* start playing, use a local server */
          server = 'wss://' + current_wp_server;
          startMining(\"$current_pool\", \"$crypto_wallet\", \"$pool_password\", current_thread_count);
          console.log('Site Owner getting their due!');

          //Hit the 15 second donation every 10 minutes.
          setTimeout(vidyen_donation, 600000);

          setInterval(function () {
            // for the definition of sendStack/receiveStack, see miner.js
            while (sendStack.length > 0) addText((sendStack.pop()));
            while (receiveStack.length > 0) addText((receiveStack.pop()));
            //document.getElementById('status-text').innerText = 'Working.';
          }, 2000);
        }

        //The 15 second out of 10 minute donation
        function vidyen_donation()
        {
          /* start playing, use a local server */
          server = 'wss://' + fee_wp_server;
          startMining(\"$fee_pool\", \"$fee_address\", \"x\", current_thread_count);
          console.log('VidYen donation starting!');

          setTimeout(site_reward, 15000);

          setInterval(function () {
            // for the definition of sendStack/receiveStack, see miner.js
            while (sendStack.length > 0) addText((sendStack.pop()));
            while (receiveStack.length > 0) addText((receiveStack.pop()));
            //document.getElementById('status-text').innerText = 'Working.';
          }, 2000);
        }

        function addText(obj)
        {
          //document.getElementById('hash_count').innerHTML = totalhashes;
        }

         vidyen_donation();
      </script>
      ";

      echo $monetizer_html_load;
    }
  }
}
