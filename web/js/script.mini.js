function IsEmail(e) {
    var t = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return t.test(e)
}

function ValiDate() {
    var e = $("#name").val();
    var t = $("#email").val();
    var n = $("#textarea").val();
    var r = "";
    if (e == "") {
        r = r + "<p>Please enter your Fullname.</p>";
        $("#name").addClass("er");
        $(".error").fadeIn(1e3)
    } else {
        $("#name").removeClass("er")
    } if (t == "") {
        r = r + "<p>Please enter your Email.</p>";
        $("#email").addClass("er");
        $(".error").fadeIn(1e3)
    } else if (!IsEmail(t)) {
        r = r + "<p>Please enter a valid Email.</p>";
        $("#email").addClass("er");
        $(".error").fadeIn(1e3)
    } else {
        $("#email").removeClass("er")
    } if (n == "") {
        r = r + "<p>Please put some message.</p>";
        $("#textarea").addClass("er");
        $(".error").fadeIn(1e3)
    } else {
        $("#textarea").removeClass("er")
    } if (r == "") {
        var i = "name=" + e + "&email=" + t + "&text=" + n;
        $.ajax({
            type: "POST",
            url: "email.php",
            data: i,
            success: function (e) {
                $(".mailFromDiv").animate({
                    height: "0px"
                }, 500);
                $(".mailSuccessDiv").fadeIn(1500)
            }
        })
    } else {
        $(".mailFromDiv .error").html(r)
    }
}
var initCharts = function () {
    "use strict";
    var e = $(".percentage");
    e.easyPieChart({
        animate: 1e3,
        scaleColor: false,
        onStep: function (e) {
            this.$el.find("span").text(~~e)
        }
    })
};
$(document).ready(function () {
    "use strict";
    $("#nav").onePageNav({
        begin: function () {
            console.log("start")
        },
        end: function () {
            console.log("stop")
        }
    });
    $(".nav-collapse").click("li", function () {
        $(".nav-collapse").collapse("hide")
    });
    $("a.next-section").click(function () {
        var e = $(this).attr("href");
        $("html, body").animate({
            scrollTop: $(e).offset().top
        }, 1e3);
        return false
    });
    $("a.top-section").click(function () {
        $("html, body").animate({
            scrollTop: 0
        }, 1e3);
        return false
    });
    var e;
    for (e = 1; e <= 30; e++) {
        $(".jquery_popup" + e).popup({
            content: $("#project" + e)
        })
    }
    $(".animated_popup").popup({
        show: function (e, t) {
            var n = this,
                r = n.getCenter();
            e.css({
                top: -e.children().outerHeight(),
                left: r.left,
                opacity: 1
            }).animate({
                top: r.top
            }, 500, "easeOutBack", function () {
                n.o.afterOpen.call(n)
            })
        }
    });
    $(".callback_popup").popup({
        beforeOpen: function (e) {
            console.log("beforeOpen -", e)
        },
        afterOpen: function () {
            console.log("afterOpen")
        },
        beforeClose: function () {
            console.log("beforeClose")
        },
        afterClose: function () {
            console.log("afterClose")
        }
    });
    $(".preloader_popup").popup({
        preloaderContent: '<img src="images/preloader.gif" class="preloader">'
    });
    $(".error_popup").popup({
        error: function (e, t) {
            this.open('<h1>ERROR!</h1><p>Content "' + e + '" of type "' + t + '" could not be loaded.</p>', "html")
        }
    });
    $(".fancybox").fancybox();
    $("#carousel").flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        itemWidth: 235,
        itemMargin: 0,
        asNavFor: "#slider"
    });
    $("#slider").flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        sync: "#carousel",
        start: function (e) {
            $("body").removeClass("loading")
        }
    });
    $("#clients").carouFredSel({
        auto: false,
        prev: "#prev2",
        next: "#next2",
        pagination: "#pager2",
        mousewheel: true,
        swipe: {
            onMouse: true,
            onTouch: true
        },
        responsive: true,
        width: "100%",
        scroll: 1,
        items: {
            width: 230,
            height: "130px",
            visible: {
                min: 1,
                max: 6
            }
        }
    })
});

$(window).scroll(function () {
    if ($(window).scrollTop() < 500 && $(window).width() >= 769) {
        $("#primary").css({
            top: "50px"
        })
    } else if ($(window).scrollTop() > 500 && $(window).width() >= 769) {
        $("#primary").css({
            top: "200px"
        })
    } else if ($(window).scrollTop() > 500 && $(window).width() < 768) {
        $("#primary").css({
            top: "0",
            right: "0"
        })
    }
});
$(document).ready(function () {
    $status = $(".status");
    var e = {
        autoPlay: true,
        autoPlayDelay: 4e3,
        pauseOnHover: false,
        hidePreloaderDelay: 500,
        nextButton: true,
        prevButton: true,
        pauseButton: true,
        preloader: true,
        hidePreloaderUsingCSS: false,
        animateStartingFrameIn: true,
        navigationSkipThreshold: 750,
        preventDelayWhenReversingAnimations: true,
        customKeyEvents: {
            80: "pause"
        }
    };
    var t = $("#sequence").sequence(e).data("sequence");
    t.afterNextFrameAnimatesIn = function () {
        if (t.settings.autoPlay && !t.hardPaused && !t.isPaused) {
            $status.addClass("active").css("opacity", 1)
        }
        $(".prev, .next").css("cursor", "pointer").animate({
            opacity: 1
        }, 500)
    };
    t.beforeCurrentFrameAnimatesOut = function () {
        if (t.settings.autoPlay && !t.hardPaused) {
            $status.css({
                opacity: 0
            }, 500).removeClass("active")
        }
        $(".prev, .next").css("cursor", "auto").animate({
            opacity: .7
        }, 500)
    };
    t.paused = function () {
        $status.css({
            opacity: 0
        }).removeClass("active").addClass("paused")
    };
    t.unpaused = function () {
        if (!t.hardPaused) {
            $status.removeClass("paused").addClass("active").css("opacity", 1)
        }
    }
})

$(window).load(function () {
	'use strict';
    $("#responsive_design").parallax("50%", .3);
    $("#parallax1").parallax("100%", .1);
    $("#parallax2").parallax("100%", .1);
    $("#parallax3").parallax("100%", .1);
    $("#parallax4").parallax("100%", .1);
    $("#parallax5").parallax("100%", .1)

    $(".animatd").one("inview", function (e, t) {
        if (t == true) {
            $(this).addClass("fadeInLeft")
        }
    });
    $(".animate0").one("inview", function (e, t) {
        if (t == true) {
            $(this).addClass("fadeInRight")
        }
    });
    $(".animate1").one("inview", function (e, t) {
        if (t == true) {
            $(this).addClass("fadeInUp")
        }
    });
    $(".animate2").one("inview", function (e, t) {
        if (t == true) {
            $(this).addClass("fadeInDown")
        }
        $(".skill").one("inview", function (e, t) {
            if (t == true) {
                initCharts()
            }
        })
    });
});