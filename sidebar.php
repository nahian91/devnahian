<?php
/**
 * The sidebar containing the main widget area
 *
 * @package devnahian
 */
?>

<aside id="secondary" class="sidebar widget-area" role="complementary">

<?php if ( is_single() ) : ?>
<div class="widget">
        <div class="section-title">
            <h5><?php esc_html_e( 'Learn with Me', 'textdomain' ); ?></h5>
        </div>
            <div class="yt-channel">
                <a href="https://www.youtube.com/@abdullahnahian?sub_confirmation=1" class="yt-sub-button" target="_blank">
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

<!-- Latest Courses Widget -->
    <div class="widget">
    <div class="section-title">
        <h5><?php esc_html_e( 'WordPress Free Plugins', 'textdomain' ); ?></h5>
    </div>
    <ul class="widget-post-box" id="plugin-widget-list">
        <li style="font-size: 13px; color: #666; padding: 10px;">Checking installations...</li>
    </ul>
    
    <div class="widget-btn-wrapper" style="margin-top: 15px; text-align: center;">
        <a href="https://profiles.wordpress.org/nahian91/#content-plugins" target="_blank" class="view-all-plugins-btn">
            View All Plugins
        </a>
    </div>
</div>

<script>
    (async function() {
        const authorSlug = 'nahian91';
        const proxy = 'https://corsproxy.io/?'; 
        const widgetContainer = document.getElementById('plugin-widget-list');

        try {
            // Fetch plugins with icons, download counts, and active install data
            const apiUrl = `${proxy}${encodeURIComponent(`https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[author]=${authorSlug}&request[fields][icons]=1&request[fields][active_installs]=1&request[fields][downloaded]=1`)}`;
            const response = await fetch(apiUrl);
            const data = await response.json();

            if (data.plugins && data.plugins.length > 0) {
                // 1. Filter: Only plugins with 10 or more active installs
                const filteredPlugins = data.plugins.filter(plugin => plugin.active_installs >= 10);

                // 2. Sort by TOTAL downloads (Descending)
                const sortedPlugins = filteredPlugins.sort((a, b) => b.downloaded - a.downloaded);
                
                // 3. Take only the top 5
                const topFive = sortedPlugins.slice(0, 5);
                
                widgetContainer.innerHTML = ''; 

                if (topFive.length === 0) {
                    widgetContainer.innerHTML = '<li>No plugins with 10+ installs found.</li>';
                    return;
                }

                topFive.forEach(plugin => {
                    const pluginLink = `https://wordpress.org/plugins/${plugin.slug}`;
                    const iconUrl = plugin.icons['1x'] || 'https://s.w.org/plugins/geopattern-icon/default.svg';

                    widgetContainer.innerHTML += `
                        <li style="display: flex; align-items: center; margin-bottom: 15px;">
                            <div class="widget-post-box-image">
                                <a href="${pluginLink}" target="_blank" style="display: block; width: 50px; height: 50px; background-image: url('${iconUrl}'); background-size: cover; border-radius: 6px; background-position: center; border: 1px solid #eee;"></a>
                            </div>
                            <div class="widget-post-box-content" style="padding-left: 12px;">
                                <p style="margin: 0; font-size: 14px; font-weight: 600;">
                                    <a href="${pluginLink}" target="_blank" style="text-decoration: none; color: #333;">${plugin.name}</a>
                                </p>
                                <small style="color: #ff0000; font-size: 11px; font-weight: bold;">
                                    ${plugin.active_installs.toLocaleString()}+ Active Installs
                                </small>
                            </div>
                        </li>
                    `;
                });
            } else {
                widgetContainer.innerHTML = '<li>No plugins found.</li>';
            }
        } catch (error) {
            console.error('Error:', error);
            widgetContainer.innerHTML = '<li>Error loading plugins.</li>';
        }
    })();
</script>

<style>
    .view-all-plugins-btn {
        display: inline-block;
        padding: 8px 20px;
        background-color: #2271b1;
        color: #fff !important;
        text-decoration: none;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
        transition: background 0.3s ease;
    }
    .view-all-plugins-btn:hover { background-color: #135e96; }
    .widget-post-box { list-style: none; padding: 0; margin: 0; }
</style>

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
<?php else : ?>

    

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
