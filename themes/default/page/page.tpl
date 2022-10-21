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
    <section class="page">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>{$page->getTitle()}</h1>
                </div>
                <div class="col-lg-12">
                    {if $page->getContent()}
                        {if $page->getImage() != null}
                            <div class="text-center mb-3">
                                <img class="img-fluid" src="{$page->getImage()->getWebPath('')}" alt="{$page->getTitle()}">
                            </div>
                        {/if}
                        <div class="page-content">
                            {$page->getContent()}
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </section>
{/block}
{block name=footerJs append}{/block}
{block name=customJs append}{/block}