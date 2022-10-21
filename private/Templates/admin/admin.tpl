<!DOCTYPE html>
<html lang="en" dir="{if $activeLanguage->isRTL()}rtl{else}ltr{/if}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="KAASoft">
        <meta name="robots" content="noindex,nofollow">
        <title>{block name=title}{/block} | Library CMS</title>
        {block name=headerCss}
            <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,700,800" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,400i,500,600,700" rel="stylesheet">
            {if $activeLanguage->isRTL()}
                <link href="{$resourcePath}assets/css/plugins/bootstrap/bootstrap.rtl.min.css" rel="stylesheet">
            {else}
                <link href="{$resourcePath}assets/css/plugins/bootstrap/bootstrap.min.css" rel="stylesheet">
            {/if}
        {/block}
        <link href="{$resourcePath}assets/css/plugins.css" rel="stylesheet">
        {if $activeLanguage->isRTL()}
            <link href="{$resourcePath}assets/css/style.rtl.css" rel="stylesheet">
        {else}
            <link href="{$resourcePath}assets/css/style.css" rel="stylesheet">
        {/if}
        {if $siteViewOptions->getOptionValue("adminColorSchema") == 'Dark'}
            <link href="{$resourcePath}assets/css/themes/dark.css" rel="stylesheet">
        {/if}
        {if $activeLanguage->isRTL()}
            <link href="{$resourcePath}assets/css/custom.rtl.css" rel="stylesheet">
        {else}
            <link href="{$resourcePath}assets/css/custom.css" rel="stylesheet">
        {/if}
        <link rel="shortcut icon" type="image/png" sizes="32x32" href="{$siteViewOptions->getOptionValue("favIconFilePath")}">
    </head>
    <body class="fix-header fix-sidebar card-no-border">
        <div id="main-wrapper">
            {include 'admin/general/header.tpl'}
            {include 'admin/general/leftSidebar.tpl'}
            <div class="page-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="page-title pull-left">
                                <h3 class="text-info mb-4">{block name=title}{t}No Name{/t}{/block}</h3>
                            </div>
                            <div class="head-controls pull-right">
                                {block name=toolbar}{/block}
                            </div>
                        </div>
                    </div>
                    {include 'admin/errors.tpl'}
                    {block name=content}{/block}
                </div>
                {include 'admin/general/footer.tpl'}
            </div>
        </div>
        {block name=footerImportantJs}
            <script src="{$resourcePath}assets/js/plugins/jquery/jquery-3.2.1.min.js"></script>
            <script src="{$resourcePath}assets/js/plugins/bootstrap/popper.min.js"></script>
            <script src="{$resourcePath}assets/js/plugins/bootstrap/bootstrap.min.js"></script>
            <script src="{$resourcePath}assets/js/plugins/malihu-custom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
            <script src="{$resourcePath}assets/js/plugins/noty/noty.min.js"></script>
            <script src="{$resourcePath}assets/js/plugins/tooltipster/jquery.tooltipster.min.js"></script>
            <script src="{$resourcePath}assets/js/plugins/metismenu/metisMenu.min.js"></script>
            <script src="{$resourcePath}assets/js/plugins/sticky-kit/sticky-kit.min.js"></script>
        {/block}
        {block name=footerPageJs}{/block}
        {block name=footerAPPJs}
            <script src="{$resourcePath}assets/js/main.js"></script>
        {/block}
        {block name=footerCustomJs}
            {if isset($user) and $user->getRole() != null and $user->getRole()->getPriority() >= 200}
                <script>
                    $(document).click(function (e) {
                        var elem = $(".header-search-input");
                        if (!elem.is(e.target) && elem.has(e.target).length === 0 && $('.header-search-input:focus').length === 0) {
                            $(".header-search-results").hide();
                        }
                    });
                    $('.header-search-input').on('focus', function () {
                        if ($('.header-search-results li').length > 0) {
                            $('.header-search-results').show();
                        }
                    });
                    $(document).on('click', 'li.user', function (e) {
                        e.preventDefault();
                        window.location.href = $(this).attr('data-url');
                    });
                    $(document).on('click', 'li.book', function (e) {
                        e.preventDefault();
                        window.location.href = $(this).attr('data-url');
                    });
                    $('#search-book').on('click', function (e) {
                        e.preventDefault();
                        var url, form = $(this).closest('form');
                        var searchText = $('.header-search-input').val();
                        var resultList = $('.header-search-results');
                        var searchBy = $('#searchBy').val();
                        if (searchBy == 'books') {
                            url = '{$routes->getRouteString("bookSearch",[])}'
                        } else {
                            url = '{$routes->getRouteString("userSearch",[])}'
                        }
                        $.ajax({
                            type: "POST",
                            url: url,
                            data: {
                                searchText: searchText
                            },
                            dataType: 'json',
                            beforeSend: function () {
                                $('.header-search-icon').removeClass('ti-search').addClass('fas fa-spinner fa-spin');
                                $(resultList).hide().mCustomScrollbar("destroy");
                            },
                            success: function (data) {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    if (data.error) {
                                        app.notification('error', data.error);
                                    } else {
                                        $(resultList).empty().show();
                                        if (searchBy === 'books') {
                                            $.each(data.books, function (index, item) {
                                                $(resultList).append(bookTemplate(item));
                                            });
                                        } else {
                                            $.each(data, function (index, item) {
                                                $(resultList).append(userTemplate(item));
                                            });
                                        }
                                        $(resultList).mCustomScrollbar({
                                            advanced: {
                                                updateOnContentResize: true,
                                                updateOnImageLoad: true
                                            },
                                            theme: "minimal"
                                        });
                                    }
                                }
                            },
                            complete: function () {
                                $('.header-search-icon').removeClass('fas fa-spinner fa-spin').addClass('ti-search');
                            },
                            error: function (jqXHR, exception) {
                                app.notification('error', app.getErrorMessage(jqXHR, exception));
                            }
                        });
                    });
                    function userTemplate(user) {
                        var url = '{$routes->getRouteString("userEdit")}';
                        var template = '<li class="flex-row d-flex user" data-url="' + url.replace("[userId]", user.id) + '">';
                        template += '<div class="user-meta">';
                        template += '<h4><strong>#' + user.id + '</strong> ' + user.firstName + ' ' + user.lastName + ' ';
                        if (user.role) {
                            template += '<span>(' + user.role.name + ')</span>';
                        }
                        template += '</h4>';
                        template += "<div><strong>{t}Email{/t}:</strong> " + user.email + "</div>";
                        template += '</div>';
                        template += '</li>';
                        return template;
                    }
                    function bookTemplate(book) {
                        var url = '{$routes->getRouteString("bookEdit")}';
                        var i, lastIndex, template = '<li class="flex-row d-flex book" data-url="' + url.replace("[bookId]", book.id) + '">';
                        template += '<div class="book-cover">';
                        if (book.cover) {
                            template += '<img src="' + book.cover.webPath + '" class="img-fluid">';
                        } else {
                            template += '<img src="{$siteViewOptions->getOptionValue("noBookImageFilePath")}" class="img-fluid">';
                        }
                        template += '</div>';
                        template += '<div class="book-meta">';
                        template += '<h4>' + book.title + '';
                        if (book.publishingYear) {
                            template += ' <span>(' + book.publishingYear + ')</span>';
                        }
                        template += '</h4>';

                        if (book.publisher) {
                            template += "<div><strong>{t}Publisher:{/t}</strong> " + book.publisher.name + "</div>";
                        }
                        if (book.ISBN13) {
                            template += "<div><strong>{t}ISBN13:{/t}</strong> " + book.ISBN13 + "</div>";
                        } else if (book.ISBN10) {
                            template += "<div><strong>{t}ISBN10:{/t}</strong> " + book.ISBN10 + "</div>";
                        }
                        if (book.genres != null && book.genres.length > 0) {
                            template += "<div><strong>{t}Genres:{/t}</strong> ";
                            lastIndex = book.genres.length - 1;
                            for (i = 0; i < book.genres.length; i++) {
                                template += book.genres[i].name;
                                if (lastIndex != i) {
                                    template += ", ";
                                }
                            }
                            template += "</div>";
                        }
                        if (book.authors != null && book.authors.length > 0) {
                            template += "<div><strong>{t}Authors:{/t}</strong> ";
                            lastIndex = book.authors.length - 1;
                            for (i = 0; i < book.authors.length; i++) {
                                if (book.authors[i].firstName) {
                                    var text = book.authors[i].firstName + ' ' + book.authors[i].lastName;
                                } else {
                                    text = book.authors[i].lastName;
                                }
                                template += text;
                                if (lastIndex != i) {
                                    template += ", ";
                                }
                            }
                            template += "</div>";
                        }
                        template += '</div>';
                        template += '</li>';
                        return template;
                    }
                </script>
            {/if}
        {/block}
    </body>
</html>