<?php
/*
 * Simple credits file to update the list so it doesn't have to be updated every time
  */

 echo "<br><br><h1>VYPS Adscend Media Shortcode Addon Plugin</h1>
	<p>This plugin needs VYPS and an <a href=\"https://adscendmedia.com\" target=\"_blank\">Adscend Media</a> account to function. The intention is to allow a quick and easy way for you to award user points for Adscend Activity.</p>
	<h2>Shortcodes Syntax</h2>
	<p><b>[vyps-as-watch pub=113812 profile=13246 pid=1]</b></p>
	<p>The above shorcode will put up an Adscend wall using the publisher and profile id. (Those are our test site numbers, replace with yours) The pid is the point ID of course.</p>
	<p>The pid is the pointID number seen on the points list page. This shortcode always requires the user to be logged in and will not let you use set the user id as you do not want other users messing with the balances.</p>
	<p>To have a user redeem points through the Adscend API (the points Adscend has said they earned). You need to get your own API off your Adscend wall page. The API key is on the integration page on your offer wall under API/SDK integration.</p>
	<p><b>[vyps-as-redeem pub=113812 profile=13246 api=typekeyhere pid=1 payout=750]</b></p>
	<p>All attributes must be set for this to function. There is no interface and is up to the site admin to add shortcode to a page or button. Future versions will include a better interface.</p>
	<p><b>[vyps-as-redeem-btn pub=113812 profile=13246 api=typekeyhere pid=1 payout=750]</b></p>
	<p>Using the btn shortcode will just have a redemption function that calls the function without having to create separate pages.</p>
	";

/* You know I'm not entirely sure if the ending of this PHP will cause problems.
*  According to WP standards it's not needed.
*/
