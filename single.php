<?php
/**
 * The template for displaying all single posts
 *
 * Optimized for:
 * - Unique post views tracking (Dhaka timezone)
 * - Total & Today views
 * - Reading time
 * - Related posts
 *
 * @package devnahian
 */

get_header();

// ---------------------------
// Track unique post views
// ---------------------------
if( function_exists('infinity_track_unique_post_views') ){
    infinity_track_unique_post_views();
}

// ---------------------------
// Reading time function
// ---------------------------
if(!function_exists('get_post_reading_time')){
    function get_post_reading_time($post_id = null){
        $post = get_post($post_id ?: get_the_ID());
        $words = str_word_count(strip_tags($post->post_content));
        $minutes = ceil($words / 200); // average reading speed: 200 wpm
        return $minutes;
    }
}
?>

<main id="primary" class="site-main">

    <section class="breadcumb-area" style="background-image:url('<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/bg-footer.jpg')">
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
                                <li>Views: <?php echo get_post_views(get_the_ID()); ?></li>
                                <li class="dot"></li>
                                <li>Reading Time: <?php echo get_post_reading_time(); ?> mins</li>
                            </ul>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </section>

    <!-- Post Content -->
    <section class="section pt-55">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 mb-20">
                    <div class="post-single">
                        <div class="post-single-image">
                            <?php the_post_thumbnail('full', ['loading'=>'lazy']); ?>
                        </div>
                        <div class="post-single-body">
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <?php
                    // Related Posts
                    $categories = wp_get_post_categories(get_the_ID());
                    if($categories){
                        global $wpdb;
                        $related_ids = $wpdb->get_col(
                            $wpdb->prepare("
                                SELECT DISTINCT p.ID
                                FROM {$wpdb->posts} p
                                INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
                                INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                                WHERE tt.term_id IN (" . implode(',', array_map('intval', $categories)) . ")
                                AND p.ID != %d
                                AND p.post_status = 'publish'
                                AND p.post_type = 'post'
                            ", get_the_ID())
                        );

                        if($related_ids){
                            $post_score = [];
                            foreach($related_ids as $pid){
                                $pid_cats = wp_get_post_categories($pid);
                                $post_score[$pid] = count(array_intersect($categories,$pid_cats));
                            }

                            arsort($post_score);
                            $top_posts = array_slice(array_keys($post_score),0,4);

                            $related_query = new WP_Query([
                                'post__in'=>$top_posts,
                                'orderby'=>'post__in',
                                'posts_per_page'=>4
                            ]);

                            if($related_query->have_posts()): ?>
                                <div class="related-posts-section">
                                    <h3 class="related-posts-heading">Related Posts</h3>
                                    <div class="row related-posts-wrapper">
                                        <?php while($related_query->have_posts()): $related_query->the_post(); 
                                            $first_cat = get_the_category();
                                            $first_cat = $first_cat ? $first_cat[0] : null;
                                        ?>
                                            <div class="col-md-6 mb-4">
                                                <div class="post-card">
                                                    <div class="post-card-image">
                                                        <a href="<?php the_permalink(); ?>">
                                                            <?php 
                                                            if(has_post_thumbnail()){
                                                                the_post_thumbnail('post-thumbnail',[
                                                                    'loading'=>'lazy',
                                                                    'width'=>1280,
                                                                    'height'=>720
                                                                ]);
                                                            } else {
                                                                echo '<img src="https://via.placeholder.com/400x225?text=No+Image" alt="">';
                                                            }
                                                            ?>
                                                        </a>
                                                    </div>
                                                    <div class="post-card-content">
                                                        <?php if($first_cat): ?>
                                                            <a href="<?php echo esc_url(get_category_link($first_cat->term_id)); ?>">
                                                                <?php echo esc_html($first_cat->name); ?>
                                                            </a>
                                                        <?php endif; ?>
                                                        <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; wp_reset_postdata(); ?>
                                    </div>
                                </div>
                            <?php endif;
                        }
                    }
                    ?>
                </div>

                <div class="col-lg-4">
                    <?php get_sidebar(); ?>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
