{switch $control}
{case "title"}
    <div class="form-group">
        <label for="name" class="control-label">{t}Title{/t}</label>
        <input type="text" class="form-control book-field-title" id="book-title" autocomplete="off" name="title" value="{if $action == "edit"}{$book->getTitle()}{/if}">
        <input type="hidden" name="rating" value="{if $action == "edit"}{$book->getRating()}{/if}">
    </div>
{/case}
{case "subtitle"}
    <div class="form-group">
        <label for="originalName" class="control-label">{t}Subtitle{/t}</label>
        <input type="text" class="form-control" autocomplete="off" name="subtitle" value="{if $action == "edit"}{$book->getSubtitle()}{/if}">
    </div>
{/case}
{*case "externalBuyLink"}
    <div class="form-group">
        <label for="externalBuyLink" class="control-label">{t}External Buy Link{/t}</label>
        <input type="text" class="form-control" autocomplete="off" name="externalBuyLink" value="{if $action == "edit"}{$book->getExternalBuyLink()}{/if}">
    </div>
{/case}
{case "externalPreview"}
    <div class="form-group">
        <label for="externalPreview" class="control-label">{t}External Preview{/t}</label>
        <input type="text" class="form-control" autocomplete="off" name="externalPreview" value="{if $action == "edit"}{$book->getExternalPreview()}{/if}">
    </div>
{/case*}
{case "url"}
    {if $siteViewOptions->getOptionValue("bookUrlType")}
        <input type="hidden" name="url" autocomplete="off" value="{if $action == "edit"}{$book->getUrl()}{/if}" id="urlPath">
    {else}
        <div class="form-group">
            <label for="url">{t}URL{/t}</label>
            <div class="input-group mb-3">
                <input type="text" name="url" class="form-control" data-url="{if $action == "edit"}{$book->getUrl()}{/if}" autocomplete="off" value="{if $action == "edit"}{$book->getUrl()}{/if}" id="urlPath">
                <div class="input-group-append">
                    <button class="btn btn-outline-default gen-url" type="button"><i class="icon-magic-wand"></i></button>
                </div>
            </div>
        </div>
    {/if}
{/case}
{*case "bookSN"}
    <div class="form-group">
        <label for="bookSN" class="control-label">{t}Book ID{/t}</label>
        <input type="text" class="form-control" autocomplete="off" name="bookSN" value="{if $action == "edit"}{$book->getBookSN()}{/if}">
    </div>
{/case*}
{case "ISBN10"}
    <div class="form-group">
        <label for="ISBN10" class="control-label">{t}ISBN 10{/t}</label>
        <input type="text" class="form-control isbn-code-10 book-field-isbn10" autocomplete="off" name="ISBN10" value="{if $action == "edit"}{$book->getISBN10()}{/if}">
    </div>
{/case}
{case "ISBN13"}
    <div class="form-group">
        <label for="ISBN13" class="control-label">{t}ISBN 13{/t}</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control isbn-code-13 book-field-isbn13" autocomplete="off" name="ISBN13" value="{if $action == "edit"}{$book->getISBN13()}{/if}">
            {if $hasGoogleAPI}
                <div class="input-group-append">
                    <button class="btn btn-outline-default search-by-isbn" type="button"><i class="ti-search"></i></button>
                </div>
            {/if}
        </div>
    </div>
{/case}
{case "series"}
    <div class="form-group">
        <label for="seriesId" class="control-label">{t}Series{/t}</label>
        <select name="seriesId" id="seriesId" class="form-control">
            {if $action == "edit" and $book->getSeries() != null}
                <option value="{$book->getSeries()->getId()}" selected>{$book->getSeries()->getName()}</option>
            {/if}
        </select>
    </div>
{/case}
{case "publisher"}
    <div class="form-group">
        <label for="publisherId" class="control-label">{t}Publisher{/t}</label>
        <select name="publisherId" id="publisherId" class="form-control">
            {if $action == "edit" and $book->getPublisher() != null}
                <option value="{$book->getPublisher()->getId()}" selected>{$book->getPublisher()->getName()}</option>
            {/if}
        </select>
    </div>
{/case}
{case "author"}
    <div class="form-group">
        <label for="authors[]" class="control-label">{t}Authors{/t}</label>
        <select class="form-control" name="authors[]" id="authors" multiple="multiple">
            {if $action == "edit" and $book->getAuthors() !== null and is_array($book->getAuthors())}
                {foreach from=$book->getAuthors() item=author name=author}
                    <option value="{$author->getId()}" selected>{$author->getLastName()} {$author->getFirstName()}</option>
                {/foreach}
            {/if}
        </select>
    </div>
{/case}
{case "genre"}
    <div class="form-group">
        <label for="genres" class="control-label">{t}Genres{/t}</label>
        <select class="form-control" name="genres[]" id="genres" multiple="multiple">
            {if $action == "edit" and $book->getGenres() !== null and is_array($book->getGenres())}
                {foreach from=$book->getGenres() item=genre name=genre}
                    <option value="{$genre->getId()}" selected>{$genre->getName()}</option>
                {/foreach}
            {/if}
        </select>
    </div>
{/case}
{case "tag"}
    <div class="form-group">
        <label for="tags" class="control-label">{t}Tags{/t}</label>
        <select class="form-control" name="tags[]" id="tags" multiple="multiple">
            {if $action == "edit" and $book->getTags() !== null and is_array($book->getTags())}
                {foreach from=$book->getTags() item=tag name=tag}
                    <option value="{$tag->getId()}" selected>{$tag->getName()}</option>
                {/foreach}
            {/if}
        </select>
    </div>
{/case}
{case "edition"}
    <div class="form-group">
        <label for="edition" class="control-label">{t}Edition{/t}</label>
        <input type="text" class="form-control" autocomplete="off" name="edition" value="{if $action == "edit"}{$book->getEdition()}{/if}">
    </div>
{/case}
{case "publishingYear"}
    <div class="form-group">
        <label for="publishingYear" class="control-label">{t}Published Year{/t}</label>
        <input type="text" class="form-control year-picker book-field-publishing-year" autocomplete="off" name="publishingYear" value="{if $action == "edit"}{$book->getPublishingYear()}{/if}">
    </div>
{/case}
{case "pages"}
    <div class="form-group">
        <label for="pages" class="control-label">{t}Pages{/t}</label>
        <input type="text" class="form-control book-field-pages" autocomplete="off" name="pages" value="{if $action == "edit"}{$book->getPages()}{/if}">
    </div>
{/case}
{case "type"}
    <div class="form-group">
        <label for="type" class="control-label">{t}Type{/t}</label>
        {if isset($bookTypes) and $bookTypes !== null}
            <select name="type" class="form-control select-picker">
                {foreach from=$bookTypes item=type name=type}
                    <option value="{$type->getName()}"{if $action == "edit" and $book->getType() == $type->getName()} selected{/if}>{$type->getName()}</option>
                {/foreach}
            </select>
        {/if}
    </div>
{/case}
{case "physicalForm"}
    <div class="form-group">
        <label for="physicalForm" class="control-label">{t}Physical Form{/t}</label>
        {if isset($physicalForms) and $physicalForms !== null}
            <select name="physicalForm" class="form-control select-picker">
                {foreach from=$physicalForms item=form name=form}
                    <option value="{$form->getName()}"{if $action == "edit" and $book->getPhysicalForm() == $form->getName()} selected{/if}>{$form->getName()}</option>
                {/foreach}
            </select>
        {/if}
    </div>
{/case}
{case "size"}
    <div class="form-group">
        <label for="size" class="control-label">{t}Size{/t}</label>
        {if isset($bookSizes) and $bookSizes !== null}
            <select name="size" class="form-control select-picker">
                {foreach from=$bookSizes item=size name=size}
                    <option value="{$size->getName()}"{if $action == "edit" and $book->getSize() == $size->getName()} selected{/if}>{$size->getName()}</option>
                {/foreach}
            </select>
        {/if}
    </div>
{/case}
{case "binding"}
    <div class="form-group">
        <label for="binding" class="control-label">{t}Binding{/t}</label>
        {if isset($bindings) and $bindings !== null}
            <select name="binding" id="bindingId" class="form-control select-picker">
                {foreach from=$bindings item=binding name=binding}
                    <option value="{$binding->getName()}"{if $action == "edit" and $book->getBinding() == $binding->getName()} selected{/if}>{$binding->getName()}</option>
                {/foreach}
            </select>
        {/if}
    </div>
{/case}
{case "store"}
    <div class="form-group">
        <label for="stores" class="control-label">{t}Store{/t}</label>
        <select class="form-control" name="stores[]" id="stores" multiple="multiple">
            {if $action == "edit" and $book->getStores() !== null and is_array($book->getStores())}
                {foreach from=$book->getStores() item=store name=store}
                    <option value="{$store->getId()}" selected>{$store->getName()}</option>
                {/foreach}
            {/if}
        </select>
    </div>
{/case}
{case "location"}
    <div class="form-group">
        <label for="locations" class="control-label">{t}Location{/t}</label>
        <select class="form-control" name="locations[]" id="locations" multiple="multiple">
            {if $action == "edit" and $book->getLocations() !== null and is_array($book->getLocations())}
                {foreach from=$book->getLocations() item=location name=location}
                    <option value="{$location->getId()}" selected>{$location->getName()} [{$location->getStore()->getName()}]</option>
                {/foreach}
            {/if}
        </select>
    </div>
{/case}
{*case "quantity"}
    <div class="form-group">
        <label for="quantity" class="control-label">{t}Quantity{/t}</label>
        <input type="text" class="form-control" autocomplete="off" name="quantity" value="{if $action == "edit"}{$book->getQuantity()}{/if}">
    </div>
{/case*}
{case "price"}
    <div class="form-group">
        <label for="price" class="control-label">{t}Price{/t} </label>
        <span>({$siteViewOptions->getOptionValue("priceCurrency")})</span>
        <input type="text" class="form-control" autocomplete="off" name="price" value="{if $action == "edit"}{$book->getPrice()}{/if}">
    </div>
{/case}
{case "language"}
    <div class="form-group">
        <label for="language" class="control-label">{t}Language{/t}</label>
        <input type="text" class="form-control book-field-lang" autocomplete="off" name="language" value="{if $action == "edit"}{$book->getLanguage()}{/if}">
    </div>
{/case}
{case "description"}
    <div class="form-group">
        <label for="description" class="control-label">{t}Description{/t}</label>
        <textarea type="text" class="form-control" autocomplete="off" name="description" id="content-box">{if $action == "edit"}{$book->getDescription()}{/if}</textarea>
    </div>
{/case}
{case "notes"}
    <div class="form-group">
        <label for="notes" class="control-label">{t}Notes{/t}</label>
        <textarea class="form-control" autocomplete="off" name="notes">{if $action == "edit"}{$book->getNotes()}{/if}</textarea>
    </div>
{/case}
{case "cover"}
    <input type="hidden" name="coverId" class="coverId" value="{if $action == "edit"}{$book->getCoverId()}{/if}">
    <div class="card" id="cover-block">
        <div class="card-body pb-0">
            <div class="drop-zone cover-drop-zone{if $action == "edit" and $book->getCoverId() != null} cover-exist{/if}">
                <label>{t escape=no}Drag & Drop your cover or <span>Browse</span>{/t}</label>
                <input type="file" accept="image/png, image/jpeg, image/gif" id="book-cover" class="disabledIt" />
                <button type="button" class="btn btn-info remove-book-cover{if $action == "edit" and $book->getCoverId() == null or $action == "create"} d-none{/if}" data-id="{if $action == "edit"}{$book->getCoverId()}{/if}"><i class="far fa-trash-alt"></i></button>
                {if $action == "edit" and $book->getCover() != null}
                    <img src="{$book->getCover()->getWebPath()}" class="img-fluid">
                {/if}
            </div>
        </div>
    </div>
{/case}
{case "eBook"}
    <input type="hidden" name="eBookId" class="eBookId" value="{if $action == "edit"}{$book->getEBookId()}{/if}">
    <div class="card" id="eBook-block">
        <div class="card-body pt-0">
            <div class="drop-zone eBook-drop-zone{if $action == "edit" and $book->getEBookId() != null} eBook-exist{/if}">
                <label>{t escape=no}Drag & Drop your eBook or <span>Browse</span>{/t}</label>
                <span class="filename">{if $action == "edit" and $book->getEBook() != null}{basename($book->getEBook()->getWebPath())}{/if}</span>
                <input type="file" id="book-eBook" class="disabledIt" />
                <button type="button" class="btn btn-info remove-book-eBook{if $action == "edit" and $book->getEBookId() == null or $action == "create"} d-none{/if}" data-id="{if $action == "edit"}{$book->getEBookId()}{/if}"><i class="far fa-trash-alt"></i></button>
                <a href="{if $action == "edit" and $book->getEBookId() != null}{$routes->getRouteString("electronicBookGet",["electronicBookId"=>$book->getEBookId()])}{/if}" class="btn btn-info download-eBook{if $action == "edit" and $book->getEBookId() == null or $action == "create"} d-none{/if}"><i class="ti-download"></i></a>
            </div>
        </div>
    </div>
{/case}
{default}
    // Default case is supported.
{/switch}