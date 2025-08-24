(function ($) {
    "use strict";

    // Show the first tab's content initially
    $('.single-theme-tab-content:first').addClass('active').show();
    $('.single-theme-tab-menu li:first').addClass('active');

    // Handle tab clicks
    $('.single-theme-tab-menu a').on('click', function(e) {
        e.preventDefault();
        
        var target = $(this).attr('href');
        
        $('.single-theme-tab-menu li').removeClass('active');
        $(this).parent().addClass('active');
        
        $('.single-theme-tab-content').removeClass('active').fadeOut('fast');
        $(target).addClass('active').fadeIn('fast');
    });

    // Copy to Clipboard
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
            Navigation
    ----------------------------------- */
    $(window).on('scroll', function () {
        if ($(".navbar").offset().top > 50) {
            $(".navbar").addClass("navbar-scroll");
        } else {
            $(".navbar ").removeClass("navbar-scroll");
        }
    });

    $('.navbar-toggler').on('click', function () {
        $('.navbar-collapse').collapse('show');
    });

    /* -----------------------------------
           Back-top
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

    $(".video")
    .on("mouseover", function () { this.play(); })
    .on("mouseout", function () { this.pause(); });

    $('#mainmenu').slicknav();

})(jQuery);
