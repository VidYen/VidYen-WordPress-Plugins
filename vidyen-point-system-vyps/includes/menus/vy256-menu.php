<?php

//Menu to show the shortcode instructions.
//NOTE: I have decided to move this plugin menu to the official as it's just shorcode

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action('admin_menu', 'vyps_vy256_submenu', 366 );

/* Creates the VY256 submenu on the main VYPS plugin */

function vyps_vy256_submenu() {

  $parent_menu_slug = 'vyps_points';
  $page_title = "VidYen Miner Shortcodes";
  $menu_title = 'VidYen Miner Shortcodes';
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
    <h1>VidYen Algo Switching Web Miner</h1>
    <p>With the closure of Coinhive and the March 9th, 2019 XMR fork, VidYen now gladly offers an algo switching web miner alternative to the market</p>
    <p>By default the miner connects through VidYen servers to by passpass adblockers and now mines alt coins with various algorithms that the MoneroOcean pool finds most profitable at any given time.</p>
    <p>This allows you to use a local web miner on your WordPress site to mine crypto payouts in XMR that bypasses adblock or other AV software while tracking your users efforts to reward them.</p>
    <p>Prior to the March 9th fork, the VY256 miner would for the most part mine just Monero, but it is now more profitable to mine alt coins and payout in XMR. As a result, hashrates can vary widly depending on the coin being mined and its difficulty.</p>
    <p>To make it easier on admins, reward payout are based on "Valid Shares" which is the amount of work over all done by your user to earn a payout rather than hashrates.</p>
    <p>Hashes worked and hash rate are now tracked by the client rather than their now to give them feed back but do not determine their rewards, but you can view exact stats on the <a href="https://moneroocean.stream/#/dashboard" target="_blank">MoneroOcean dashboard</a>.</p>
    <p><b>NOTE:</b>When you access MoneroOcean, your antivirus or malware software may say the site contains a trojan, but if you are switching from Coinhive, you would already be aware of this as it is a precauctionary measure by AV companies to block all Monero pools due to the fact malware creators will often use them to mine to even though the majority of the miners are legitimate miners. Always take precautionary measures.</p>
    <p>Unless you link MoneroOcean on your site or you decide to use the MoneroOcean webminerpool server directly, your end users clients will never see those server and therefore never get an AV alert.</p>
    <p>To get payouts, all you need is an up to date Monero XMR wallet compatiable with the most recent fork. <a href="https://mymonero.com" target="_blank">MyMonero</a> works well enough (or visit the <a href="https://www.reddit.com/r/Monero/" target="_blank">Monero Reddit</a> for altenrative options) and access to the <a href="https://moneroocean.stream/" target="_blank">MoneroOcean</a> pool, which only requires the XMR wallet to use and no KYC account.</p>
    <p>The payouts and accepted hash monitoring are through MoneroOcean (not VidYen) and if you need to change your minimum payouts please refer to their <a href="https://moneroocean.stream/#/help/faq" target="_blank">FAQ</a>.</p>
    <p>Even though this version did not cause uBlock, Brave Browser, or Malwarebytes to complain, you should take full advantage of the consent system so your site does not get blacklisted by mistake by causing CPU usage on users who were not aware this would happen.</p>
    <p>You should make users aware that this miner may drain their battery and use their electrcity in your consent disclaimer. The consent system only loads the mining code to the client after it has been accepted.</p>
    <h1>Virtual Rewards vs Tangible Rewards Philosophy</h1>
    <p>Due to the confusion and frustration of users, I would like to take a moment to explain what the miner is good for and what it is not. For the sake of proper understanding when I say virutal reward, I mean a digital reward that costs nothing for you to reproduce or sell. Tangible, even if digital, it goods that cost you money to obtain even if digital like crypto currency.</p>
    <p>Although I have plans on adding a more dynamic point system based on the price of Monero, I inteded it to be used for virtual rewards because of the nature of mining and crypto currency, knowing exactly how much a point earned from mining is worth is diffuclty because not only doe sthe price of Monero changes but so does the difficulty of the block being mined making hashes and shares worth more or lesss depending.</p>
    <p>So as mining costs you nothing more than the bandwidth and hosting costs I am going to list items that I believe are good rewards for crypto mining</p>
    <h2>Rewards That Are Good For Mining</h2>
    <p>Donations (obvious)</p>
    <p>Digital Downloads (images, musics, and videos)</p>
    <p>Game currency that costs nothing to make (say the VidYen Poker game)</p>
    <p>Coupons that still allow for end profit</p>
    <p>Intagible rewards like premium accounts or various other perks</p>
    <h2>Rewards that are a bad idea to use for crypto mining</h2>
    <p>USD</p>
    <p>Actual Crypto</p>
    <p>Gift cards</p>
    <p>Physical items that require shipping</p>
    <p>Anything that you had to pay to put on your store</p>
    <p>NOTE: It might still be ok if you use a raffle system or gambling system where 1 out of 1,000 users get the rewards, but you need to make the threshold really high.</p>
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
    <p>hash: Hashes by default redeemed for a 10,000 hashes for for 1 of your points. MoneroOcean decides this number and is updated every 30 seconds but may take 3 minutes before it is determined.</p>
    <p>marketmulti: This turns on the marketmultiplier based on the USD price of XMR. Ex: marketmulti=1 will times every point earned by 50 if that were the current price of XMR. marketmulti=2 will times it by 100 or market marketmulti=.001 will dividen the points earned by half. This number will changed based on market price so lower the market price, the lower the returns or higher depending.</p>
    <p>threads: Default is 1 to only mine one thread. We would recommend leaving it set to 1 in the event your users have slow devices. Users can change this with the + or - buttons.</p>
    <p>throttle: The default is 50, which reduces CPU use by 50%, so they only use 50% for mining. I would also recommend leaving this set to 50 and allowing the user to increase it if they want to mine faster via the slider on the menu.</p>
    <p>timebar: The colored part of the top status bar. The default is timebar=yellow. As its CSS, it can be set to anything as such.</p>
    <p>workerbar: The colored part of the bottom status bar. The default is workerbar=orange. As its CSS, it can be set to anything as such.</p>
    <p>timebartext: The colored part of the top text status bar. The default is timebartext=white. As its CSS, it can be set to anything as such.</p>
    <p>workerbartext: The colored part of the bottom text status bar. The default is workerbartext=white. As its CSS, it can be set to anything as such.</p>
    <p>debug=true: This turns on optons so you can see the MoneroOcean API and other stats. Mostly for development, but can be useful for seeing what is happening in background</p>
    <p>Example: <b>[vyps-256 wallet=48Vi6kadiTtTyemhzigSDrZDKcH6trUTA7zXzwamziSmAKWYyBpacMjWbwaVe4vUMveKAzAiA4j8xgUi29TpKXpm3wL5K5a pid=4 throttle=10 site=vidyen hash=20000 timebar=yellow workerbar=orange][vyps-256-consent]</b></p>
    <p>This will show the miner after the consent button has been clicked.</p>
    <h2>Graphics</h2>
    <p>You can use the shortcode graphic=0 to turn off animated worker or graphic=1 or 2 if you want to select a particular one. Plans for holidays coming soon!</p>
    <p>Also you can use your own graphics with cstatic=https://yourwordpresssite.com/your_still_image.gif and cworker=https://yourworedpresssite.com/your_animated_image.gif</p>
    <p>This will have a stopped miner who then animates after your user hits the start button.</p>
    <h2>Client Hashes</h2>
    <p>As I have added a third bar for better understanding by end user of client versus pool rewards, I have added option to turn it off and on with due to user feedback.</p>
    <p>clienthashes=none will turn it off.</p>
    <p>poolhashes=none will also turn off the rewards bar, but not sure why you would do that.</p>
    <h2>Mining on multiple devices.</h2>
    <p>On the conent shortcode set to <b>[vyps-256-consent multidevice=TRUE]</b></p>
    <h2>Donate mode</h2>
    <p>If users wish to mine but donate points to another user then you can use the [vyps-refer] referral system with the shortcode addtion in the miner shortcode:</p>
    <p><b>donate=TRUE</b></p>
    <p>Then have users set their accounts to the referral to the main account to donate the rewards to that. A bit complicated, but because we do not run the pool, it is required to prevent over rewarding or loss of rewards.</p>
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
    <h1>Worker Name Explanation</h1>
    <p>Something I have never explained, but if you are looking on MoneroOcean dashboard, you will see worker names such as "3vidyenlive1297"</p>
    <p>The leading 3 is the user id number on WordPress db, and the 1297 is the transaction id.</p>
    <p>Since worker statistics are not meant to be kept long term on the pools, when a user redeems or refreshes the page, it will check to see how many hashes are currently accounted for and then makes a new worker so that it does not double count.</p>
    <p>This does cause issues with users mining on same account with different devices. If you have users who want to do this, then create different pages or accounts and have them transfer points between accounts.</p>
    <p>This is an issue I would like to resolve, but if we used the worker name long term for the overall balance, it is possible that it gets wiped and user ends up with a negative balance.</p>
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
    <p>Hash rates displayed for the user are client side now, but the reward is pulled from the MoneroOcean every 60 seconds showing the hashes which is the reward divided by your hash= shortcode attribute.</p>
    <p>The hash rates may vary from client to what you see on the MoneroOcean dashboard as not all hashes are accepted.</p>
    <p>There are some bugs with mobile devices which show an hash rate on client that is way higher than what MO says, but since the reward is now based on the shares, that is what they should be concerned about.</p>
    <p>For optimal rewards, please recommend to users to let it run over time as jobs will have to processed by not only MoneroOcean\'s server but whatever block chain is being mined currently.</p>
    <p>In theory, I could allow the use of more than 6 threads, but in testing it was easy to lock up my computer testing in multiple tabs so for now, I believe its a safe comprimise. There is a way to override but is undocumented.</p>
    <p></p>
    <br><img src="' . $mo_example_url . '" >
    <h2>Future plans</h2>
    <p>We do have a goal to add more pools down the road, but they will now need to be algo switching pools. MoneroOcean has worked well for the plugin over the past year, and the developer of that pool is very responsive and fair.</p>
    <p>I do plan on showing the current algo being used and there are talks in the Web Mining Pool developemenet git that Bit Tube will be added and I personally plan on supporting that. -Felty</p>
    <p>Also, I do plan on having the API be a bit more flexible in which server it looks at.</p>
    <p>And a tutorial video for setup... Feel free to reach out any means possible if you are having problems in the meantime.</p>
    <h2>Hidden Features</h2>
    <p>Setting <b>debug=true</b> well let you use the server being used and the MoneroOcean API link. Not really on the page for users to see, but hopeful for troubleshooting.</p>
    <p>Setting <b>roundup=TRUE</b> will round up to the nearest point as long as one point was mined. Site admins may not like this, but for my own projects I felt it was better to keep users from complaining about lost effort.</p>
    <h2>Known Issues</h2>
    <p>Users on iPhone or Android with mobile browsers will be locked to 2 threads. Some newer Androids will not be locked as they are not actually mobile browsers.</p>
    <p>You can only mine in one tab on a browser at any given time. If they want to mine in more than one page, then they need to open up a guest profile, make a new user profile, or just use another type of browser at the same time. Given that they can just crank their usage up to 100%, I am not sure why they would.</p>
    <h2>Algo Switching Information</h2>
    <p>Currently supported algorithms</p>
      <table>
        <tr>
          <th>#</th><th>xmrig short notation</th><th>webminerpool internal</th><th>description</th>
        </tr>
        <tr>
          <td>1</td><td>cn</td><td>algo="cn", variant=-1</td><td>autodetect cryptonight variant (block.major - 6)</td>
        </tr>
        <tr>
          <td>2</td><td>cn/0</td><td>algo="cn", variant=0  </td><td>original cryptonight</td>
        </tr>
        <tr>
          <td>3</td><td>cn/1</td><td>algo="cn", variant=1 </td><td>also known as monero7 and cryptonight v7</td>
        </tr>
        <tr>
          <td>4</td><td>cn/2</td><td>algo="cn", variant=2 or 3   </td><td>cryptonight variant 2</td>
        </tr>
        <tr>
          <td>5</td><td>cn/r</td><td>algo="cn", variant=4 </td><td>cryptonight variant 4 also known as cryptonightR</td>
        </tr>
        <tr>
          <td>6</td><td>cn-lite</td><td>algo="cn-lite", variant=-1 </td><td>same as #1 with memory/2, iterations/2</td>
        </tr>
        <tr>
          <td>7</td><td>cn-lite/0</td><td>algo="cn-lite", variant=0</td><td>same as #2 with memory/2, iterations/2</td>
        </tr>
        <tr>
          <td>8</td><td>cn-lite/1</td><td>algo="cn-lite", variant=1  </td><td>same as #3 with memory/2, iterations/2</td>
        </tr>
        <tr>
          <td>9</td><td>cn-pico/trtl</td><td>algo="cn-pico", variant=2 or 3  </td><td>same as #4 with memory/8, iterations/8</td>
        </tr>
        <tr>
          <td>10</td><td>cn-half </td><td>algo="cn-half", variant=2 or 3</td><td>same as #4 with memory/1, iterations/2</td>
        </tr>
      </table>
    </div>';

  	/* I may not want advertising, but I suppose putting it here never hurts */
  	//$credits_include = $VYPS_root_path . 'includes/credits.php';

}
