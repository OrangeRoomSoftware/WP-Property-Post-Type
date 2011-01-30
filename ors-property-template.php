<?php
/*
Plugin Name: Property Template
Plugin URI: http://www.orangeroomsoftware.com/website-plugin/
Version: 1.0
Author: <a href="http://www.orangeroomsoftware.com/">Orange Room Software</a>
Description: A template for Properties
*/

# Post Thumbnails
add_theme_support( ‘post-thumbnails’ );

# Property Stylesheet
function ors_property_template_stylesheets() {
  wp_enqueue_style('property-template-style', '/wp-content/plugins/'.basename(dirname(__FILE__)).'/style.css', 'ors-property', null, 'all');
}
add_action('wp_print_styles', 'ors_property_template_stylesheets', 5);

# Custom post type
add_action( 'init', 'create_property_post_type' );
function create_property_post_type() {
  $labels = array(
    'name' => _x('Properties', 'post type general name'),
    'singular_name' => _x('Property', 'post type singular name'),
    'add_new' => _x('Add New', 'property'),
    'add_new_item' => __('Add New Property'),
    'edit_item' => __('Edit Property'),
    'new_item' => __('New Property'),
    'view_item' => __('View Property'),
    'search_items' => __('Search Properties'),
    'not_found' =>  __('No properties found'),
    'not_found_in_trash' => __('No properties found in Trash'),
    'parent_item_colon' => '',
    'menu_name' => 'Properties'

  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'query_var' => true,
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => 6,
    'supports' => array('title','location','editor','thumbnail'),
    'menu_icon' => '/wp-content/plugins/'.basename(dirname(__FILE__)).'/icon.png',
    'rewrite' => array(
      'slug' => 'properties',
      'with_front' => false
    )
  );

  register_post_type( 'property', $args );
}

add_action("admin_init", "admin_init");
function admin_init(){
  add_meta_box("location_meta", "Property Location", "location_meta", "property", "normal", "high");
}

function location_meta() {
  global $post;
  $custom = get_post_custom($post->ID);
  $location = $custom["location"][0];
  ?>
  <label>Location:</label>
  <input name="location" value="<?php echo $location; ?>" size="80"/>
  <?php
}

add_action('save_post', 'save_details');
function save_details(){
  global $post;
  update_post_meta($post->ID, "location", $_POST["location"]);
}

add_filter("manage_edit-property_columns", "property_edit_columns");
function property_edit_columns($columns){
  $columns = array(
    "cb" => "<input type=\"checkbox\" />",
    "title" => "Property Title",
    "location" => "Location",
    "author" => "Author",
    "date" => "Date Added"
  );

  return $columns;
}

add_action("manage_posts_custom_column",  "property_custom_columns");
function property_custom_columns($column){
  global $post;

  switch ($column) {
    case "location":
      $custom = get_post_custom();
      echo $custom["location"][0];
      break;
  }
}

add_filter( 'default_content', 'property_template' );
function property_template( $content ) {
  if ( $GLOBALS['post_type'] == 'property' ) {
  	$content = <<<END
<table width="100%" height="50">
  <tbody>
    <tr>
      <td align="center" valign="top" class="photo" width="20%">Photo Here</td>
      <td valign="top" class="detail" width="50%">Description</td>
      <td align="center" valign="top" class="downloads" width="30%"><div class="header">Downloads</div><br>File-1</td>
    </tr>
  </tbody>
</table>
END;
  	return $content;
  }
}
