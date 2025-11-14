<?php
/**
 * Template Name: Home
 */

get_header();

?>

<section class="courses-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title">
                    <span>Featured Courses</span>
                    <h4>Gain Knowledge That Matters</h4>
                    <p>Our courses combine practical learning with hands-on experience. Whether you are starting out or advancing your career, our programs provide the knowledge and tools needed to achieve measurable results.</p>
                </div>
            </div>
        </div>
        <div class="row">
            
        </div>
    </div>
</section>

<!--blog-grid-->
    <section class="blog-grid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title">
                        <span>Latest Insights</span>
                        <h4>Explore Our Recent Blog Posts</h4>
                        <p>Stay updated with fresh insights, practical tips, and in-depth guides crafted to help you grow your skills and stay ahead in the digital world. Our latest articles cover trends, tutorials, and strategies you can apply right away.</p>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <?php

                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                $args = array(
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'paged' => $paged,
                );

                $query = new WP_Query($args);

                if ($query->have_posts()) :

                    /* Start the Loop */
                    while ($query->have_posts()) :
                        $query->the_post();
                        ?>
                        <div class="col-lg-4 col-md-4">
                            <!--Post-1-->
                            <div class="post-card">
                                <div class="post-card-image">
                                    <?php the_post_thumbnail(); ?>
                                </div>
                                <div class="post-card-content">
                                    <?php the_category(', '); ?>
                                    <h5>
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h5>
                                    <?php the_excerpt(); ?>
                                    <div class="post-card-info">
                                        <ul class="list-inline">
                                            <li><?php echo get_avatar(get_the_author_meta('ID'), 64); ?>
                                            </li>
                                            <li>
                                                <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a>
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

                    get_template_part('template-parts/content', 'none');

                endif;

                wp_reset_postdata();
                ?>

            </div>
        </div>
    </section><!--/-->

<?php
get_footer();
