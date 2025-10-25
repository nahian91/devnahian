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
                <div class="col-lg-4">
                    <div class="widget">
    <div class="section-title">
        <h5>Latest Courses</h5>
    </div>
    <ul class="widget-post-box">
        <?php
        $args = array(
            'post_type'      => 'courses', // Tutor LMS course post type
            'post__in'       => array(2273, 4263, 4345, 4969, 4853), // Replace with your course IDs
            'orderby'        => 'post__in',
            'posts_per_page' => 5,
            'post_status'    => 'publish',
        );

        $query = new WP_Query($args);
        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post();
                $price = tutor_utils()->get_course_price(get_the_ID());
                ?>
                <li>
                    <div class="widget-post-box-image">
    <a href="<?php the_permalink(); ?>" 
       style="background-image: url('<?php 
            if ( has_post_thumbnail() ) {
                echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) );
            } else {
                echo esc_url( get_template_directory_uri() . '/assets/img/default-course.jpg' );
            }
        ?>');">
    </a>
</div>

                    <div class="widget-post-box-content">
                        <p>
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </p>
                    </div>
                </li>
                <?php
            endwhile;
            wp_reset_postdata();
        else :
            echo '<li>No courses found.</li>';
        endif;
        ?>
    </ul>
</div>

<div class="widget">
    <div class="section-title">
        <h5><?php esc_html_e( 'Categories', 'textdomain' ); ?></h5>
    </div>
    <ul class="widget-categories">
        <?php
        $args = array(
            'taxonomy'   => 'category',
            'orderby'    => 'count',
            'order'      => 'DESC',
            'number'     => 5, // show only 5 categories
            'hide_empty' => true,
        );
        $categories = get_categories( $args );

        if ( ! empty( $categories ) ) {
            foreach ( $categories as $cat ) {
                $cat_link  = get_category_link( $cat->term_id );
                $cat_name  = $cat->name;
                $cat_count = $cat->count;
                ?>
                <li>
                    <a href="<?php echo esc_url( $cat_link ); ?>" class="categorie">
                        <?php echo esc_html( $cat_name ); ?>
                    </a>
                    <span class="ml-auto">
                        <?php echo esc_html( $cat_count ); ?> <?php echo esc_html__( 'Posts', 'textdomain' ); ?>
                    </span>
                </li>
                <?php
            }
        }
        ?>
    </ul>
</div>

<div class="widget">
    <div class="section-title">
        <h5><?php esc_html_e( 'Most Popular Posts', 'textdomain' ); ?></h5>
    </div>
    <ul class="widget-post-box">
        <?php
        $popular_posts = new WP_Query( array(
            'post_type'           => array('post', 'tutorial', 'themes'),
            'posts_per_page'      => 5,
            'meta_key'            => 'post_views_count',
            'orderby'             => 'meta_value_num',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
        ) );

        if ( $popular_posts->have_posts() ) :
            while ( $popular_posts->have_posts() ) : $popular_posts->the_post();
                ?>
                <li>                    
                    <div class="widget-post-box-image">
                        <a href="<?php the_permalink(); ?>"
                           style="background-image: url('<?php 
                                echo has_post_thumbnail() 
                                    ? esc_url( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ) 
                                    : esc_url( get_template_directory_uri() . '/assets/img/default.jpg' );
                           ?>');">
                        </a>
                    </div>

                    <div class="widget-post-box-content">
                        <p>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </p>

                        <small>
                            <span class="fa fa-eye"></span>
                            <?php echo esc_html( number_format( get_post_views( get_the_ID() ) ) ); ?> Views
                        </small>
                    </div>

                </li>
                <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </ul>
</div>

<div class="widget">
    <div class="section-title">
        <h5>Visitor Statistics</h5>
    </div>

    <?php $stats = mysite_get_visitor_stats(); ?>

    <ul class="widget-post-box">
        <li>
            <div class="widget-post-box-content">
                <p>Today's Visitors</p>
            </div>
            <span class="ml-auto"><?php echo $stats['today']; ?></span>
        </li>

        <li>
            <div class="widget-post-box-content">
                <p>Yesterday</p>
            </div>
            <span class="ml-auto"><?php echo $stats['yesterday']; ?></span>
        </li>

        <li>
            <div class="widget-post-box-content">
                <p>Last 7 Days</p>
            </div>
            <span class="ml-auto"><?php echo $stats['last7']; ?></span>
        </li>

        <li>
            <div class="widget-post-box-content">
                <p>Total Visitors</p>
            </div>
            <span class="ml-auto"><?php echo $stats['total']; ?></span>
        </li>
    </ul>
</div>

                </div>
            </div>
        </div>
    </section><!--/-->

</main><!-- #main -->	

<?php get_footer();
