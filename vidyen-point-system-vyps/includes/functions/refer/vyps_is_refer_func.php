<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This function is designed to see if a refer code is actually a reer and return the user id it is FROM
//AND check to see if it exists (in case invalid or user id was deleted a while back)
//Also it checks to make sure you aren't putting in yoru own code. NO CHEATING

/*** IS REFER FUNCTION ***/
function vyps_is_refer_func($current_refer) {

  //NOTE: I went with variable pass. As we needed to check to post to see if it was valid before entering before it pulled the meta.
  //NOTE NOTE is_refer checks to see if the refer code is valid and who its from, not who is the code and who that is. Make sense?

  //Checking to see if the variable is empty or not
  if (!empty($current_refer)) {

    $current_user_id = get_current_user_id();

    //Ok we take it back out again and base64_decode() it;
    //NOTE: I intvaled it because when people put in crap, it still has to be checked. In theory it will force a value that can be divided by 256
    //But will return a non-user. Unless you have millions of users? Why are you using WordPress then?
    $current_refer_user_id = (intval(base64_decode($current_refer)) / 256); //Making it a bet less obvious with the base 64, but please don't take me as a security expert -Felty

    //NOTE: It can return a number that could be a user id but we might not actually know.
    //So we have to check.
    $current_refer_data = get_userdata( $current_refer_user_id ); //This is an array btw. See WP codex for details.

    //OK if this is empty because it returned nothing, then we have issues, Also I'm just checking to make sure user_id not less than 1 for some odd reason
    if ( is_wp_error($current_refer_data) OR $current_refer_data == false OR $current_refer_data == '' OR $current_refer_user_id < 1){

      //If it gives an error then it was not valid. Or if was a zero or false to begin withBack to zero here as well.
      //Or blank but not empty
      return 0;

    } elseif($current_user_id == $current_refer_user_id) {

      return 0; //In case your users thought they'd be cute and set themselves as their own refer.

    } elseif(!empty($current_refer_user_id)) {

      //This following data call should work. In theory.
      return $current_refer_user_id; //This should be the id that the miner sends to. Pretty much the purpose of this function.

    } //End of is_wp_error if chain

  }
  //So if nothing found the userid, I guess we throw out a zero with our hands up in a shrug and say "nothing returned"
  return 0; //There was no id. Return a 0 so the extra part does nothing

}
