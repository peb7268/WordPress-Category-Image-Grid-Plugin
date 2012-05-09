<?php
/**
 * Plugin Name: WordPress Cateogry Image Grid
 * Plugin URI: 
 * Description: Creates a grid of images for all of your categories
 * Version: 0.1
 * Author: Paul Barrick
 * Author URI: 
 * License: GPL2
 * 
 */
 
 
 /* -- Plugin Notes ---------------------------------------------------------------------------------------------------
 * -  Looks for a permalink structure /%category%/%postname%/
 *
 *
 * #plan of attack
 * 1) Register it as a shortcode
 *
 * 
 *-------------------------------------------------------------------------------------------------------------------*/

//Do so setup


//wp_register_script( $handle, $src, $deps, $ver, $in_footer );
wp_register_script( 'grid_js', plugins_url( '/js/grid.js', __FILE__ ), array('jquery'));
wp_enqueue_script( 'grid_js');

function printGrid(){
	$categories = get_categories();
	$cat_array = array();
	$img_array = array();
	$string = '<div id="category-grid" style="overflow: hidden;">';
	$i = 0;

	foreach ($categories as $category){
		//Grab All the object Props
		$obj_array = get_object_vars($category);
		
		//Convert the keys to an array
		$array_keys = array_keys($obj_array);
		
		//Grab the keys and values I want and push them on an array
		$cat_array[$i][$array_keys[13]] = $category->category_nicename;
		$cat_array[$i][$array_keys[9]] = $category->cat_ID;
		($category->category_nicename !== 'uncategorized') ? $nicename = $category->category_nicename : $nicename = 'uncategorized';
		$cat_array[$i]['category_url'] = site_url().'/category/'.$nicename;
		
		
		//Grab the first post from each category and get its featured image
		$args = 'posts_per_page=1&cat='.$category->cat_ID;
		$query = new WP_Query($args);
		
		if ($query-> have_posts() ) while ($query-> have_posts() ) : $query-> the_post(); 
			array_push($img_array, (has_post_thumbnail()) ? get_the_post_thumbnail($post->ID) : '<img src="'.get_bloginfo('stylesheet_directory').'/images/default.png" >');
		endwhile; 

		// Reset Post Data
		wp_reset_postdata();	

		
		//Build each image and url and store it in the $string var
		$string .= '<a href="'.
		$cat_array[$i]['category_url'].
		'" class="category-block">'.$img_array[$i].'</a>';
		
		$i++; 
	} //ends the foreach loop 
	$string .= "</div><!-- ends #category-grid -->";
	
	return $string;
}
add_shortcode('print_grid', 'printGrid');



//Add A button to tinyMCE for the shortcode
add_action('init', 'add_button');

function add_button() {
   if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )
   {
     add_filter('mce_external_plugins', 'add_plugin');
     add_filter('mce_buttons', 'register_button');
   }
}

function register_button($buttons) {  
   array_push($buttons, "print_grid");  
   return $buttons;  
} 

function add_plugin($plugin_array) {
   $plugin_array['print_grid'] = 'http://imperativedesign.net/wp-content/plugins/wp-category-grid/js/custom_buttons.js';
   return $plugin_array;
}


?>