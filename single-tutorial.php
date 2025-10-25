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
                        <h4><?php the_title();?></h4>
                    </div> 
                </div>
            </div>
        </div>
    </section>
    <!--post-default-->
    <section class="section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 mb-20">
                    <!--Post-single-->
                    <div class="post-single">
                        <div class="post-single-image">
                            <?php the_post_thumbnail();?>
                        </div>                 
                        <div class="post-single-body">
                            <div class="post-single-video">

                                <?php
                                    $tutorial_video_id = get_field('tutorial_video_id');
                                    $tutorial_important_links = get_field('tutorial_important_links');
                                    $tutorial_download_shortcode = get_field('tutorial_download_shortcode');
                                    if ($tutorial_video_id) {
                                        // Embed YouTube video using iframe
                                        echo '<div class="embed-responsive embed-responsive-16by9">';
                                        echo '<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/' . $tutorial_video_id . '" allowfullscreen></iframe>';
                                        echo '</div>';
                                    }
                                ?>

                            </div>   
							
							<div class="post-video-links">
								<h4>Important Links</h4>
								<?php 
								if (isset($tutorial_important_links) && is_array($tutorial_important_links)) {
									foreach($tutorial_important_links as $link) {
										$important_link_url = $link['important_link_url'];
										$important_link_title = $link['important_link_title'];
										// Output the link
										echo '<span><a href="' . esc_url($important_link_url) . '">' . esc_html($important_link_title) . '</a></span>';
									}
								} else {
									echo 'No important links found.';
								}
								?>
							</div>

							<?php
$select_download = get_field('select_download');

if ( ! empty($select_download['value']) && $select_download['value'] === 'download_shortcode' ) {
    
    $tutorial_download_shortcode = get_field('tutorial_download_shortcode');
    
    if ( ! empty($tutorial_download_shortcode) ) {
        ?>
        <div class="post-video-download">
            <?php echo do_shortcode($tutorial_download_shortcode); ?>
        </div>
        <?php
    }

} else {

    $tutorial_download_paid_url  = get_field('tutorial_download_paid_url');
    $tutorial_download_paid_text = get_field('tutorial_download_paid_text');

    if ( ! empty($tutorial_download_paid_url) && ! empty($tutorial_download_paid_text) ) {
        ?>
        <div class="post-video-download-btn">
            <a href="<?php echo esc_url($tutorial_download_paid_url); ?>" target="_blank">
                <?php echo esc_html($tutorial_download_paid_text); ?>
            </a>
        </div>
        <?php
    }
}
?>

                        </div>
                    </div> <!--/-->
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



                </div>
            </div>
        </div>
    </section><!--/-->

</main><!-- #main -->

<?php
get_footer();
