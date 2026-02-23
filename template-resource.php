<?php
/*
Template Name: Resources Showcase
*/
get_header();
?>

<main id="primary" class="site-main">

    <section class="breadcumb-area" style="background-image:url('<?php echo get_template_directory_uri();?>/assets/img/bg-footer.jpg'); background-size: cover; background-position: center; padding: 80px 0;">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">                    
                    <div class="post-single-content">
                        <h4><?php the_title();?></h4>
                    </div> 
                </div>
            </div>
        </div>
    </section>

    <section class="blog-grid py-5" style="background: #f8f9fa;">
        <div class="container">
            <div class="row">
                <?php
                $args = array(
                    'post_type'      => 'resource', 
                    'posts_per_page' => 12,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                );

                $query = new WP_Query($args);

                if ($query->have_posts()) :
                    while ($query->have_posts()) : $query->the_post(); 
                        
                        // 1. Fetch ACF Fields
                        $acf_image  = get_field('resource_image'); // ACF Image Field
                        $badge      = get_field('resource_badge');
                        $price      = get_field('resource_price') ?: '0'; 
                        $short_desc = get_field('resource_short_description');

                        // 2. Taxonomy Fallback for Badge
                        if(!$badge) {
                            $terms = get_the_terms(get_the_ID(), 'resource_cat');
                            $badge = (!is_wp_error($terms) && !empty($terms)) ? $terms[0]->name : 'Resource';
                        }

                        // 3. Image Logic: Priority to ACF Image, then Featured Image, then Placeholder
                        $img_url = '';
                        if( !empty($acf_image) ) {
                            $img_url = is_array($acf_image) ? $acf_image['url'] : $acf_image;
                        } elseif ( has_post_thumbnail() ) {
                            $img_url = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
                        } else {
                            $img_url = get_template_directory_uri() . '/assets/img/placeholder.jpg';
                        }
                    ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <article class="dnr-card shadow-sm h-100 bg-white rounded overflow-hidden d-flex flex-column" style="transition: 0.3s; border: 1px solid #eee;">
                                
                                <div class="dnr-img-wrapper position-relative overflow-hidden">
                                    <?php if($badge): ?>
                                    <span class="position-absolute badge bg-primary m-3 px-3 py-2" style="z-index: 2; top:0; left:0; border-radius: 50px;">
                                        <?php echo esc_html($badge); ?>
                                    </span>
                                    <?php endif; ?>
                                    
                                    <a href="<?php the_permalink(); ?>">
                                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>" class="w-100" style="height: 230px; object-fit: cover; transition: 0.5s;">
                                    </a>
                                </div>
                                
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <h4 class="resourcs-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h4>
                                    
                                    <p class="resourcs-desc">
                                        <?php echo $short_desc ? wp_trim_words($short_desc, 12) : wp_trim_words(get_the_excerpt(), 10); ?>
                                    </p>

                                    <div class="resourcs-title-price">
                                        <div class="price-box">
                                            <span><?php echo esc_html($price); ?></span>
                                        </div>
                                        <div class="button-box">
                                            <a href="<?php the_permalink(); ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </article>
                        </div>
                    <?php 
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<div class="col-12 text-center py-5"><h3>No Resources Found</h3></div>';
                endif; 
                ?>
            </div>
        </div>
    </section>
</main>

<style>
.dnr-card { transition: all 0.3s ease; }
.dnr-card:hover { transform: translateY(-8px); box-shadow: 0 12px 25px rgba(0,0,0,0.1) !important; }
.dnr-card:hover img { transform: scale(1.1); }
.hover-blue:hover { color: #0d6efd !important; }
</style>

<?php get_footer(); ?>