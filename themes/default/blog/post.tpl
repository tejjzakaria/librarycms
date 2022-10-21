{extends file='public.tpl'}
{block name=metaTitle}{if $post->getMetaTitle() !== null}{$post->getMetaTitle()}{else}{$post->getTitle()}{/if}{/block}
{block name=metaDescription}{$post->getMetaDescription()|replace:'"':''}{/block}
{block name=metaKeywords}{$post->getMetaKeywords()}{/block}
{assign var="pageURL" value="{$SiteURL}{$smarty.server.REQUEST_URI}"}
{block name=socialNetworksMeta}
    <meta property="og:title" content="{if $post->getMetaTitle() !== null}{$post->getMetaTitle()}{else}{$post->getTitle()}{/if}"/>
    <meta property="og:image" content="{$SiteURL}{if $post->getImage() != null}{$post->getImage()->getWebPath('')}{else}{$siteViewOptions->getOptionValue("noImageFilePath")}{/if}"/>
    <meta property="og:description" content="{$post->getMetaDescription()|truncate:200|replace:'"':''}"/>
    <meta property="og:url" content="{$pageURL}"/>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{if $post->getMetaTitle()}{$post->getMetaTitle()}{else}{$post->getTitle()}{/if}">
    <meta name="twitter:description" content="{$post->getMetaDescription()|truncate:200|replace:'"':''}">
    <meta name="twitter:image:src" content="{$SiteURL}{if $post->getImage() != null}{$post->getImage()->getWebPath('')}{else}{$siteViewOptions->getOptionValue("noImageFilePath")}{/if}">
{/block}
{block name=content}
    <section class="single-post">
        {if $post->getImage() != null}
            <div class="post-img text-center" style="background-image: url('{$post->getImage()->getWebPath('')}');"></div>
        {/if}
        <script type="application/ld+json">
            {
              "@context": "http://schema.org",
              "@type": "Article",
              "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "{$pageURL}"
              },
              "headline": "{$post->getTitle()}",
              {if $post->getImage() != null}
              "image": [
                "{$SiteURL}{$post->getImage()->getWebPath('')}"
               ],
               {/if}
              "datePublished": "{$post->getPublishDateTime()}",
              "dateModified": "{$post->getPublishDateTime()}",
              {if $post->getUser() != null}
              "author": {
                "@type": "Person",
                "name": "{$post->getUser()->getFirstName()} {$post->getUser()->getLastName()}"
              },
              {/if}
               "publisher": {
                "@type": "Organization",
                "name": "{$siteViewOptions->getOptionValue("siteName")}",
                "logo": {
                  "@type": "ImageObject",
                  "url": "{$SiteURL}{$siteViewOptions->getOptionValue("logoFilePath")}"
                }
              },
              "description": "{$post->getMetaDescription()|replace:'"':''}"
            }


        </script>

        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="post-content">
                        <h1>{$post->getTitle()}</h1>
                        <div class="post-meta text-center mt-1">
                            {if $post->getUser() != null}<span class="user">
                                <i class="icon-user"></i>
                                {$post->getUser()->getFirstName()} {$post->getUser()->getLastName()}</span>{/if}
                            <span class="categories">
                                {if count($post->getCategories()) > 0}{foreach from=$post->getCategories() item=category name=categories}
                                <a href="{$routes->getRouteString("postListByCategoryViewPublic",["categoryUrl"=>$category->getUrl()])}">
                                    <i class="fa fa-tag" aria-hidden="true"></i>
                                    {$category->getName()}
                                    </a>{if $smarty.foreach.categories.last !== true}, {/if}{/foreach}{/if}
                            </span>
                            <span class="time"><i class="icon-clock"></i> {$post->getPublishDateTime()|date_format:$siteViewOptions->getOptionValue("dateFormat")}</span>
                        </div>
                        {if isset($smarty.server.REQUEST_URI)}
                            <div class="social-btns mt-3">
                                <a class="btn facebook" href="https://www.facebook.com/share.php?u={$pageURL}&title={$post->getTitle()}" target="blank"><i class="fab fa-facebook-f"></i></a>
                                <a class="btn twitter" href="https://twitter.com/intent/tweet?status={$post->getTitle()}+{$pageURL}" target="blank"><i class="fab fa-twitter"></i></a>
                                <a class="btn google" href="https://plus.google.com/share?url={$pageURL}" target="blank"><i class="fab fa-google"></i></a>
                                <a class="btn vk" href="http://vk.com/share.php?url={$pageURL}" target="blank"><i class="fab fa-vk"></i></a>
                                <a class="btn pinterest" href="http://pinterest.com/pin/create/button/?url={$pageURL}&description={$post->getTitle()}" target="blank"><i class="fab fa-pinterest"></i></a>
                                <a class="btn email" href="mailto:?subject={$post->getTitle()}{if $post->getContent()}&amp;body={$post->getContent()|strip_tags:true|strip}{/if}" target="blank"><i class="fas fa-at"></i></a>
                            </div>
                        {/if}
                        <div class="post-text">
                            {if $post->getContent()}
                                {$post->getContent()}
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
{/block}
{block name=footerJs append}
{/block}
{block namecustomJs append}
{/block}
