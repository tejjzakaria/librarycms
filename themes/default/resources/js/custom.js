/**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */
(function ($) {
    'use strict';
    $(document).on('click', '.request-book', function (e) {
        e.preventDefault();
        var book = $(this).closest('.book');
        var bookId = $(this).data('id');
        var url = $(this).data('url');
        if(bookId && url) {
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: 'bookIds[]='+bookId,
                url: url,
                beforeSend: function () {
                    app.card.loading.start(book);
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else if (data.warning) {
                            app.notification('warning', data.warning);
                            $('#login-box').modal('show');
                        } else {
                            app.notification('success', data.success);
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish(book);
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        }
    });
    $(document).on('click', '.issue-book', function (e) {
        e.preventDefault();
        var book = $(this).closest('.book');
        var bookId = $(this).data('id');
        var url = $(this).data('url');
        if(bookId && url) {
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: 'bookId='+bookId,
                url: url,
                beforeSend: function () {
                    app.card.loading.start(book);
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (data.error) {
                            app.notification('error', data.error);
                        } else if (data.warning) {
                            app.notification('warning', data.warning);
                            $('#login-box').modal('show');
                        } else {
                            app.notification('success', data.success);
                        }
                    }
                },
                complete: function () {
                    app.card.loading.finish(book);
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        }
    });
    $('.menu-toggler').on('click', function () {
        $('.bar').toggleClass('animate');
    });
})(jQuery);
var app = {
    stickyHeader: function () {
        $(".header").sticky({
            topSpacing: 0,
            zIndex: 999
        });
    },
    smoothScroll: function () {
        var amountScrolled = 100;
        $(window).scroll(function () {
            if ($(window).scrollTop() > amountScrolled) {
                $('button.back-to-top').addClass('show');
            } else {
                $('button.back-to-top').removeClass('show');
            }
        });

        $('button.back-to-top').click(function () {
            $('html, body').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    },
    card: {
        loading: {
            start: function (elm) {
                $(elm).append("<div class=\"card-loading-layer\"><div class=\"app-spinner loading loading-info\"></div></div>");
                return true;
            },
            finish: function (elm) {
                $(elm).find(".card-loading-layer").remove();
                return true;
            }
        }
    },
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
    ajax_redirect: function (data) {
        if (data.redirect) {
            window.location.href = data.redirect;
            return false;
        }
    },
    searchForm: function () {
        $('.search-close').on('click', function () {
            $('.search-header').removeClass('open').addClass('out');
            setTimeout(function () {
                $('.search-header').hide().addClass('hide');
            }, 850);
        });
        $('.search-open').on('click', function (e) {
            e.preventDefault();
            $('.search-header').show().addClass('open').removeClass('hide out');
            $('.search-input').focus();
        });
    },
    notification: function (type, text) {
        //var msg = '<strong>' + text + '</strong>';
        new Noty({
            text: text,
            type: type, // success, error, warning, information
            layout: 'topRight',
            progressBar: true,
            theme: 'library-cms',
            timeout: 7000,
            animation: {
                open: "animated bounceIn",
                close: "animated fadeOut",
                speed: 200
            }
        }).show();
    },
    checkbox_radio: function () {
        if ($(".app-checkbox").length > 0 || $(".app-radio").length > 0) {
            $(".app-checkbox label, .app-radio label").each(function () {
                $(this).append("<span></span>");
            });
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
    bootstrap_tooltip: function () {
        $("[data-toggle='tooltip']").tooltip();
    },
    preloader: {
        start: function (elm) {
            $(elm).prepend("<div class=\"preloader\"><div class=\"overlay\"></div><div class=\"loader\"></div></div>");
            return true;
        },
        finish: function (elm) {
            $(elm).find(".preloader").remove();
            return true;
        }
    },
    removeFacebookAppendedHash: function () {
        if (window.location.hash && window.location.hash == '#_=_') {
            if (window.history && history.pushState) {
                window.history.pushState("", document.title, window.location.pathname);
            } else {
                var scroll = {
                    top: document.body.scrollTop,
                    left: document.body.scrollLeft
                };
                window.location.hash = '';
                document.body.scrollTop = scroll.top;
                document.body.scrollLeft = scroll.left;
            }
        }
    },
    loaded: function () {
        app.removeFacebookAppendedHash();
        app.stickyHeader();
        app.smoothScroll();
        app.searchForm();
        app.checkbox_radio();
        app.bootstrap_tooltip();
    }
};
$(document).ready(function () {
    app.loaded();
});