<?php
/**
 * devnahian functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package devnahian
 */

if ( ! defined( '_S_VERSION' ) ) {
    // Replace the version number of the theme on each release.
    define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function devnahian_setup() {
    // Make theme available for translation.
    load_theme_textdomain( 'devnahian', get_template_directory() . '/languages' );

    // Let WordPress manage the document title.
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support( 'post-thumbnails', array( 'post', 'tutorial', 'themes' ) );

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus(
        array(
            'menu-1' => esc_html__( 'Primary', 'devnahian' ),
            'footer-1' => esc_html__( 'Footer 1', 'devnahian' ),
            'footer-2' => esc_html__( 'Footer 2', 'devnahian' ),
        )
    );

    // Switch default core markup for search form, comment form, and comments to output valid HTML5.
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Set up the WordPress core custom background feature.
    add_theme_support(
        'custom-background',
        apply_filters(
            'devnahian_custom_background_args',
            array(
                'default-color' => 'ffffff',
                'default-image' => '',
            )
        )
    );

    // Add theme support for selective refresh for widgets.
    add_theme_support( 'customize-selective-refresh-widgets' );

    // Add support for core custom logo.
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        )
    );
}
add_action( 'after_setup_theme', 'devnahian_setup' );

require get_template_directory() . '/inc/basic-seo.php';

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function devnahian_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'devnahian_content_width', 640 );
}
add_action( 'after_setup_theme', 'devnahian_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function devnahian_widgets_init() {
    register_sidebar(
        array(
            'name'          => esc_html__( 'Sidebar', 'devnahian' ),
            'id'            => 'sidebar-1',
            'description'   => esc_html__( 'Add widgets here.', 'devnahian' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}
add_action( 'widgets_init', 'devnahian_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function devnahian_scripts() {
    // Google Font
    wp_enqueue_style( 'google-font', 'https://fonts.googleapis.com/css?family=Muli:300,400,500,600,700,800,900&display=swap', array(), null );

    // Font Awesome CSS 
    wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', array(), _S_VERSION, 'all' );

    // Elegant Font Icons
    wp_enqueue_style( 'elegant-font-icons', get_template_directory_uri() . '/assets/css/elegant-font-icons.css', array(), _S_VERSION, 'all' );

    // Bootstrap CSS
    wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), _S_VERSION, 'all' );

    // Slicknav CSS
    wp_enqueue_style( 'slicknav', get_template_directory_uri() . '/assets/css/slicknav.min.css', array(), _S_VERSION, 'all' );

    // Style CSS
    wp_enqueue_style( 'style', get_template_directory_uri() . '/assets/css/style.css', array(), _S_VERSION, 'all' );

    // Responsive CSS
    wp_enqueue_style( 'responsive-theme', get_template_directory_uri() . '/assets/css/responsive.css', array(), _S_VERSION, 'all' );

    // Main stylesheet
    wp_enqueue_style( 'devnahian-style', get_stylesheet_uri(), array(), _S_VERSION );
    wp_style_add_data( 'devnahian-style', 'rtl', 'replace' );

    // Popper JS
    wp_enqueue_script( 'popper', get_template_directory_uri() . '/assets/js/popper.min.js', array('jquery'), _S_VERSION, true );

    // Bootstrap JS
    wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), _S_VERSION, true );

    // Slicknav JS
    wp_enqueue_script( 'slicknav', get_template_directory_uri() . '/assets/js/slicknav.min.js', array('jquery'), _S_VERSION, true );

    // Main JS
    wp_enqueue_script( 'main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), _S_VERSION, true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'devnahian_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
    require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load Custom Post
 */
require get_template_directory() . '/inc/custom-post.php';

/**
 * Register custom ACF blocks
 */
add_action('acf/init', 'my_custom_acf_blocks');
function my_custom_acf_blocks() {
    if( function_exists('acf_register_block_type') ) {
        acf_register_block_type(array(
            'name'              => 'collection',
            'title'             => __('Collection'),
            'description'       => __('Image background with text & call to action.'),
            'render_callback'   => 'collection_render_callback',
            'category'          => 'formatting',
            'icon'              => 'format-image',
            'mode'              => 'preview',
            'keywords'          => array( 'collection', 'image' ),
        ));

        acf_register_block_type(array(
            'name'              => 'theme-collections',
            'title'             => __('Theme Collections'),
            'description'       => __('Full width hero banner with title & button.'),
            'render_callback'   => 'theme_collections_render_callback',
            'category'          => 'layout',
            'icon'              => 'cover-image',
            'mode'              => 'preview',
            'keywords'          => array( 'theme', 'collection' ),
        ));

        acf_register_block_type(array(
            'name'              => 'code-collections',
            'title'             => __('Code Collections'),
            'description'       => __('Full width hero banner with title & button.'),
            'render_callback'   => 'code_collections_render_callback',
            'category'          => 'layout',
            'icon'              => 'cover-image',
            'mode'              => 'preview',
            'keywords'          => array( 'code', 'collection' ),
        ));
    }
}

function collection_render_callback($block) {
    include get_theme_file_path('/template-parts/blocks/collection.php');
}
function theme_collections_render_callback($block) {
    include get_theme_file_path('/template-parts/blocks/theme-collection.php');
}
function code_collections_render_callback($block) {
    include get_theme_file_path('/template-parts/blocks/code-collection.php');
}

/**
 * Change ACF JSON save point.
 */
function my_acf_json_save_point( $path ) {
    return get_stylesheet_directory() . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'my_acf_json_save_point' );

/**
 * Track unique post views (cookie-based)
 */
function track_unique_post_views() {
    if (is_single()) {
        global $post;
        $post_id = $post->ID;
        $cookie_name = 'viewed_post_' . $post_id;

        if (!isset($_COOKIE[$cookie_name])) {
            $count_key = 'post_views_count';
            $count = get_post_meta($post_id, $count_key, true);

            if ($count == '') {
                $count = 1;
                update_post_meta($post_id, $count_key, $count);
            } else {
                $count++;
                update_post_meta($post_id, $count_key, $count);
            }

            setcookie($cookie_name, 'true', time() + 3600, '/');
        }
    }
}
add_action('wp_head', 'track_unique_post_views');

/**
 * Track total post views
 */
if (!function_exists('get_post_views')) {
    function get_post_views($post_id) {
        $count_key = 'post_views_count';
        $count = get_post_meta($post_id, $count_key, true);
        return $count ? $count : '0';
    }
}
function track_post_views($post_id) {
    if (!is_single()) return;

    $views = get_post_meta($post_id, 'post_views_count', true);

    if ($views == '') {
        $views = 0;
        delete_post_meta($post_id, 'post_views_count');
        add_post_meta($post_id, 'post_views_count', '0');
    } else {
        $views++;
        update_post_meta($post_id, 'post_views_count', $views);
    }
}
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
add_action('wp_head', 'track_post_views');

// Add a custom column to the admin posts table
function add_post_views_column($columns) {
    $columns['post_views'] = 'Views';
    return $columns;
}
add_filter('manage_posts_columns', 'add_post_views_column');

// Populate the custom column with data
function show_post_views_column($column_name, $post_id) {
    if ($column_name === 'post_views') {
        $views = get_post_meta($post_id, 'post_views_count', true);
        echo $views ? esc_html($views) : '0';
    }
}
add_action('manage_posts_custom_column', 'show_post_views_column', 10, 2);

// Make the custom column sortable
function make_post_views_column_sortable($columns) {
    $columns['post_views'] = 'post_views_count';
    return $columns;
}
add_filter('manage_edit-post_sortable_columns', 'make_post_views_column_sortable');

// Handle the sorting for the custom column
function post_views_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) return;
    if ('post_views_count' === $query->get('orderby')) {
        $query->set('meta_key', 'post_views_count');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'post_views_column_orderby');

// ==========================================================
// 🧠 Post Unique Daily Views Tracker (Ordered by Most Views)
// ==========================================================

// ✅ Track unique post views per day (by IP)
function nahian_track_unique_post_views() {
    if ( is_single() ) {
        global $post;
        if ( empty( $post->ID ) ) return;

        $post_id   = $post->ID;
        $today     = date( 'Y-m-d' );
        $views_key = '_unique_views_' . $today;
        $user_ip   = $_SERVER['REMOTE_ADDR'];

        $viewers = get_post_meta( $post_id, $views_key, true );
        if ( ! is_array( $viewers ) ) $viewers = [];

        if ( ! in_array( $user_ip, $viewers ) ) {
            $viewers[] = $user_ip;
            update_post_meta( $post_id, $views_key, $viewers );
        }
    }
}
add_action( 'wp_head', 'nahian_track_unique_post_views' );

// ✅ Add submenu under Posts
function nahian_add_views_report_submenu() {
    add_submenu_page(
        'edit.php',
        'Today\'s Views Report',
        'Today\'s Views',
        'manage_options',
        'today-views-report',
        'nahian_today_views_report_page'
    );
}
add_action( 'admin_menu', 'nahian_add_views_report_submenu' );

// ✅ Display admin table like “All Posts”
function nahian_today_views_report_page() {
    $today = date( 'Y-m-d' );

    // Pagination setup
    $paged       = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
    $posts_per_page = 20;

    // Fetch posts
    $posts = get_posts([
        'post_type'   => 'post',
        'post_status' => 'publish',
        'numberposts' => -1,
    ]);

    $posts_with_views = [];

    // Collect posts with today's views
    foreach ( $posts as $post ) {
        $viewers = get_post_meta( $post->ID, '_unique_views_' . $today, true );
        $views   = is_array( $viewers ) ? count( $viewers ) : 0;

        if ( $views > 0 ) {
            // Optional: all-time views (sum of all days)
            $meta_keys = get_post_meta( $post->ID );
            $total = 0;
            foreach ( $meta_keys as $key => $value ) {
                if ( strpos( $key, '_unique_views_' ) === 0 && is_array( maybe_unserialize( $value[0] ) ) ) {
                    $total += count( maybe_unserialize( $value[0] ) );
                }
            }

            $posts_with_views[] = [
                'post'  => $post,
                'views' => $views,
                'total' => $total,
            ];
        }
    }

    // Sort by today’s views descending
    usort( $posts_with_views, fn($a, $b) => $b['views'] - $a['views'] );

    // Paginate results
    $total_posts = count( $posts_with_views );
    $offset = ( $paged - 1 ) * $posts_per_page;
    $paged_posts = array_slice( $posts_with_views, $offset, $posts_per_page );

    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">Today\'s Unique Post Views</h1>';
    echo '<p><strong>Date:</strong> ' . esc_html( $today ) . '</p>';
    echo '<hr class="wp-header-end">';

    if ( ! empty( $paged_posts ) ) {
        echo '<table class="wp-list-table widefat fixed striped posts">';
        echo '<thead>
                <tr>
                    <th scope="col" class="manage-column column-title column-primary"><span>Post Title</span></th>
                    <th scope="col" class="manage-column"><span>Unique Views Today</span></th>
                    <th scope="col" class="manage-column"><span>Total Views (All Time)</span></th>
                    <th scope="col" class="manage-column"><span>Date</span></th>
                </tr>
              </thead>';

        echo '<tbody id="the-list">';

        foreach ( $paged_posts as $item ) {
            $post  = $item['post'];
            $views = $item['views'];
            $total = $item['total'];

            echo '<tr>';
            echo '<td class="title column-title has-row-actions column-primary">
                    <strong><a href="' . esc_url( get_permalink( $post->ID ) ) . '" target="_blank">' . esc_html( get_the_title( $post ) ) . '</a></strong>
                    <div class="row-actions">
                        <span class="view"><a href="' . esc_url( get_permalink( $post->ID ) ) . '" target="_blank">View</a></span>
                    </div>
                  </td>';
            echo '<td>' . esc_html( $views ) . '</td>';
            echo '<td>' . esc_html( $total ) . '</td>';
            echo '<td>' . esc_html( $today ) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        // Pagination links
        $total_pages = ceil( $total_posts / $posts_per_page );
        if ( $total_pages > 1 ) {
            echo '<div class="tablenav bottom">';
            echo '<div class="tablenav-pages">';
            echo paginate_links([
                'base'      => add_query_arg( 'paged', '%#%' ),
                'format'    => '',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
                'total'     => $total_pages,
                'current'   => $paged,
            ]);
            echo '</div></div>';
        }

    } else {
        echo '<p>No posts have unique views today.</p>';
    }

    echo '</div>';
}


// ==========================================================
// 🔹 Other Custom Functions
// ==========================================================

// Restrict username registration for "blogspot"
function restrict_username_registration($user_login) {
    $disallowed_pattern = '/blogspot/i';
    if (preg_match($disallowed_pattern, $user_login)) {
        wp_die(
            'Registration failed: Usernames containing "blogspot" are not allowed.', 
            'Username Restriction Error', 
            array('back_link' => true)
        );
    }
    return $user_login;
}
add_filter('pre_user_login', 'restrict_username_registration');

// Allow WebP uploads
function allow_webp_uploads($mime_types) {
    $mime_types['webp'] = 'image/webp';
    return $mime_types;
}
add_filter('upload_mimes', 'allow_webp_uploads');

// Get reading time
function get_post_reading_time( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $content    = get_post_field( 'post_content', $post_id );
    $word_count = str_word_count( wp_strip_all_tags( $content ) );
    $reading_time = max( 1, ceil( $word_count / 200 ) ); // at least 1 min
    return $reading_time;
}

// Remove multiple fields and sections from the WooCommerce checkout
add_filter( 'woocommerce_checkout_fields', 'custom_remove_checkout_fields' );
function custom_remove_checkout_fields( $fields ) {
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_district']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['order']['order_comments']);
    return $fields;
}

// Handle course retake action
add_action('init', 'handle_course_retake_action');
function handle_course_retake_action() {
    if (isset($_GET['action']) && $_GET['action'] === 'retake_course' && is_user_logged_in()) {
        $course_id = get_the_ID();
        $user_id = get_current_user_id();
        tutor_utils()->delete_course_progress($user_id, $course_id);
        wp_redirect(get_permalink($course_id));
        exit;
    }
}
