{extends file='admin/admin.tpl'}
{block name=title}{t}View Book{/t}{/block}
{block name=toolbar}{/block}
{block name=headerCss append}{/block}
{block name=content}
    <div class="row">
        <div class="col-lg-8 col-xlg-9 col-md-7">
            <div class="card table-responsive-sm">
                <table class="table view-book">
                    <tbody>
                        <tr class="heading">
                            <td colspan="2">{t}Title{/t}</td>
                            <td colspan="2">{t}Subtitle{/t}</td>
                        </tr>
                        <tr>
                            <td colspan="2">{$book->getTitle()}</td>
                            <td colspan="2">{$book->getSubtitle()}</td>
                        </tr>

                        <tr class="heading">
                            <td colspan="2">{t}ISBN 10{/t}</td>
                            <td colspan="2">{t}ISBN 13{/t}</td>
                        </tr>
                        <tr>
                            <td colspan="2">{$book->getISBN10()}</td>
                            <td colspan="2">{$book->getISBN13()}</td>
                        </tr>

                        <tr class="heading">
                            <td colspan="2">{t}Series{/t}</td>
                            <td colspan="2">{t}Publisher{/t}</td>
                        </tr>
                        <tr>
                            <td colspan="2">{if $book->getSeries() != null}{$book->getSeries()->getName()}{/if}</td>
                            <td colspan="2">{if $book->getPublisher() != null}{$book->getPublisher()->getName()}{/if}</td>
                        </tr>

                        <tr class="heading">
                            <td colspan="4">{t}Authors{/t}</td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                {foreach from=$book->getAuthors() item=author name=author}
                                    {$author->getLastName()} {$author->getFirstName()}
                                {/foreach}
                            </td>
                        </tr>
                        <tr class="heading">
                            <td colspan="4">{t}Genres{/t}</td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                {foreach from=$book->getGenres() item=genre name=genre}
                                    {$genre->getName()}
                                {/foreach}
                            </td>
                        </tr>

                        <tr class="heading">
                            <td>{t}Edition{/t}</td>
                            <td>{t}Published Year{/t}</td>
                            <td>{t}Pages{/t}</td>
                            <td>{t}Language{/t}</td>
                        </tr>
                        <tr>
                            <td>{$book->getEdition()}</td>
                            <td>{$book->getPublishingYear()}</td>
                            <td>{$book->getPages()}</td>
                            <td>{$book->getLanguage()}</td>
                        </tr>

                        <tr class="heading">
                            <td>{t}Type{/t}</td>
                            <td>{t}Physical Form{/t}</td>
                            <td>{t}Size{/t}</td>
                            <td>{t}Binding{/t}</td>
                        </tr>
                        <tr>
                            <td>{$book->getType()}</td>
                            <td>{$book->getPhysicalForm()}</td>
                            <td>{$book->getSize()}</td>
                            <td>{$book->getBinding()}</td>
                        </tr>
                        {if isset($user) and $user->getRole() != null and $user->getRole()->getPriority() > 100}
                            <tr class="heading">
                                <td colspan="4">{t}Stores{/t}</td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    {if $book !== null and $book->getStores() !== null and is_array($book->getStores())}
                                        {foreach from=$book->getStores() item=store name=store}
                                            {$store->getName()}{if $smarty.foreach.store.last}{else},{/if}
                                        {/foreach}
                                    {/if}
                                </td>
                            </tr>
                            <tr class="heading">
                                <td colspan="4">{t}Locations{/t}</td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    {if $book !== null and $book->getLocations() !== null and is_array($book->getLocations())}
                                        {foreach from=$book->getLocations() item=location name=location}
                                            {$location->getName()} [{$location->getStore()->getName()}]{if $smarty.foreach.location.last}{else},{/if}
                                        {/foreach}
                                    {/if}
                                </td>
                            </tr>
                        {/if}
                        <tr class="heading">
                            <td colspan="4">{t}Description{/t}</td>
                        </tr>
                        <tr>
                            <td colspan="4">{$book->getDescription()}</td>
                        </tr>
                        <tr class="heading">
                            <td colspan="4">{t}Notes{/t}</td>
                        </tr>
                        <tr>
                            <td colspan="4">{$book->getNotes()}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-4 col-xlg-3 col-md-5">
            <div class="card" id="cover-block">
                <div class="card-body text-center">
                    {if $book->getCover() != null}
                        <img src="{$book->getCover()->getWebPath()}" class="img-fluid">
                    {else}
                        <img src="{$siteViewOptions->getOptionValue("noBookImageFilePath")}" class="img-fluid">
                    {/if}
                </div>
            </div>

            {if $book->getEBookId() != null}
                <div class="card">
                    <div class="card-body text-center">
                        {if $siteViewOptions->getOptionValue("showDownloadLink") or (isset($user) and $user->getRole() != null and $user->getRole()->getPriority() > 100)}
                            <a href="{$routes->getRouteString("electronicBookGet",["electronicBookId"=>$book->getEBookId()])}" class="btn btn-outline-info btn-block mt-2"><i class="fa fa-download" aria-hidden="true"></i> {t}Download{/t}
                            </a>
                        {/if}
                        <a href="{$routes->getRouteString("electronicBookViewAdmin",["bookId"=>$book->getId()])}" class="btn btn-outline-info btn-block mt-2"><i class="fa fa-eye" aria-hidden="true"></i> {t}Read{/t}
                        </a>
                    </div>
                </div>
            {/if}

        </div>
    </div>
{/block}
{block name=footerPageJs append}{/block}
{block name=footerCustomJs append}{/block}