<?php

/**
 * 1. Register Professional Custom Post Types: Tutorial & Resource
 */
function dnr_register_post_types() {
    
    // --- TUTORIAL POST TYPE ---
    $tutorial_labels = array(
        'name'               => 'Tutorials',
        'singular_name'      => 'Tutorial',
        'menu_name'          => 'Tutorials',
        'name_admin_bar'     => 'Tutorial',
        'add_new'            => 'Add New Tutorial',
        'add_new_item'       => 'Add New Tutorial',
        'new_item'           => 'New Tutorial',
        'edit_item'          => 'Edit Tutorial',
        'view_item'          => 'View Tutorial',
        'all_items'          => 'All Tutorials',
        'search_items'       => 'Search Tutorials',
        'parent_item_colon'  => 'Parent Tutorials:',
        'not_found'          => 'No tutorials found.',
        'not_found_in_trash' => 'No tutorials found in Trash.'
    );

    register_post_type('tutorial', array(
        'labels'             => $tutorial_labels,
        'public'             => true,
        'show_in_rest'       => true, // Gutenberg enabled
        'menu_position'      => 25,
        'menu_icon'          => 'dashicons-welcome-learn-more',
        'supports'           => array('title', 'editor', 'thumbnail', 'revisions', 'custom-fields'),
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'tutorial'),
    ));

    // --- RESOURCE POST TYPE ---
    $resource_labels = array(
        'name'               => 'Resources',
        'singular_name'      => 'Resource',
        'menu_name'          => 'Resources',
        'name_admin_bar'     => 'Resource',
        'add_new'            => 'Add New Resource',
        'add_new_item'       => 'Add New Resource',
        'new_item'           => 'New Resource',
        'edit_item'          => 'Edit Resource',
        'view_item'          => 'View Resource',
        'all_items'          => 'All Resources',
        'search_items'       => 'Search Resources',
        'not_found'          => 'No resources found.',
        'not_found_in_trash' => 'No resources found in Trash.'
    );

    register_post_type('resource', array(
        'labels'             => $resource_labels,
        'public'             => true,
        'menu_position'      => 26,
        'menu_icon'          => 'dashicons-database',
        'supports'           => array('title', 'thumbnail', 'custom-fields'),
        'has_archive'        => true,
        'show_in_rest'       => false, 
        'rewrite'            => array('slug' => 'resource'),
    ));

    // --- RESOURCE ORDERS (NESTED) ---
    $order_labels = array(
        'name'          => 'Orders',
        'singular_name' => 'Order',
        'all_items'     => 'All Orders',
        'search_items'  => 'Search Orders',
    );

    register_post_type('resource_order', array(
        'labels'             => $order_labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => 'edit.php?post_type=resource',
        'supports'           => array('title'),
    ));
}
add_action('init', 'dnr_register_post_types');

/**
 * 2. Professional Resource Categories (Taxonomy)
 */
function dnr_register_taxonomies() {
    $labels = array(
        'name'              => 'Resource Categories',
        'singular_name'     => 'Category',
        'search_items'      => 'Search Categories',
        'all_items'         => 'All Categories',
        'edit_item'         => 'Edit Category',
        'update_item'       => 'Update Category',
        'add_new_item'      => 'Add New Resource Category',
        'new_item_name'     => 'New Category Name',
        'menu_name'         => 'Categories',
    );

    register_taxonomy('resource_cat', 'resource', array(
        'labels'            => $labels,
        'rewrite'           => array('slug' => 'resource-category'),
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
    ));
}
add_action('init', 'dnr_register_taxonomies');

/**
 * 3. Custom Admin Submenus (Reports Dashboard)
 */
function dnr_resource_admin_menus() {
    add_submenu_page(
        'edit.php?post_type=resource',
        'Sales Reports',
        'Reports',
        'manage_options',
        'resource-reports',
        'dnr_sales_reports_render'
    );
}
add_action('admin_menu', 'dnr_resource_admin_menus');

function dnr_sales_reports_render() {
    // 1. Fetch all orders to calculate stats
    $orders = get_posts(array(
        'post_type'   => 'resource_order',
        'post_status' => 'publish',
        'numberposts' => -1,
    ));

    $total_orders = count($orders);
    $total_revenue = 0;
    $sales_data = array();

    foreach ($orders as $order) {
        $product_id = get_post_meta($order->ID, '_product_id', true);
        $price = get_field('resource_price', $product_id) ?: 0;
        $total_revenue += (float)$price;

        // Group by product for "Top Selling" section
        if ($product_id) {
            $sales_data[$product_id] = isset($sales_data[$product_id]) ? $sales_data[$product_id] + 1 : 1;
        }
    }
    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-chart-bar"></span> Resource Sales Reports</h1>
        <hr class="wp-header-end">

        <div style="display: flex; gap: 20px; margin-top: 20px;">
            <div class="card" style="flex: 1; padding: 20px; border-left: 4px solid #2271b1; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin:0; font-size: 14px; color: #646970;">Total Revenue</h2>
                <p style="font-size: 28px; font-weight: bold; margin: 10px 0 0;">৳<?php echo number_format($total_revenue, 2); ?></p>
            </div>
            <div class="card" style="flex: 1; padding: 20px; border-left: 4px solid #25D366; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin:0; font-size: 14px; color: #646970;">Total Orders</h2>
                <p style="font-size: 28px; font-weight: bold; margin: 10px 0 0;"><?php echo $total_orders; ?></p>
            </div>
            <div class="card" style="flex: 1; padding: 20px; border-left: 4px solid #ffb900; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin:0; font-size: 14px; color: #646970;">Average Order Value</h2>
                <p style="font-size: 28px; font-weight: bold; margin: 10px 0 0;">
                    ৳<?php echo $total_orders > 0 ? number_format($total_revenue / $total_orders, 2) : '0'; ?>
                </p>
            </div>
        </div>

        <div style="margin-top: 30px; background: #fff; padding: 20px; border: 1px solid #ccd0d4;">
            <h2>Top Selling Resources</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Sales Count</th>
                        <th>Estimated Earnings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    arsort($sales_data); // Sort by highest sales
                    if (!empty($sales_data)) :
                        foreach ($sales_data as $pid => $count) : 
                            $p_price = get_field('resource_price', $pid) ?: 0;
                            ?>
                            <tr>
                                <td><strong><?php echo get_the_title($pid); ?></strong></td>
                                backyard<td><?php echo $count; ?></td>
                                <td>৳<?php echo number_format($p_price * $count, 2); ?></td>
                            </tr>
                        <?php endforeach; 
                    else : ?>
                        <tr><td colspan="3">No sales data found yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

/**
 * 4. AJAX Handler: Order Submission Logic
 */
add_action('wp_ajax_submit_resource_order', 'dnr_handle_order_submission');
add_action('wp_ajax_nopriv_submit_resource_order', 'dnr_handle_order_submission');

function dnr_handle_order_submission() {
    $prod_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $name    = sanitize_text_field($_POST['client_name']);
    $email   = sanitize_email($_POST['client_email']);
    $phone   = sanitize_text_field($_POST['client_phone']);
    $b_num   = sanitize_text_field($_POST['bkash_number']);
    $trx_id  = sanitize_text_field($_POST['trx_id']);

    if (!$prod_id || empty($trx_id)) {
        wp_send_json_error('Missing required information.');
    }

    $order_id = wp_insert_post(array(
        'post_title'  => 'Order: ' . get_the_title($prod_id) . ' - ' . $name,
        'post_type'   => 'resource_order',
        'post_status' => 'publish',
    ));

    if (!is_wp_error($order_id)) {
        update_post_meta($order_id, '_customer_email', $email);
        update_post_meta($order_id, '_customer_phone', $phone);
        update_post_meta($order_id, '_bkash_sent_from', $b_num);
        update_post_meta($order_id, '_transaction_id', $trx_id);
        update_post_meta($order_id, '_product_id', $prod_id);
        wp_send_json_success();
    } else {
        wp_send_json_error('Database error.');
    }
    wp_die();
}

/**
 * 5. Professional Orders List Table
 */
function dnr_order_columns($columns) {
    return array(
        'cb'            => '<input type="checkbox" />',
        'title'         => 'Order Name',
        'customer_info' => 'Customer Details',
        'payment_info'  => 'bKash Info',
        'source_url'    => 'Source Product',
        'whatsapp_btn'  => 'Action',
        'date'          => 'Date',
    );
}
add_filter('manage_resource_order_posts_columns', 'dnr_order_columns');

function dnr_order_column_data($column, $post_id) {
    $email   = get_post_meta($post_id, '_customer_email', true);
    $phone   = get_post_meta($post_id, '_customer_phone', true);
    $bkash   = get_post_meta($post_id, '_bkash_sent_from', true);
    $trx     = get_post_meta($post_id, '_transaction_id', true);
    $prod_id = get_post_meta($post_id, '_product_id', true);

    switch ($column) {
        case 'customer_info':
            echo '<strong>' . esc_html($phone) . '</strong><br>' . esc_html($email);
            break;
        case 'payment_info':
            echo 'From: ' . esc_html($bkash) . '<br>TrxID: <code>' . esc_html($trx) . '</code>';
            break;
        case 'source_url':
            echo $prod_id ? '<a href="'.get_permalink($prod_id).'" target="_blank">View Item ↗</a>' : 'N/A';
            break;
        case 'whatsapp_btn':
            $clean_phone = preg_replace('/[^0-9]/', '', $phone);
            if (substr($clean_phone, 0, 2) !== '88') $clean_phone = '88' . $clean_phone;
            $msg = rawurlencode("Hello! Thanks for your purchase of " . get_the_title($prod_id) . ". Link: ");
            echo '<a href="https://wa.me/'.$clean_phone.'?text='.$msg.'" target="_blank" class="button" style="background:#25D366;color:#fff;border:none;box-shadow:none;">WhatsApp Link</a>';
            break;
    }
}
add_action('manage_resource_order_posts_custom_column', 'dnr_order_column_data', 10, 2);








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

