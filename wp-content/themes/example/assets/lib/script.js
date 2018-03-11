jQuery(document).ready(function ($) {
    $("#tariff-link1").click(function () {
        $("#tariff-content1").toggleClass("active-tab");
    });
    $("#tariff-link2").click(function () {
        $("#tariff-content2").toggleClass("active-tab");
    });
    $("#tariff-link3").click(function () {
        $("#tariff-content3").toggleClass("active-tab");
    });
    $("#tariff-link4").click(function () {
        $("#tariff-content4").toggleClass("active-tab");
    });
    $("#tariff-link5").click(function () {
        $("#tariff-content5").toggleClass("active-tab");
    });
    $("#tariff-link6").click(function () {
        $("#tariff-content6").toggleClass("active-tab");
    });
    $("#tariff-link7").click(function () {
        $("#tariff-content7").toggleClass("active-tab");
    });

    $("#tariff-link8").click(function () {
        $("#tariff-content8").toggleClass("active-tab");
    });

    $("#more-options_link").click(function() {
        $( "#more-options" ).toggleClass("active-tab");
    });

    $(".nav-line-menu").click(function () {
        $(".main_header__nav").toggle();
        $(".delivery").toggle();
        $(".homepage_screen").toggleClass("homepage_screen-textbox_padd");
        $(".track_nav").css("z-index", "-1");
    });

    $("#open_track").click(function () {
        $(".track_nav").css("z-index", "2");

    });
    $("#go_back").click(function () {
        $(".track_nav").css("z-index", "-1");
    });
});

function countUp() {
    jQuery('.count_weight').val(function (i, val) {
        return val * 1 + 0.5;
    });
}

function countDown() {
    var countValue = jQuery('.count_weight').val();

    if (countValue <= '0.5') {
    } else {
        jQuery('.count_weight').val(function (i, val) {
            return val * 1 - 0.5;
        });
    }
}
	