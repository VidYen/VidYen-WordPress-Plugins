<?php

//Menu to show the shortcode instructions.
//NOTE: I have decided to move this plugin menu to the official as it's just shorcode

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action('admin_menu', 'vyps_vy256_submenu', 366 );

/* Creates the VY256 submenu on the main VYPS plugin */

function vyps_vy256_submenu() {

  $parent_menu_slug = 'vyps_points';
  $page_title = "VY256 Miner Shortcodes";
  $menu_title = 'VY256 Miner Shortcodes';
  $capability = 'manage_options';
  $menu_slug = 'vyps_vy256_page';
  $function = 'vyps_vy256_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* this next function creates the page on the VY256 submenu */

function vyps_vy256_sub_menu_page()
{
  //Image URLS
  //NOTE It took me a while to realize, I needed the dirname()
  $VYPS_logo_url = plugins_url( 'images/logo.png', dirname(__FILE__) );
  $VYPS_worker_url = plugins_url( 'images/vyworker_small.gif', dirname(__FILE__) );
  $mo_example_url = plugins_url( 'images/mo_example.png', dirname(__FILE__) );

	echo '<br><br><img src="' . $VYPS_logo_url . '" > ';
  echo '<br><img src="' . $VYPS_worker_url . '" > ';

  echo '
  <div class="wrap">
    <h1>VidYen VY256 Algo Switching Miner</h1>
    <p>With the closure of Coinhive and the March 9th, 2019 XMR fork, VidYen now gladly offers an algo switching web miner alternative to the market</p>
    <p>By default the miner connects through VidYen servers to by passpass adblockers and now mines alt coins with various algorithms that the MoneroOcean pool finds most profitable at any given time.</p>
    <p>This allows you to use a local web miner on your WordPress site to mine crypto payouts in XMR that bypasses adblock or other AV software while tracking your users efforts to reward them.</p>
    <p>Prior to the March 9th fork, the VY256 miner would for the most part mine just Monero, but it is now more profitable to mine alt coins and payout in XMR. As a result, hashrates can vary widly depending on the coin being mined and its difficulty.</p>
    <p>To make it easier on admins, reward payout are based on "Valid Shares" which is the amount of work over all done by your user to earn a payout rather than hashrates.</p>
    <p>Hashes worked and hash rate are now tracked by the client rather than their now to give them feed back but do not determine their rewards, but you can view exact stats on the <a href="https://moneroocean.stream/#/dashboard" target="_blank">MoneroOcean dashboard</a>.</p>
    <p><b>NOTE:</b>When you access MoneroOcean, your antivirus or malware software may say the site contains a trojan, but if you are switching from Coinhive, you would already be aware of this as it is a precauctionary measure by AV companies to block all Monero pools due to the fact malware creators will often use them to mine to even though the majority of the miners are legitimate miners. Always take precautionary measures.</p>
    <p>Unless you link MoneroOcean on your site or you decide to use the MoneroOcean webminerpool server directly, your end users clients will never see those server and therefore never get an AV alert.</p>
    <p>To get payouts, all you need is an up to date Monero XMR wallet compatiable with the most recent fork. <a href="https://mymonero.com" target="_blank">MyMonero</a> works well enough (or visit the <a href="https://www.reddit.com/r/Monero/" target="_blank">Monero Reddit</a> for altenrative options) and access to the <a href="https://moneroocean.stream/" target="_blank">MoneroOcean</a> pool, which only requires the XMR wallet to use and no KYC account.</p>
    <p>The payouts and valid share monitoring are through MoneroOcean (not VidYen) and if you need to change your minimum payouts please refer to their <a href="https://moneroocean.stream/#/help/faq" target="_blank">FAQ</a>.</p>
    <p>Even though this version did not cause uBlock, Brave Browser, or Malwarebytes to complain, you should take full advantage of the consent system so your site does not get blacklisted by mistake by causing CPU usage on users who were not aware this would happen.</p>
    <p>You should make users aware that this miner may drain their battery and use their electrcity in your consent disclaimer. The consent system only loads the mining code to the client after it has been accepted.</p>
    <h1>Shortcodes and Syntax</h1>
    <p><b>NOTE:</b> To display the simple miner you need to place the consent button shortcode on your page to create a POST call that the simple miner shortcode will recognize.</p>
    <p>Users must be logged in to see any of these shortcode as the intent was to track user effort.</p>
    <h2>Consent Button Shortcode</h2>
    <p>Replace (optional) and (required) with your info.</p>
    <p><b>[vyps-256-consent text=(optional) disclaimer=(optional)]</b></p>
    <p>Display the simple miner consent button. You can customize the text on the button by using text= and the disclaimer= by using. To add spaces in the button text, use quotes.</p>
    <h2>VY256 Miner Display Shortcode</h2>
    <p><b>[vyps-256 wallet=(required) pid=(required) throttle=(optional) site=(required)]</b></p>
    <p>This will display the simple miner after the button on the consent shortcode has been pressed.</p>
    <p>wallet: Your XMR wallet.</p>
    <p>pid: The VYPS point ID found in the “VYPS Point List” of the point type you want to redeem to.</p>
    <p>hash: Hashes by default redeemed for a 10000 hashes for 1 of your points. You can use the shortcode attribute hash= to set the has to any number above or below 10000. This was originaly set to 1024, but the 10000 hash worked out better with new speeds and GUI.</p>
    <p>threads: Default is 1 to only mine one thread. We would recommend leaving it set to 1 in the event your users have slow devices. Users can change this with the + or - buttons.</p>
    <p>throttle: The default is 50, which reduces CPU use by 50%, so they only use 50% for mining. I would also recommend leaving this set to 50 and allowing the user to increase it if they want to mine faster via the slider on the menu.</p>
    <p>timebar: The colored part of the top status bar. The default is timebar=yellow. As its CSS, it can be set to anything as such.</p>
    <p>workerbar: The colored part of the bottom status bar. The default is workerbar=orange. As its CSS, it can be set to anything as such.</p>
    <p>timebartext: The colored part of the top text status bar. The default is timebartext=white. As its CSS, it can be set to anything as such.</p>
    <p>workerbartext: The colored part of the bottom text status bar. The default is workerbartext=white. As its CSS, it can be set to anything as such.</p>
    <p>Example: <b>[vyps-256 wallet=48Vi6kadiTtTyemhzigSDrZDKcH6trUTA7zXzwamziSmAKWYyBpacMjWbwaVe4vUMveKAzAiA4j8xgUi29TpKXpm3wL5K5a pid=4 throttle=10 site=vidyen hash=1024 timebar=yellow workerbar=orange][vyps-256-consent]</b></p>
    <p>This will show the miner after the consent button has been clicked.</p>
    <h2>Graphics</h2>
    <p>You can use the shortcode graphic=0 to turn off animated worker or graphic=1 or 2 if you want to select a particular one. Plans for holidays coming soon!</p>
    <p>Also you can use your own graphics with cstatic=https://yourwordpresssite.com/your_still_image.gif and cworker=https://yourworedpresssite.com/your_animated_image.gif</p>
    <p>This will have a stopped miner who then animates after your user hits the start button.</p>
    <h2>Referral Mining</h2>
    <p>You can set shortcode option to <b>refer=10</b> to give 10% mining bonus to their referral code invite. This shortcode accepts whole numbers only and rounds down. In theory, a site admin could set refer=200 to give a 200% mining bonus, but I am not sure why you would as that could be abused.</p>
    <p>Users can set their referral code with the shortcode <b>[vyps-refer]</b>, which should give them a page to give and get their referral code. This is not an automatic system, and your users will have to manually add the code. There are plenty of login customization and user account plugins an admin can use to integrate this system with.</p>
    <h2>Miner Sharing</h2>
    <p>Mining sharing is a feature used to allow the XMR address by users to be shared for randomly allowing users to mine for other users based on the total owned points shared. This actually lets miners and even adwatchers to participate in getting faucets to pools by being shareholders in your point system.</p>
    <p><b>[vyps-xmr-wallet]</b></p>
    <p>To enable mining sharing, add the shortcode shareholder=1. Replace the 1 with the pointid that you want users to be ranked in. The way it works is that there is a weighted change for holder of most points of that type to get their wallet mined to say if one user owned 60% of that point type, they would have 60% type to win the display. If no one wins, it defaults to the site’s wallet. Admins can always be point owners as well, so look at leaderboards for fairness. I will make a leaderboard with percentage of ownership in next update.</p>
    <h1>Payout</h1>
    <p>The VY256 works in conjunction with the <a href="https://moneroocean.stream/" target="_blank">MoneroOcean</a> pool as a third-party service to handle wallet payouts.</p>
    <p>The rates of XMR per hash is determined by them as well as the minimum payouts. An account is not required, but by default, the minimum payout is 0.3 XMR, and if you wish to adjust that, you will have to create an account through them.</p>
    <p>To see your progress toward payout, visit the <a href="https://moneroocean.stream/#/dashboard" target="_blank">dashboard</a> and add your XMR wallet where it says Enter Payment Address at the bottom of page. There, you can see total hashes, current hash rate, and account option if you wish to change payout rate.</p>
    <p>NOTE: The hashes the user mines versus what MoneroOcean shows will differ because of fees and rejected hashes. As browser mining is often low CPU, many hashes are rejected, but VY256 rewards user regardless for fair effort. VY256 has a range of 1%–5% developer fee, depending on the overall network rate.</p>
    <h1>Localization</h1>
    <p>The following will allow you change the text to a localized version in your language:</p>
    <p>Redeem: <b>redeembtn="Redimir"</b></p>
    <p>Start: <b>startbtn="Comienzo"</b></p>
    <p>Example: <b>[vyps-256 wallet=48Vi6kadiTtTyemhzigSDrZDKcH6trUTA7zXzwamziSmAKWYyBpacMjWbwaVe4vUMveKAzAiA4j8xgUi29TpKXpm3wL5K5a pid=4 site=vidyen redeembtn="Redimir" startbtn="Comienzo"] Por favor consienta a la minería. [vyps-256-consent text="Yo Consiento"]</b></p>
    <h1>Custom Server</h1>
    <p>If you want to run your own <a href="https://github.com/notgiven688/webminerpool/" target="_blank">webminerpool</a> you can, but it needs to support MoneroOcean.</p>
    <p>You can use MoneroOcean webminepool directly if you want, but your end users may get an AV or adblocker hit on it.</p>
    <p>Websocket Server URL: <b>server=yourserver.com</b></p>
    <p>Websocket Port: <b>wsport=8181</b></p>
    <p>You will need to use the MoneroOcean pool and API currently.</p>
    <p>As this is rather complex, unless you want to learn Debian (or other Linux server) and host the server yourself, we recommend just using the VY256 default server.</p>
    <h1>Support</h1>
    <p>Since this is running on our servers and we expanded the code, VidYen, LLC is the one handling the support. Please go to <a href="https://www.vidyen.com/about/" target="_blank">VidYen About</a> or on our <a href="https://wordpress.org/support/plugin/vidyen-point-system-vyps" target="_blank">WordPress support page</a>.</p>
    <p>If server is down and/or you need assistance immediately, join the <a href="https://discord.gg/6svN5sS" target="_blank">VidYen Discord</a> (It will ping my phone, so do not abuse. -Felty)</p>
    <p>For anyone who is trying this, we want to thank you for testing and that please let us know if you have any problems!</p>
    <p>Keep in mind that we and the pools take some fees, but it is generally way less than the Coinhives 20% fee, and you can get a smaller minimum payout of 0.003 XMR on MoneroOcean.</p>
    <p>Our code is open source, so if you want to run your own version, just go to our <a href="https://github.com/VidYen/VYPS-Plugin" target="_blank">github</a> and grab the code.</p>
    <h2>Notes about hash rates:</h2>
    <p>Hash rates displayed for the user are client side now, but the reward is pulled from the MoneroOcean every 60 seconds showing the valid shares which is the reward.</p>
    <p>The hash rates may vary from client to what you see on the MoneroOcean dashboard as not all hashes are accepted.</p>
    <p>There are some bugs with mobile devices which show an hash rate on client that is way higher than what MO says, but since the reward is now based on the shares, that is what they should be concerned about.</p>
    <p>From testing, my iPhone 7 can earn about 5-10 valid shares at 100% CPU eveyr 5 minutes depending, but I would not recommend this as a primary method as it drained my battery fairly quickly.</p>
    <p>For optimal rewards, please recommend to users to let it run over time as jobs will have to processed by not only MoneroOcean\'s server but whatever block chain is being mined currently.</p>
    <p>In theory, I could allow the use of more than 6 threads, but in testing it was easy to lock up my computer testing in multiple tabs so for now, I believe its a safe comprimise. There is a way to override but is undocumented.</p>
    <p></p>
    <br><img src="' . $mo_example_url . '" >
    <h2>Future plans</h2>
    <p>We do have a goal to add more pools down the road, but they will now need to be algo switching pools. MoneroOcean has worked well for the plugin over the past year, and the developer of that pool is very responsive and fair.</p>
    <p>I do plan on showing the current algo being used and there are talks in the Web Mining Pool developemenet git that Bit Tube will be added and I personally plan on supporting that. -Felty</p>
    <p>Also, I do plan on having the API be a bit more flexible in which server it looks at.</p>
    <h2>Hidden Features</h2>
    <p>Setting <b>debug=true</b> well let you use the server being used and the MoneroOcean API link. Not really on the page for users to see, but hopeful for troubleshooting.</p>
    </div>';

  	/* I may not want advertising, but I suppose putting it here never hurts */
  	//$credits_include = $VYPS_root_path . 'includes/credits.php';

}
