(function ($) {
    "use strict";

    $(document).ready(function() {

        /* -----------------------------------
                Tabs
        ----------------------------------- */
        $('.single-theme-tab-content:first').addClass('active').show();
        $('.single-theme-tab-menu li:first').addClass('active');

        $('.single-theme-tab-menu a').on('click', function(e) {
            e.preventDefault();

            var target = $(this).attr('href');

            $('.single-theme-tab-menu li').removeClass('active');
            $(this).parent().addClass('active');

            $('.single-theme-tab-content').removeClass('active').fadeOut('fast');
            $(target).addClass('active').fadeIn('fast');
        });

        /* -----------------------------------
                Copy to Clipboard
        ----------------------------------- */
        $(document).on('click', '.copy-btn', function () {
            var $btn = $(this);
            var codeBlock = $btn.closest('pre').find('code');

            if (codeBlock.length) {
                var text = codeBlock.text().trim();
                navigator.clipboard.writeText(text).then(function () {
                    $btn.text('Copied!').css('background', '#28a745');
                    setTimeout(function () {
                        $btn.text('Copy').css('background', '#007acc');
                    }, 2000);
                }).catch(function(err) {
                    console.error("Failed to copy: ", err);
                });
            }
        });

        /* -----------------------------------
                Preloader
        ----------------------------------- */
        $('.loading').delay(500).fadeOut(500);

        /* -----------------------------------
                Navbar Scroll
        ----------------------------------- */
        $(window).on('scroll', function () {
            if ($(".navbar").offset().top > 50) {
                $(".navbar").addClass("navbar-scroll");
            } else {
                $(".navbar").removeClass("navbar-scroll");
            }
        });

        $('.navbar-toggler').on('click', function () {
            $('.navbar-collapse').collapse('show');
        });

        /* -----------------------------------
                Back to Top
        ----------------------------------- */
        $(window).on("scroll", function () {
            if ($(window).scrollTop() > 250) {
                $('.back-top').fadeIn(300);
            } else {
                $('.back-top').fadeOut(300);
            }
        });

        $('.back-top').on('click', function (event) {
            event.preventDefault();
            $('html, body').animate({ scrollTop: 0 }, 300);
            return false;
        });

        /* -----------------------------------
                Video Hover Play/Pause
        ----------------------------------- */
        $(".video")
        .on("mouseover", function () { this.play(); })
        .on("mouseout", function () { this.pause(); });

        /* -----------------------------------
                SlickNav
        ----------------------------------- */
        $('#mainmenu').slicknav();

        /* -----------------------------------
                Sticky Table of Contents
        ----------------------------------- */
        const tocLinks = document.querySelectorAll('.toc-list a[href^="#"]');
        const sections = document.querySelectorAll('.theme-collection h2');

        // Smooth scroll on click
        tocLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 80, // adjust for sticky header height
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Scrollspy: highlight current TOC link
        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.getBoundingClientRect().top + window.pageYOffset;
                if (window.pageYOffset >= sectionTop - 100) {
                    current = section.id;
                }
            });
            tocLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });

    });

})(jQuery);
