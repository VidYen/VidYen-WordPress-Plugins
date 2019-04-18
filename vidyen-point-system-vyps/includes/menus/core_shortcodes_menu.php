<?php

//Menu to show the shortcode instructions.
//NOTE: I have decided to move this plugin menu to the official as it's just shorcode

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action('admin_menu', 'vyps_core_shortcodes_submenu', 360 );

/* Creates the VY256 submenu on the main VYPS plugin */

function vyps_core_shortcodes_submenu()
{
  $parent_menu_slug = 'vyps_points';
  $page_title = "VidYen Shortcodes";
  $menu_title = 'VidYen Shortcodes';
  $capability = 'manage_options';
  $menu_slug = 'vyps_core_shortcodes_page';
  $function = 'vyps_core_shortcodes_sub_menu_page';

  add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}

/* this next function creates the page on the shortcodes submenu */
function vyps_core_shortcodes_sub_menu_page()
{
  //Image URLS
  //NOTE It took me a while to realize, I needed the dirname()
  $VYPS_logo_url = plugins_url( 'images/logo.png', dirname(__FILE__) );
  $VYPS_worker_url = plugins_url( 'images/vyworker_small.gif', dirname(__FILE__) );

  //HTML ECHO of graphics.
	echo '<br><br><img src="' . $VYPS_logo_url . '" > ';
  echo '<br><img src="' . $VYPS_worker_url . '" > ';

  echo "<br>
 	<h1>Shortcodes and Syntax</h1>
 	<p>This plugin allows you to add shortcodes so your users can see a complete transaction log on the VidYen Point System.</p>
 	<h2>Public Log</h2>
 	<p><b>[vidyen-point-log]</b></p>
 	<p>This shows 50 log entries at a time.</p>
  <p><b>[vidyen-point-log rows=25 pages=25 bootstrap=yes]</b></p>
 	<p>Set the rows options to set the number of rows per page of the transaction log. If you use a bootstrap theme, you can turn option on to bootstrap=yes to get bootstrap pagination. pagination is set to default of 10 but setting pages=(number) can set to desired amount.</p>
  <p>NOTE: You can set log rows to something big, like 1000000, but it may slow the site if you have that many transactions on your VYPS table.</p>
  <p><b>[vidyen-point-log current=TRUE]</b></p>
  <p>This shows the log just for a specific user and is useful for the new post back implementation with Wannads as their response takes time to be reported.</p>
 	<p><b>[vyps-balance pid=# uid=optional icon=optional ]</b></p>
 	<p>This shortcode shows the balance of a particular point. Replace # with the corresponding pointid (optional). You can set a specific user ID by setting the uid attribute. If you want to turn off icons, set to icon=0.</p>
 	<p><b>[vyps-balance-ww]</b></p>
 	<p>This shows the current <a href=\"https://wordpress.org/plugins/woo-wallet/\" target=\"_blank\">WooCommerce Wallet</a> if it is installed.</p>
 	<p><b>[vyps-balance-ww-menu]</b></p>
 	<p>Use this if you wish to display the My Icon on the menu. This, of course, requires you to use shortcodes in the menu plugin and may have unexpected results.</p>
  <p>NOTE: As of 1.6.2, you can use the WooWallet menu itself, but because of loading order issues, if the button of the transfer goes first, the wallet on header will be behind it. Side bar and footer options should load fine.</p>
  <h2>Public Balance Leaderboard</h2>
  <p><b>[vyps-pb pid=1]</b></p>
  <p>This shows all users’ balance of that point type by order of amount and also functions as a leaderboard. PID is not required but will default to pid=1 if not set. Users who had a balance at one time will show up as zero. Otherwise, until a user earns that point type, they will not be on the leaderboard for that PID.</p>
  <p>NOTE: It will show negative balances. This is intended.</p>
  <p>The number of rows can be set, up to a maximum of 50, rows=50, and it is 50 by default, i.e., <b>[vyps-pb pid=3 rows=10]</b></p>
  <p>NOTE: The number of row/transactions in the log may slow the site, depending on the amount of users.</p>
  <p>You can also show the amount by percent of total with percent=yes, [vyps-pb pid=2 percent=yes]. This way, you can see who owns the most by percent, which is useful for the mining share option in the VY256 Miner.</p>
  <h2>Public Earnings Leaderboard</h2>
  <p><b>[vyps-pbe pid=1]</b></p>
  <p>Same as above except you see all earnings without their expenditures to show who earned the most but maybe spent more than everyone else.(Good for a rewards site rather than gaming site)</p>
  <h2>Point Exchange</h2>
 	<p>This plugin requires VYPS Base and two point types to function. The intention is to allow a quick and easy way for users to transfer one type of point to another at varying rates.</p>
 	<p><b>[vyps-pe firstid=1 outputid=3 firstamount=1 outputamount=900]</b></p>
 	<p>Function debits points from one point type to another, with in being how many points used to transfer and out as how many points they get in the new point type.</p>
 	<p>The firstid is the source pointID, and the outputid is the destination seen on the Point List page. This shortcode always requires the user to be logged in and will not let you use set the user id as you do not want other users messing with the balances.</p>
 	<p>The firstamount attribute is how many points are spent. The outputamount attribute is how much currency the user gets in the other point type.</p>
  <p>On mobile devices or screen-impaired devices, you can set mobile=true to do the table vertically.</p>
  <h2>Point Transfer with Two Inputs</h2>
  <p><b>[vyps-pe firstid=1 secondid=2 outputid=3 firstamount=10 secondamount=10 outputamount=20]</b></p>
  <p>There was a need for game-like situations where points were to be combined to create items. In this case, you can combine two points of the same or differing values into a third point of your determined amount.</p>
  <p>All you need to do is add values to secondid= and secondamount= to enable a combination of points into the output.</p>
  <h2>Transfer Points into WooCommerce Credit</h2>
  <p><b>[vyps-pe firstid=3  firstamount=1000 outputamount=0.01 woowallet=true]</b></p>
 	<p>This creates a table transfer menu to transfer points to the WooWallet if you have it installed. This shortcode requires both VYPS and <a href=\"https://wordpress.org/plugins/woo-wallet/\" target=\"_blank\">WooCommerce Wallet</a> to function. It is assumed that you are going straight to WooWallet, so there is no need to set outputid if woowallet=true, but you still need to set outputamount.</p>
  <h2>Transfer Points into myCred</h2>
  <p><b>[vyps-pe firstid=3  firstamount=1000 outputamount=100 mycred=true]</b></p>
  <p>This takes VYPS points and transfers it into <a href=\"https://wordpress.org/plugins/mycred/\" target=\"_blank\">myCred</a> if you have it installed. You need at least the first id and firstamount and outputamount and mycred=true in the shortcode.</p>
  <h2>Transfer Points into GamiPress</h2>
  <p><b>[vyps-pe firstid=3 firstamount=1000 outputid=gamiyen outputamount=100 gamipress=true]</b></p>
  <p>This takes VYPS points and transfers it into <a href=\"https://wordpress.org/plugins/gamipress/\" target=\"_blank\">GamiPress</a>  if you have it installed. You need at least the firstid and firstamount and output amount and gamipress=true in the shortcode.</p>
  <h2>Point Exchange and Reward Timers</h2>
  <p><b>[vyps-pe firstid=3 outputid=3 firstamount=0 outputamount=100 minutes=4]</b></p>
  <p>Options include hours=, days=, minutes=.</p>
  <h2>User-to-User Point Exchanges</h2>
  <p>[vyps-pe firstid=3 outputid=3 firstamount=1000 outputamount=1000 transfer=true]</p>
  <p>If a user has set a referral code from another user, they user can do a point transfer to that referral user. It is recommended to put this on your referral page so they can see to whom the points are going. This is handy for multi-account users who want to mine with more than one device.</p>
  <p>Note: This is also very experimental and may create a gray market on your site if enabled. You can Set the outputamount to something less than the firstamount to impose a minimal fee to prevent abuse.</p>
  <h2>Login Awareness Shortcode</h2>
 	<p>Other plugins do this. It provides a quick way to let users know they are logged off and, therefore, cannot interact with VYPS.</p>
 	<p><b>[vyps-lg]</b></p>
 	<p>This shows a generic \"You are not logged in.\" message.</p>
 	<p><b>[vyps-lg message=\"Hey you! You need to log in!\"]</b></p>
 	<p>This allows custom message by admin when setting up the awareness message.</p>
  <h2>Threshold Raffle</h2>
  <p>Rather than a timed raffle, you can have users buy ticks to play an RNG game for the pot. This gets around the issue of one user having to spend months to earn their way to large-dollar amount items on your WooCommerce store.</p>
  <p><b>[vyps-tr spid=3 dpid=3 samount=1000 damount=10000 tickets=10]</b></p>
  <p>It is the same setup as the Point Transfer, but it is fine to have ticket purchases and payout with the same point type. The samount is ticket price, with damount with the pot payout, and tickets how many tickets are in the game. You can make this odd or even if you like. When the last ticket is sold, a random number of the ticket range is picked, and the owner of that ticket is awarded the pot.</p>
  <p><b>[vyps-tr-log spid=3 dpid=3 samount=1000 damount=10000 tickets=10]</b></p>
  <p>This is an optional log of the raffle so users can see who bought which ticket number and how many. Users can also see the ticket purchases on public log.</p>
  <p>NOTES: The attributes must be set for the same as the game as it is possible to run multiple games with different point, payout, and total amount of ticket numbers. That way, you can have several pages with different games.</p>
  <p>Also, there is no pagination as of yet (coming soon), and it is recommended not to run a 10,000-ticket game unless you do not show the log.</p>
  <p>If you have a custom theme that reloads page different than default themes, you may need to add <b>refresh=true</b></p>
  <h2>QUADS Game</h2>
  <p>This adds a small RNG game that allows user to make bets to get consecutive same numbers, up to four of a kind.</p>
  <p><b>[vyps-quads pid=4 betbase=100]</b></p>
  <p>The pid is the point type number, and the betbase is the number that is the smallest best, must be an integer.</p>
  <p>Payout is as follows:</p>
  <p>4 of a kind (QUADS) = 10x</p>
  <p>3 of a kind (TRIPS)= 5x</p>
  <p>2 doubles (DOUBLE DUBS) = 5x</p>
  <p>2 of a kind (DUBS) = 2.3x</p>
  <p>These odds are based on fair odds, and there is a plan to let admins adjust as they see fit down the road.</p>
  <h2>Referral System</h2>
  <p>Users can set their referral code with the shortcode [vyps-refer], which should give them a page to give and get their referral code. This is not an automatic system, and your users will have to manually add the code. There are plenty of login customization and user account plugins an admin can use to integrate this in the system.</p>
  <p>You can set shortcode option to refer=10 in either the [vyps-pe] or [vyps-256] to give 10% referral reward to the user who entered their referral code.</p>
  <p>Currently, users cannot see who has the code, only the history of the points earned. This is because simply having a referral is useless until someone does something with it. Also, a weird gamification system is added, wherein users can switch user referral codes on the fly. Of course, if other users can see this activity on the public log, it may cause engagement drama and competition to woo other users to their side.</p>
  <p>The shortcode for earnings history is [vyps-refer-bal pid=4], with 4 being the pointid you wish to display. Site admins will have to set the pid for each point type, but the idea is that you don't really need to show all points if you did not intend to do referrals for all of them.</p>
  <h2>Dashed Slug’s Wallet Integration</h2>
  <p>You can find this awesome <a href=\"https://wordpress.org/plugins/wallets/\" target=\"_blank\">Bitcoin and Altcoin Wallets</a> that allow you to integrate crypto payouts and exchange system on your WordPress site. It is rather complicated to set up (even for a developer like me -Felty), and you may have to work on it with Dashed Slug himself to get it to work.</p>
  <p>Once you get it working, you can call the API to exchange VYPS points to crypto with the following example:</p>
  <p><b>[vyps-pe firstid=3 outputid=5 firstamount=1000 outputamount=0.0001 symbol=LTC from_user_id=11 amount=0.0001]</b></p>
  <p>By adding symbol= from_user_id= and amount=, you can call the API into action. You need to create a bank user that holds the crypto you deposited and use that user’s id for the from.</p>
  <p>You will need to create a dummy point for the coin in VYPS with the graphic of the crypto you want and make the outputamount and amount the same.</p>
  <p>If you add a refer= attribute to the shortcode, it behaves the same as a point to point, except the referral is paid out in the input point rather than either the output point or the crypto itself as it would get messy if referrals are paid in crypto.</p>
  <p>It is complicated to test as someone else’s software is being used, but it does a user-to-user off-blockchain transfer on your site. The user will still need to use the third-party wallet via Dashed Slug’s plugin to withdraw it off your site.</p>
  <p>For this particular feature as the rest of VYPS,</p>
  <p>No Warranty. EXCEPT AS OTHERWISE EXPRESSLY SET FORTH IN THIS AGREEMENT, NEITHER PARTY HERETO MAKES ANY REPRESENTATION AND EXTENDS NO WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED, WITH RESPECT TO THE SUBJECT MATTER OF THIS AGREEMENT, INCLUDING WITHOUT LIMITATION WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND ANY WARRANTY ARISING OUT OF PRIOR COURSE OF DEALING AND USAGE OF TRADE. IN PARTICULAR, BUT WITHOUT LIMITATION, VidYen, LLC MAKES NO REPRESENTATION AND EXTENDS NO WARRANTY CONCERNING WHETHER THE LICENSED COMPOUND OR A LICENSED PRODUCT IS FIT FOR ANY PARTICULAR PURPOSE OR SAFE FOR HUMAN CONSUMPTION.</p>
  <h2>Utility Shortcodes</h2>
  <p>Login menu if using Profile Grid: <b>[vyps-pg-lg]</b></p>
  <p>Images that only show when not logged in: <b>[vyps-lg-img url=https://vidyen.com/imgage/image.png]</b></p>
  ";
}
