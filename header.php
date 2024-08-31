<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package devnahian
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
    <meta name="google-adsense-account" content="ca-pub-5810116187271532">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'devnahian' ); ?></a>

	<!-- Loading -->
    <div class="loading">
        <div class="circle"></div>
    </div>
    <!-- Loading -->

    <section class="header-top-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <ul class="header-info d-flex">
                        <li class="d-flex align-items-center"><a href="mailt: nahiansylhet@gmail.com"><i class="fa-regular fa-envelope"></i> nahiansylhet@gmail.com</a></li>
                        <li class="d-flex align-items-center"><a href="tel: 01686195607"><i class="fa-brands fa-whatsapp"></i> 01686195607</a></li>
                    </ul>
                </div>
                <div class="col-md-6">                    
                    <ul class="header-social d-flex justify-content-end">
                        <li><a href=""><i class="fa-brands fa-facebook-f"></i></a></li>
                        <li><a href=""><i class="fa-brands fa-linkedin-in"></i></a></li>
                        <li><a href=""><i class="fa-brands fa-youtube"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Navigation -->
    <nav class="header-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <!-- Logo -->
                    <div class="logo">
                        <a href="<?php echo site_url();?>">Abdullah Nahian</a>
                    </div>
                    <!-- Logo -->
                </div>
                <div class="col-md-6 text-center">
                <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'menu-1',
                        )
                    );
                    ?>
                </div>
                <div class="col-md-4">

                <div class="header-profile">
                <?php if ( is_user_logged_in() ) : ?>
    <?php
    // Get the current user info
    $current_user = wp_get_current_user();
    $user_name = $current_user->display_name;

    // Get the user's avatar
    $avatar = get_avatar( $current_user->ID, 64 );

    // Get Tutor LMS dashboard URL
    $dashboard_url = tutor_utils()->tutor_dashboard_url();

    // Debug: Display the generated dashboard URL
    echo '<!-- Dashboard URL: ' . esc_url( $dashboard_url ) . ' -->';
    ?>
    <p><a href="<?php echo esc_url( $dashboard_url ); ?>"><?php echo $avatar; ?> Hi, <?php echo esc_html( $user_name ); ?></a></p>
<?php else : ?>
    <?php
    // Get Tutor LMS login URL
    $login_url = tutor_utils()->login_url();
    print_r($login_url);

    // Debug: Display the generated login URL
    echo '<!-- Login URL: ' . esc_url( $login_url ) . ' -->';
    ?>
    <p><a class="btn-custom" href="http://devnahian.local/dashboard/">Dashboard</a></p>
<?php endif; ?>


<?php if ( function_exists('WC') && WC()->cart->get_cart_contents_count() > 0 ) : ?>
    <div class="mini-cart">
        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-icon">
            <i class="fa fa-shopping-cart"></i> <!-- Replace with your preferred cart icon -->
            <span class="cart-count">
                <?php echo WC()->cart->get_cart_contents_count(); ?>
            </span>
        </a>
    </div>
<?php endif; ?>



                </div>




                </div>
            </div>
        </div>
    </nav>
    <!-- Navigation -->
