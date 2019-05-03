<?php
/*
Template Name: Adgate Post Back Template
*/

/*
language_attributes();
if ( ! get_theme_support( 'title-tag' ) )
{
  wp_title();
}
wp_head();
body_class();
*/
  while ( have_posts() ) : the_post();

  	 //the_ID();
     //post_class();
     the_content();

  endwhile;
  //wp_footer();
