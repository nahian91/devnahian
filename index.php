<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
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
                <div class="col-lg-8 mt-30">
                    <div class="row">
                        <?php
                        $category_slug = 'free-wordpress-themes';
                        $category = get_category_by_slug($category_slug);
                        $category_id = $category->term_id;

                        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                        $args = array(
                            'post_type' => 'post',
                            'posts_per_page' => get_option('posts_per_page'),
                            'category__not_in' => array($category_id),
                            'paged' => $paged,
                        );

                        $query = new WP_Query($args);

                        if ($query->have_posts()) :

                            /* Start the Loop */
                            while ($query->have_posts()) :
                                $query->the_post();
                                ?>
                                <div class="col-lg-6 col-md-6">
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

                        <div class="pagination-main">
                            <?php
                            // Pagination links
                            the_posts_pagination(array(
                                'prev_text' => __('Previous', 'anahian'),
                                'next_text' => __('Next', 'anahian'),
                            ));
                            ?>
                        </div>

                    </div>
                </div>
                <div class="col-lg-4 max-width">
                    <?php get_sidebar(); ?>
                </div>
            </div>
        </div>
    </section><!--/-->

</main><!-- #main -->

<?php
get_footer();
?>
