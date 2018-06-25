<?php
/* 
 * Simple credits file to update the list so it doesn't have to be updated every time
  */

 echo "<br><br>
	<h1>Shortcodes and Syntax</h1>
	<p>This plugin addon to the VYPS allow you add shortcodes so your users can see a log of all transactions on the VidYen Point System.</p>
	<h2>Public Log</h2>
	<p><b>[vyps-pl]</b></p>
	<p>Shows the entire log.</p>
	<h2>Balances</h2>
	<p><b>[vyps-balance pid=# uid=optional icon=optional ]</b></p>
	<p>Shows the balance of a particular point thorugh shortcode. Replace the # with the corresponding point ID. (Optional) You can set the user ID specifically by setting the uid attribute or if you want to turn off icons by setting to icon=0</p>
	<p><b>[vyps-balance-ww]</b></p>
	<p>Shows the current <a href=\"https://wordpress.org/plugins/woo-wallet/\" target=\"_blank\">WooCommerce Wallet</a> if it is installed.</p>
	<p><b>[vyps-balance-ww-menu]</b></p>
	<p>Used only if you wish to stick the My Icon in a menu. This of course does require you to use a Shortcode In Menu Plugin and may have unexpected results.</p>
	<h2>Point Transfer</h2>
	<p>This plugin needs VYPS Base and two point types to function. The intention is to allow a quick and easy way for users to transfer one type of point to another at varrying rates</p>
	<p><b>[vyps-pt spid=1 dpid=2 samount=100000 damount=100]</b></p>
	<p>Function debits points from one point type to another with in being how many points used to transfer and out as how many points they get in the new point type</p>
	<p>The spid is the source pointID and the dpid is the destination seen on the points list page. This shortcode always requires the user to be logged in and will not let you use set the user id as you do not want other users messing with the balances.</p>
	<p>The earn attribute is how much currency the user gets in the other poitn type. The spend attribute is how many VYPS points is spent.</p>
	<p>All attributes must be set for this to function. There is no interfact and is up to the site admin to add shortcode to a page or button.</p>
	<p><b>[vyps-pt-btn spid=1 dpid=2 samount=100000 damount=100]</b></p>
	<p>Does the same except now has built in button and user confirmation rather than a direct page call.</p>
	<p><b>[vyps-pt-tbl spid=1 dpid=2 samount=100000 damount=100]</b></p>
	<p>Creates a table interface UI for the point transfer which includes a button and better interface including point icons and feedback. If you do not want to mess with your special layout, use this.</p>
	<p><b>[vyps-pt-ww spid=1 samount=100000 damount=0.010]</b></p>
	<p>Creates table transer menu to transfer points to the WooWallet if you have it installed. This shortcode needs both VYPS and <a href=\"https://wordpress.org/plugins/woo-wallet/\" target=\"_blank\">WooCommerce Wallet</a> to function.
	As its assumed that you are going straigh to WooWallet there is no need to set dpid, but you still need to set damount.</p>
	<h2>Login Awareness Shortcode</h2>
	<p>Other plugins do this, but made a quick way to let users know they are not logged and therefore cannot interact with VYPS.</p>
	<p><b>[vyps-lg]</b></p>
	<p>Just shows a generic \"You are not logged in.\" message when user not logged in.</p>
	<p><b>[vyps-lg message=\"Hey foo! You need to log in.\"]</b></p>
	<p>Allows custom message by admin when setting up the awareness message.</p>
	";

/* You know I'm not entirely sure if the ending of this PHP will cause problems.
*  According to WP standards it's not needed.
*/


