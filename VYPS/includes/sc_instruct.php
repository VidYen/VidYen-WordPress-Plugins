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
	<br><br>
	<h2>Balances</h2>
	<p><b>[vyps-balance-list]</b></p>
	<p> Shows a list of all points with the current balance along with name for logged in user. They must be logged into see this.</p>
	<p><b>[vyps-balance-list pid=1 uid=12]</b></p>
	<p>Replace the numbers with desired numerical value of the pid and uid.</p>
	<p>The pid is the pointID number seen on the points list page along with the uid which is the user id in WordPress. Leaving the uid option out (ie. [vyps-balance-list pid=1]  will default to the logged on user.</p>
	<p>Note: Leaving the uid blank will tell the user they need to log in if you intend to show this to users who are not log in.</p>
	<p>Also Note: pid will default to 1 which is the first point you have unless you delete it. I would recommend specifing pid at all times.</p>
	<br><br>
	";

/* You know I'm not entirely sure if the ending of this PHP will cause problems.
*  According to WP standards it's not needed.
*/


