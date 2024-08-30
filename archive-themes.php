<?php

/*
Template Name: Template Theme 
*/

get_header();
?>

<main id="primary" class="site-main">

    <section class="blog-grid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcumb">
                        <h4>Themes</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mt-30">
                    <div class="row">
                        <?php
                        $args = array(
                            'post_type'      => 'themes',
                            'posts_per_page' => 20,
                        );

                        $query = new WP_Query($args);

                        if ($query->have_posts()) :
                            while ($query->have_posts()) :
                                $query->the_post();
                                ?>
                                <div class="col-lg-6 col-md-6">
                                    <div class="post-card">
                                        <div class="post-card-image">
                                            <?php
                                            $demo_image = get_field('demo_image');
                                            print_r($demo_image);
                                            if ($demo_image) {
                                                echo '<img src="' . esc_url($demo_image['url']) . '" alt="' . esc_attr($demo_image['alt']) . '">';
                                            } else {
                                                echo '<img src="placeholder-image.jpg" alt="Placeholder Image">';
                                            }
                                            ?>
                                        </div>
                                        <div class="post-card-content">
                                            <?php
                                            $tutorial_categories = get_the_terms(get_the_ID(), 'tutorial_category');
                                            if ($tutorial_categories && !is_wp_error($tutorial_categories)) {
                                                $category_links = array();
                                                foreach ($tutorial_categories as $category) {
                                                    $category_links[] = '<a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a>';
                                                }
                                                echo '<span class="tutorial-categories">' . implode(', ', $category_links) . '</span>';
                                            }
                                            ?>
                                            <h5>
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h5>
                                        </div>
                                    </div>
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
                            the_posts_pagination(array(
                                'prev_text' => __('Previous', 'devnahian'),
                                'next_text' => __('Next', 'devnahian'),
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

<?php get_footer();
