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

    <section class="breadcumb-area" style="background-image:url('<?php echo get_template_directory_uri();?>/assets/img/bg-footer.jpg')">
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
                    <?php
$related_posts = get_field('related_posts');

if( $related_posts ):
?>
<div class="related-posts-section">
    <h3 class="related-posts-heading">Related Posts</h3>
    <div class="row related-posts-wrapper">
        <?php foreach( $related_posts as $post ): 
            setup_postdata($post); 
            $category = get_the_category();
            $first_cat = $category ? $category[0] : null;
        ?>
        <div class="col-md-6 mb-4">
            <div class="post-card">
                <div class="post-card-image">
                    <a href="<?php the_permalink(); ?>">
                        <?php 
                        if( has_post_thumbnail() ) {
                            the_post_thumbnail('post-thumbnail', array(
                                'loading' => 'lazy',
                                'width' => 1280,
                                'height' => 720,
                            )); 
                        }
                        ?>
                    </a>
                </div>
                <div class="post-card-content">
                    <?php if($first_cat): ?>
                        <a href="<?php echo get_category_link($first_cat->term_id); ?>" rel="category tag"><?php echo esc_html($first_cat->name); ?></a>
                    <?php endif; ?>
                    
                    <h5>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h5>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php 
    wp_reset_postdata();
endif;
?>

                </div>
                <div class="col-lg-4">
                    <?php get_sidebar();?>


                </div>
            </div>
        </div>
    </section><!--/-->

</main><!-- #main -->

<?php
get_footer();
?>
