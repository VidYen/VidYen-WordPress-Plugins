<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Creating a function specifically to only run whats in the text (such as consol.log() only when dubug mode is true)

/*** DEBUG FUNCTION ***/
function vyps_point_debug_func($debug_mode, $input_code)
{
  if ($debug_mode == TRUE)
  {
    return $input_code; //returns the code as is as a string
  }
  else
  {
    return ''; //returns blank otherwise.
  }

  return '';
}
