/**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */
var app = {
    markMatch: function (text, term) {
        var match = text.toUpperCase().indexOf(term.toUpperCase());
        var $result = $('<span></span>');
        if (match < 0) {
            return $result.text(text);
        }
        $result.text(text.substring(0, match));
        var $match = $('<span class="select2-rendered__match"></span>');
        $match.text(text.substring(match, match + term.length));
        $result.append($match);
        $result.append(text.substring(match + term.length));
        return $result;
    },
    setting_header_sidebar: function () {
        $(".nav-toggler").click(function () {
            $("body").toggleClass("show-sidebar");
        });
        $(".sidebartoggler").on('click', function () {
            if ($("body").hasClass("mini-sidebar")) {
                $("body").trigger("resize");
                $("body").removeClass("mini-sidebar");
                $('.navbar-brand span').show();
                $(".navbar-brand b").hide();
            }
            else {
                $("body").trigger("resize");
                $("body").addClass("mini-sidebar");
                $('.navbar-brand span').hide();
                $(".navbar-brand b").show();
            }
        });
        var hs = function () {
            var width = (window.innerWidth > 0) ? window.innerWidth : this.screen.width;
            var topOffset = 70;
            if (width < 1170) {
                $("body").addClass("mini-sidebar");
                $('.navbar-brand span').hide();
                $(".scroll-sidebar, .slimScrollDiv").css("overflow-x", "visible").parent().css("overflow", "visible");
            }
            else {
                $("body").removeClass("mini-sidebar");
                $('.navbar-brand span').show();
            }
        };
        $(window).ready(hs);
        $(window).on("resize", hs);
    },
    sticky_header: function () {
        if($(".fix-header .header").length > 0){
            $(".fix-header .header").stick_in_parent({});
        }
    },
    card: {
        delete: function (elm, fn) {
            elm = $(elm);

            elm.fadeOut(200, function () {
                $(this).remove();
            });

            if (typeof fn === "function") fn();


            return false;
        },
        toggle: function (elm, fn) {
            elm = $(elm);

            elm.toggleClass("block-toggled");

            if (typeof fn === "function") fn();

            return false;
        },
        expand: function (elm, fn) {
            elm = $(elm);

            elm.toggleClass("block-expanded");

            if (typeof fn === "function") fn();

            return false;
        },
        loading: {
            start: function (elm) {
                $(elm).append("<div class=\"card-loading-layer\"><div class=\"app-spinner loading loading-primary\"></div></div>");
                return true;
            },
            finish: function (elm) {
                $(elm).find(".card-loading-layer").remove();
                return true;
            }
        }
    },
    getErrorMessage: function (jqXHR, exception) {
        var msg = '';
        if (jqXHR.status === 0) {
            msg = 'Not connect.\n Verify Network.';
        } else if (jqXHR.status == 404) {
            msg = 'Requested page not found. [404]';
        } else if (jqXHR.status == 500) {
            msg = 'Internal Server Error [500].';
        } else if (exception === 'parsererror') {
            msg = 'Requested JSON parse failed.';
        } else if (exception === 'timeout') {
            msg = 'Time out error.';
        } else if (exception === 'abort') {
            msg = 'Ajax request aborted.';
        } else {
            msg = 'Uncaught Error.\n' + jqXHR.responseText;
        }
        return msg;
    },
    bootstrap_select: function () {
        if ($(".select-picker").length > 0) {
            $('.select-picker').selectpicker();
        }
    },
    select2_select: function () {
        if ($(".select2-picker").length > 0) {
            $('.select2-picker').select2();
        }
    },
    switch_button: function () {
        if ($(".switch").length > 0) {
            $(".switch").each(function () {
                $(this).append("<span></span>");
            });
        }
    },
    checkbox_radio: function () {
        if ($(".app-checkbox").length > 0 || $(".app-radio").length > 0) {
            $(".app-checkbox label, .app-radio label").each(function () {
                $(this).append("<span></span>");
            });
        }
    },
    notification: function (type, text) {
        var msg = '<strong>' + text + '</strong>';
        new Noty({
            text: msg,
            type: type, // success, error, warning, information, notification
            layout: 'topRight',
            progressBar: true,
            theme: 'library',
            timeout: 15000,
            animation: {
                open: "animated bounceIn",
                close: "animated fadeOut",
                speed: 200
            }
        }).show();
    },
    tooltip_popover: function () {
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
        $(function () {
            $('[data-toggle="popover"]').popover()
        });
    },
    customScrollBar: function () {
        if ($(".scrollable-sidebar").length > 0) {
            $(".scrollable-sidebar").mCustomScrollbar({
                setHeight: '100%',
                axis: "y",
                autoHideScrollbar: true,
                scrollInertia: 200,
                advanced: {autoScrollOnFocus: false}
            });
        }
    },
    sidebarMenu: function () {
        var url = window.location;
        var element = $('#sidebar-menu a').filter(function () {
            return this.href == url;
        }).addClass('active').parent().addClass('active');
        while (true) {
            if (element.is('li')) {
                element = element.parent().addClass('in').parent().addClass('active');
            }
            else {
                break;
            }
        }
        if ($("#sidebar-menu").length > 0) {
            $("#sidebar-menu").metisMenu();
        }
    },
    validateMethods: function () {
        if(jQuery.validator) {
            jQuery.validator.addMethod("urlpath", function (value, element) {
                return this.optional(element) || /^[/.a-zA-Z0-9-]+$/.test(value);
            }, "Please enter a valid URL.");
        }
    },
    generateSlug: function  (value) {
        return value.toLowerCase().replace(/-+/g, '').replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
    },
    loaded: function () {
        app.setting_header_sidebar();
        app.sidebarMenu();
        app.bootstrap_select();
        app.select2_select();
        app.sticky_header();
        app.switch_button();
        app.checkbox_radio();
        app.customScrollBar();
        app.tooltip_popover();
        app.validateMethods();
    }
};

$(document).ready(function () {
    app.loaded();
    $("body").trigger("resize");
});