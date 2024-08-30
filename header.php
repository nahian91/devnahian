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
            <div class="row">
                <div class="col-md-6">
                    <ul>
                        <li><a href="mailt: nahiansylhet@gmail.com">nahiansylhet@gmail.com</a></li>
                        <li><a href="tel: 01686195607">01686195607</a></li>
                    </ul>
                </div>
                <div class="col-md-6">                    
                    <ul>
                        <li><a href=""></a></li>
                        <li><a href=""></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Navigation -->
    <nav class="header-area">
        <div class="container-fluid">
            <div class="row">
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
                            'menu_class'     => 'navbar-nav ml-auto',
                        )
                    );
                    ?>
                </div>
                <div class="col-md-3">

                <?php if ( is_user_logged_in() ) : ?>
    <?php
    // Get the current user info
    $current_user = wp_get_current_user();
    $user_name = $current_user->display_name; // Or use $current_user->user_login for the username
    ?>
    <p>Hello, <?php echo esc_html( $user_name ); ?>!</p>
    <!-- Optionally, add a logout link or profile link here -->
    <a href="<?php echo esc_url( wp_logout_url() ); ?>">Log Out</a>
<?php else : ?>
    <p><a href="<?php echo esc_url( wp_login_url() ); ?>">Sign In</a></p>
<?php endif; ?>

                </div>
            </div>
        </div>
    </nav>
    <!-- Navigation -->
