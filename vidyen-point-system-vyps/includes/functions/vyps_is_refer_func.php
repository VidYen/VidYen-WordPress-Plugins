<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//

/*** POINT NAME FUNCTION ***/
function vyps_is_refer_func() {

  //Side note. I don't think there will be a variable pass in as this will always just ask who is the refer of the current user.
  //Boot user out if not logged in. Like how can we tell who is the refer then?
  if ( ! is_user_logged_in() ) {

    return; //Not logged in. You see nothing.

  }

  //Get current user Id obviously to figure out who their refer is
  $user_id = get_current_user_id();

  //I thought about this while. Perhaps I should just use refer, I'm tempted to brand my own software even further
  //Because I could but then they'd ask for more premium from people who already paid. Either way. The word needs to be static so it can be removed and checked.
  $user_refer = 'REFER'. $user_id;

  //This is hardcoded, but the label we are going to cram into the usermeta table
  $key = 'vyps_current_refer';

  //NO WIRE ARRAYS! Only one value at all time. Unless someone messed something up somewhere.
  $single = TRUE;

  //Now we pull what the users current referr was
  $current_refer = get_user_meta( $user_id, $key, $single );

  //Checking to see if the variable is empty or not
  if (!empty($current_refer)) {

    //I'm going to check trim it what we pull from meta
    $current_refer_user_id = trim($current_refer, "REFER"); //Tear that string down Mr. Gorbachev!
    $current_refer_user_id = trim($current_refer, "refer"); //some dummy is going to type it in.

    //And then check it if it actually works and that is a users.
    $current_refer_data = get_userdata( $current_refer_user_id ); //This is an array btw. See WP codex for details.

    //OK if this is empty because it returned nothing, then we have issues
    if ( is_wp_error($current_refer_data) OR $current_refer_data == false OR $current_refer_data == ''){

      //If it gives an error then it was not valid. Or if was a zero or false to begin withBack to zero here as well.
      //Or blank but not empty
      return 0;

    } elseif ($user_id == $current_refer_user_id) {

      return 0; //In case your users thought they'd be cute and set themselves as their own refer.

    } elseif (!empty($current_refer_user_id)) {

      //This following data call should work. In theory.
      return $current_refer_user_id; //This should be the id that the miner sends to. Pretty much the purpose of this function.

    } //End of is_wp_error if chain

  } //End of !empty if.

  //So if nothing found the userid, I guess we throw out a zero with our hands up in a shrug and say "nothing returned"
  return 0; //There was no id. Return a 0 so the extra part does nothing

}
