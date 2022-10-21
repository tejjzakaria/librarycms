{extends file='admin/admin.tpl'}
{block name=title}{t}Dashboard{/t}{/block}
{block name=content}
    {if isset($user) and $user->getRole() != null and $user->getRole()->getPriority() >= 200}
        <div class="row">
            <div class="col-md-6 col-lg-3 col-xlg-3">
                <div class="card">
                    <div class="box bg-info text-center">
                        <h1 class="text-thin text-white">{$bookTotal|number_format:0:".":","}</h1>
                        <h6 class="text-white text-uppercase text-bold mb-0">{t}Total Books{/t}</h6>
                        <span class="text-white text-thin text-uppercase text-sm">&nbsp;</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 col-xlg-3">
                <div class="card">
                    <div class="box bg-primary text-center">
                        <h1 class="text-thin text-white">{$issueCount|number_format:0:".":","}</h1>
                        <h6 class="text-white text-uppercase text-bold mb-0">{t}Issued Books{/t}</h6>
                        <span class="text-white text-thin text-uppercase text-sm">({t}last month{/t})</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 col-xlg-3">
                <div class="card">
                    <div class="box bg-success text-center">
                        <h1 class="text-thin text-white">{$returnCount|number_format:0:".":","}</h1>
                        <h6 class="text-white text-uppercase text-bold mb-0">{t}Returned Books{/t}</h6>
                        <span class="text-white text-thin text-uppercase text-sm">({t}last month{/t})</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 col-xlg-3">
                <div class="card">
                    <div class="box bg-warning text-center">
                        <h1 class="text-thin text-white">{$lostCount|number_format:0:".":","}</h1>
                        <h6 class="text-white text-uppercase text-bold mb-0">{t}Lost Books{/t}</h6>
                        <span class="text-white text-thin text-uppercase text-sm">({t}last month{/t})</span>
                    </div>
                </div>
            </div>
        </div>
    {/if}
    {if isset($lastIssuedBooks) and $lastIssuedBooks != null}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <h4 class="card-title ml-3 mt-3">{t}Last Issued Books{/t}</h4>
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{t}Book{/t}</th>
                                <th>{t}Publisher{/t}</th>
                                <th style="width: 140px;">{t}Publishing Year{/t}</th>
                                <th style="width: 150px;">{t}ISBN{/t}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$lastIssuedBooks item=book name=book}
                                <tr>
                                    <td>
                                        <a href="{$routes->getRouteString("bookEdit",["bookId"=>$book->getId()])}">{$book->getTitle()}</a>
                                    </td>
                                    <td>
                                        {if $book->getPublisher() != null}
                                            {$book->getPublisher()->getName()}
                                        {/if}
                                    </td>
                                    <td>{if $book->getPublishingYear() != null}{$book->getPublishingYear()}{/if}</td>
                                    <td>
                                        {if $book->getISBN13()}
                                            {$book->getISBN13()}
                                        {else}
                                            {$book->getISBN10()}
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    {/if}
    {if isset($lastRequestedBooks) and $lastRequestedBooks != null}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <h4 class="card-title ml-3 mt-3">{t}Last Requested Books{/t}</h4>
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{t}Book{/t}</th>
                                <th>{t}Publisher{/t}</th>
                                <th style="width: 140px;">{t}Publishing Year{/t}</th>
                                <th style="width: 150px;">{t}ISBN{/t}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$lastRequestedBooks item=book name=book}
                                <tr>
                                    <td>
                                        <a href="{$routes->getRouteString("bookEdit",["bookId"=>$book->getId()])}">{$book->getTitle()}</a>
                                    </td>
                                    <td>
                                        {if $book->getPublisher() != null}
                                            {$book->getPublisher()->getName()}
                                        {/if}
                                    </td>
                                    <td>{if $book->getPublishingYear() != null}{$book->getPublishingYear()}{/if}</td>
                                    <td>
                                        {if $book->getISBN13()}
                                            {$book->getISBN13()}
                                        {else}
                                            {$book->getISBN10()}
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    {/if}
{/block}