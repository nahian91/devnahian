<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package devnahian
 */

get_header();
?>

	<main id="primary" class="site-main">
		<section class="blog-grid">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-8">
						<div class="page-content">
							<?php
								while ( have_posts() ) :
									the_post();

									get_template_part( 'template-parts/content', 'page' );

								endwhile; // End of the loop.
							?>
						</div>
					</div>
					<div class="col-lg-4">
						<?php get_sidebar(); ?>
					</div>
				</div>
			</div>
		</section>
	</main><!-- #main -->

<?php

get_footer();
