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

/*-- Do some setup ---------------------------------------------------------------------------------------------------------------------------------------------------------*/
function pre($array){
	echo '<pre>'; 	
		 	print_r($array);
	echo '</pre><br />';
}


/* wp_register_script( $handle, $src, $deps, $ver, $in_footer );
wp_register_style( $handle, $src, $deps, $ver, $media ) */
wp_register_script( 'grid_js', plugins_url( '/js/grid.js', __FILE__ ), array('jquery'));
wp_register_style( 'grid_css', plugins_url( '/css/grid.css', __FILE__ ));
wp_enqueue_script( 'grid_js');

/*---------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/




/*-- Main Function ---------------------------------------------------------------------------------------------------------------------------------------------------------*/
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
		$cat_array[$i][$array_keys[6]] = $category->description;
		
		($category->category_nicename !== 'uncategorized') ? $nicename = $category->category_nicename : $nicename = 'uncategorized';
		$cat_array[$i]['category_url'] = site_url().'/category/'.$nicename;
		
		
		//Grab the first post from each category and get its featured image
		$args = 'posts_per_page=1&cat='.$category->cat_ID;
		$query = new WP_Query($args);
		
		if ($query-> have_posts() ) while ($query-> have_posts() ) : $query-> the_post(); 
			global $post;
			array_push($img_array, (has_post_thumbnail()) ? get_the_post_thumbnail($post->ID) : '<img src="'.get_bloginfo('stylesheet_directory').'/images/default.png" >');
		endwhile; 

		// Reset Post Data
		wp_reset_postdata();	

		
		//Build each image and url and store it in the $string var
		$string .= '<a href="'.
		$cat_array[$i]['category_url'].
		'" class="category-block">'.$img_array[$i].'<div class="description">'.$cat_array[$i]['description'].'</div><!-- .description --></a>';
		$i++; 
	} //ends the foreach loop 
	$string .= "</div><!-- ends #category-grid -->";
	return $string;
}
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/





/*-- Define my functions---------------------------------------------------------------------------------------------------------------------------------------------------------*/
function sendStyles(){
	wp_enqueue_style( 'grid_css');
}

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

/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/







/*-- Render The Menu -----------------------------------------------------------------------------------------------------------------------------------------------------------*/
function create_grid_menu_page(){
	add_menu_page(				//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		'Grid Options',			// The title to be displayed on the corresponding page for this menu
		'Category Grid',		// The text to be displayed for this actual menu item
		'administrator',		// Which type of users can see this menu
		'grid',					// The unique ID - that is, the slug - for this menu item
		'grid_page_render',		// The name of the function to call when rendering the menu for this page
		''
	);
}


//Makes the options page
function grid_page_render(){ ?>

	<div class="wrap">
		<div id="icon-themes" class="icon32"></div>
		<h2>Category Grid Options</h2>
	
		<!-- check for errors -->
		<?php //settings_errors(); ?>
		
		<!-- Create the form that will be used to render our options -->
		<form method="post" action="<?php echo $_SERVER['self']; ?>">
			<?php settings_fields( 'grid_display_options' ); ?>
			<?php do_settings_sections( 'grid' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	
<?php }



function grid_options() { 
	if( false == get_option( 'grid_display_options' ) ) {
		update_option( 'grid_display_options' );
	}
	add_settings_section(
		'grid_settings_section',			// ID used to identify this section and with which to register options
		'',	// Title to be displayed on the administration page
		'grid_render_options',				// Callback used to render the content of the section
		'grid'								// Page on which to add this section of options ( do_settings_sections( ) accepts this name as a parameter )
	);
	
	add_settings_field(						//add_settings_field( $id, $title, $callback, $page, $section, $args );
		'show_header',						// ID used to identify the input to be generated
		'Header',							// The label to the left of the input. The text that will be generated
		'grid_render_first',				// The name of the function responsible for rendering the option interface, Name and id of the input should match the $id given to this function.
		'grid',								// The page on which this option will be displayed
		'grid_settings_section',			// The name of the section to which this field belongs
		'The First Input'
	);
	
	add_settings_field(
		'show_body',						// ID used to identify the field throughout the theme
		'Body',								// The label to the left of the option interface element
		'grid_render_second',				// The name of the function responsible for rendering the option interface
		'grid',								// The page on which this option will be displayed
		'grid_settings_section',			// The name of the section to which this field belongs
		'The Second Input'
	);
	
	//Should Match the settings_field() parameter above in the form
	register_setting('grid_settings_section','grid_display_options');

}

function grid_render_first($args) { 
 
     // Get an array of the options
    $options = get_option('grid_display_options'); 
 	
    // Next, we update the name attribute to access this element's ID in the context of the display options array  
    // We also access the show_header element of the options collection in the call to the checked() helper function  
    $html = '<input type="checkbox" id="show_header" name="grid_display_options[show_header]" value="1"'.checked(1, get_option('show_footer'), false).'/>';   
 
    echo $html; 
}

function grid_render_second($args) {  	
    // Get an array of the options
    $options = get_option('grid_display_options'); 
 	
    // Next, we update the name attribute to access this element's ID in the context of the display options array  
    // We also access the show_header element of the options collection in the call to the checked() helper function  
    $html = '<input type="checkbox" id="show_body" name="grid_display_options[show_body]" value="1"'.checked(1, get_option('show_footer'), false).'/>';   
    
    echo $html; 
}

/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/





/*-- Hook my toys into WordPress ---------------------------------------------------------------------------------------------------------------------------------------------------------*/
add_action('wp_print_styles','sendStyles');
add_action('init', 'add_button');
add_action('admin_init', 'grid_options');
add_action('admin_menu', 'create_grid_menu_page');

//Add my shortcodes
add_shortcode('print_grid', 'printGrid');

/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/