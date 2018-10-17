<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Moving transfer system to a function in itself.
//I'm doing this to make it easier on me and 3rd party devs who just want to call a function direct
//I'm going to have an arry come in via $atts from the short code side, but assume its already handled
//So don't have to call it a second time.
//This was a copy and paste of PE but i'm rescraping and just having a user to user transfer in this function
//I honestly don't know if its needed to have point to point transfer, but rather same to same.

//NOTE: This is a place holder for now.

/*** CREATE USER TRANSFER FUNCTION ***/
function vyps_user_transfer_func( $atts ) {

  //NOTE: I'm going with mobile view from now on. Although I like the wide screen view, it was not needed
  //Also I'm transfering to


}
