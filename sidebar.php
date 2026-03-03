<?php
/**
 * The sidebar containing the main widget area
 *
 * @package devnahian
 */
?>

<aside id="secondary" class="sidebar widget-area" role="complementary">

<?php if ( is_single() ) : ?>

    <div class="widget" style="text-align:center">
        <div class="section-title">
            <h5><?php esc_html_e( 'Support Me', 'textdomain' ); ?></h5>
        </div>
        <a href="https://www.buymeacoffee.com/nahian" target="_blank"><img src="https://img.buymeacoffee.com/button-api/?text=Buy Me a Coffee&emoji=☕&slug=nahian&button_colour=FFDD00&font_colour=000000&font_family=Poppins&outline_colour=000000&coffee_colour=ffffff" /></a>
    </div>
<div class="widget">
        <div class="section-title">
            <h5><?php esc_html_e( 'Learn with Me', 'textdomain' ); ?></h5>
        </div>
            <div class="yt-channel">
                <a href="https://www.youtube.com/@codewithAbdullahNahian?sub_confirmation=1" class="yt-sub-button" target="_blank">
    <img src="https://upload.wikimedia.org/wikipedia/commons/0/09/YouTube_full-color_icon_%282017%29.svg" width="20">
    Code with Abdullah Nahian
</a>
            </div>
</div>

<style>
    .yt-channel{
        text-align: center;
    }
    .yt-sub-button {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background-color: #FF0000;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    font-family: sans-serif;
    transition: background 0.3s;
}
.yt-sub-button:hover {
    background-color: #cc0000;
    color: #fff;
}
</style>




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
