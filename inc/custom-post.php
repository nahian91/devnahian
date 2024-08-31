<?php

// Add this to your theme's functions.php file or a custom plugin

// Function to register 'Tutorial' custom post type
function create_posttype_tutorial() {
    $labels = array(
        'name'                  => _x( 'Tutorials', 'Post type general name', 'textdomain' ),
        'singular_name'         => _x( 'Tutorial', 'Post type singular name', 'textdomain' ),
        'menu_name'             => _x( 'Tutorials', 'Admin Menu text', 'textdomain' ),
        'name_admin_bar'        => _x( 'Tutorial', 'Add New on Toolbar', 'textdomain' ),
        'add_new'               => __( 'Add New', 'textdomain' ),
        'add_new_item'          => __( 'Add New Tutorial', 'textdomain' ),
        'new_item'              => __( 'New Tutorial', 'textdomain' ),
        'edit_item'             => __( 'Edit Tutorial', 'textdomain' ),
        'view_item'             => __( 'View Tutorial', 'textdomain' ),
        'all_items'             => __( 'All Tutorials', 'textdomain' ),
        'search_items'          => __( 'Search Tutorials', 'textdomain' ),
        'parent_item_colon'     => __( 'Parent Tutorials:', 'textdomain' ),
        'not_found'             => __( 'No Tutorials found.', 'textdomain' ),
        'not_found_in_trash'    => __( 'No Tutorials found in Trash.', 'textdomain' ),
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'tutorial' ),
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => 25,
        'show_in_rest'          => true, // For REST API support
        'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields' ),
    );

    register_post_type( 'tutorial', $args );
}
add_action( 'init', 'create_posttype_tutorial' );

// Function to register 'Theme' custom post type
function create_posttype_theme() {
    $labels = array(
        'name'                  => _x( 'Themes', 'Post type general name', 'textdomain' ),
        'singular_name'         => _x( 'Theme', 'Post type singular name', 'textdomain' ),
        'menu_name'             => _x( 'Themes', 'Admin Menu text', 'textdomain' ),
        'name_admin_bar'        => _x( 'Theme', 'Add New on Toolbar', 'textdomain' ),
        'add_new'               => __( 'Add New', 'textdomain' ),
        'add_new_item'          => __( 'Add New Theme', 'textdomain' ),
        'new_item'              => __( 'New Theme', 'textdomain' ),
        'edit_item'             => __( 'Edit Theme', 'textdomain' ),
        'view_item'             => __( 'View Theme', 'textdomain' ),
        'all_items'             => __( 'All Themes', 'textdomain' ),
        'search_items'          => __( 'Search Themes', 'textdomain' ),
        'parent_item_colon'     => __( 'Parent Themes:', 'textdomain' ),
        'not_found'             => __( 'No Themes found.', 'textdomain' ),
        'not_found_in_trash'    => __( 'No Themes found in Trash.', 'textdomain' ),
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'theme' ),
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => 25,
        'show_in_rest'          => true,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields' ),
    );

    register_post_type( 'theme', $args );
}
add_action( 'init', 'create_posttype_theme' );


// Function to register 'Digital' custom post type
function create_posttype_digital() {
    $labels = array(
        'name'                  => _x( 'Digital', 'Post type general name', 'textdomain' ),
        'singular_name'         => _x( 'Digital', 'Post type singular name', 'textdomain' ),
        'menu_name'             => _x( 'Digital', 'Admin Menu text', 'textdomain' ),
        'name_admin_bar'        => _x( 'Digital', 'Add New on Toolbar', 'textdomain' ),
        'add_new'               => __( 'Add New', 'textdomain' ),
        'add_new_item'          => __( 'Add New Digital', 'textdomain' ),
        'new_item'              => __( 'New Digital', 'textdomain' ),
        'edit_item'             => __( 'Edit Digital', 'textdomain' ),
        'view_item'             => __( 'View Digital', 'textdomain' ),
        'all_items'             => __( 'All Digital', 'textdomain' ),
        'search_items'          => __( 'Search Digital', 'textdomain' ),
        'parent_item_colon'     => __( 'Parent Digital:', 'textdomain' ),
        'not_found'             => __( 'No Digital found.', 'textdomain' ),
        'not_found_in_trash'    => __( 'No Digital found in Trash.', 'textdomain' ),
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'digital' ),
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => 25,
        'show_in_rest'          => true,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields' ),
    );

    register_post_type( 'Digital', $args );
}
add_action( 'init', 'create_posttype_digital' );
