<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package devnahian
 */

get_header();
?>

<main id="primary" class="site-main">

    <!--post-default-->
    <section class="section pt-55 ">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 mb-20">
                    <!--Post-single-->
                    <div class="post-single">
                        <div class="post-single-image">
                            <?php the_post_thumbnail();?>
                        </div>
                        <div class="post-single-content">
                            <?php the_category(', '); ?> 
                            <h4><?php the_title();?></h4>
                            <div class="post-single-info">
                                <ul class="list-inline">
                                    <li class="dot"></li>
                                    <li>Published: <?php echo get_the_date(); ?></li>
                                    <li class="dot"></li>
                                    <li>Updated: <?php echo get_the_modified_date(); ?></li>
                                </ul>
                            </div>
                        </div>                  
                        <div class="post-single-body">
                            <?php the_content(); ?>                           
                        </div>
                    </div> <!--/-->
                </div>
                <div class="col-lg-4">
                    <?php get_sidebar();?>
                </div>
            </div>
        </div>
    </section><!--/-->

</main><!-- #main -->

<?php
get_footer();
?>
