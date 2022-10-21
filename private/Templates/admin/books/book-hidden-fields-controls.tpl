{switch $control}
{case "title"}
    <input type="hidden" class="book-field-title" id="book-title" autocomplete="off" name="title" value="{if $action == "edit"}{$book->getTitle()}{/if}">
    <input type="hidden" name="rating" value="{if $action == "edit"}{$book->getRating()}{/if}">
{/case}
{case "subtitle"}
    <input type="hidden" name="subtitle" value="{if $action == "edit"}{$book->getSubtitle()}{/if}">
{/case}
{case "url"}
    <input type="hidden" name="url" autocomplete="off" value="{if $action == "edit"}{$book->getUrl()}{/if}" id="urlPath">
{/case}
{case "bookSN"}
    <input type="hidden" name="bookSN" autocomplete="off" value="{if $action == "edit"}{$book->getBookSN()}{/if}">
{/case}
{case "ISBN10"}
    <input type="hidden" class="isbn-code-10 book-field-isbn10" autocomplete="off" name="ISBN10" value="{if $action == "edit"}{$book->getISBN10()}{/if}">
{/case}
{case "ISBN13"}
    <input type="hidden" class="isbn-code-13 book-field-isbn13" autocomplete="off" name="ISBN13" value="{if $action == "edit"}{$book->getISBN13()}{/if}">
{/case}
{case "series"}
    <input type="hidden" name="seriesId" value="{if $action == "edit" and $book->getSeries() != null}{$book->getSeries()->getId()}{/if}">
{/case}
{case "publisher"}
    <input type="hidden" name="publisherId" value="{if $action == "edit" and $book->getPublisher() != null}{$book->getPublisher()->getId()}{/if}">
{/case}
{case "author"}
    {if $action == "edit" and $book->getAuthors() !== null and is_array($book->getAuthors())}
        {foreach from=$book->getAuthors() item=author name=author}
            <input type="hidden" name="authors[]" value="{$author->getId()}">
        {/foreach}
    {else}
        <input type="hidden" name="authors[]" value="">
    {/if}
{/case}
{case "genre"}
    {if $action == "edit" and $book->getGenres() !== null and is_array($book->getGenres())}
        {foreach from=$book->getGenres() item=genre name=genre}
            <input type="hidden" name="genres[]" value="{$genre->getId()}">
        {/foreach}
    {else}
        <input type="hidden" name="genres[]" value="">
    {/if}
{/case}
{case "tag"}
    {if $action == "edit" and $book->getTags() !== null and is_array($book->getTags())}
        {foreach from=$book->getTags() item=tag name=tag}
            <input type="hidden" name="tags[]" value="{$tag->getId()}">
        {/foreach}
    {else}
        <input type="hidden" name="tags[]" value="">
    {/if}
{/case}
{case "edition"}
    <input type="hidden" name="edition" autocomplete="off" value="{if $action == "edit"}{$book->getEdition()}{/if}">
{/case}
{case "publishingYear"}
    <input type="hidden" class="book-field-publishing-year" name="publishingYear" autocomplete="off" value="{if $action == "edit"}{$book->getPublishingYear()}{/if}">
{/case}
{case "pages"}
    <input type="hidden" class="book-field-pages" autocomplete="off" name="pages" value="{if $action == "edit"}{$book->getPages()}{/if}">
{/case}
{case "type"}
    <input type="hidden" autocomplete="off" name="type" value="{if $action == "edit"}{$book->getType()}{/if}">
{/case}
{case "physicalForm"}
    <input type="hidden" autocomplete="off" name="physicalForm" value="{if $action == "edit"}{$book->getPhysicalForm()}{/if}">
{/case}
{case "size"}
    <input type="hidden" autocomplete="off" name="size" value="{if $action == "edit"}{$book->getSize()}{/if}">
{/case}
{case "binding"}
    <input type="hidden" autocomplete="off" name="binding" value="{if $action == "edit"}{$book->getBinding()}{/if}">
{/case}
{case "store"}
    {if $action == "edit" and $book->getStores() !== null and is_array($book->getStores())}
        {foreach from=$book->getStores() item=store name=store}
            <input type="hidden" name="stores[]" value="{$store->getId()}">
        {/foreach}
    {else}
        <input type="hidden" name="stores[]" value="">
    {/if}
{/case}
{case "location"}
    {if $action == "edit" and $book->getLocations() !== null and is_array($book->getLocations())}
        {foreach from=$book->getLocations() item=location name=location}
            <input type="hidden" name="locations[]" value="{$location->getId()}">
        {/foreach}
    {else}
        <input type="hidden" name="locations[]" value="">
    {/if}
{/case}
{case "quantity"}
    <input type="hidden" autocomplete="off" name="quantity" value="{if $action == "edit"}{$book->getQuantity()}{/if}">
{/case}
{case "price"}
    <input type="hidden" class="form-control" autocomplete="off" name="price" value="{if $action == "edit"}{$book->getPrice()}{/if}">
{/case}
{case "language"}
    <input type="hidden" class="book-field-lang" autocomplete="off" name="language" value="{if $action == "edit"}{$book->getLanguage()}{/if}">
{/case}
{case "description"}
    <input type="hidden" autocomplete="off" name="description" value="{if $action == "edit"}{$book->getDescription()}{/if}">
{/case}
{case "notes"}
    <input type="hidden" autocomplete="off" name="notes" value="{if $action == "edit"}{$book->getNotes()}{/if}">
{/case}
{case "cover"}
    <input type="hidden" name="coverId" class="coverId" value="{if $action == "edit"}{$book->getCoverId()}{/if}">
{/case}
{case "eBook"}
    <input type="hidden" name="eBookId" class="eBookId" value="{if $action == "edit"}{$book->getEBookId()}{/if}">
{/case}
{default}
{/switch}