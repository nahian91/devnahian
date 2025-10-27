<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package devnahian
 */
?>

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
        <h5><?php esc_html_e( 'Latest Updates', 'textdomain' ); ?></h5>
    </div>
    <ul class="widget-post-box">
        <?php
        $latest_updates = new WP_Query( array(
            'post_type'           => 'post', // Only blog posts
            'posts_per_page'      => 5,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
        ) );

        if ( $latest_updates->have_posts() ) :
            while ( $latest_updates->have_posts() ) : $latest_updates->the_post();
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
                        <p><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>

                        <small>
                            <span class="fa fa-clock-o"></span>
                            <?php echo esc_html( get_the_date() ); ?>
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
