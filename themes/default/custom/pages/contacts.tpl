{extends file='public.tpl'}
{block name=metaTitle}{if $page->getMetaTitle()}{$page->getMetaTitle()}{else}{$page->getTitle()}{/if}{/block}
{block name=metaDescription}{$page->getMetaDescription()}{/block}
{block name=metaKeywords}{$page->getMetaKeywords()}{/block}
{block name=headerCss append}{/block}
{assign var="pageURL" value="{$SiteURL}{$smarty.server.REQUEST_URI}"}
{block name=socialNetworksMeta}
    <meta property="og:title" content="{if $page->getMetaTitle()}{$page->getMetaTitle()}{else}{$page->getTitle()}{/if}"/>
    <meta property="og:image" content="{$SiteURL}{if $page->getImage() != null}{$page->getImage()->getWebPath('')}{else}{$siteViewOptions->getOptionValue("noImageFilePath")}{/if}"/>
    <meta property="og:description" content="{$page->getMetaDescription()|truncate:200|replace:'"':''}"/>
    <meta property="og:url" content="{$pageURL}"/>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{if $page->getMetaTitle()}{$page->getMetaTitle()}{else}{$page->getTitle()}{/if}">
    <meta name="twitter:description" content="{$page->getMetaDescription()|truncate:200|replace:'"':''}">
    <meta name="twitter:image:src" content="{$SiteURL}{if $page->getImage() != null}{$page->getImage()->getWebPath('')}{else}{$siteViewOptions->getOptionValue("noImageFilePath")}{/if}">
{/block}
{block name=content}
    <section class="page contacts">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>{$page->getTitle()}</h1>
                </div>
                <div class="col-lg-12">
                    {if $page->getContent()}
                        <div class="page-content">
                            {$page->getContent()}
                        </div>
                    {/if}
                    <form id="contact-form" method="post" class="validate mb-3 contact-form">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input class="form-control" placeholder="{t}Email{/t}" type="email" name="email">
                            </div>
                            <div class="col-md-6 mb-3">
                                <input class="form-control" placeholder="{t}Full Name{/t}" type="text" name="name">
                            </div>
                            <div class="col-md-12 mb-3">
                                <input class="form-control" placeholder="{t}Subject{/t}" type="text" name="subject">
                            </div>
                            <div class="col-md-12">
                                <textarea class="form-control mb-3" name="message" rows="3" placeholder="{t}Message{/t}"></textarea>
                                <button type="submit" class="btn btn-primary submit">{t}Send Message{/t}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
{/block}
{block name=footerJs append}
    <script type='text/javascript' src="{$themePath}resources/js/jquery.validate.js"></script>
{/block}
{block name=customJs append}
    <script type="text/javascript">
        var contactFormUrl = '{$routes->getRouteString("userMessageCreate",[])}';
        $(".validate").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            }
        });
        $('.submit').on('click', function (e) {
            e.preventDefault();
            var form = $(this).closest('form');
            if (form.valid()) {
                $.ajax({
                    dataType: 'json',
                    method: 'POST',
                    data: form.serialize(),
                    url: contactFormUrl,
                    beforeSend: function () {
                        $(form).append('<div class="form-message"><i class="fa fa-spinner fa-spin"></i><span class="sr-only">{t}Loading...{/t}</span> {t}Sending, Please Wait..{/t} </div>');
                    },
                    success: function (data) {
                        app.ajax_redirect(data);
                        if (data.error) {
                            $(form).find('.form-message').addClass('error').text('{t}Failed to send your message. Please try later or contact the administrator{/t} {$siteViewOptions->getOptionValue("adminEmail")}');
                        } else {
                            $(form).find('.form-message').addClass('success').text('{t}Your message was sent successfully. Thanks.{/t}');
                            $(form).find('input, textarea').val('');
                        }
                    },
                    error: function (jqXHR, exception) {
                        $(form).find('.form-message').addClass('error').text('{t}Failed to send your message. Please try later or contact the administrator{/t} {$siteViewOptions->getOptionValue("adminEmail")}');
                    },
                    complete: function () {
                        setTimeout(function () {
                            $(form).find('.form-message').fadeOut().remove();
                        }, 10000);
                    }
                });
            }
        });
    </script>
{/block}