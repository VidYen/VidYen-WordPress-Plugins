<?php

//Simple credits file to update the list so it doesn't have to be updated every time


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 echo "<br>
	<h1>Shortcodes and Syntax</h1>
	<p>This plugin allows you add shortcodes so your users can see a log of all transactions on the VidYen Point System</p>
	<h2>Public Log</h2>
	<p><b>[vyps-pl]</b></p>
	<p>Shows the log for every 50 log entries.</p>
  <p><b>[vyps-pl rows=25 bootstrap=yes]</b></p>
	<p>Setting the rows options will set the amount of rows per page of the transaction log. If you use a bootstrap theme you can turn option on to bootstrap=yes to get bootstrap pagination. NOTE: You can set log rows to something big, like 1000000, but it may cause the site to slow if you have that many transactions on your VYPS table.</p>
	<p><b>[vyps-balance pid=# uid=optional icon=optional ]</b></p>
	<p>Shows the balance of a particular point through shortcode. Replace the # with the corresponding point ID. (Optional) You can set the user ID specifically by setting the uid attribute or if you want to turn off icons by setting to icon=0</p>
	<p><b>[vyps-balance-ww]</b></p>
	<p>Shows the current <a href=\"https://wordpress.org/plugins/woo-wallet/\" target=\"_blank\">WooCommerce Wallet</a> if it is installed.</p>
	<p><b>[vyps-balance-ww-menu]</b></p>
	<p>Used only if you wish to stick display the My Icon in a menu. This of course does requires you to use a the Shortcodes In Menu Plugin and may have produce unexpected results.</p>
  <p>Note: As of 1.6.2, you can use the WooWallet menu itself, but due to loading order issues, if the button of the transfer goes first the wallet on header will be behind. Side bar and footer options should load fine.</p>
  <h2>Public balance leaderboard</h2>
  <p><b>[vyps-pb pid=1]</b></p>
  <p>Shows all user's balance of that point type by order of amount and also functions as a leaderboard. PID is not required but will default to pid=1 if not set. Users who had a balance at one time will show up as zero. Otherwise, until a user earns that point type, they will not be on leaderboard for that PID. NOTE: It will show negative balances. This is intended.</p>
  <p>You can also show amount by percent of total with percent=yes such as <b>[vyps-pb pid=2 percent=yes]</b> so that you can see who owns the most by percent which is useful for the mining share option in the VY256 Miner.</p>
  <h2>Point Exchange</h2>
	<p>This plugin needs requires VYPS Base and two point types to function. The intention is to allow a quick and easy way for users to transfer one type of point to another at varying rates.</p>
	<p><b>[vyps-pe firstid=1 outputid=3 firstamount=1 outputamount=900]</b></p>
	<p>Function debits points from one point type to another. with in being how many points used to transfer and out as how many points they get in the new point type.</p>
	<p>The firstid is the source pointID and the outputid is the destination seen on the Point List page. This shortcode always requires the user to be logged in and will not let you use set the user id as you do not want other users messing with the balances.</p>
	<p>The firstamount attribute is how many points is are spent. The outputamount attribute is how much currency the user gets in the other point type.</p>
  <p>On mobile devices or screen impaired devices, you can set <b>mobile=true</b> to do the table vertically.</p>
  <h2>Point Transfer with Two Inputs</h2>
  <p><b>[vyps-pe firstid=1 secondid=2 outputid=3 firstamount=10 secondamount=10 outputamount=20]</b></p>
  <p>There was a need for game like situations where points were to be combined to create items. In this case, you can combine two points of the same or differeing values into a third point of the amount you decide.</p>
  <p>All you need to do is add secondid= and secondamount= with values to enable a combination of poitns into the output.</p>
  <h2>Transfer points into WooCommerce Credit</h2>
  <p><b>NOTE: Due to changes to WooWallet it has been difficult to continue using it with VYPS so we forked an older version off GitHub under GPLv3 and recommend you use that.</b></p>
  <p><b>[vyps-pe firstid=3  firstamount=1000 outputamount=0.01 woowallet=true]</b></p>
	<p>Creates a table transfer menu to transfer points to the WooWallet if you have it installed. This shortcode needs requires both VYPS and <a href=\"https://wordpress.org/plugins/woo-wallet/\" target=\"_blank\">WooCommerce Wallet</a> to function.
	It is assumed that you are going straight to WooWallet so there is no need to set output id if woowallet=true, but you still need to set outputamount.</p>
  <h2>Point Exchange and Reward Timers</h2>
  <p><b>[vyps-pe firstid=3 outputid=3 firstamount=0 outputamount=100 minutes=4]</b></p>
  <p>Options include hours=, days=, minutes=,  </p>
  <h2>User to User Point Exchanges</h2>
  <p>[vyps-pe firstid=3 outputid=3 firstamount=1000 outputamount=1000 transfer=true]</p>
  <p>If a user has set a referral code from another user, they can do a point transfer to that referral user. I would recommend putting on your referral page so they can see who the points are going to. This is handy for multi account users who want to mine with more than one device.</p>
  <p>Note: This is also very experimental and possibly create a gray market on your site if enabled. You can set output amount to something less than the first amount to have a soft fee to prevent abuse.</p>
  <h2>Login Awareness Shortcode</h2>
	<p>Other plugins do this, but made this one provides a quick way to let users know they are not logged in and therefore cannot interact with VYPS.</p>
	<p><b>[vyps-lg]</b></p>
	<p>Shows a generic \"You are not logged in.\" message when user is not logged in.</p>
	<p><b>[vyps-lg message=\"Hey foo! You need to log in.\"]</b></p>
	<p>Allows custom message by admin when setting up the awareness message.</p>
  <h2>Threshold Raffle</h2>
  <p>Rather than a timed raffle, you can have users buy ticks to play an RNG game for the pot. This gets around the issue of one user having to spend months to earn their way to large dollar amount items on your WooCommerce store.</p>
  <p><b>[vyps-tr spid=3 dpid=3 samount=1000 damount=10000 tickets=10]</b></p>
  <p>It is the same setup as the Point Transfer but it is fine have ticket purchases and payout with the same poin type. The samount is ticket price, with damount with the pot payout, and tickets how many tickets are in the game. You can make this odd or even if you like. When the last ticket is sold, a random number of the ticket range is picked and the owner of that ticket is awarded the pot.</p>
  <p><b>[vyps-tr-log spid=3 dpid=3 samount=1000 damount=10000 tickets=10]</b></p>
  <p>This is an optional log of the raffle so users can see who bought which ticket number and how many. Users can also see the ticket purchases on public log. NOTE: The attributes must be set for the same as the game as it is possible to run multiple games with different point, payout, and total amount of ticket numbers. That way you can have several pages with different games.</p>
  <p>Also note, there is no pagnation as of yet (coming soon) and we would recommend not running a 10,000 ticket game unless you do not show the log.</p>
  <h2>Referral System</h2>
  <p>Users can set their referral code with the shortcode <b>[vyps-refer]</b> which should give them a page to give and get their referral code. This is not an automatic system and your users will have to manually add the code. There are plenty of login customization and user account plugins an admin can use to integrate this system with.</p>
  <p>You can set shortcode option to <b>refer=10</b> in either the [vyps-pe] or [vyps-256] to give 10% percent referral reward to the user who entered their referal code. There will be no forth coming addition to Adscend or Coinhive refer codes, but you can gate those points through the point exchange system easy enough.</p>
  <p>Currently users cannot see who has the code, but rather the history of the points earned. This is because simply having a referral is useless until someone does somethign with it. Also, I've added a weird gamification system where users can switch user referral codes on the fly. Of course, if the other users can see this activity on the public log if you show it to cause engagement drama and competition to woo other users to their side.</p>
  <p>The shortcode for earnings history is <b>[vyps-refer-bal pid=4]</b> with 4 being the point id you wish to display. Site admins will have to set the pid for each point type, but the idea is that you don't really need to show all points if you did not intend to do referrals for all of them.</p>
  <h2>Dashed Slug's Wallet Integration</h2>
  <p>You can find this awesome <a href=\"https://wordpress.org/plugins/wallets/\" target=\"_blank\">Bitcoin and Altcoin Wallets</a> that allows you to integrate crypto payouts and exchange system on your WordPress site. It is rather complicated to setup (Even for a developer like me. -Felty) and you may have to work with Dashed Slug to get it to work.</p>
  <p>Once you get it working, you can call the API to exchange VYPS points to crypto with the following example:</p>
  <p><b>[vyps-pe firstid=3 outputid=5 firstamount=1000 outputamount=0.0001 symbol=LTC from_user_id=11 amount=0.0001]</b></p>
  <p>By adding symbol= from_user_id= and amount=, you can call the API into action. You need to create a bank user that holds your crypto that you desosited to and use that user's id for the from.</p>
  <p>You will need to create a dummy point for the coin in VYPS with the graphic of the crypto you want and make the outpoutamount and amount the same.</p>
  <p>If you add a refer= attribute to the shortcode, it behaves the same as a point to point except the referral is paid out in the input point rather than than either the output point or the crypto itself as it would get messy if we paid referrals in crypto.</p>
  <p>It is complicated to test as I'm using someone elses software but it does a user to user off blockchain transfer on your site. The user will still need to use the 3rd party Wallet via Dashed Slug's plugin to withdraw it off your site.</p>
  <p>For this particular feature as the rest of VYPS:</p>
  <p>No Warranty. EXCEPT AS OTHERWISE EXPRESSLY SET FORTH IN THIS AGREEMENT, NEITHER PARTY HERETO MAKES ANY REPRESENTATION AND EXTENDS NO WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED, WITH RESPECT TO THE SUBJECT MATTER OF THIS AGREEMENT, INCLUDING WITHOUT LIMITATION WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND ANY WARRANTY ARISING OUT OF PRIOR COURSE OF DEALING AND USAGE OF TRADE. IN PARTICULAR, BUT WITHOUT LIMITATION, VidYen, LLC MAKES NO REPRESENTATION AND EXTENDS NO WARRANTY CONCERNING WHETHER THE LICENSED COMPOUND OR A LICENSED PRODUCT IS FIT FOR ANY PARTICULAR PURPOSE OR SAFE FOR HUMAN CONSUMPTION.</p>
  ";

/* You know I'm not entirely sure if the ending of this PHP will cause problems.
*  According to WP standards it's not needed.
*/
