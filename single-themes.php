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
                    <?php 
                        if($theme_price) {
                            $theme_price_amount = $theme_price['theme_price']; 
                            $theme_download_url = $theme_price['theme_download_url']; 
                            $theme_download_text = $theme_price['theme_download_text']; 
                            ?>
                            <div class="single-theme-price">
                                <div class="single-theme-price-top">
                                    <p>Price <span><?php echo $theme_price_amount;?></span></p>
                                    <a href="<?php echo $theme_download_url;?>" target="_blank"><?php echo $theme_download_text;?></a>
                                </div>
                            </div>
                            <?php
                        }
                    ?>
                    
                    <?php 
                        if($theme_infos) {
                                ?>
                            <div class="single-theme-info-right">
                                <ul>
                                    <?php 
                                        foreach($theme_infos as $info) {
                                            $theme_info_title = $info['theme_info_title'];
                                            $theme_info_description = $info['theme_info_description'];
                                            ?>
                                                <li><span><?php echo $theme_info_title;?></span><?php echo $theme_info_description;?></li>
                                            <?php
                                        }
                                    ?>
                                </ul>
                            </div>
                            <?php 
                        }
                    ?>
                </div>
            </div>
        </div>
    </section>
    <section class="pt-80 pb-80">
        <div class="container single-theme-area">
            <div class="row">
                <div class="col-lg-12">
                    <div class="single-theme-title">
                        <h4>Amazing Demos</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Commodi, aut.</p>
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
    <section class="pb-80">
        <div class="container single-theme-area"> 
            <div class="row">
                <div class="col-lg-12">
                    <div class="single-theme-title">
                        <h4>Pikme features at a glance </h4>
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
