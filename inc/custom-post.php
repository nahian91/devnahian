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

// Shortcode to display multiple WordPress.org plugins info with banner using Bootstrap, sorted by active installs
function dnew_plugins_info_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'slugs' => 'contact-form-7', // comma-separated plugin slugs
    ), $atts, 'plugins_infos' );

    $slugs = array_map( 'trim', explode( ',', $atts['slugs'] ) );

    if ( empty( $slugs ) ) {
        return '<p>Please provide at least one plugin slug.</p>';
    }

    if ( ! function_exists( 'plugins_api' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    }

    $plugins_data = [];

    // Fetch plugin info
    foreach ( $slugs as $slug ) {
        if ( empty( $slug ) ) continue;

        $plugin_info = plugins_api(
            'plugin_information',
            array(
                'slug'   => $slug,
                'fields' => array(
                    'banners'        => true,
                    'versions'       => true,
                    'requires'       => true,
                    'tested'         => true,
                    'downloaded'     => true,
                    'downloadlink'   => true,
                    'last_updated'   => true,
                    'active_installs'=> true,
                ),
            )
        );

        if ( is_wp_error( $plugin_info ) ) continue;

        // Adjust active installs if fewer than 10
        if ( $plugin_info->active_installs < 10 ) {
            $plugin_info->active_installs = rand(6, 9);
        }

        $plugins_data[] = $plugin_info;
    }

    // Sort plugins by active installs descending
    usort( $plugins_data, function( $a, $b ) {
        return $b->active_installs - $a->active_installs;
    });

    // Output
    $output = '<div class="row">';

    foreach ( $plugins_data as $plugin_info ) {
        $output .= '<div class="col-md-6 mb-4">';
        $output .= '<div class="plugin-card h-100">';

        // Banner image (high res if available, otherwise low res)
        $banner_url = '';
        if ( ! empty( $plugin_info->banners['high'] ) ) {
            $banner_url = $plugin_info->banners['high'];
        } elseif ( ! empty( $plugin_info->banners['low'] ) ) {
            $banner_url = $plugin_info->banners['low'];
        }

        if ( $banner_url ) {
            $output .= '<img src="' . esc_url( $banner_url ) . '" class="plugin-card-img-top" alt="' . esc_attr( $plugin_info->name ) . '">';
        }

        $output .= '<div class="plugin-card-body">';
        $output .= '<h5 class="plugin-card-title">' . esc_html( $plugin_info->name ) . '</h5>';

        // Plugin info
        $output .= '<ul class="plugin-card-info list-unstyled">';
        $output .= '<li>Author: <span>' . esc_html( wp_strip_all_tags( $plugin_info->author ) ) . '</span></li>';
        $output .= '<li>Version: <span>' . esc_html( $plugin_info->version ) . '</span></li>';
        $output .= '<li>Requires WP: <span>' . esc_html( $plugin_info->requires ) . '</span></li>';
        $output .= '<li>Tested Up To: <span>' . esc_html( $plugin_info->tested ) . '</span></li>';
        $output .= '<li>Downloads: <span>' . number_format_i18n( $plugin_info->downloaded ) . '</span></li>';
        $output .= '<li>Active Installations: <span>' . number_format_i18n( $plugin_info->active_installs ) . '+</span></li>';
        $output .= '<li>Last Updated: <span>' . date_i18n( get_option( 'date_format' ), strtotime( $plugin_info->last_updated ) ) . '</span></li>';
        $output .= '<li><a href="' . esc_url( $plugin_info->download_link ) . '" target="_blank" class="btn btn-primary btn-sm">Download Now</a></li>';
        $output .= '</ul>';

        $output .= '</div>'; // end card-body
        $output .= '</div>'; // end card
        $output .= '</div>'; // end col
    }

    $output .= '</div>'; // end row

    return $output;
}
add_shortcode( 'plugins_infos', 'dnew_plugins_info_shortcode' );

