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
	<p><b>[vyps-balance-list]</b></p>
	<p> Shows a list of all points with the current balance along with name for logged in user. They must be logged into see this.</p>
	<p><b>[vyps-balance-list pid=1 uid=12]</b></p>
	<p>Replace the numbers with desired numerical value of the pid and uid.</p>
	<p>The pid is the pointID number seen on the points list page along with the uid which is the user id in WordPress. Leaving the uid option out (ie. [vyps-balance-list pid=1]  will default to the logged on user.</p>
	<p>Note: Leaving the uid blank will tell the user they need to log in if you intend to show this to users who are not log in.</p>
	<p>Also Note: pid will default to 1 which is the first point you have unless you delete it. I would recommend specifing pid at all times.</p>
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
	";

/* You know I'm not entirely sure if the ending of this PHP will cause problems.
*  According to WP standards it's not needed.
*/


