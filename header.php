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
                <div class="col-md-3">
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
                            'container' => 'ul',
                            'add_li_class'  => 'nav-item',
                            'menu_class'     => 'navbar-nav',
                        )
                    );
                    ?>
                </div>
                <div class="col-md-3">

                <?php if ( is_user_logged_in() ) : ?>
    <?php
    // Get the current user info
    $current_user = wp_get_current_user();
    $user_name = $current_user->display_name;

    // Tutor LMS specific URLs
    $dashboard_url = tutor_utils()->tutor_dashboard_url();
    $profile_url = tutor_utils()->profile_url($current_user->ID);
    $change_password_url = tutor_utils()->get_tutor_profile_url('settings');
    $logout_url = wp_logout_url( home_url() ); // Redirect to homepage after logout
    ?>
    <p>Hello, <?php echo esc_html( $user_name ); ?></p>
    <ul>
        <li><a href="<?php echo esc_url( $dashboard_url ); ?>">Dashboard</a></li>
        <li><a href="<?php echo esc_url( $profile_url ); ?>">Profile</a></li>
        <li><a href="<?php echo esc_url( $change_password_url ); ?>">Change Password</a></li>
        <li><a href="<?php echo esc_url( $logout_url ); ?>">Log Out</a></li>
    </ul>
<?php else : ?>
    <?php
    // Use the Tutor LMS login URL
    $login_url = tutor_utils()->get_tutor_login_url(); // Use get_tutor_login_url to ensure the correct function
    ?>
    <p><a href="<?php echo esc_url( $login_url ); ?>">Sign In</a></p>
<?php endif; ?>


                </div>
            </div>
        </div>
    </nav>
    <!-- Navigation -->
