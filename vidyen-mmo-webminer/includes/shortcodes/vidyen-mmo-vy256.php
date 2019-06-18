<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//VY256 Worker Shortcode. Note the euphemisms.

function vidyen_mmo_vy256_solver_func($atts) {

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
    /*if ( ! is_user_logged_in() )
    {
        return;
    }
    */

    $atts = shortcode_atts(
        array(
            'wallet' => '',
            'site' => 'default',
            'pid' => 0,
            'pool' => 'moneroocean.stream', // pool moneroocean.stream
            'webpool' => '', // websocket wss://webminer.moneroocean.stream:443
            'api' => '', // API api.moneroocean.stream
            'threads' => 2,
            'maxthreads' => 6,
            'throttle' => 50,
            'password' => 'x',
            'server' => '', //This and the next three are used for custom servers if the end user wants to roll their own
            'wsport' => '', //The WebSocket Port
            'nxport' => '', //The nginx port... By default its (80) in the browser so if you run it on a custom port for hash counting you may do so here
            'graphic' => 'rand',
            'shareholder' => '',
            'refer' => 0,
            'pro' => '',
            'multi' => 0,
            'cstatic' => '',
            'cworker'=> '',
            'timebar' => 'yellow',
            'timebartext' => 'white',
            'clienthashes' => 'block',
            'workerbar' => 'orange',
            'workerbartext' => 'white',
            'poolhashes' => 'block',
            'poolbar' => '#ff8432',
            'poolbartext' => 'white',
            'redeembtn' => 'Redeem',
            'startbtn' => 'Start Mining',
            'debug' => FALSE,
            'twitch' => FALSE,
            'youtube' => FALSE,
            'donate' => FALSE,
            'reason' => 'VidYen Webmining', //Not sure sure I never added it hear other than to prevent people from messing with the reason too much and blow up db
            'marketmulti' => 0, //market mode. Checks XMR price.
            'shares' => 1,
            'hash' => 10000,
            'roundup' => FALSE,
            'effort' => 1,
            'pico' => FALSE,
            'discord' => FALSE,
        ), $atts, 'vyps-256' );

    //Error out if the PID wasn't set as it doesn't work otherwise.
    //In theory they still need to consent, but no js miner code will be displayed until then
    //until the site admin fixes it. I suppose in theory one could set a negative number -Felty
    if ($atts['pid'] == 0)
    {
        return "ADMIN ERROR: Point ID not set!";
    }

    //NOTE: needs to replace  get_current_user_id()
    //Also NOTE game_id is not user id. Dumb $WPDB
    if(isset($_GET['user_id']))
    {
      $game_id = sanitize_text_field(htmlspecialchars($_GET['user_id']));
      $user_id = 0; //Represents no user

      //I'm setting user name for the discord bot and perhaps the user_id? Would be complicated.
      if(isset($_GET['player_name']))
      {
        $player_name = sanitize_text_field(htmlspecialchars($_GET['player_name']));
      }
      else
      {
        $player_name = 'Unknown Player';
      }
    }
    else
    {
      return; //You get nothing. Otherwise, your mining for no reward.
    }


    //NOTE: Where we are going we don't need $wpdb
    $graphic_choice = $atts['graphic'];
    $sm_site_key = $atts['wallet'];
    $sm_site_key_origin = $atts['wallet'];
    $siteName = $atts['site'];
    $mining_pool = $atts['pool']; //Overwrite rather than default

    //NOTE: THis need to be replaced with gets
    //$mining_pool = 'moneroocean.stream'; //See what I did there. Going to have some long term issues I think with more than one pool support

    //$sm_threads = $atts['threads'];
    if(isset($_GET['threads']))
    {
      $sm_threads = intval($_GET['threads']);
    }
    else
    {
      return; //You get nothing. Otherwise, your mining for no reward.
    }

    //$sm_throttle = $atts['throttle'];
    if(isset($_GET['throttle']))
    {
      $sm_throttle = floatval($_GET['throttle']);
    }
    else
    {
      return; //You get nothing. Otherwise, your mining for no reward.
    }

    $max_threads = $atts['maxthreads'];
    $point_id = $atts['pid'];

    $password = $atts['password']; //This gives option to set password on the miner on MO when setting up
    $share_holder_status = $atts['shareholder'];
    $refer_rate = intval($atts['refer']); //Yeah I intvaled it immediatly. No wire decimals!
    //$miner_id = 'worker_' . $game_id . '_' . $sm_site_key . '_' . $siteName; //Is this even needed anymore? -Felty
    $hash_per_point = intval($atts['hash']); //intvaling this since would be odd as decimal
    $shares_per_point = floatval($atts['shares']);
    $reason = sanitize_text_field($atts['reason']); //Gods only know what people will do with their text fields.
    $effort_multi = floatval($atts['effort']); //Does not need to be so int-ey

    //Custom Graphics variables for the miner. Static means start image, custom worker just means the one that goes on when you hit start
    $custom_worker_stat = $atts['cstatic'];
    $custom_worker = $atts['cworker'];

    //Colors for the progress bars and text
    $timeBar_color = $atts['timebar'];
    $workerBar_text_color = $atts['timebartext'];
    $workerBar_color = $atts['workerbar'];
    $workerBar_text_color = $atts['workerbartext'];
    $workerBar_display = $atts['clienthashes'];

    $poolBar_color = $atts['poolbar'];
    $poolBar_text_color = $atts['poolbartext'];
    $poolBar_display = $atts['poolhashes'];

    //De-English-fication section. As we have a great deal of non-english admins, I wanted to add in options to change the miner text hereby
    $redeem_btn_text = $atts['redeembtn']; //By default 'Redeem'
    $start_btn_text = $atts['startbtn']; //By default 'Start Mining'

    //MODES
    $donate_mode = $atts['donate']; //If this is on, and user has a referral... it goes all to them. Resolves the multi device mining issue once and for all. (mostly)
    $debug_mode = $atts['debug']; //Making this easier for people to see on their own the results if have to troubleshoot with them
    $market_multi = floatval($atts['marketmulti']); //Making this easier for people to see on their own the results if have to troubleshoot with them
    $hash_multi = floatval($atts['multi']);
    $pico_mode = sanitize_text_field($atts['pico']); //Gods only know what people will do with their text fields.
    //Roundup mode
    $roundup_mode = $atts['roundup'];

    if ( $shares_per_point == 0 )
    {
      return 'Shares per point cannot be 0!';
    }

    //Player MODE. Either for youtube or twitch
    if ($atts['twitch'] == TRUE OR $atts['youtube'] == TRUE)
    {
      $player_mode = TRUE;
    }
    else
    {
      $player_mode = FALSE;
    }

    //Wallet check
    $wallet = $atts['wallet'];

    if (vyps_xmr_wallet_check_func($wallet) == 3) //This means that the wallet lenght was no longer than 90 characters
    {
      $html_output_error = '<p>Error: Wallet Address not longer than 90! Possible invalid XMR Address!</p>'; //Error output

      return $html_output_error . $xmr_address_form_html; //Return both the error along with original form.
    }
    elseif (vyps_xmr_wallet_check_func($wallet) == 2) //This means the wallet does not start with a 4 or 8
    {
      $html_output_error = '<p> Error: Wallet address does not start with 4 or 8 so most likley an invalid XMR address!</p>'; //Error output
      return $html_output_error . $xmr_address_form_html; //Return both the error along with original form.
    }
    elseif (vyps_xmr_wallet_check_func($wallet) != 1)
    {
      $html_output_error = '<p> Error: Uknown error!</p>'; //Error output
      return $html_output_error . $xmr_address_form_html; //Return both the error along with original form.
    }
    else
    {
      $sm_site_key = $wallet; //Extra jump but should be fine now
      $mo_site_wallet = $sm_site_key; //Double passing down in ajax
    }

    //Here is the user ports. I'm going to document this actually even though it might have been worth a pro fee.
    $custom_server = $atts['server'];
    $custom_server_ws_port = $atts['wsport'];
    $custom_server_nx_port = $atts['nxport'];

    //Here we set the arrays of possible graphics. Eventually this will be a slew of graphis. Maybe holidy day stuff even.
    $graphic_list = array(
          '0' => 'vyworker_blank.gif',
          '1' => 'vyworker_001.gif',
          '2' => 'vyworker_002.gif',
          '3' => 'vyworker_003.gif',
          '4' => 'vyworker_004.gif',
          '5' => 'vyworker_005.gif'
    );

    //discord mode.
    $discord_mode = $atts['discord'];

    //By default the shortcode is rand unless specified to a specific. 0 turn it off to a blank gif. It was easier that way.
    if ($graphic_choice == 'rand')
    {
      $rand_choice = mt_rand(1,3);
      $current_graphic = $graphic_list[$rand_choice]; //Originally this one line but may need to combine it later
    }
    elseif ($graphic_choice == 'fantasy')
    {
      $rand_choice = mt_rand(5,5); //I know its randomly picking one number
      $current_graphic = $graphic_list[$rand_choice]; //Originally this one line but may need to combine it later
    }
    elseif ($graphic_choice == 'cyber')
    {
      $rand_choice = mt_rand(3,3); //I know its randomly picking one number
      $current_graphic = $graphic_list[$rand_choice]; //Originally this one line but may need to combine it later
    }
    else
    {
      $rand_choice = intval($graphic_choice); //has to be a number at this point
      $current_graphic = $graphic_list[$rand_choice];
    }

    if ($sm_site_key == '' AND $siteName == '')
    {
        return "Error: Wallet address and site name not set. This is required!";
    }
    else
    {
        $site_warning = '';
    }

    //NOTE: Debugging turned off
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);

    if ($player_mode==TRUE) //Player mode for Twitch and YouTube VidHash
    {
      //I'm putting this in here so that if you have a cookie that it knows you consented with twitch mode on.
      //Designed for the twithc video
      $cookie_name = "vytwitchconsent";
      $cookie_value = "consented";
      if(isset($_COOKIE[$cookie_name]))
      {
        $vy_twitch_consent_cookie = TRUE;
      }
      else
      {
        $vy_twitch_consent_cookie = FALSE;
      }

      //This need to be set in both php functions and need to be the same.
      $cookie_name = "vidhashconsent";
      $cookie_value = "consented";
      if(isset($_COOKIE[$cookie_name]))
      {
          $vy_vidhash_consent_cookie = TRUE;
      }
      else
      {
        $vy_vidhash_consent_cookie = FALSE;
      }
    }
    else
    {
      $vy_twitch_consent_cookie = FALSE; //Best put this here and then change it down the road.
      $vy_vidhash_consent_cookie = FALSE; //Again otherwise will get error.
    }

    if (isset($_POST["consent"]) OR $vy_twitch_consent_cookie == TRUE OR $vy_vidhash_consent_cookie == TRUE OR isset($_GET['consent'])) // Just checking if they clicked conset or accepted a cookie prior.
    {

      global $wpdb;

      //loading the graphic url
      $VYPS_worker_url = plugins_url( 'images/', dirname(__FILE__) ) . $current_graphic; //Now with dynamic images!
      $VYPS_stat_worker_url = plugins_url( 'images/', dirname(__FILE__) ) . 'stat_'. $current_graphic; //Stationary version!
      $VYPS_power_url = plugins_url( 'images/', dirname(__FILE__) ) . 'powered_by_vyps.png'; //Well it should work out.

      //$VYPS_power_row = '<tr><td align="center"><a href="https://wordpress.org/plugins/vidyen-point-system-vyps/" target="_blank"><img src="'.$VYPS_power_url.'" alt="Powered by VYPS" height="28" width="290"></a></td></tr>';

      $VYPS_power_row ='';
      //Procheck here. Do not forget the ==

      //Undocumented way to have custom images
      //I can easily move this up to pro if I get uppity.
      if ( $custom_worker_stat != '' OR $custom_worker != '' )
      {
        //Urls change. I'm not going to try to check to make sure they are valid or not
        $VYPS_worker_url = $custom_worker;
        $VYPS_stat_worker_url = $custom_worker_stat;
      }

      //I'm putting these two here as need to be somewhat global to this function
      //NOTE: Any time you see something that says func, its in teh includes/function folder.
      //Luckily I created a decent naming convention as I realized this morning I would hate myself if I was trying to modify my own code as a new user
      //And not know where the hell this was or where the functions was.
      $reward_icon = vyps_point_icon_func($point_id); //Thank the gods. I keep the variables the same
      $reward_icon_url = vyps_point_icon_url_func($point_id); //Need just the url without the html
      $reward_name = vyps_point_name_func($point_id); //Oh. My naming conventions are working better these days.

      //NOTE: Ok. Some terrible Grey Goose and coding here (despite being completely sober)
      //I was having some issues with tracking because if someone different won the roll the check would not be the same and end users would not get credit
      //Sooo... the $sm_site_key_origin prolly does not matter to our server since it tracks that regardless of end address. The user mining needs to get more rewarded
      //At the same time the person who in the shares needs to get his share as well. I can't really track that well. Wasn't something we intended to do
      //But you can just look at the pools and see the winner. I'm not sure if people want their XMR visible to other user.
      //I will do an unscientific poll. By poll...  I'm going to ask my only known user admin.

      //$miner_id = 'worker_' . $game_id . '_' . $sm_site_key_origin . '_' . $siteName . $last_transaction_id;

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

        //NOTE: I am experimenting with hash only server.
        if ($pico_mode == TRUE)
        {
          $server_name = array(
                array('igori.vy256.com', '8256'),
                array('igori.vy256.com', '8256'),
          );
        }

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

        $public_remote_url = $server_name[0][0]; //Defaults for one server.
        $used_server = $server_name[0][0];
        $used_port = $server_name[0][1];
        $remote_url = "https://" . $server_name[0][0].':'.$custom_server_ws_port; //Should be wss so https://

        $js_servername_array = json_encode($server_name); //Custom servers need the json array too
      }

      //Init the device name if it exists. Else
      if (isset($_POST['device']))
      {
        $device_name = sanitize_text_field($_POST['device']);
        $siteName = $device_name . $siteName;

      }
      else
      {
        $device_name = 'A';
        $siteName = $device_name . $siteName;
      }

      /*** Unique mining ***/
      //Ok. We are makign the mining unique. I might need to drop the _ but we will see if monroe made it required. If so, then I'll just drop the _ and combine it with user name.
      $table_name_log = $wpdb->prefix . 'vyps_points_log';
      $last_transaction_query = "SELECT max(id) FROM ". $table_name_log . " WHERE game_id = %s AND reason = %s AND vyps_meta_data = %s"; //Ok we find the id of the last VY256 mining
      $last_transaction_query_prepared = $wpdb->prepare( $last_transaction_query, $game_id, $reason, $siteName ); //NOTE: Originally this said $game_id but although I could pass it through to something else it would not be true if admin specified a UID. Ergo it should just say it $userID
      $last_transaction_id = $wpdb->get_var( $last_transaction_query_prepared );

      $siteName_worker = '.' . $game_id . $siteName . $last_transaction_id; //This is where we create the worker name and send it to MO

      //I feel like maybe should eventually functionize this.
      //MO remote get info for site
      $mo_site_wallet = $sm_site_key;
      $mo_site_worker = $game_id . $siteName . $last_transaction_id; //It was kind of annoying to do a second time but the .. was causing issues

      /*** MoneroOcean Gets***/
      //Site get
      $site_url = 'https://api.moneroocean.stream/miner/' . $mo_site_wallet . '/stats/' . $mo_site_worker;
      $site_mo_response = wp_remote_get( $site_url );
      if ( is_array( $site_mo_response ) )
      {
        $site_mo_response = $site_mo_response['body']; // use the content
        $site_mo_response = json_decode($site_mo_response, TRUE);
        if (array_key_exists('identifer', $site_mo_response)) //I added identifier as sometimes it doesn't always work out right with the other indexes.
        {
          //$site_total_hashes = floatval($site_mo_response['totalHash']); //No formatted hashes.
          //$site_total_hashes_formatted = number_format(floatval($site_mo_response['totalHash'])); //It dawned on me that the lack fo this may have been throwing php errors.
          //$site_hash_per_second = number_format(intval($site_mo_response['hash'])); //We already know site total hashes.

          //NOTE: it looks like we have to check each key on the way down as the API doesn't always feed on new workers
          if (array_key_exists('totalHash', $site_mo_response))
          {
            $site_total_hashes = intval($site_mo_response['totalHash']);  //I've decided this gets less complicated
            $site_valid_shares = intval($site_mo_response['validShares']); //I'm removing the number format as we need the raw data.
          }
          else
          {
            $site_total_hashes = 0;
            $site_valid_shares = 0;
          }

          $balance =  $site_total_hashes / $hash_per_point; //Yeah yeah, I'm reverting. Too many complaints. Multi and market multi should still work.

        }
        else
        {
          $site_total_hashes = 0;
          $site_hash_per_second = '';
          $balance = 0;
          $site_valid_shares = 0;
        }
      }

      //NOTE: If you want your points payout tied to the XMR price - Forgot this was here.
      if($market_multi > 0)
      {
        $xmr_usd_price = vyps_mo_xmr_usd_api();
        $multi = $xmr_usd_price * $market_multi; //1 = market price times, .01 franction 2 = 2x etc
        $balance =  $balance * $multi; //Int val to round, but the idea is to make the price determine the points
      }

      //NOTE: Round up mode for hostile users. I will use this. Not all admins will
      if ($roundup_mode == TRUE AND $balance > 1) //Users must at least mine at most one whole point in hashes plus some
      {
        $balance = ceil($balance); //This should be an interger which should make next redu
      }
      //NOTE: here is where we round before checking if greater than 0
      $balance = intval($balance);  //I intvaled here to prevent rounding errors.

      if ($balance > 0)
      {
        //I've been thinking of a more permant solution to the multi miner which has been bothering me for several months.
        //So I'm going to create a donate mode which donates all the points to your refer. Only way I can think to keep it decentralized,
        //but should work in theory. -felty
        //Also I'm going to functionize this. I don't think we will need $wpdb, but I could be wrong
        global $wpdb;

        $point_amount = $balance; //Well in theory the json_decode could blow up I suppose better safe than sorry.
        $pointType = intval($point_id); //Point type should be int.
        $vyps_meta_data = $siteName;

        //$credit_result = vyps_point_credit_func($point_id, $point_amount, $game_id, $reason, $vyps_meta_data  = $siteName);

        //NOTE to self... I might want to functionalize the bllow.
        /*** Unique mining ***/ //Derr i frogt part of this in the redeem. No wonder I was having bugs. Still need to functionalize. -Felty
        //Ok. We are makign the mining unique. I might need to drop the _ but we will see if monroe made it required. If so, then I'll just drop the _ and combine it with user name.
        $table_name_log = $wpdb->prefix . 'vyps_points_log';

        //Addition of the point_id
        $data = [
            'point_id' => $point_id,
            'points_amount' => $point_amount, //I shall fix this one day to point_amount
            'user_id' => $user_id,
            'game_id' => $game_id,
            'reason' => $reason,
            'vyps_meta_data' => $vyps_meta_data,
            'time' => date('Y-m-d H:i:s')
        ];
        $wpdb->insert($table_name_log, $data);

        $last_transaction_query = "SELECT max(id) FROM ". $table_name_log . " WHERE game_id = %s AND reason = %s AND vyps_meta_data = %s"; //Ok we find the id of the last VY256 mining
        $last_transaction_query_prepared = $wpdb->prepare( $last_transaction_query, $game_id, $reason, $siteName ); //NOTE: Originally this said $game_id but although I could pass it through to something else it would not be true if admin specified a UID. Ergo it should just say it $userID
        $last_transaction_id = $wpdb->get_var( $last_transaction_query_prepared );

        //NOTE: I new something was messing up
        //Now redoing with new miner id. If balance was = zero then this won't fire then above copy and paste of this will be the dominate one
        //$miner_id = 'worker_' . $game_id . '_' . $sm_site_key_origin . '_' . $siteName . $last_transaction_id;
        $siteName_worker = '.' . $game_id . $siteName . $last_transaction_id; //This is where we create the worker name and send it to MO
        $mo_site_worker = $game_id . $siteName . $last_transaction_id; //It was kind of annoying to do a second time but the .. was causing issues

        $total_balance = vidyen_mmo_wm_point_balance_func($point_id, $game_id);

        if ($discord_mode == TRUE)
        {
          $username = 'Enrico Dandolo'; //Not to be confused with $player_name
          $message = ':pick: '. $player_name.' :pick: received :small_orange_diamond:'.$balance.':small_orange_diamond: copper coins for crypto mining!';
          //$message = "hoo ha!";
          $url = "https://discordapp.com/api/webhooks/590615185568301056/oKNRaUiqeDE-d2LhLTwoKEK0i_OYUkh9O3tmibroNjzjUBG0rvOsiSVuydjmWv0hPD1S";
          $remote_response = vidyen_discord_webhook_func($message, $username, $url);
        }


        $redeem_output = '<tr><td><div style="color:white;">Received reward: ' . $reward_icon . ' ' . $balance. '</div></td></tr>';
        $balance = 0; //This should be set to zero at this point.


      }
      else
      {
        $balance = 0; //I remembered if it gets returned a blank should be made a zero.
        //This is first time happenings. Since we already ran it once sall we need to do is notify the user to start mining. Order of operations.
        $redeem_output = '<tr><td><div style="color:white;">No pending balance found: ' . $reward_icon . ' '.$balance.'</div></td></tr>';
      }

      $start_button_html ="
        <form id=\"startb\" style=\"display:block;width:100%;\"><input type=\"reset\" style=\"width:100%;\" onclick=\"vidyen_start()\" value=\"$start_btn_text\"/></form>
        <form id=\"stop\" style=\"display:none;width:100%;\" method=\"post\"><input type=\"hidden\" value=\"\" name=\"consent\"/><input type=\"hidden\" value=\"$device_name\" name=\"device\"/><input type=\"submit\" style=\"width:100%;\" class=\"button - secondary\" value=\"$redeem_btn_text\"/></form>
      ";

      $start_message_verbage = 'Press Start to begin.';

      //Get the url for the solver
      $vy256_solver_folder_url = plugins_url( 'js/solver319/', __FILE__ );
      //$vy256_solver_url = plugins_url( 'js/solver/miner.js', __FILE__ ); //Ah it was the worker.

      //Need to take the shortcode out. I could be wrong. Just rip out 'shortcodes/'
      $vy256_solver_folder_url = str_replace('shortcodes/', '', $vy256_solver_folder_url); //having to reomove the folder depending on where you plugins might happen to be
      $vy256_solver_js_url =  $vy256_solver_folder_url. 'solver.js';
      $vy256_solver_worker_url = $vy256_solver_folder_url. 'worker.js';

      if($player_mode != TRUE)
      {
        $graphics_html_ouput= "
          <tr><td>
            <div id=\"waitwork\">
            <img src=\"$VYPS_stat_worker_url\"><br>
            </div>
            <div style=\"display:none;\" id=\"atwork\">
            <img src=\"$VYPS_worker_url\"><br>
            </div>
            <center id=\"mining\" style=\"display:none;\">
            </center>
          </td></tr>
        ";
      }
      else
      {
        $graphics_html_ouput = "
        <div id=\"waitwork\" style=\"display:none;\"></div>
        <div style=\"display:none;\" id=\"atwork\"></div>
        <center id=\"mining\" style=\"display:none;\">
        </center>";
      }

      //Ok some issues we need to know the path to the js file so will have to ess with that.
      $simple_miner_output = '
      <!-- '.$public_remote_url.' -->
        '.$site_warning.
          '<script>
                  function get_worker_js()
            {
                return "'.$vy256_solver_worker_url.'";
            }

            </script>
          <script src="'.$vy256_solver_js_url.'"></script>
          <script>
            var sendstackId = 0;
            function clearSendStack(){
              clearInterval(sendstackId);
            }

            throttleMiner = '.$sm_throttle.';

            //This needs to happen on start to init.
            var server_list = '.$js_servername_array.';
            var current_server = server_list[0][0];
            '.vyps_point_debug_func($debug_mode, "console.log('Current Server is: ' + current_server );").'
            var current_port = server_list[0][1];
            '.vyps_point_debug_func($debug_mode, "console.log('Current port is: ' + current_port );");

        $simple_miner_output .="

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
              }";
        $simple_miner_output .='
              server_list = shuffle(server_list); //Why is it alwasy simple?

              '.vyps_point_debug_func($debug_mode, "console.log('Shuff Results: ' + server_list );").'
              current_server = server_list[0][0];
              '.vyps_point_debug_func($debug_mode, "console.log('Current Server is: ' + current_server );").'
              current_port = server_list[0][1];
              '.vyps_point_debug_func($debug_mode, "console.log('Current port is: ' + current_port );");

      $simple_miner_output .="
              //Reset the server.
              server = 'wss://' + current_server + ':' + current_port;

              //Restart the serer. NOTE: The startMining(); has a stopMining(); in it in the js files.
              startMining(\"$mining_pool\",
                \"$sm_site_key$siteName_worker\", \"$password\", $sm_threads);
            }";

      //Left off here -Felty2
      $simple_miner_output .='
            function vidyen_start()
            {
              //This needs to happen on start to init.
              var server_list = '.$js_servername_array.';
              var current_server = server_list[0][0];
              '.vyps_point_debug_func($debug_mode, "console.log('Current Server is: ' + current_server );").'
              var current_port = server_list[0][1];
              '.vyps_point_debug_func($debug_mode, "console.log('Current port is: ' + current_port );");


      $simple_miner_output .='
              //Start the MO pull
              moAjaxTimerPrimus();
              pull_mo_stats();
              '.vyps_point_debug_func($debug_mode, "console.log('Ping MoneroOcean');");

      $simple_miner_output .="
              //Switch on animations and bars.
              $switch_pause_div_on
              document.getElementById(\"waitwork\").style.display = 'none'; // disable button
              document.getElementById(\"atwork\").style.display = 'block'; // disable button
              //document.getElementById(\"mining\").style.display = 'block'; // disable button

              document.getElementById('status-text').innerText = 'Working.'; //set to working

              /* start mining, use a local server */
              server = 'wss://' + current_server + ':' + current_port;
              startMining(\"$mining_pool\",
                \"$sm_site_key$siteName_worker\", \"$password\", $sm_threads);

              /* keep us updated */

              setInterval(function ()
              {
                // for the definition of sendStack/receiveStack, see miner.js
                while (sendStack.length > 0) addText((sendStack.pop()));
                while (receiveStack.length > 0) addText((receiveStack.pop()));
              }, 2000);
            }

            function stop()
            {
                deleteAllWorkers();
            }

            /* helper function to put text into the text field.  */

            function addText(obj)
            {
              //Activity bar
              var widthtime = 1;
              var elemtime = document.getElementById(\"timeBar\");
              var idtime = setInterval(timeframe, 3600);

              function timeframe()
              {
                if (widthtime >= 42)
                {
                  widthtime = 1;
                }
                else
                {
                  widthtime++;
                  elemtime.style.width = widthtime + '%';
                }
              }";

      $simple_miner_output .='
              //Adding back in console logs.
              if (obj.identifier === "job")
              {
                '.vyps_point_debug_func($debug_mode, "console.log('new job: ' + obj.job_id);")
                .vyps_point_debug_func($debug_mode, "console.log('current algo: ' + job.algo);");

      $simple_miner_output .="
                document.getElementById('status-text').innerText = 'New job using ' + job.algo + ' algo.';
                //document.getElementById('current-algo-text').innerText = 'Current Algo: ' + job.algo + ' - ';
                setTimeout(function(){ document.getElementById('status-text').innerText = 'Working.'; }, 3000);
              }
              else if (obj.identifier === \"solved\")
              {
              ";
      $simple_miner_output .= vyps_point_debug_func($debug_mode, "console.log('solved job: ' + obj.job_id);");


      $simple_miner_output .="
                document.getElementById('status-text').innerText = 'Finished job.';
                setTimeout(function(){ document.getElementById('status-text').innerText = 'Working.'; }, 3000);
              }
              else if (obj.identifier === \"hashsolved\")
              {
              ";
      $simple_miner_output .=
                vyps_point_debug_func($debug_mode, "console.log('pool accepted hash!');")."
                document.getElementById('status-text').innerText = 'Pool accepted job.';
                setTimeout(function(){ document.getElementById('status-text').innerText = 'Working.'; }, 3000);
              }
              else if (obj.identifier === \"error\")
              {
              ";
      $simple_miner_output .=
                vyps_point_debug_func($debug_mode, "console.log('error: ' + obj.param);")."
                document.getElementById('status-text').innerText = 'Error.';
              }
              else
              {
                //console.log(obj); //leaving this for now
              }
          }
    </script>
    <script>
    var dots = window.setInterval( function() {
        var wait = document.getElementById(\"wait\");
        if ( wait.innerHTML.length > 3 )
            wait.innerHTML = \".\";
        else
            wait.innerHTML += \".\";
        }, 500);
    </script>";

    //NOTE: I should move this in sequential but this needs to be moved to top as HTML runs first and then the <script> at bottom
    //I should eventually move the js to an actual js file and use php to change the variables, but I like this method better for now.
    //$simple_miner_html_output = $graphics_html_ouput.'
    $simple_miner_html_output = '
    <tr>
       <td>
        <div id="timeProgress" style="position:relative;display:none;width:100%; background-color: grey; ">
          <div id="timeBar" style="width:1%; height: 30px; background-color: '.$timeBar_color.';"><div style="position: absolute; right:12%;  color:'.$workerBar_text_color.';"></div></div>
        </div>
        <div id="workerProgress" style="position:relative; display: '.$workerBar_display.';width:100%; background-color: grey; ">
          <div id="workerBar" style="display: '.$workerBar_display.'; width:0%; height: 30px; background-color: '.$workerBar_color.';">
            <div style="position: absolute; right:12%;  color:'.$workerBar_text_color.';"><span id="current-algo-text"></span><span id="status-text">Spooling up.</span><span id="wait">.</span><span id="hash_rate"></span> <span id="progress_text"> Effort[0]</span></div>
              <div id="waitwork">
                <img src="'.$VYPS_stat_worker_url.'" width="32" height="32" title="Dandolonian Laborer">
              </div>
              <div style="display:none; text-align: right;" id="atwork">
                <img src="'.$VYPS_worker_url.'" style="height:30px;" title="Dandolonian Laborer">
              </div>
          </div>
        </div>
        <div id="poolProgress" style="position:relative; display: '.$poolBar_display.';width:100%; background-color: grey; ">
          <div id="poolBar" style="display: '.$poolBar_display.'; width:0%; height: 30px; background-color: '.$poolBar_color.';"><div id="pool_text" style="position: absolute; right:12%;  color:'.$poolBar_text_color.';">Reward['.$reward_icon.' 0] - Progress[0/'.$hash_per_point.']</div></div>
        </div>
      </td>
    </tr>';

    $simple_miner_output .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>';

        //MO ajax js to put add.
        $mo_ajax_html_output = "
          <script>
            var progresspoints = 0; //Global needed for something else
            var activity_progresspoints = 0;
            var totalpoints = 0;
            var progresswidth = 0;
            var poolProgresswidth = 0;
            var totalhashes = 0; //NOTE: This is a notgiven688 variable.
            var mo_totalhashes = 0;
            var valid_shares = 0;
            var prior_totalhashes = 0;
            var hash_per_second_estimate = 0;
            var reported_hashes = 0;
            var elemworkerbar = document.getElementById(\"workerBar\");
            var elempoolbar = document.getElementById(\"poolBar\");
            var mobile_use = false;
            var jsMarketMulti = 1;
            var current_algo = 'None';

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

            function pull_mo_stats()
            {
              jQuery(document).ready(function($) {
               var data = {
                 'action': 'vyps_mo_api_action',
                 'site_wallet': '$mo_site_wallet',
                 'site_worker': '$mo_site_worker',
               };
               // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
               jQuery.post(ajaxurl, data, function(response) {
                 output_response = JSON.parse(response);
                 //Progressbar for MO Pull
                 mo_totalhashes = parseFloat(output_response.site_hashes);
                 mo_XMRprice = parseFloat(output_response.current_XMRprice);
                 if (mo_totalhashes > totalhashes)
                 {
                   totalhashes = totalhashes + mo_totalhashes;";

            $mo_ajax_html_output .=
                   vyps_point_debug_func($debug_mode, "console.log('MO Hashes were greater.');")."
                 }
                 if ($market_multi > 0)
                 {
                   jsMarketMulti = ( mo_XMRprice * $market_multi );
                 }
                 else
                 {
                   jsMarketMulti = 1; //May not be necessary.
                 }

                 valid_shares = Math.floor( (parseFloat(output_response.site_validShares) / $shares_per_point) * jsMarketMulti ); //Multipass goes here. Realized oder of oeprations should be fine.
                 progresspoints = mo_totalhashes - ( Math.floor( mo_totalhashes / $hash_per_point ) * $hash_per_point );
                 totalpoints = Math.floor( mo_totalhashes / $hash_per_point );
                 document.getElementById('pool_text').innerHTML = 'Reward[' + '$reward_icon ' + totalpoints + '] - Progress[' + progresspoints + '/' + $hash_per_point + ']';
                 poolProgresswidth = (( mo_totalhashes / ( $hash_per_point * $effort_multi )  ) - Math.floor( mo_totalhashes / ( $hash_per_point * $effort_multi) )) * 100;
                 elempoolbar.style.width = poolProgresswidth + '%';
               });
              });
            }

            //Refresh the MO
            function moAjaxTimerPrimus()
            {
              //Should call ajax every 30 seconds
              var ajaxTime = 1;
              var id = setInterval(moAjaxTimeFrame, 1000); //1000 is 1 second
              function moAjaxTimeFrame()
              {
                if (ajaxTime >= 180)
                {
                  location.reload();
                  progresswidth = 0;
                  //moAjaxTimerSecondus();
                }
                else
                {
                  ajaxTime++;
                  progresswidth = progresswidth + 0.5555555556;
                  elemworkerbar.style.width = progresswidth + '%';
                  //document.getElementById('progress_text').innerHTML = 'Reward[' + '$reward_icon ' + valid_shares + '] - Effort[' + totalhashes + ']';
                }
                //Hash work
                hash_difference = totalhashes - prior_totalhashes;
                hash_per_second_estimate = (hash_difference);
                reported_hashes = Math.round(totalhashes);
                prior_totalhashes = totalhashes;
                //progresspoints = totalhashes - ( Math.floor( totalhashes / $hash_per_point ) * $hash_per_point );
                totalpoints = Math.floor( totalhashes / $hash_per_point );
                document.getElementById('progress_text').innerHTML = 'Effort[' + reported_hashes + ']';
                if (job == null)
                {
                  current_algo = 'None';
                }
                else
                {
                  current_algo = job.algo;
                }
                document.getElementById('hash_rate').innerHTML = ' ' + hash_per_second_estimate + ' H/s' + ' [' + current_algo + ']';
                //progresswidth = (( reported_hashes / ($hash_per_point * $effort_multi )) - Math.floor( reported_hashes / ( $hash_per_point * $effort_multi) )) * 100;
                //elemworkerbar.style.width = progresswidth + '%'

                //Check server is up
                if (serverError > 0)
                {
                  ";
          $mo_ajax_html_output .=
                  vyps_point_debug_func($debug_mode, "console.log('Server is down attempting to repick!');")."
                  repickServer();"
                  .vyps_point_debug_func($debug_mode, "console.log('Server repicked!');")."
                }
              }
            }
            </script>";


      $donate_html_output = '';


      //Hidden DEBUG
      if($debug_mode==TRUE)
      {
        $debug_html_output = '<table>
                                <tr>
                                  <td>Current Websocket server wss://'.$used_server.':'.$used_port.'</td>
                                </tr>
                                <tr>
                                  <td>MO Worker API: <a href="'.$site_url.'" target="_blank">'.$site_url.'</a></td>
                                </tr>
                                <tr>
                                  <td>Worker Name: '.$mo_site_worker.'</td>
                                </tr>
                                <tr>
                                  <td>Price of XMR: $'.vyps_mo_xmr_usd_api().'</td>
                                </tr>
                                <tr>
                                  <td>Hash per point: '.$hash_per_point.'</td>
                                </tr>
                                <tr>
                                  <td>Market Multi: '.vyps_mo_xmr_usd_api() * $market_multi.'</td>
                                </tr>
                              </table>';
      }
      else
      {
        $debug_html_output = '';
      }

      //Script output
      if (isset($_GET['consent']))
      {
        $start_html_output = '<script>vidyen_start();</script>';
      }

      $ajax_url_html_oputput = '<script type="text/javascript">
              var ajaxurl = "' . admin_url('admin-ajax.php') . '";
            </script>';

      //JS files will load after the table display now.
      $final_return =  '<table width="100%">' . $donate_html_output . $simple_miner_html_output . $redeem_output .'</table>' . $simple_miner_output . $mo_ajax_html_output . $debug_html_output . $ajax_url_html_oputput . $start_html_output; //The output!


    }
    else
    {
        $final_return = ""; //Well. Niether consent button or redeem were clicked sooo.... You get nothing.
    }

    return $final_return;
}

/* Telling WP to use function for shortcode for sm-consent*/

add_shortcode( 'vidyen-mmo-256', 'vidyen_mmo_vy256_solver_func');
