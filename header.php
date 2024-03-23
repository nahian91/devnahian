<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package anahian
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'anahian' ); ?></a>

	<!--loading -->
    <div class="loading">
        <div class="circle"></div>
    </div>
    <!--/-->

    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <!--logo-->
            <div class="logo">
                <a href="<?php echo site_url();?>">Abdullah Nahian</a>
            </div>
            <!--/-->
    
            <!--navbar-collapse-->
            <div class="collapse navbar-collapse" id="main_nav">
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
            <!--/-->
        </div>
    </nav>
    <!--/-->
