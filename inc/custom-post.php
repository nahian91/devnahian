<?php
add_action( 'init', 'create_posttype_themes' );
function create_posttype_themes() {
    $labels = array(
        'name'               => _x( 'Themes', 'post type general name', 'devnahian' ),
        'singular_name'      => _x( 'Themes', 'post type singular name', 'devnahian' ),
        'menu_name'          => _x( 'Themes', 'admin menu', 'devnahian' ),
        'name_admin_bar'     => _x( 'Themes', 'add new on admin bar', 'devnahian' ),
        'add_new'            => _x( 'Add New', 'theme', 'devnahian' ),
        'add_new_item'       => __( 'Add New Theme', 'devnahian' ),
        'new_item'           => __( 'New Theme', 'devnahian' ),
        'edit_item'          => __( 'Edit Theme', 'devnahian' ),
        'view_item'          => __( 'View Theme', 'devnahian' ),
        'all_items'          => __( 'All Themes', 'devnahian' ),
        'search_items'       => __( 'Search Theme', 'devnahian' ),
        'parent_item_colon'  => __( 'Parent Theme:', 'devnahian' ),
        'not_found'          => __( 'No theme found.', 'devnahian' ),
        'not_found_in_trash' => __( 'No theme found in Trash.', 'devnahian' )
    );
 
    $args = array(
        'labels'             => $labels,
        'description'        => __( '', 'devnahian' ),
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
        'supports'           => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields' ),
        'taxonomies'         => array( 'category' )
    );
 
    register_post_type( 'themes', $args );
}

add_action( 'init', 'create_posttype_tutorial' );
function create_posttype_tutorial() {
    $labels = array(
        'name'               => _x( 'Tutorial', 'post type general name', 'devnahian' ),
        'singular_name'      => _x( 'Tutorial', 'post type singular name', 'devnahian' ),
        'menu_name'          => _x( 'Tutorial', 'admin menu', 'devnahian' ),
        'name_admin_bar'     => _x( 'Tutorial', 'add new on admin bar', 'devnahian' ),
        'add_new'            => _x( 'Add New', 'tutorial', 'devnahian' ),
        'add_new_item'       => __( 'Add New Tutorial', 'devnahian' ),
        'new_item'           => __( 'New Tutorial', 'devnahian' ),
        'edit_item'          => __( 'Edit Tutorial', 'devnahian' ),
        'view_item'          => __( 'View Tutorial', 'devnahian' ),
        'all_items'          => __( 'All Tutorials', 'devnahian' ),
        'search_items'       => __( 'Search Tutorial', 'devnahian' ),
        'parent_item_colon'  => __( 'Parent Tutorial:', 'devnahian' ),
        'not_found'          => __( 'No tutorial found.', 'devnahian' ),
        'not_found_in_trash' => __( 'No tutorial found in Trash.', 'devnahian' )
    );
 
    $args = array(
        'labels'             => $labels,
        'description'        => __( '', 'devnahian' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'tutorial' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'show_in_rest'       => true,
        'rest_base'          => 'tutorials',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'supports'           => array( 'title', 'revisions', 'custom-fields' ),
        'taxonomies'         => array( 'category' )
    );
 
    register_post_type( 'tutorial', $args );
}