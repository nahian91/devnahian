<?php
/**
 * The sidebar containing the main widget area
 *
 * @package devnahian
 */
?>

<aside id="secondary" class="sidebar widget-area" role="complementary">

<?php if ( is_single() ) : ?>

    <!-- Table of Contents Widget -->
    <div class="widget toc-widget sticky-toc">
        <div class="section-title">
            <h5><?php esc_html_e( 'Table of Contents', 'textdomain' ); ?></h5>
        </div>

        <div id="post-toc">
            <?php
            global $post;

            // Parse Gutenberg blocks
            $blocks = parse_blocks( $post->post_content );
            $toc_items = [];

            foreach ( $blocks as $block ) {
                // Only Theme Collections blocks
                if ( $block['blockName'] === 'acf/theme-collections' ) {
                    $title = $block['attrs']['data']['title'] ?? '';
                    if ( $title ) {
                        $id = sanitize_title( $title );
                        $toc_items[] = [
                            'title' => $title,
                            'id'    => $id,
                        ];
                    }
                }
            }

            if ( ! empty( $toc_items ) ) :
                echo '<ul class="toc-list">';
                foreach ( $toc_items as $item ) :
                    echo '<li><a href="#' . esc_attr( $item['id'] ) . '">' . esc_html( $item['title'] ) . '</a></li>';
                endforeach;
                echo '</ul>';
            else :
                echo '<p>' . esc_html__( 'No headings found in this post.', 'textdomain' ) . '</p>';
            endif;
            ?>
        </div>
    </div>

<?php else : ?>

    <!-- Latest Courses Widget -->
    <div class="widget">
        <div class="section-title">
            <h5><?php esc_html_e( 'Latest Courses', 'textdomain' ); ?></h5>
        </div>
        <ul class="widget-post-box">
            <?php
            $args = array(
                'post_type'      => 'courses',
                'posts_per_page' => 5,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post(); ?>
                    <li>
                        <div class="widget-post-box-image">
                            <a href="<?php the_permalink(); ?>" style="background-image: url('<?php 
                                echo has_post_thumbnail() 
                                    ? esc_url( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) )
                                    : esc_url( get_template_directory_uri() . '/assets/img/default-course.jpg' );
                            ?>');"></a>
                        </div>
                        <div class="widget-post-box-content">
                            <p><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                        </div>
                    </li>
                <?php endwhile;
                wp_reset_postdata();
            else :
                echo '<li>' . esc_html__( 'No courses found.', 'textdomain' ) . '</li>';
            endif;
            ?>
        </ul>
    </div>

    <!-- Categories Widget -->
    <div class="widget">
        <div class="section-title">
            <h5><?php esc_html_e( 'Categories', 'textdomain' ); ?></h5>
        </div>
        <ul class="widget-categories">
            <?php
            $categories = get_categories(array(
                'orderby'    => 'count',
                'order'      => 'DESC',
                'number'     => 5,
                'hide_empty' => true,
            ));
            if ( ! empty( $categories ) ) :
                foreach ( $categories as $cat ) :
                    $cat_link = get_category_link( $cat->term_id ); ?>
                    <li>
                        <a href="<?php echo esc_url( $cat_link ); ?>" class="categorie"><?php echo esc_html( $cat->name ); ?></a>
                        <span class="ml-auto"><?php echo esc_html( $cat->count ); ?> <?php esc_html_e( 'Posts', 'textdomain' ); ?></span>
                    </li>
                <?php endforeach;
            endif;
            ?>
        </ul>
    </div>

    <!-- Most Popular Posts Widget -->
    <div class="widget">
        <div class="section-title">
            <h5><?php esc_html_e( 'Most Popular Posts', 'textdomain' ); ?></h5>
        </div>
        <ul class="widget-post-box">
            <?php
            $popular_posts = new WP_Query(array(
                'post_type'           => array('post', 'tutorial', 'themes'),
                'posts_per_page'      => 5,
                'meta_key'            => 'post_views_count',
                'orderby'             => 'meta_value_num',
                'order'               => 'DESC',
                'ignore_sticky_posts' => true,
            ));
            if ($popular_posts->have_posts()) :
                while ($popular_posts->have_posts()) : $popular_posts->the_post(); ?>
                    <li>
                        <div class="widget-post-box-image">
                            <a href="<?php the_permalink(); ?>" style="background-image: url('<?php
                                echo has_post_thumbnail() 
                                    ? esc_url( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) )
                                    : esc_url( get_template_directory_uri() . '/assets/img/default.jpg' );
                            ?>');"></a>
                        </div>
                        <div class="widget-post-box-content">
                            <p><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                            <small>
                                <span class="fa fa-eye"></span>
                                <?php echo esc_html( number_format( get_post_views( get_the_ID() ) ) ); ?> Views
                            </small>
                        </div>
                    </li>
                <?php endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </ul>
    </div>

    <!-- Latest Updates Widget -->
    <div class="widget">
        <div class="section-title">
            <h5><?php esc_html_e( 'Latest Updates', 'textdomain' ); ?></h5>
        </div>
        <ul class="widget-post-box">
            <?php
            $latest_updates = new WP_Query(array(
                'post_type'           => 'post',
                'posts_per_page'      => 5,
                'orderby'             => 'date',
                'order'               => 'DESC',
                'ignore_sticky_posts' => true,
            ));
            if ($latest_updates->have_posts()) :
                while ($latest_updates->have_posts()) : $latest_updates->the_post(); ?>
                    <li>
                        <div class="widget-post-box-image">
                            <a href="<?php the_permalink(); ?>" style="background-image: url('<?php
                                echo has_post_thumbnail() 
                                    ? esc_url( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ) 
                                    : esc_url( get_template_directory_uri() . '/assets/img/default.jpg' );
                            ?>');"></a>
                        </div>
                        <div class="widget-post-box-content">
                            <p><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                            <small>
                                <span class="fa fa-clock-o"></span>
                                <?php echo esc_html( get_the_date() ); ?>
                            </small>
                        </div>
                    </li>
                <?php endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </ul>
    </div>

<?php endif; ?>

</aside>
