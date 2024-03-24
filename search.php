<?php get_header(); ?>
<?php
$s = get_search_query();
$args = array(
    's' => $s,
    'posts_per_page' => -1 // Display all search results
);
// The Query
$the_query = new WP_Query($args);
?>
<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php
        if ($the_query->have_posts()) {
            echo "<h2 style='font-weight:bold;color:#000'>Search Results for: " . get_query_var('s') . "</h2>";
            echo "<ul>";
            while ($the_query->have_posts()) {
                $the_query->the_post();
        ?>
                <li>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </li>
            <?php
            }
            echo "</ul>";
        } else {
            ?>
            <h2 style='font-weight:bold;color:#000'>Nothing Found</h2>
            <div class="alert alert-info">
                <p>Sorry, but nothing matched your search criteria. Please try again with some different keywords.</p>
            </div>
        <?php
        }
        ?>
    </main>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
