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
	<p>Setting the rows options will set the amount of rows per page of the transaction log. If you use a bootstrap theme you can turn option on to bootstrap=yes to get boostrap pagnation. NOTE: You can set log rows to something enourage like 1000000, but it may cause the site to slow if you have that many transactions on your VYPS table.</p>
	<p><b>[vyps-balance pid=# uid=optional icon=optional ]</b></p>
	<p>Shows the balance of a particular point through shortcode. Replace the # with the corresponding point ID. (Optional) You can set the user ID specifically by setting the uid attribute or if you want to turn off icons by setting to icon=0</p>
	<p><b>[vyps-balance-ww]</b></p>
	<p>Shows the current <a href=\"https://wordpress.org/plugins/woo-wallet/\" target=\"_blank\">WooCommerce Wallet</a> if it is installed.</p>
	<p><b>[vyps-balance-ww-menu]</b></p>
	<p>Used only if you wish to stick display the My Icon in a menu. This of course does requires you to use a the Shortcodes In Menu Plugin and may have produce unexpected results.</p>
	<h2>Point Transfer</h2>
	<p>This plugin needs requires VYPS Base and two point types to function. The intention is to allow a quick and easy way for users to transfer one type of point to another at varying rates.</p>
	<p><b>[vyps-pt spid=1 dpid=2 samount=100000 damount=100]</b></p>
	<p>Function debits points from one point type to another. with in being how many points used to transfer and out as how many points they get in the new point type.</p>
	<p>The spid is the source pointID and the dpid is the destination seen on the points list page. This shortcode always requires the user to be logged in and will not let you use set the user id as you do not want other users messing with the balances.</p>
	<p>The earn attribute is how much currency the user gets in the other point type. The spend attribute is how many VYPS points is are spent.</p>
	<p>All attributes must be set for this to function. There is no interface and is up to the site admin to add shortcode to a page or button.</p>
	<p><b>[vyps-pt-btn spid=1 dpid=2 samount=100000 damount=100]</b></p>
	<p>Does the same except now has built in button and user confirmation rather than a direct page call.</p>
	<p><b>[vyps-pt-tbl spid=1 dpid=2 samount=100000 damount=100]</b></p>
	<p>Creates a table interfacesimple UI in a table for the point transfer, which includes an improved  button and better interface including a button, point icons, and feedback. If you do not want to mess with your special layout, use this.</p>
  <h2>Transfer points into WooCommerce Credit</h2>
  <p><b>[vyps-pt-ww spid=1 samount=100000 damount=0.010]</b></p>
	<p>Creates a table transfer menu to transfer points to the WooWallet if you have it installed. This shortcode needs requires both VYPS and <a href=\"https://wordpress.org/plugins/woo-wallet/\" target=\"_blank\">WooCommerce Wallet</a> to function.
	It is assumed that you are going straight to WooWallet so there is no need to set dpid, but you still need to set damount.</p>
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
	";

/* You know I'm not entirely sure if the ending of this PHP will cause problems.
*  According to WP standards it's not needed.
*/
