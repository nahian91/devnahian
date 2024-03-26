<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package anahian
 */

get_header();

?>

<main id="primary" class="site-main">

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
                        <div class="post-single-content">
                            <?php the_category(', '); ?>
                            <h4><?php the_title();?></h4>
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
								// Check if $tutorial_important_links is set and is an array
								if (isset($tutorial_important_links) && is_array($tutorial_important_links)) {
									foreach($tutorial_important_links as $link) {
										// Retrieve values from the $link array
										$important_link_url = $link['important_link_url'];
										$important_link_title = $link['important_link_title'];
										// Output the link
										echo '<span><a href="' . esc_url($important_link_url) . '">' . esc_html($important_link_title) . '</a></span>';
									}
								} else {
									// Output a message if $tutorial_important_links is not set or not an array
									echo 'No important links found.';
								}
								?>
							</div>

							<?php
                                $select_download = get_field('select_download');
                                if($select_download['value'] == 'download_shortcode') {
                                    $tutorial_download_shortcode = get_field('tutorial_download_shortcode');
                                    ?>
                                        <div class="post-video-download">
                                            <?php echo do_shortcode($tutorial_download_shortcode); ?>
                                        </div>
                                    <?php 
                                } else { 
                                    $tutorial_download_paid_url = get_field('tutorial_download_paid_url');    
                                    $tutorial_download_paid_text = get_field('tutorial_download_paid_text');    
                                ?>
                                    <div class="post-video-download-btn">
                                        <a href="<?php echo $tutorial_download_paid_url; ?>" target="_blank"><?php echo $tutorial_download_paid_text; ?></a>
                                    </div>
                                <?php
                                }
                            ?>
                        </div>
                    </div> <!--/-->
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
