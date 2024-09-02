<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package devnahian
 */

?>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="single-footer">
                        <h4>about me</h4>
                        <p>I'm a web developer and freelancer who loves teaching, making video tutorials, and blogging about web development to help others learn and understand the subject better.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="single-footer">
                    <h4>popular course</h4>
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'footer-1',
                        )
                    );
                ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="single-footer">
                    <h4>quick links</h4>
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'footer-2',
                        )
                    );
                ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="single-footer">
                        <h4>contact me</h4>
                        <ul>
                            <li>
                                <a href="mailto: nahiansylhet@gmail.com" target="_blank"><i class="fa fa-envelope"></i>
                                nahiansylhet@gmail.com</a></li>
                            <li>
                                <a href="tel: 01686195607" target="_blank"><i class="fa fa-whatsapp"></i>
                                 01686195607</a>
                            </li>
                            <li>
                                <a href="www.facebook.com/nahian01" target="_blank"><i class="fa fa-facebook"></i>
                                Abdullah Nahian</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="copyright">
                        <p>&copy; Copyright 2024 <a href="#">Abdullah Nahian</a>. All rights reserved.</p>
                    </div>
                    <div class="back">
                        <a href="#" class="back-top">
                            <i class="arrow_up"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/-->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
