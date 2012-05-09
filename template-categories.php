<?php 
/* Template Name: Categories */

get_header(); ?>
<div class="clear">&nbsp;</div>
</div><!-- #header -->

<div id="container">
<div id="left-col" class="left">
	
	<div id="thePosts" <?php post_class(); ?>>
	
	
	
<?php 
//This looks for a permalink structure /%category%/%postname%/

//A little debugging setup
function pre($array){
	echo '<pre>';
		print_r($array);
	echo '</pre>';
}

	$categories = get_categories();
	$cat_array = array();
	$img_array = array();
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
	query_posts('posts_per_page=1&cat='.$category->cat_ID);
	if ( have_posts() ) while ( have_posts() ) : the_post(); 
		array_push($img_array, (has_post_thumbnail()) ? get_the_post_thumbnail($post->ID) : '<img src="'.get_bloginfo('stylesheet_directory').'/images/default.png" >');
	endwhile; 
	
	//Build each image and url ?>		
	<a href="<?php echo $cat_array[$i]['category_url']; ?>" class="category-block">
		<?php echo $img_array[$i]; ?>
	</a>
<?php	$i++; } //ends the foreach loop ?>
		
	</div><!-- #thePosts -->
</div><!-- #left-col -->			

</div><!-- #container -->


</div><!-- #wrapper -->
<div class="clear">&nbsp;</div>


<?php /* The CSS For the effect
.page-template-template-categories-php #thePosts {
	width: 100%;
	overflow: hidden;
}
.page-template-template-categories-php #left-col {
	width: 100%;
	overflow: hidden;
	background: #999;
}

.page #left-col .category-block {
	text-decoration: underline;
	width: 128px;
	height: 128px;
	float: left;
	overflow: hidden;
	margin: 0 5px 5px 5px;
	padding: 0;
}
.page #left-col .category-block img {
	padding: 0;
	width: 128px;
	height: 128px;
	margin: 0;
}

*/
?>