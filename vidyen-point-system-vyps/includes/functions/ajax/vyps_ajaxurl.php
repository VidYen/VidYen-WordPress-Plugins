<?php

/*** Fix for the ajaxurl not found with custom template sites ***/
add_action('wp_head', 'vyms_ajaxurl');

function vyps_ajaxurl()
{
   echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}
