{extends file='public.tpl'}
{block name=metaTitle}{if $book->getMetaTitle() !== null}{$book->getMetaTitle()}{else}{$book->getTitle()}{/if}{/block}
{block name=metaDescription}{$book->getMetaDescription()|replace:'"':''}{/block}
{block name=metaKeywords}{$book->getMetaKeywords()}{/block}
{block name=headerCss append}
    <link rel="stylesheet" href="{$themePath}resources/css/photoswipe.css">
    <link rel="stylesheet" href="{$themePath}resources/css/photoswipe-skin.css">
{/block}
{assign var="pageURL" value="{$SiteURL}{$smarty.server.REQUEST_URI}"}
{block name=headerPrefix}prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# books: http://ogp.me/ns/books#"{/block}
{block name=socialNetworksMeta append}
    {if $book != null}
        <meta property="og:type" content="books.book"/>
        <meta property="og:title" content="{$book->getTitle()}"/>
        <meta property="og:image" content="{$SiteURL}{if $book->getCover() != null}{$book->getCover()->getWebPath('')}{else}{$siteViewOptions->getOptionValue("noBookImageFilePath")}{/if}"/>
        <meta property="og:description" content="{$book->getDescription()|strip_tags:true|strip|truncate:255}"/>
        <meta property="og:url" content="{$pageURL}"/>
        {if $book->getAuthors() !== null and is_array($book->getAuthors()) and count($book->getAuthors()) > 0}
            {foreach from=$book->getAuthors() item=author name=author}
                <meta property="book:author" content="{$author->getLastName()} {$author->getFirstName()}">
            {/foreach}
        {/if}
        {if $book->getISBN13() != null}
            <meta property="book:isbn" content="{$book->getISBN13()}">
        {elseif $book->getISBN10() != null}
            <meta property="book:isbn" content="{$book->getISBN10()}">
        {/if}
        {if $book->getPublishingYear() != null}
            <meta property="book:release_date" content="{$book->getPublishingYear()}">
        {/if}
    {/if}
{/block}
{block name=content}
    <section class="single-book" data-book="{$book->getId()}" itemscope itemtype="http://schema.org/Book">
        <meta itemprop="url" content="{$smarty.server.REQUEST_URI}"/>
        {if $book->getAuthors() !== null and is_array($book->getAuthors()) and count($book->getAuthors()) > 0}
            {foreach from=$book->getAuthors() item=author name=author}
                <meta itemprop="author" content="{$author->getLastName()} {$author->getFirstName()}"/>
            {/foreach}
        {/if}
        {if $book->getPublisher() != null}
            <meta itemprop="publisher" content="{$book->getPublisher()->getName()}"/>
        {/if}

        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="sticky-left-column">
                        <div class="book-cover">
                            {if $book->getCover() != null}
                                <img src="{$book->getCover()->getWebPath('')}" alt="{$book->getTitle()}" class="img-fluid" itemprop="image">
                            {else}
                                <img src="{$siteViewOptions->getOptionValue("noBookImageFilePath")}" alt="{$book->getTitle()}" class="img-fluid" itemprop="image">
                            {/if}
                            {if isset($user) and $user->getRole() != null and $user->getRole()->getPriority() >= 200}
                                <a href="{$routes->getRouteString("bookEdit",["bookId"=>$book->getId()])}" class="edit-book" title="{t}Edit Book{/t}"><i class="ti-pencil" aria-hidden="true"></i></a>
                            {/if}
                        </div>
                        <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="pswp__bg"></div>
                            <div class="pswp__scroll-wrap">
                                <div class="pswp__container">
                                    <div class="pswp__item"></div>
                                    <div class="pswp__item"></div>
                                    <div class="pswp__item"></div>
                                </div>
                                <div class="pswp__ui pswp__ui--hidden">
                                    <div class="pswp__top-bar">
                                        <div class="pswp__counter"></div>
                                        <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                                        <button class="pswp__button pswp__button--share" title="Share"></button>
                                        <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                                        <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                                        <div class="pswp__preloader">
                                            <div class="pswp__preloader__icn">
                                                <div class="pswp__preloader__cut">
                                                    <div class="pswp__preloader__donut"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                                        <div class="pswp__share-tooltip"></div>
                                    </div>
                                    <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
                                    </button>
                                    <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
                                    </button>
                                    <div class="pswp__caption">
                                        <div class="pswp__caption__center"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {if $bookFieldSettings->getBookFieldOptions('images')->isVisible() and $book->getImages() != null}
                            <div class="book-image-gallery" itemscope itemtype="http://schema.org/ImageGallery">
                                {foreach from=$book->getImages() item=image name=image}
                                    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
                                        <a href="{$image->getWebPath()}" itemprop="contentUrl" data-size="{$image->getWidth()}x{$image->getHeight()}">
                                            <img src="{$image->getWebPath('small')}" itemprop="thumbnail" alt="{$image@index}" />
                                        </a>
                                    </figure>
                                {/foreach}
                            </div>
                        {/if}

                        {if isset($smarty.server.REQUEST_URI) and $siteViewOptions->getOptionValue("showShareLinks")}
                            <div class="social-btns">
                                <a class="btn facebook" href="https://www.facebook.com/share.php?u={$pageURL}&title={$book->getTitle()}" target="blank"><i class="fab fa-facebook-f"></i></a>
                                <a class="btn twitter" href="https://twitter.com/intent/tweet?status={$book->getTitle()}+{$pageURL}" target="blank"><i class="fab fa-twitter"></i></a>
                                <a class="btn google" href="https://plus.google.com/share?url={$pageURL}" target="blank"><i class="fab fa-google"></i></a>
                                <a class="btn vk" href="http://vk.com/share.php?url={$pageURL}" target="blank"><i class="fab fa-vk"></i></a>
                                <a class="btn pinterest" href="http://pinterest.com/pin/create/button/?url={$pageURL}&description={$book->getTitle()}" target="blank"><i class="fab fa-pinterest"></i></a>
                                <a class="btn email" href="mailto:?subject={$book->getTitle()}{if $book->getDescription()}&amp;body={$book->getDescription()|strip_tags:true|strip}{/if}" target="blank"><i class="fas fa-at"></i></a>
                            </div>
                        {/if}

                        <div class="book-links row justify-content-center">
                            {*if $book->getEBookId() != null}
                                {if ($siteViewOptions->getOptionValue("showDownloadLinkToRegisteredOnly") and isset($user) and $user != null) or $siteViewOptions->getOptionValue("showDownloadLink")}
                                    <a href="{$routes->getRouteString("electronicBookGet",["electronicBookId"=>$book->getEBookId()])}" class="col-lg-4 col-sm-4 download-link">
                                        <i class="far fa-hdd ml-1" aria-hidden="true"></i> {t}Download{/t}
                                    </a>
                                {/if}
                            {/if}

                            {if $siteViewOptions->getOptionValue("enableBookRequest")}
                                <a class="{if $book->getEBookId() != null and $siteViewOptions->getOptionValue("showDownloadLink") or $siteViewOptions->getOptionValue("showReadLink") or (($siteViewOptions->getOptionValue("showReadLinkToRegisteredOnly") or $siteViewOptions->getOptionValue("showDownloadLinkToRegisteredOnly")) and isset($user) and $user != null)}col-lg-4 col-sm-4{else}col-lg-12{/if} {if $siteViewOptions->getOptionValue("enableBookIssueByUser")}issue-link{else}request-link{/if}" id="{if $siteViewOptions->getOptionValue("enableBookIssueByUser")}issue-book{else}request-book{/if}" href="#"><i class="ti-book{if $book->getEBookId() != null and $siteViewOptions->getOptionValue("showDownloadLink") or $siteViewOptions->getOptionValue("showReadLink") or (($siteViewOptions->getOptionValue("showReadLinkToRegisteredOnly") or $siteViewOptions->getOptionValue("showDownloadLinkToRegisteredOnly")) and isset($user) and $user != null)}{else} inline{/if}"></i> {if $book->getEBookId() != null and $siteViewOptions->getOptionValue("showDownloadLink") or $siteViewOptions->getOptionValue("showReadLink") or (($siteViewOptions->getOptionValue("showReadLinkToRegisteredOnly") or $siteViewOptions->getOptionValue("showDownloadLinkToRegisteredOnly")) and isset($user) and $user != null)}{if $siteViewOptions->getOptionValue("enableBookIssueByUser")}{t}Issue Book{/t}{else}{t}Request Book{/t}{/if}{else} {t}Book {/t}{/if}</a>
                            {/if}
                            {if $bookFieldSettings->getBookFieldOptions('externalBuyLink')->isVisible() and $book->getExternalBuyLink() != null}
                                <div class="col-lg-12 mt-2">
                                    <a href="{$book->getExternalBuyLink()}" target="_blank"><i class="icon-basket-loaded"></i> {t}Buy This Book{/t}</a>
                                </div>
                            {/if*}

                            {if $book->getEBookId() != null}
                                {if ($siteViewOptions->getOptionValue("showDownloadLinkToRegisteredOnly") and isset($user) and $user != null) or $siteViewOptions->getOptionValue("showDownloadLink")}
                                    <a href="{$routes->getRouteString("electronicBookGet",["electronicBookId"=>$book->getEBookId()])}" class="col-lg-4 col-sm-4 download-link">
                                        <i class="far fa-hdd ml-1" aria-hidden="true"></i> {t}Download{/t}
                                    </a>
                                {/if}
                                {if ($siteViewOptions->getOptionValue("showReadLinkToRegisteredOnly") and isset($user) and $user != null) or $siteViewOptions->getOptionValue("showReadLink")}
                                    <a href="{$routes->getRouteString("electronicBookView",["bookId"=>$book->getId()])}" class="col-lg-4 col-sm-4 read-link">
                                        <i class="fas fa-glasses"></i> {t}Read{/t}
                                    </a>
                                {/if}
                            {/if}
                            {if $siteViewOptions->getOptionValue("enableBookRequest")}
                                <a class="{if $book->getEBookId() != null and $siteViewOptions->getOptionValue("showDownloadLink") or $siteViewOptions->getOptionValue("showReadLink") or (($siteViewOptions->getOptionValue("showReadLinkToRegisteredOnly") or $siteViewOptions->getOptionValue("showDownloadLinkToRegisteredOnly")) and isset($user) and $user != null)}col-lg-4 col-sm-4{else}col-lg-12{/if} {if $siteViewOptions->getOptionValue("enableBookIssueByUser")}issue-link{else}request-link{/if}" id="{if $siteViewOptions->getOptionValue("enableBookIssueByUser")}issue-book{else}request-book{/if}" href="#"><i class="ti-book{if $book->getEBookId() != null and $siteViewOptions->getOptionValue("showDownloadLink") or $siteViewOptions->getOptionValue("showReadLink") or (($siteViewOptions->getOptionValue("showReadLinkToRegisteredOnly") or $siteViewOptions->getOptionValue("showDownloadLinkToRegisteredOnly")) and isset($user) and $user != null)}{else} inline{/if}"></i> {if $book->getEBookId() != null and $siteViewOptions->getOptionValue("showDownloadLink") or $siteViewOptions->getOptionValue("showReadLink") or (($siteViewOptions->getOptionValue("showReadLinkToRegisteredOnly") or $siteViewOptions->getOptionValue("showDownloadLinkToRegisteredOnly")) and isset($user) and $user != null)}{if $siteViewOptions->getOptionValue("enableBookIssueByUser")}{t}Issue Book{/t}{else}{t}Request Book{/t}{/if}{else} {t}Book {/t}{/if}</a>
                            {/if}
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <h1 itemprop="name">{$book->getTitle()}{if $bookFieldSettings->getBookFieldOptions('subtitle')->isVisible() and $book->getSubTitle() != null} {$book->getSubTitle()}{/if}</h1>
                    {if $bookFieldSettings->getBookFieldOptions('publishingYear')->isVisible() and $book->getPublishingYear() != null}
                        <div class="book-year" itemprop="datePublished">{$book->getPublishingYear()}</div>
                    {/if}
                    {if $bookFieldSettings->getBookFieldOptions('rating')->isVisible()}
                    <div class="book-rating general" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                        {include 'books/components/rating.tpl' rating=$book->getRating()}
                        <div class="whole-rating">
                            <span class="average">{$book->getRating()|number_format:2:".":","} {t}Avg rating{/t}</span><span class="separator">—</span><span>{$book->getBookRatingVotesNumber()}</span> {t}Votes{/t}
                        </div>
                        <meta itemprop="ratingValue" content="{$book->getRating()|number_format:2:".":","}"/>
                        <meta itemprop="ratingCount" content="{$book->getBookRatingVotesNumber()}"/>
                    </div>
                    {/if}
                    <table class="table book-meta">
                        <tbody>
                            {if $bookFieldSettings->getBookFieldOptions('series')->isVisible() and $book->getSeries() != null}
                                <tr>
                                    <td>{t}Series:{/t}</td>
                                    <td>
                                        <a href="{$routes->getRouteString("seriesBooksPublic",["seriesId"=>$book->getSeries()->getId()])}">{$book->getSeries()->getName()}</a>
                                    </td>
                                </tr>
                            {/if}
                            {if $bookFieldSettings->getBookFieldOptions('publisher')->isVisible() and $book->getPublisher() != null}
                                <tr>
                                    <td>{t}Publisher:{/t}</td>
                                    <td>
                                        <a href="{$routes->getRouteString("publisherBooksPublic",["publisherId"=>$book->getPublisher()->getId()])}" itemprop="publisher">{$book->getPublisher()->getName()}</a>
                                    </td>
                                </tr>
                            {/if}
                            {if $bookFieldSettings->getBookFieldOptions('genre')->isVisible() and $book->getGenres() !== null and is_array($book->getGenres()) and count($book->getGenres()) > 0}
                                <tr>
                                    <td>{t}Genres:{/t}</td>
                                    <td>
                                        {foreach from=$book->getGenres() item=genre name=genre}
                                            <a href="{$routes->getRouteString("genreBooksPublic",["genreId"=>$genre->getId()])}">{$genre->getName()}</a>{if $smarty.foreach.genre.last}{else},{/if}
                                        {/foreach}
                                    </td>
                                </tr>
                            {/if}
                            {if $bookFieldSettings->getBookFieldOptions('author')->isVisible() and $book->getAuthors() !== null and is_array($book->getAuthors()) and count($book->getAuthors()) > 0}
                                <tr>
                                    <td>{t}Authors:{/t}</td>
                                    <td>
                                        {foreach from=$book->getAuthors() item=author name=author}
                                            <a href="{$routes->getRouteString("authorBooksPublic",["authorId"=>$author->getId()])}" itemprop="author">{$author->getLastName()} {$author->getFirstName()}</a>{if $smarty.foreach.author.last}{else},{/if}
                                        {/foreach}
                                    </td>
                                </tr>
                            {/if}
                            {if $bookFieldSettings->getBookFieldOptions('pages')->isVisible() and $book->getPages() != null}
                                <tr>
                                    <td>{t}Pages:{/t}</td>
                                    <td>
                                        <span itemprop="numberOfPages">{$book->getPages()} {t}pages{/t}</span>
                                    </td>
                                </tr>
                            {/if}
                            {if $bookFieldSettings->getBookFieldOptions('binding')->isVisible() and $book->getBinding() !== null}
                                <tr>
                                    <td>{t}Binding:{/t}</td>
                                    <td>
                                        {$book->getBinding()}
                                    </td>
                                </tr>
                            {/if}
                            {if $bookFieldSettings->getBookFieldOptions('ISBN10')->isVisible() and $book->getISBN10() != null}
                                <tr>
                                    <td>{t}ISBN10:{/t}</td>
                                    <td>{$book->getISBN10()}</td>
                                </tr>
                            {/if}
                            {if $bookFieldSettings->getBookFieldOptions('ISBN13')->isVisible() and $book->getISBN13() != null}
                                <tr>
                                    <td>{t}ISBN13:{/t}</td>
                                    <td>
                                        <span itemprop="isbn">{$book->getISBN13()}</span>
                                    </td>
                                </tr>
                            {/if}
                            {if $bookFieldSettings->getBookFieldOptions('tag')->isVisible() and $book->getTags() !== null and is_array($book->getTags()) and count($book->getTags()) > 0}
                                <tr>
                                    <td>{t}Tags:{/t}</td>
                                    <td>
                                        {foreach from=$book->getTags() item=tag name=tag}
                                            <a href="{$routes->getRouteString("tagBooksPublic",["tagId"=>$tag->getId()])}">{$tag->getName()}</a>{if $smarty.foreach.tag.last}{else},{/if}
                                        {/foreach}
                                    </td>
                                </tr>
                            {/if}
                            {if $bookFieldSettings->getBookFieldOptions('edition')->isVisible() and $book->getEdition() !== null}
                                <tr>
                                    <td>{t}Edition:{/t}</td>
                                    <td>
                                        {$book->getEdition()}
                                    </td>
                                </tr>
                            {/if}
                            {if $bookFieldSettings->getBookFieldOptions('language')->isVisible() and $book->getLanguage() !== null}
                                <tr>
                                    <td>{t}Language:{/t}</td>
                                    <td>
                                        {$book->getLanguage()}
                                    </td>
                                </tr>
                            {/if}
                            {if $bookFieldSettings->getBookFieldOptions('physicalForm')->isVisible() and $book->getPhysicalForm() !== null}
                                <tr>
                                    <td>{t}Physical Form:{/t}</td>
                                    <td>
                                        {$book->getPhysicalForm()}
                                    </td>
                                </tr>
                            {/if}
                            {if $bookFieldSettings->getBookFieldOptions('size')->isVisible() and $book->getSize() !== null}
                                <tr>
                                    <td>{t}Size:{/t}</td>
                                    <td>
                                        {$book->getSize()}
                                    </td>
                                </tr>
                            {/if}
                            {if $bookFieldSettings->getBookFieldOptions('type')->isVisible() and $book->getType() !== null}
                                <tr>
                                    <td>{t}Type:{/t}</td>
                                    <td>
                                        {$book->getType()}
                                    </td>
                                </tr>
                            {/if}
                            {assign var=bookClass value="KAASoft\Database\Entity\General\Book"}
                            {assign var=customFields value=$bookClass::getCustomFields()}
                            {if $customFields != null}
                                {foreach from=$customFields item=customField name=customField}
                                    {if $bookFieldSettings->getBookFieldOptions($customField->getName())->isVisible() and $book->getCustomFieldValue($customField->getName()) !== null}
                                        <tr>
                                            <td>{$customField->getTitle()}:</td>
                                            <td>
                                                {$book->getCustomFieldValue($customField->getName())}
                                            </td>
                                        </tr>
                                    {/if}
                                {/foreach}
                            {/if}
                        </tbody>
                    </table>
                    {if $bookFieldSettings->getBookFieldOptions('description')->isVisible() and $book->getDescription()}
                        <div class="book-description" itemprop="description">
                            {$book->getDescription()}
                        </div>
                    {/if}

                    {if strcmp($siteViewOptions->getOptionValue("reviewCreator"),"Nobody") != 0 or ($book->getReviews() != null and count($book->getReviews()) > 0)}
                        <div class="row mt-5 mb-3">
                            <div class="col-lg-6 col-6">
                                {if ($book->getReviews() != null and count($book->getReviews()) > 0) or ($siteViewOptions->getOptionValue("reviewCreator") == "Everybody" or ($siteViewOptions->getOptionValue("reviewCreator") == "User" and isset($user)))}
                                    <h2>{t}Reviews{/t}</h2>
                                {/if}
                            </div>
                            <div class="col-lg-6 col-6 text-right">
                                {if $siteViewOptions->getOptionValue("reviewCreator") == "Everybody" or ($siteViewOptions->getOptionValue("reviewCreator") == "User" and isset($user))}
                                    <button class="btn btn-primary btn-rounded shadow add-review-collapse" data-toggle="collapse" data-target="#addReview" aria-expanded="false" aria-controls="addReview">{t}Write a review{/t}</button>
                                {/if}
                            </div>
                        </div>
                    {/if}
                    {*t}Review creation is allowed to registered users only.{/t*}
                    {if $siteViewOptions->getOptionValue("reviewCreator") == "Everybody" or ($siteViewOptions->getOptionValue("reviewCreator") == "User" and isset($user))}
                        <form class="add-review validate-review collapse" id="addReview">
                            <div class="row">
                                {if isset($user) and $bookFieldSettings->getBookFieldOptions('rating')->isVisible()}
                                    <div class="col-lg-12 mb-3 {if $activeLanguage->isRTL()}text-right{/if}">
                                        <div class="rate-book">
                                            {t}Rate this book{/t}
                                        </div>
                                        {if isset($userBookRating)}
                                            <div class="book-rating user-rating">
                                                {for $ratingIndex=1 to 5}
                                                    <i class="fas fa-star{if $userBookRating == $ratingIndex} active{/if}" data-value="{$ratingIndex}"></i>
                                                {/for}
                                                <div class="save-rating">{t}saving{/t}...</div>
                                            </div>
                                            <div class="user-mark"> {t}Your mark is {/t}
                                                <strong class="font-weight-bold">{$userBookRating}</strong>.
                                            </div>
                                        {else}
                                            <div class="book-rating user-rating">
                                                <i class="far fa-star" data-value="1"></i>
                                                <i class="far fa-star" data-value="2"></i>
                                                <i class="far fa-star" data-value="3"></i>
                                                <i class="far fa-star" data-value="4"></i>
                                                <i class="far fa-star" data-value="5"></i>
                                                <div class="save-rating">{t}saving{/t}...</div>
                                            </div>
                                            <div class="user-mark off"> {t}Your mark is {/t}
                                                <strong class="font-weight-bold">{$userBookRating}</strong>.
                                            </div>
                                        {/if}
                                    </div>
                                {/if}
                                {if $siteViewOptions->getOptionValue("reviewCreator") == "Everybody" and !isset($user)}
                                    <div class="col-lg-12">
                                        <div class="notes">{t}Required fields are marked *. Your email address will not be published.{/t}</div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <input name="name" class="form-control" placeholder="{t}Name{/t} *" type="text">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <input name="email" class="form-control" placeholder="{t}Email{/t} *" type="text">
                                        </div>
                                    </div>
                                {/if}
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <input type="hidden" name="bookId" value="{$book->getId()}">
                                        <textarea name="text" cols="30" rows="5" class="form-control" placeholder="{t}Review{/t}"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12 text-center">
                                    <button class="btn btn-primary shadow submit-review">{t}Submit{/t}</button>
                                </div>
                            </div>
                        </form>
                    {/if}

                    {if $book->getReviews() != null}
                        <div class="reviews">
                            {foreach from=$book->getReviews() item=review name=review}
                                <div class="review">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <strong class="review-user">
                                                {if $review->getUserId() != null and $review->getUser() != null}
                                                    {$review->getUser()->getFirstName()} {$review->getUser()->getLastName()}
                                                {else}
                                                    {$review->getName()}
                                                {/if}
                                            </strong>
                                            <span class="review-meta">{$review->getCreationDateTime()|date_format:$siteViewOptions->getOptionValue("dateFormat")}</span>
                                        </div>
                                        <div class="col-lg-6">
                                            {if $review->getBookRating() != null}
                                                <div class="review-rating">
                                                    {include 'books/components/rating.tpl' rating=$review->getBookRating() readOnly=true}
                                                </div>
                                            {/if}
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="review-content">
                                                {$review->getText()}
                                            </div>
                                        </div>
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
    <script type='text/javascript' src="{$themePath}resources/js/readmore.min.js"></script>
    <script type='text/javascript' src="{$themePath}resources/js/jquery.validate.js"></script>
    <script type='text/javascript' src="{$themePath}resources/js/photoswipe.min.js"></script>
    <script type='text/javascript' src="{$themePath}resources/js/photoswipe-ui-default.min.js"></script>
{/block}
{block name=customJs append}
    <script>
        var $pswp = $('.pswp')[0];
        var image = [];

        $('.book-image-gallery').each( function() {
            var $gallery = $(this), getItems = function() {
                var items = [];
                $gallery.find('a').each(function() {
                    var $href   = $(this).attr('href'),
                            $size   = $(this).data('size').split('x'),
                            $width  = $size[0],
                            $height = $size[1];

                    var item = {
                        src : $href,
                        w   : $width,
                        h   : $height
                    };

                    items.push(item);
                });
                return items;
            };

            var items = getItems();

            $.each(items, function(index, value) {
                image[index]     = new Image();
                image[index].src = value['src'];
            });

            $gallery.on('click', 'figure', function(event) {
                event.preventDefault();

                var $index = $(this).index();
                var options = {
                    index: $index,
                    bgOpacity: 0.95,
                    showHideOpacity: true,
                    history: false,
                    shareEl: false,
                    zoomEl: true,
                    closeOnScroll: false
                };

                var lightBox = new PhotoSwipe($pswp, PhotoSwipeUI_Default, items, options);
                lightBox.init();
            });
        });

        if ($(".book-image-gallery").length > 0) {
            $(".book-image-gallery").mCustomScrollbar({
                axis: "x",
                autoExpandScrollbar:true,
                autoHideScrollbar: true,
                scrollInertia: 200,
                advanced:{
                    autoExpandHorizontalScroll:true
                }
            });
        }
        {if $siteViewOptions->getOptionValue("enableBookIssueByUser")}
        $('#issue-book').on('click', function (e) {
            e.preventDefault();
            var btnIcon = $(this).find('i');
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: 'bookId={$book->getId()}',
                url: '{$routes->getRouteString("issueCreatePublic")}',
                beforeSend: function () {
                    $(btnIcon).removeClass('ti-book').addClass('fas fa-spinner fa-spin');
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
                    $(btnIcon).removeClass('fas fa-spinner fa-spin').addClass('ti-book');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        {else}
        $('#request-book').on('click', function (e) {
            e.preventDefault();
            var btnIcon = $(this).find('i');
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: 'bookIds[]={$book->getId()}',
                url: '{$routes->getRouteString("requestCreate")}',
                beforeSend: function () {
                    $(btnIcon).removeClass('ti-book').addClass('fas fa-spinner fa-spin');
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
                    $(btnIcon).removeClass('fas fa-spinner fa-spin').addClass('ti-book');
                },
                error: function (jqXHR, exception) {
                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                }
            });
        });
        {/if}

        $(".user-rating i").hover(function () {
            var container = $(this).parent();
            var $this = $(this);
            $this.nextAll('i').removeClass('fas').addClass("far");
            $this.prevUntil("div").removeClass("far").addClass('fas');
            $this.removeClass("far").addClass('fas');
        });
        $(".user-rating i").mouseout(function () {
            var container = $(this).parent();
            var select = $(container).find('.active');
            select.nextAll('i').removeClass('fas').addClass('far');
            select.prevUntil("div").removeClass('far').addClass('fas');
            select.removeClass('far').addClass('fas');
            if (container.find('i.active').length == 0) {
                container.find('i').removeClass('fas').addClass('far');
            }
        });
        $(".user-rating i").click(function () {
            $(this).addClass('active').siblings().removeClass('active');
            $(this).removeClass('far').addClass('fas');
            $(this).prevUntil("").removeClass('far').addClass('fas');
            $(this).nextAll('i').removeClass('fas').addClass('far');

            var starValue = $(this).data('value');
            var stars = $(this).parent().children('i');
            var text = $(this).parent().find('.save-rating');
            var bookId = $('.single-book').data('book');
            var url = '{$routes->getRouteString("bookRatingSet")}';
            url = url.replace('[bookId]', bookId).replace('[rating]', starValue);

            if (bookId = !null) {
                $.ajax({
                    dataType: 'json',
                    method: 'POST',
                    url: url,
                    beforeSend: function () {
                        $(stars).hide();
                        $(text).addClass('on');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            app.ajax_redirect(data.redirect);
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $('.user-mark').removeClass('off');
                                $('.user-mark strong').text(starValue);
                                //calculatedRating
                            }
                        }
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', data.error);
                    },
                    complete: function () {
                        $(stars).show();
                        $(text).removeClass('on');
                    }
                });
            }


        });
        {if strcmp($siteViewOptions->getOptionValue("reviewCreator"),"Nobody") != 0}
        $(".validate-review").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                name: {
                    required: true
                }
            }
        });
        var reviewCreatePublicUrl = '{$routes->getRouteString("reviewCreatePublic")}';
        $('.submit-review').on('click', function (e) {
            e.preventDefault();
            var form = $(this).closest('.add-review');
            if (form.valid()) {
                $.ajax({
                    dataType: 'json',
                    method: 'POST',
                    data: $(form).serialize(),
                    url: reviewCreatePublicUrl,
                    beforeSend: function (data) {
                        $(form).after('<div class="form-message"><i class="fa fa-spinner fa-spin"></i><span class="sr-only">{t}Loading...{/t}</span> {t}Sending, Please Wait..{/t} </div>');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            app.ajax_redirect(data.redirect);
                        } else {
                            if (data.error) {
                                $('.form-message').addClass('error').text(data.error);
                            } else {
                                $('.form-message').addClass('success').text(data.success);
                                $(form).find('input, textarea').val('');
                            }
                        }
                    },
                    error: function (jqXHR, exception) {
                        $('.form-message').addClass('error').text('{t}Failed to send your message. Please try later or contact the administrator{/t} {$siteViewOptions->getOptionValue("adminEmail")}');
                    },
                    complete: function (data) {
                        $('#addReview').collapse('hide');
                        setTimeout(function () {
                            $('.form-message').fadeOut().remove();
                        }, 5000);
                    }
                });
            }
        });
        {/if}
        $(window).resize(function () {
            var windowWidth = $(window).width();
            if (windowWidth > 992) {
                stick();
            }
            else {
                unstick();
            }
        });
        function stick() {
            $(".sticky-left-column").sticky({
                topSpacing: 100,
                bottomSpacing: 100,
                zIndex: 999
            });
        }

        function unstick() {
            $(".sticky-left-column").unstick();
        }
        var windowWidth = $(window).width();
        if (windowWidth > 992) {
            stick();
        }
        $('.review-content').readmore({
            speed: 75,
            moreLink: '<a href="#" class="read-more">{t}read more{/t}</a>',
            lessLink: false
        });
    </script>
{/block}