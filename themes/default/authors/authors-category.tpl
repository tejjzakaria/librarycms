{extends file='public.tpl'}
{block name=metaTitle}{$author->getFirstName()} {$author->getLastName()}{/block}
{block name=metaDescription}{/block}
{block name=metaKeywords}{/block}
{block name=headerCss append}{/block}
{block name=content}
    <section class="single-author">
        <div class="container">
            <div class="row author">
                <div class="col-lg-12">
                    <div class="author-photo m-auto">
                        {if $author->getPhoto() != null}
                            <img src="{$author->getPhoto()->getWebPath('medium')}" alt="{$author->getLastName()} {$author->getFirstName()}" class="img-fluid">
                        {else}
                            <img src="{$siteViewOptions->getOptionValue("noBookImageFilePath")}" alt="{$author->getLastName()} {$author->getFirstName()}" class="img-fluid">
                        {/if}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="author-info">
                        <h1>{$author->getFirstName()} {$author->getLastName()}</h1>
                        <div class="description-author more">
                            {if $author->getDescription() != null}
                                {$author->getDescription()}
                            {else}
                                <div class="text-center">{t}Information about the author{/t} {$author->getFirstName()} {$author->getLastName()} {t}will soon be added to the site.{/t}</div>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 books-listing">
                    <div class="books-list">
                        {include 'books/components/books-by-category-list.tpl'}
                    </div>
                </div>
            </div>
        </div>
    </section>
{/block}
{block name=footerJs append}
    <script src="{$themePath}resources/js/readmore.min.js"></script>
{/block}
{block name=customJs append}
    <script>
        $('.description-author').readmore({
            speed: 75,
            moreLink: '<a href="#">{t}read more ...{/t}</a>',
            lessLink: '<a href="#">{t}read less{/t}</a>'
        });

        $(document).on('change', '#countPerPage', function (e) {
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: $('#book-filter, #books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                beforeSend: function () {
                    app.preloader.start('.books-list');
                },
                success: function (data) {
                    app.ajax_redirect(data);
                    if (data.error) {
                        app.notification('error', data.error);
                    } else {
                        $('.books-list').html(data.html);
                    }
                },
                complete: function () {
                    app.preloader.finish('.books-list');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('change', '#books-sort', function (e) {
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: $('#book-filter, #books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                beforeSend: function () {
                    app.preloader.start('.books-list');
                },
                success: function (data) {
                    app.ajax_redirect(data);
                    if (data.error) {
                        app.notification('error', data.error);
                    } else {
                        $('.books-list').html(data.html);
                    }
                },
                complete: function () {
                    app.preloader.finish('.books-list');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        $(document).on('click', '.ajax-page', function (e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: $('#book-filter, #books-sort, #countPerPage').serialize() + '&sortOrder=' + $('option:selected', '#books-sort').attr('data-order'),
                url: $(this).attr('href'),
                beforeSend: function () {
                    app.preloader.start('.books-list');
                },
                success: function (data) {
                    app.ajax_redirect(data);
                    if (data.error) {
                        app.notification('error', data.error);
                    } else {
                        $('.books-list').html(data.html);
                    }
                },
                complete: function () {
                    app.preloader.finish('.books-list');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
    </script>
{/block}