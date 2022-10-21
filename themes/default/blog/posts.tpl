{extends file='public.tpl'}
{block name=metaTitle}{if isset($category)}{$category->getMetaTitle()}{else}{$blog->getMetaTitle()}{/if}{foreach from=$pages item=page}{if $page->isCurrent()}{if $page->getTitle() != 1} : Page #{$page->getTitle()}{/if}{/if}{/foreach}{/block}
{block name=metaDescription}{if isset($category)}{$category->getMetaDescription()}{else}{$blog->getMetaDescription()}{/if}{/block}
{block name=metaKeywords}{if isset($category)}{$category->getMetaKeywords()}{else}{$blog->getMetaKeywords()}{/if}{/block}
{block name=content}
    <section class="blog-posts">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-12">
                    {if isset($category)}
                        <h1>{$category->getTitle()}</h1>
                    {else}
                        <h1>{$blog->getSecondTitle()}{foreach from=$pages item=page}{if $page->isCurrent()}{if $page->getTitle() != 1} : Page #{$page->getTitle()}{/if}{/if}{/foreach}</h1>
                    {/if}
                </div>
            </div>
            <div class="row">
                {if isset($posts) and $posts != null}
                    {foreach from=$posts item=post name=post}
                        <div class="col-lg-4 col-md-6">
                            <div class="post">
                                <div class="post-img">
                                    {if $post->getImage() != null}
                                        <a href="{$routes->getRouteString("postViewPublic",["postUrl"=>$post->getUrl()])}">
                                            <img src="{$post->getImage()->getWebPath('small')}" alt="{$post->getTitle()}">
                                        </a>
                                    {/if}
                                </div>
                                <div class="post-info">
                                    <div class="post-meta">
                                        {$post->getPublishDateTime()|date_format:$siteViewOptions->getOptionValue("dateFormat")}{*$post->getUser()->getFirstName()} {$post->getUser()->getLastName()*}
                                    </div>
                                    <a href="{$routes->getRouteString("postViewPublic",["postUrl"=>$post->getUrl()])}" class="post-title">
                                        <h4>{$post->getTitle()}</h4>
                                    </a>
                                    <div class="post-short-description">{if $post->getShortDescription() != null}{$post->getShortDescription()}{else}{$post->getContent()|strip_tags:true|strip|truncate:250}{/if}</div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {/if}
            </div>
            <div class="row mt-3">
                <div class="col-lg-12">
                    {include "general/pagination.tpl"}
                </div>
            </div>
        </div>
    </section>
{/block}