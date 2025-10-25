<?php get_header(); ?>

<?php 
    $demo_info = get_field('demo_info');
    $single_demo = get_field('single_demo');
    $demo_features = get_field('demo_features');
    $demo_faq = get_field('demo_faq');
    $theme_infos = get_field('theme_infos');
    $theme_price = get_field('theme_price');
?>

<main id="primary" class="site-main">
    <section class="blog-grid">
        <div class="container single-theme-area">
            <div class="row">
                <div class="col-lg-8">
                    <?php 
                        if($demo_info) {
                            $demo_title = $demo_info['demo_title'];
                            $demo_image = $demo_info['demo_image']['url'];
                            $demo_image_alt = $demo_info['demo_image']['alt'];
                            $demo_description = $demo_info['demo_description'];
                            ?>
                                <div class="single-theme-info">
                                    <h4><?php echo $demo_title;?></h4>
                                    <p><?php echo $demo_description;?></p>
                                    <img src="<?php echo $demo_image;?>" alt="<?php echo $demo_image_alt;?>">
                                </div>
                            <?php
                        }
                    ?>
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
    </section>
    <section>
        <div class="container single-theme-area">
            <div class="row">
                <div class="col-lg-12">
                    <div class="single-theme-title">
                        <h4>Amazing Demos</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php 
                    foreach($single_demo as $demo) {
                        $single_demo_title = $demo['single_demo_title'];
                        $single_demo_url = $demo['single_demo_url'];
                        $single_demo_image = $demo['single_demo_image']['url'];
                        ?>
                            <div class="col-md-4">
                                <div class="single-demo-box">
                                    <div class="single-demo" style="background-image: url('<?php echo $single_demo_image;?>');">
                                    </div>
                                    <div class="single-demo-content">
                                        <h4><?php echo $single_demo_title;?></h4>
                                        <a href="<?php echo $single_demo_url;?>" target="_blank">View Demo</a>
                                    </div>
                                </div>
                            </div>    
                        <?php
                    }
                ?>
            </div>
        </div>
    </section>
    <section>
        <div class="container single-theme-area"> 
            <div class="row">
                <div class="col-lg-12">
                    <div class="single-theme-title">
                        <h4>David Features</h4>
                        <p>Some of the features which you should consider when using this theme </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php 
                    foreach($demo_features as $feature) {
                        $demo_feature_title = $feature['demo_feature_title'];
                        $demo_feature_description = $feature['demo_feature_description'];
                        ?>
                            <div class="col-md-4">
                                <div class="single-feature">
                                    <h4><?php echo $demo_feature_title;?></h4>
                                    <p><?php echo $demo_feature_description;?></p>
                                </div>
                            </div>    
                        <?php
                    }
                ?>
            </div>
        </div>
    </section><!--/.blog-grid-->
</main><!-- #main -->

<?php get_footer(); ?>
