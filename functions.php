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

    // Style CSS
    wp_enqueue_style( 'style', get_template_directory_uri() . '/assets/css/style.css', array(), _S_VERSION, 'all' );

    // Main stylesheet
    wp_enqueue_style( 'devnahian-style', get_stylesheet_uri(), array(), _S_VERSION );
    wp_style_add_data( 'devnahian-style', 'rtl', 'replace' );

    // Popper JS
    wp_enqueue_script( 'popper', get_template_directory_uri() . '/assets/js/popper.min.js', array('jquery'), _S_VERSION, true );

    // Bootstrap JS
    wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), _S_VERSION, true );

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
require get_template_directory() . '/inc/widgets/popular-posts.php';
require get_template_directory() . '/inc/widgets/categories-list.php';
require get_template_directory() . '/inc/widgets/search-box.php';
require get_template_directory() . '/inc/widgets/about-me.php';

// Add a class to the body tag if the admin bar is visible
function add_admin_bar_body_class( $classes ) {
    if ( is_user_logged_in() && is_admin_bar_showing() ) {
        $classes[] = 'admin-bar';
    }
    return $classes;
}
add_filter( 'body_class', 'add_admin_bar_body_class' );



/**
 * Register hero block
 */
add_action('acf/init', 'hero');
function hero() {
    // check function exists
    if( function_exists('acf_register_block') ) {
        // register a collection block
        acf_register_block(array(
            'name'              => 'collection',
            'title'             => __('Collection'),
            'description'       => __('Image background with text & call to action.'),
            'render_callback'   => 'collection_render_callback',
            'category'          => 'formatting',
            'icon'              => 'format-image',
            'mode'              => 'preview',
            'keywords'          => array( 'collection', 'image' ),
        ));
    }
}

/**
 * This is the callback that displays the hero block
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block content (empty string).
 * @param bool $is_preview True during AJAX preview.
 */
function collection_render_callback( $block, $content = '', $is_preview = false ) {
    // create id attribute for specific styling
    $id = 'collection-' . $block['id'];

    // create align class ("alignwide") from block setting ("wide")
    $align_class = $block['align'] ? 'align' . $block['align'] : '';

    // ACF field variables
    $item_image = get_field('item_image');
    $item_author_name = get_field('item_author_name');
    $item_link = get_field('item_link');
    $item_made_with = get_field('item_made_with');
    $item_title = get_field('item_title');
    $item_description = get_field('item_description');
    $item_compatible_browsers = get_field('item_compatible_browsers');
    $item_responsive = get_field('item_responsive');
    $item_dependencies = get_field('item_dependencies');
    ?>

    <div class="item">
        <div class="item-file">
            <img src="<?php echo esc_url($item_image['url']); ?>" alt="">
        </div>
        <div class="item-meta">
            <div class="single-item-meta">
                <span>Author</span>
                <p><?php echo esc_html($item_author_name); ?></p>
            </div>
            <div class="single-item-meta">
                <span>Links</span>
                <p><a href="<?php echo esc_url($item_link); ?>">Preview & Download</a></p>
            </div>
            <div class="single-item-meta">
                <span>Made With</span>
                <p><?php echo esc_html($item_made_with); ?></p>
            </div>
        </div>

        <div class="item-content">
            <span>About Code</span>
            <h4><?php echo esc_html($item_title); ?></h4>
            <p><?php echo esc_html($item_description); ?></p>
        </div>

        <div class="item-bottom">
            <table>
                <tr>
                    <td>Compatible browsers:</td>
                    <td><?php echo esc_html(implode(', ', $item_compatible_browsers)); ?></td>
                </tr>
                <tr>
                    <td>Responsive:</td>
                    <td><?php echo esc_html($item_responsive); ?></td>
                </tr>
                <tr>
                    <td>Dependencies:</td>
                    <td><?php echo esc_html($item_dependencies); ?></td>
                </tr>
            </table>
        </div>
    </div>
    
    <?php
}

/**
 * Change ACF JSON save point.
 */
function my_acf_json_save_point( $path ) {
    return get_stylesheet_directory() . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'my_acf_json_save_point' );


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
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0); // Remove the prefetching for adjacent posts
add_action('wp_head', 'track_post_views');


if ( ! function_exists( 'etlms_course_categories' ) ) {
	function etlms_course_categories() {
		$course_categories      = array();
		$course_categories_term = tutils()->get_course_categories_term();
		foreach ( $course_categories_term as $term ) {
			$course_categories[ $term->term_id ] = $term->name;
		}

		return $course_categories;
	}
}


if ( ! function_exists( 'etlms_get_course' ) ) {
    function etlms_get_course() {
        global $post;
        $course_id        = $post->ID;
        $course_post_type = tutor()->course_post_type;

        // Check if the current post is a single course post
        if ( is_single() && $post->post_type == $course_post_type ) {
            return true;
        }

        // Check if the post is a Tutor LMS Elementor template
        if ( $post->post_type == 'elementor_library' ) {
            $is_tutor_template = get_post_meta( $post->ID, '_tutor_lms_elementor_template_id', true );
            if ( $is_tutor_template ) {
                return setup_course_data(); // Ensure this function is defined and available
            }
        }

        return false;
    }
}
?>
