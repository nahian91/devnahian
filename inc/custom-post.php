<?php

/* Custom post type 'themes' with full labels, support for the REST API, a customized rest_base, and explicit registry of the default controller: */

add_action( 'init', 'create_posttype_themes' );
function create_posttype_themes() {
  $labels = array(
    'name'               => _x( 'Themes', 'post type general name', 'anahian' ),
    'singular_name'      => _x( 'Themes', 'post type singular name', 'anahian' ),
    'menu_name'          => _x( 'Themes', 'admin menu', 'anahian' ),
    'name_admin_bar'     => _x( 'Themes', 'add new on admin bar', 'anahian' ),
    'add_new'            => _x( 'Add New', 'theme', 'anahian' ),
    'add_new_item'       => __( 'Add New Theme', 'anahian' ),
    'new_item'           => __( 'New Theme', 'anahian' ),
    'edit_item'          => __( 'Edit Theme', 'anahian' ),
    'view_item'          => __( 'View Theme', 'anahian' ),
    'all_items'          => __( 'All Themes', 'anahian' ),
    'search_items'       => __( 'Search Theme', 'anahian' ),
    'parent_item_colon'  => __( 'Parent Theme:', 'anahian' ),
    'not_found'          => __( 'No theme found.', 'anahian' ),
    'not_found_in_trash' => __( 'No theme found in Trash.', 'anahian' )
  );
 
  $args = array(
    'labels'             => $labels,
    'description'        => __( '', 'anahian' ),
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array( 'slug' => 'themes' ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => null,
    'show_in_rest'       => true,
    'rest_base'          => 'themes',
    'rest_controller_class' => 'WP_REST_Posts_Controller',
    'supports'           => array( 'title', 'editor', '', '', 'thumbnail', '', 'revisions', 'custom-fields' ),
	'taxonomies'         => array( 'category' )
  );
 
  register_post_type( 'themes', $args );
}