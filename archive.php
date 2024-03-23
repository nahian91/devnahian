<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package anahian
 */

get_header();
?>

	<main id="primary" class="site-main">
		<!--blog-grid-->
		<section class="blog-grid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
					<div class="row">
					<div class="col-lg-12">
					<div class="categorie-title"> 
                         <small>
                            <a href="index.html">Home</a>
                            <span class="arrow_carrot-right"></span> <?php echo get_the_archive_title(); ?>
                        </small>
                        <h3><?php echo get_the_archive_title(); ?></h3>
                    </div>
					</div>
					</div>
                    <div class="row">
					<?php
						if ( have_posts() ) :

							/* Start the Loop */
							while ( have_posts() ) :
								the_post();
								?>
									<div class="col-lg-4 col-md-6">
										<!--Post-1-->
										<div class="post-card">
											<div class="post-card-image">
												<?php the_post_thumbnail();?>
											</div>
											<div class="post-card-content">
												<?php the_category(', '); ?>
												<h5>
													<a href="<?php the_permalink();?>"><?php the_title();?></a>
												</h5>
												<?php the_excerpt();?>
												<div class="post-card-info">
													<ul class="list-inline">
														<li><?php echo get_avatar( 'nahiansylhet@gmail.com', 64 ); ?>
														</li>
														<li>
															<?php  $author_id = get_the_author_meta( 'ID' );  ?>
															<a href="author.html"><?php echo get_the_author_meta( 'nicename', $author_id );?></a>
														</li>
														<li class="dot"></li>
														<li><?php echo get_the_date(); ?></li>
													</ul>
												</div>
											</div>
										</div>
										<!--/-->
									</div>
								<?php

							endwhile;

						else :

							get_template_part( 'template-parts/content', 'none' );

						endif;
						?>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </section><!--/-->

	</main><!-- #main -->

<?php
get_footer();
