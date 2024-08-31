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

<section class="breadcumb-area" style="background-image:url('<?php echo get_template_directory_uri();?>/assets/img/breadcumb.jpg')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				
			<div class="post-single-content">
						<h4><?php the_title();?></h4>
					</div> 
			</div>
		</div>
	</div>
</section>

<?php
	while ( have_posts() ) :
		the_post();

		?>
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="page-content">
							<?php the_content(); ?>
						</div>
					</div>
				</div>
			</div>
		<?php 

	endwhile; // End of the loop.
?>

<?php

get_footer();
