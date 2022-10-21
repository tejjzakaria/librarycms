{extends file='public.tpl'}
{block name=metaTitle}{if $indexPage != null}{if $indexPage->getMetaTitle() != null}{$indexPage->getMetaTitle()}{else}{$indexPage->getTitle()}{/if}{/if}{/block}
{block name=metaDescription}{if $indexPage != null}{$indexPage->getMetaDescription()}{/if}{/block}
{block name=metaKeywords}{if $indexPage != null}{$indexPage->getMetaKeywords()}{/if}{/block}
{block name=headerCss append}
    <link rel="stylesheet" href="{$themePath}resources/css/owl.carousel.min.css">
    <link rel="stylesheet" href="{$themePath}resources/css/owl.theme.default.min.css">
{/block}
{block name=content}
    {if $siteViewOptions->getOptionValue("showCarousel")}
        {assign var=colorArray value=['green-bg','purple-bg','blue-bg','burgundy-bg','beige-bg','pink-bg']}
        {assign var=colorIndex value=0}
        <section class="home-book-carousel owl-carousel">
            {if isset($topRatedBooks) and $topRatedBooks != null}
                {foreach from=$topRatedBooks item=book name=book}
                    <div class="book {$colorArray[$colorIndex]}">
                        <div class="row">
                            <div class="book-cover col-lg-4 col-4">
                                {if $book->getCover()}
                                    <a href="{if $siteViewOptions->getOptionValue("bookUrlType")}{$routes->getRouteString("bookViewPublic",["bookId"=>$book->getId()])}{else}{$routes->getRouteString("bookViewViaUrlPublic",["bookUrl"=>$book->getUrl()])}{/if}"><img src="{$book->getCover()->getWebPath('small')}" alt="{$book->getTitle()}" class="img-fluid"></a>
                                {else}
                                    <a href="{if $siteViewOptions->getOptionValue("bookUrlType")}{$routes->getRouteString("bookViewPublic",["bookId"=>$book->getId()])}{else}{$routes->getRouteString("bookViewViaUrlPublic",["bookUrl"=>$book->getUrl()])}{/if}"><img src="{$siteViewOptions->getOptionValue("noBookImageFilePath")}" alt="{$book->getTitle()}" class="img-fluid"></a>
                                {/if}
                            </div>
                            <div class="book-info col-lg-8 col-8">
                                <div class="book-title">
                                    <a href="{if $siteViewOptions->getOptionValue("bookUrlType")}{$routes->getRouteString("bookViewPublic",["bookId"=>$book->getId()])}{else}{$routes->getRouteString("bookViewViaUrlPublic",["bookUrl"=>$book->getUrl()])}{/if}">{$book->getTitle()}</a>
                                </div>
                                <div class="book-attr">
                                    {if $book->getPublishingYear() != null}
                                        <span class="book-publishing-year">{$book->getPublishingYear()}{if $book->getAuthors() !== null}, {/if}</span>{/if}
                                    {if $book->getAuthors() !== null and is_array($book->getAuthors()) and count($book->getAuthors()) > 0}
                                        {assign var="bookAuthors" value=$book->getAuthors()}
                                        <span class="book-author">{$bookAuthors[0]->getLastName()} {$bookAuthors[0]->getFirstName()}</span>
                                    {/if}
                                </div>
                                <div class="book-rating">
                                    {include 'books/components/rating.tpl' rating=$book->getRating() readOnly=true}
                                    {*<div class="whole-rating d-inline-block">
                                        <span>{$book->getBookRatingVotesNumber()}</span> {t}Votes{/t}
                                    </div>*}
                                </div>
                                {if $book->getDescription()}
                                    <div class="book-short-description">{$book->getDescription()|strip_tags:true|strip|truncate:150}</div>
                                {/if}
                                <div class="book-settings">
                                    <a href="#"><i class="fa fa-ellipsis-v"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    {assign var=colorIndex value=$colorIndex+1}
                    {if $colorIndex == count($colorArray)}
                        {assign var=colorIndex value=0}
                    {/if}
                {/foreach}
            {/if}
        </section>
    {/if}
    <section class="home-book-list">
        <div class="container">
            <div class="row book-list">
                {*
                <div class="col-sm-12">
                    {if $indexPage != null}
                        {$indexPage->getContent()}
                    {/if}
                </div>
                *}
                <div class="col-lg-9">
                    <div class="row">
                        {if isset($books) and $books != null}
                            <div class="col-lg-12">
                                <div class="section-title">
                                    <h2>{t}Last Books{/t}</h2>
                                </div>
                            </div>
                            {foreach from=$books item=book name=book}
                                <div class="col-lg-6 col-md-6">
                                    <div class="row book">
                                        <div class="book-cover col-lg-3 col-3">
                                            {if $book->getCover()}
                                                <a href="{if $siteViewOptions->getOptionValue("bookUrlType")}{$routes->getRouteString("bookViewPublic",["bookId"=>$book->getId()])}{else}{$routes->getRouteString("bookViewViaUrlPublic",["bookUrl"=>$book->getUrl()])}{/if}"><img src="{$book->getCover()->getWebPath('small')}" alt="{$book->getTitle()}" class="img-fluid"></a>
                                            {else}
                                                <a href="{if $siteViewOptions->getOptionValue("bookUrlType")}{$routes->getRouteString("bookViewPublic",["bookId"=>$book->getId()])}{else}{$routes->getRouteString("bookViewViaUrlPublic",["bookUrl"=>$book->getUrl()])}{/if}"><img src="{$siteViewOptions->getOptionValue("noBookImageFilePath")}" alt="{$book->getTitle()}" class="img-fluid"></a>
                                            {/if}
                                        </div>
                                        <div class="book-info col-lg-9 col-9">
                                            <div class="book-title">
                                                <a href="{if $siteViewOptions->getOptionValue("bookUrlType")}{$routes->getRouteString("bookViewPublic",["bookId"=>$book->getId()])}{else}{$routes->getRouteString("bookViewViaUrlPublic",["bookUrl"=>$book->getUrl()])}{/if}">{$book->getTitle()}</a>
                                            </div>
                                            <div class="book-attr">
                                                {if $book->getPublishingYear() != null}
                                                    <span class="book-publishing-year">{$book->getPublishingYear()}{if $book->getAuthors() !== null}, {/if}</span>{/if}
                                                {if $book->getAuthors() !== null and is_array($book->getAuthors()) and count($book->getAuthors()) > 0}
                                                    {assign var="bookAuthors" value=$book->getAuthors()}
                                                    <span class="book-author">{$bookAuthors[0]->getLastName()} {$bookAuthors[0]->getFirstName()}</span>
                                                {/if}
                                            </div>
                                            <div class="book-rating">
                                                {include 'books/components/rating.tpl' rating=$book->getRating() readOnly=true}
                                                {*<div class="whole-rating d-inline-block">
                                                    <span>({$book->getBookRatingVotesNumber()})</span>
                                                </div>*}
                                            </div>
                                            {if $book->getDescription()}
                                                <div class="book-short-description">{$book->getDescription()|strip_tags:true|strip|truncate:90}</div>
                                            {/if}
                                            <div class="book-settings">
                                                <a href="#"><i class="fa fa-ellipsis-v"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        {/if}
                    </div>
                </div>
                <div class="col-lg-3">
                    {if isset($authors) and $authors != null and count($authors) > 0}
                        <div class="row mini-list-authors">
                            <div class="col-lg-12">
                                <div class="section-title">
                                    <h2>{if strcmp($siteViewOptions->getOptionValue("mainPageAuthorListType"),"TopAuthors")===0}{t}Popular Authors{/t}{else}{t}Random Authors{/t}{/if}</h2>
                                </div>
                            </div>
                            {foreach from=$authors item=author name=author}
                                <div class="col-lg-12 col-md-6 author">
                                    <div class="author-photo">
                                        {if $author->getPhoto() != null}
                                            <a href="{$routes->getRouteString("authorBooksPublic",["authorId"=>$author->getId()])}" class="text-center"><img src="{$author->getPhoto()->getWebPath('medium')}" alt="{$author->getLastName()} {$author->getFirstName()}" class="rounded-circle"></a>
                                        {else}
                                            <a href="{$routes->getRouteString("authorBooksPublic",["authorId"=>$author->getId()])}" class="text-center"><img src="{$siteViewOptions->getOptionValue("noUserImageFilePath")}" alt="{$author->getLastName()} {$author->getFirstName()}" class="rounded-circle"></a>
                                        {/if}
                                        {*<a href="{$routes->getRouteString("authorBooksPublic",["authorId"=>$author->getId()])}"><img src="{$themePath}resources/img/people.png" alt="{$author->getFirstName()} {$author->getLastName()}" class="rounded-circle"></a>*}
                                    </div>
                                    <div class="author-info">
                                        <div class="author-name">
                                            <a href="{$routes->getRouteString("authorBooksPublic",["authorId"=>$author->getId()])}">{$author->getFirstName()} {$author->getLastName()}</a>
                                        </div>
                                        <div class="author-books">{$author->getBookCount()} {t}books{/t}</div>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    {/if}
                    {if isset($posts) and $posts != null}
                        <div class="row mini-list-posts">
                            <div class="col-lg-12">
                                <div class="section-title">
                                    <h2>{t}Last News{/t}</h2>
                                </div>
                            </div>
                            {foreach from=$posts item=post name=post}
                                <div class="col-lg-12 col-md-6 post">
                                    {if $post->getImage() != null}
                                        <a href="{$routes->getRouteString("postViewPublic",["postUrl"=>$post->getUrl()])}">
                                            <img src="{$post->getImage()->getWebPath('small')}" alt="{$post->getTitle()}" class="img-fluid">
                                        </a>
                                    {/if}
                                    <div class="post-info">
                                        <div class="post-meta">
                                            {$post->getPublishDateTime()|date_format:$siteViewOptions->getOptionValue("dateFormat")}
                                        </div>
                                        <a href="{$routes->getRouteString("postViewPublic",["postUrl"=>$post->getUrl()])}" class="post-title">
                                            {$post->getTitle()}
                                        </a>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </section>
{/block}
{block name=footerJs append}
    <script src="{$themePath}resources/js/owl.carousel.min.js"></script>
{/block}
{block name=customJs append}
    {if $siteViewOptions->getOptionValue("showCarousel")}
        <script>
            $('.home-book-carousel').owlCarousel({
                center: true,
                items: 3,
                loop: true,
                margin: 0,
                merge: true,
                {if $activeLanguage->isRTL()}rtl:true,{/if}
                responsive: {
                    2600: {
                        items: 6
                    },
                    2000: {
                        items: 5
                    },
                    1800: {
                        items: 4
                    },
                    1200: {
                        items: 3
                    },
                    600: {
                        items: 2
                    },
                    0: {
                        items: 1
                    }
                }
            });
        </script>
    {/if}
{/block}