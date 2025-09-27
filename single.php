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

    <section class="breadcumb-area" style="background-image:url('<?php echo get_template_directory_uri();?>/assets/img/breadcumb.jpg')">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    
                <div class="post-single-content">
                            <?php the_category(', '); ?> 
                            <h4><?php the_title();?></h4>
                            <div class="post-single-info">
                                <ul class="list-inline">
                                    <li class="dot"></li>
                                    <li>Updated: <?php echo get_the_modified_date(); ?></li>
                                    <li class="dot"></li>
                                    <li><?php echo get_post_views(get_the_ID()) . ' Views';?></li>
                                    <li class="dot"></li>
                                    <li>Reading Time: <?php echo get_post_reading_time (); ?> Mins</li>
                                </ul>
                            </div>
                        </div> 
                </div>
            </div>
        </div>
    </section>

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
                        <div class="post-single-body">
                            <?php the_content(); ?>                           
                        </div>
                    </div> <!--/-->
                </div>
                <div class="col-lg-4">
                    <?php get_sidebar();?>
                    <div class="widget">
    <div class="section-title">
        <h5>Latest Courses</h5>
    </div>
    <ul class="widget-latest-posts">
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
                <li class="last-post">
                    <div class="image">
                        <a href="<?php the_permalink(); ?>">
                            <?php 
                            if (has_post_thumbnail()) {
                                the_post_thumbnail('mediun');
                            } else {
                                echo '<img src="' . esc_url(get_template_directory_uri() . '/assets/img/default-course.jpg') . '" alt="' . esc_attr(get_the_title()) . '">';
                            }
                            ?>
                        </a>
                    </div>
                    <div class="content">
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
                </div>
            </div>
        </div>
    </section><!--/-->

</main><!-- #main -->

<?php
get_footer();
?>
