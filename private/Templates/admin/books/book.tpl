{extends file='admin/admin.tpl'}
{block name=title}{if $action == "create"}{t}Add Book{/t}{else}{t}Edit Book{/t}{/if}{/block}
{block name=toolbar}
    {if $action == "edit" and isset($book)}
        <div class="heading-elements">
            <a href="{$routes->getRouteString("bookClone",["bookId"=>$book->getId()])}" class="btn btn-outline-info btn-sm btn-icon-fixed clone-this-book">
                <span class="btn-icon"><i class="far fa-clone"></i></span> {t}Clone{/t}
            </a>
        </div>
    {/if}
{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
    <link href="{$resourcePath}assets/js/plugins/summernote/summernote-bs4.css" rel="stylesheet"/>
{/block}
{block name=content}
    {if $action == "create"}
        {assign var=route value=$routes->getRouteString("bookCreate")}
    {elseif $action == "edit" and isset($book)}
        {assign var=route value=$routes->getRouteString("bookEdit",["bookId"=>$book->getId()])}
    {elseif $action == "delete"}
        {assign var=route value=""}
    {/if}
    <div class="modal fade" id="book-search-by-isbn-modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="modal-body card result-books-by-isbn mb-0">
                    <div id="result-books-by-isbn"></div>
                </div>
            </div>
        </div>
    </div>
    {if isset($bookLayoutSettings) and $bookLayoutSettings->getLayoutContainers() != null}
        {assign var=tempBookVisibleFieldList value=$bookVisibleFieldList}
        {foreach from=$bookLayoutSettings->getLayoutContainers() item=container name=container}
            {if $container->getElements() != null}
                {foreach from=$container->getElements() item=element name=element}
                    {unset array=tempBookVisibleFieldList index=$element->getName()}
                {/foreach}
            {/if}
        {/foreach}
    {/if}
    
    <form action="{$route}" method="post" class="book-form validate" data-edit="{if $action == "create"}false{else}true{/if}">
        <ul class="nav nav-tabs special-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#general" role="tab">
                    {t}General{/t}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#images" role="tab">
                    {t}Images{/t}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#seo" role="tab">
                    {t}SEO{/t}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#copies" role="tab">
                    {t}Copies{/t}
                </a>
            </li>
            {if $siteViewOptions->getOptionValue("enableBookLogs")}
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#logs" role="tab">
                    {t}Logs{/t}
                </a>
            </li>
            {/if}
        </ul>
        <div class="tab-content">
            <div class="tab-pane card p-20 active" id="general" role="tabpanel" aria-labelledby="general-tab">
                {if isset($bookLayoutSettings) and $bookLayoutSettings->getLayoutContainers() != null}
                    {if $tempBookVisibleFieldList != null}
                        {foreach from=$tempBookVisibleFieldList item=title key=key}
                            {assign var=bookClass value="KAASoft\Database\Entity\General\Book"}
                            {assign var=customField value=$bookClass::getCustomField($key)}
                            {if $customField == null}
                                {include file='admin/books/book-hidden-fields-controls.tpl' control=$key}
                            {else}
                                {include file='admin/books/book-hidden-custom-fields-controls.tpl' control=$customField->getControl() title=$customField->getTitle() name=$customField->getName() listValues=$customField->getListValues()}
                            {/if}
                        {/foreach}
                    {/if}
                    <div class="row">
                        <div class="col-lg-8 col-md-7">
                            {assign var=row value=-1}
                            {if $bookLayoutSettings->getContainer('content')->getElements() != null}
                                {assign var=elements value=$bookLayoutSettings->getContainer('content')->getElements()}
                                {assign var=elementsCount value=count($elements)}
                                {for $index=0 to $elementsCount-1 step 1}
                                    {assign var=element value=$elements[$index]}

                                    {if $element->getY() != $row}
                                        {assign var=row value=$element->getY()}
                                        <div class="row">
                                    {/if}

                                    <div class="col-lg-{$element->getWidth()}">
                                        {assign var=bookClass value="KAASoft\Database\Entity\General\Book"}
                                        {assign var=customField value=$bookClass::getCustomField($element->getName())}
                                        {if $customField == null}
                                            {include file='admin/books/book-fields-controls.tpl' control=$element->getName()}
                                        {else}
                                            {include file='admin/books/book-custom-fields-controls.tpl' control=$customField->getControl() title=$customField->getTitle() name=$customField->getName() listValues=$customField->getListValues()}
                                        {/if}
                                    </div>

                                    {if (isset($elements[$index+1]) and $elements[$index+1]->getY() != $row) or $index+1 == $elementsCount}
                                        </div>
                                    {/if}

                                {/for}
                            {/if}
                        </div>
                        <div class="col-lg-4 col-md-5">
                            {if $bookLayoutSettings->getContainer('sidebar')->getElements() != null}
                                {assign var=elements value=$bookLayoutSettings->getContainer('sidebar')->getElements()}
                                {assign var=elementsCount value=count($elements)}
                                {for $index=0 to $elementsCount-1 step 1}
                                    {assign var=element value=$elements[$index]}

                                    {if $element->getY() != $row}
                                        {assign var=row value=$element->getY()}
                                        <div class="row">
                                    {/if}

                                    <div class="col-lg-{$element->getWidth()*3}">
                                        {assign var=bookClass value="KAASoft\Database\Entity\General\Book"}
                                        {assign var=customField value=$bookClass::getCustomField($element->getName())}
                                        {if $customField == null}
                                            {include file='admin/books/book-fields-controls.tpl' control=$element->getName()}
                                        {else}
                                            {include file='admin/books/book-custom-fields-controls.tpl' control=$customField->getControl() title=$customField->getTitle() name=$customField->getName() listValues=$customField->getListValues()}
                                        {/if}
                                    </div>

                                    {if (isset($elements[$index+1]) and $elements[$index+1]->getY() != $row) or $index+1 == $elementsCount}
                                        </div>
                                    {/if}

                                {/for}
                            {/if}
                        </div>
                    </div>
                {/if}

                <div class="row mt-3">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-secondary disabled pull-right save-book">
                                <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane card" id="images" role="tabpanel" aria-labelledby="images-tab">
                <div class="drop-zone">
                    <label>{t escape=no}Drag & Drop your files or <span>Browse</span>{/t}</label>

                    <input type="file" accept="image/png, image/jpeg, image/gif" id="book-images" class="disabledIt" multiple />
                </div>
                <div class="book-image-list row" id="book-image-list" data-count="{if $action == "edit" and $book->getImages() != null}{count($book->getImages())}{else}0{/if}">
                    {if $action == "edit" and $book->getImages() != null}
                        {foreach from=$book->getImages() item=image name=image}
                            <div class="card book-image col-lg-3 text-center" id="book-img-{$image@index}">
                                <button type="button" class="btn btn-info remove-book-image" data-id="{$image->getId()}"><i class="far fa-trash-alt"></i></button>
                                <div class="book-img-wrapper">
                                    <img src="{$image->getWebPath()}" class="img-fluid">
                                </div>
                                <input type="hidden" name="imageIds[]" value="{$image->getId()}">
                            </div>
                        {/foreach}
                    {/if}
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-secondary disabled pull-right m-3 save-book">
                                <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane card p-20" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="metaTitle">{t}Meta Title{/t}</label>
                            <input type="text" name="metaTitle" class="form-control" value="{if $action == "edit"}{$book->getMetaTitle()}{/if}">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="metaKeywords">{t}Meta Keywords{/t}</label>
                            <select name="metaKeySelect" id="meta-key" class="form-control" multiple>
                                {if $action == "edit"}
                                    {assign var="tagList" value=","|explode:$book->getMetaKeywords()}
                                    {foreach from=$tagList item=tag}
                                        {if $tag != null}
                                            <option value="{$tag}" selected>{$tag}</option>
                                        {/if}
                                    {/foreach}
                                {/if}
                            </select>
                            <input type="hidden" name="metaKeywords" id="metaKeyList" value="{if $action == "edit"}{$book->getMetaKeywords()}{/if}">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="metaDescription">{t}Meta Description{/t}</label>
                            <textarea name="metaDescription" cols="30" rows="2" style="width:100% !important" class="form-control">{if $action == "edit"}{$book->getMetaDescription()}{/if}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-secondary disabled pull-right save-book">
                                <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {if $siteViewOptions->getOptionValue("enableBookLogs")}
            <div class="tab-pane card" id="logs" role="tabpanel" aria-labelledby="logs-tab">
                <div class="row">
                    <div class="col-sm-12" id="issueLogList">
                        {if $action == "edit" and $book->getLogs() != null}
                            {include "admin/books/bookIssueLogs.tpl" issueLogs=$book->getLogs()}
                        {else}
                            <div class="card-body text-center">{t}This book has never been requested or issued.{/t}</div>
                        {/if}
                    </div>
                </div>
            </div>
            {/if}
            <div class="tab-pane card" id="copies" role="tabpanel" aria-labelledby="copies-tab">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="copiesList">
                            <div class="table-responsive-sm">
                                <table class="table table-hover table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>{t}Book Copy ID{/t}</th>
                                        <th style="width: 250px;">{t}Book Status{/t}</th>
                                        <th style="width: 155px;" class="text-center">{t}Issue Status{/t}</th>
                                        <th style="width: 70px;"></th>
                                    </tr>
                                    </thead>
                                    {if $action == "edit"}
                                    {assign var=bookCopies value=$book->getBookCopies()}
                                    {assign var=bookCopiesCount value=count($bookCopies)}
                                    {/if}
                                    <tbody class="repeat-container book-copies-container" data-row-length="{if $action == "edit" and $bookCopiesCount != null and $bookCopiesCount > 0}{$bookCopiesCount}{else}0{/if}">
                                    {if $action == "edit" and isset($bookCopies) and $bookCopies != null and $bookCopiesCount > 0}
                                        {foreach from=$bookCopies item=copy name=copy}
                                            <tr class="book-copy" data-id="{$smarty.foreach.copy.index}">
                                                <td>
                                                    <input name="bookCopies[{$smarty.foreach.copy.index}][id]" class="copy-id"  id="book-copy-id-{$smarty.foreach.copy.index}" type="hidden" value="{$copy->getId()}"/>
                                                    <input class="form-control copy-sn" data-sn="{$copy->getBookSN()}" type="text" name="bookCopies[{$smarty.foreach.copy.index}][bookSN]" value="{$copy->getBookSN()}">
                                                </td>
                                                <td>
                                                    {if isset($bookStatuses) and $bookStatuses !== null}
                                                        <select name="bookCopies[{$smarty.foreach.copy.index}][status]" class="form-control custom-select">
                                                            {foreach from=$bookStatuses item=status key=key}
                                                                <option value="{$key}"{if $action == "edit" and $copy->getStatus() == $key} selected{/if}>{$status}</option>
                                                            {/foreach}
                                                        </select>
                                                    {/if}
                                                </td>
                                                <td class="text-center">
                                                    {foreach from=$issueStatuses item=status key=key}
                                                        {if $action == "edit" and $copy->getIssueStatus() == $key}
                                                            <span class="badge {if $copy->getIssueStatus() == 'LOST'}badge-danger{elseif $copy->getIssueStatus() == 'ISSUED'}badge-warning{else}badge-success{/if}">
                                                                {$status}
                                                            </span>
                                                        {/if}
                                                    {/foreach}
                                                </td>
                                                <td class="text-center">
                                                    <div class="dropdown" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                                                        <button class="btn btn-outline-info no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="far fa-trash-alt"></i>
                                                        </button>
                                                        <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                                            <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                                            <li class="divider"></li>
                                                            <li class="text-center">
                                                                <button class="btn btn-outline-danger delete-table-row" data-delete="{$routes->getRouteString("bookCopyDelete",['bookCopyId'=>{$copy->getId()}])}">
                                                                    <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        {/foreach}
                                    {/if}
                                    <tr class="copy-template book-copy" data-id="tempId">
                                        <td>
                                            <input name="bookCopies[tempId][id]" class="copy-id" id="book-copy-id-tempId" type="hidden" value="" disabled>
                                            <input name="bookCopies[tempId][bookSN]" data-sn=""  class="form-control copy-sn" type="text" disabled>
                                        </td>
                                        <td>
                                            {if isset($bookStatuses) and $bookStatuses !== null}
                                                <select name="bookCopies[tempId][status]" class="form-control custom-select" disabled>
                                                    {foreach from=$bookStatuses item=status key=key}
                                                        <option value="{$key}">{$status}</option>
                                                    {/foreach}
                                                </select>
                                            {/if}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-success">{t}Available{/t}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                                                <button class="btn btn-outline-info no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                                <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                                    <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                                    <li class="divider"></li>
                                                    <li class="text-center">
                                                        <button class="btn btn-outline-danger delete-table-row">
                                                            <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-success no-border add-copy" data-container=".book-copies-container" data-trigger="hover" data-toggle="tooltip" data-title="{t}Add Book Copy{/t}">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <button type="submit" class="btn mb-3 mr-3 btn-outline-secondary disabled pull-right save-book">
                                <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/select2/select2.full.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jasnyupload/fileinput.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/inputmask/jquery.inputmask.bundle.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/summernote/summernote-bs4.min.js"></script>
{/block}

{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $('.add-copy').on('click', function (e) {
                e.preventDefault();
                var container = $(this).attr('data-container');
                console.log(container);
                console.log($(container).find('.copy-template'));
                var template = $(container).find('.copy-template');
                var newRow = template.clone();
                var rowLength = $(container).attr('data-row-length');
                var count = parseInt(rowLength) + 1;
                $('input,textarea,select', newRow).each(function () {
                    $.each(this.attributes, function (index, element) {
                        this.value = this.value.replace('tempId', count);
                    });
                });
                newRow.removeClass('copy-template').attr('data-id', count);
                newRow.find('input,textarea,select').removeAttr('disabled');
                newRow.appendTo(container);
                tooltipsterInit();
                bookCopyValidation(newRow.find(".copy-sn"));


                $(container).attr('data-row-length',count);
                app.tooltip_popover();
                return false;
            });

            $(document).on('click', '.delete-table-row', function (e) {
                e.preventDefault();
                var url = $(this).attr('data-delete');
                var row = $(this).closest('tr');
                if (url) {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        url: $(this).attr('data-delete'),
                        beforeSend: function () {
                            app.card.loading.start($("#copies"));
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    row.remove();
                                    app.notification('success', data.success);
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function () {
                            app.card.loading.finish($("#copies"));
                        }
                    });
                } else {
                    row.remove();
                    $('.tooltip.show').remove();
                }
            });


            var bookGoogleSearchByIsbnPublicUrl = '{$routes->getRouteString("bookGoogleSearchByIsbnPublic",[])}';
            var bookByGoogleDataGetUrl = '{$routes->getRouteString("bookByGoogleDataGet",[])}';
            $(document).on('click', '.select-book-by-isbn', function (e) {
                e.preventDefault();
                var googleBookId = $(this).attr('data-id');
                $.ajax({
                    dataType: 'json',
                    method: 'POST',
                    url: bookByGoogleDataGetUrl.replace('[googleBookId]', googleBookId),
                    beforeSend: function (data) {
                        app.card.loading.start('.result-books-by-isbn');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                app.notification('success', data.success);
                                app.notification('success', '{t}Please don\'t forget to save the book{/t}');

                                $('#book-search-by-isbn-modal').modal('hide');
                                if(data.book.title) {
                                    $('.book-field-title').val(data.book.title);
                                }
                                if(data.book.publishingYear) {
                                    $('.book-field-publishing-year').val(data.book.publishingYear);
                                }
                                if(data.book.ISBN10) {
                                    $('.book-field-isbn10').val(data.book.ISBN10);
                                }
                                if(data.book.ISBN13) {
                                    $('.book-field-isbn13').val(data.book.ISBN13);
                                }
                                if(data.book.pages) {
                                    $('.book-field-pages').val(data.book.pages);
                                }
                                if(data.book.description) {
                                    $('#content-box').summernote('code', data.book.description);
                                }
                                if(data.book.language){
                                    $('.book-field-lang').val(data.book.language);
                                }
                                if(data.book.publisher) {
                                    var publisher = new Option(data.book.publisher.name, data.book.publisher.id, true, true);
                                    $('#publisherId').val(null).append(publisher).trigger('change');
                                }
                                if(data.book.authors) {
                                    $('#authors').val(null);
                                    for (var i = 0; i < data.book.authors.length; i++) {
                                        var item = data.book.authors[i];
                                        var insertData = {
                                            id: item.id,
                                            text: item.lastName
                                        };
                                        var author = new Option(insertData.text, insertData.id, false, true);
                                        $('#authors').append(author).trigger('change');
                                    }
                                }
                                if(data.book.coverId && data.cover){
                                    $('.coverId').val(data.book.coverId);
                                    var coverDropzone = $('.cover-drop-zone');
                                    if($(coverDropzone).hasClass('cover-exist')) {
                                        $(coverDropzone).find('img').remove();
                                        $(coverDropzone).append('<img src="' + data.cover.webPath + '" class="img-fluid">');
                                    } else {
                                        $(coverDropzone).addClass('cover-exist').find('.remove-book-cover').removeClass('d-none');
                                        $(coverDropzone).append('<img src="' + data.cover.webPath + '" class="img-fluid">');
                                    }
                                }
                            }
                        }
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    },
                    complete: function (data) {
                        app.card.loading.finish('.result-books-by-isbn');
                    }
                });
            });
            $('.search-by-isbn').on('click', function (e) {
                e.preventDefault();
                var container = $('#result-books-by-isbn');
                if($('.isbn-code-13').val()) {
                $.ajax({
                    dataType: 'json',
                    method: 'POST',
                    data: 'searchText=' + $('.isbn-code-13').val(),
                    url: bookGoogleSearchByIsbnPublicUrl,
                    beforeSend: function (data) {
                        $('#book-search-by-isbn-modal').modal('show');
                        app.card.loading.start('.result-books-by-isbn');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                var books = $.parseJSON(data.books);
                                $(container).mCustomScrollbar('destroy');
                                $(container).html('');
                                if(books.items) {
                                    for (var i = 0; i < books.items.length; i++) {
                                        var item = books.items[i];
                                        $('#result-books-by-isbn').append(formatBook(item));
                                    }
                                } else {
                                    app.notification('information', '{t}Nothing found (Make sure Google API setting is correct){/t}');
                                    $('#book-search-by-isbn-modal').modal('hide');
                                }
                                $(container).mCustomScrollbar({
                                    setHeight: '100%',
                                    axis: "y",
                                    autoHideScrollbar: true,
                                    scrollInertia: 200,
                                    advanced: {
                                        autoScrollOnFocus: false
                                    }
                                });
                            }
                        }
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    },
                    complete: function (data) {
                        app.card.loading.finish('.result-books-by-isbn');
                    }
                });
                } else {
                    app.notification('information', '{t}ISBN is required.{/t}');
                }
            });
            function formatBook(book) {
                if (book.loading) return book.text;
                var markup = "<div class='select-book row'>";
                markup += "<div class='select-book-cover col-lg-3'>";
                if (book.volumeInfo.imageLinks && book.volumeInfo.imageLinks.smallThumbnail) {
                    markup += "<img class='img-fluid' src='" + book.volumeInfo.imageLinks.smallThumbnail + "' />";
                } else {
                    markup += "<img class='img-fluid' src='{$siteViewOptions->getOptionValue("noImagePath")}' />";
                }
                markup += "</div>";
                markup += "<div class='select-book-info col-lg-9'>";
                markup += "<div class='select-book-title'>" + book.volumeInfo.title + "";
                if (book.volumeInfo.publishedDate) {
                    markup += " <span>(" + book.volumeInfo.publishedDate + ")</span>";
                }
                markup += "</div>";
                if (book.volumeInfo.publisher) {
                    markup += "<div class='select-book-publisher'><strong>{t}Publisher:{/t}</strong> " + book.volumeInfo.publisher + "</div>";
                }
                var isbnLength = $(book.volumeInfo.industryIdentifiers).length;
                if (isbnLength > 0) {
                    for (var i = 0; i < isbnLength; i++) {
                        if (book.volumeInfo.industryIdentifiers[i].type == "ISBN_13") {
                            markup += "<div class='select-book-isbn'><strong>{t}ISBN13:{/t}</strong> " + book.volumeInfo.industryIdentifiers[i].identifier + "</div>";
                        }
                        if (book.volumeInfo.industryIdentifiers[i].type == "ISBN_10") {
                            markup += "<div class='select-book-isbn'><strong>{t}ISBN10:{/t}</strong> " + book.volumeInfo.industryIdentifiers[i].identifier + "</div>";
                        }
                    }
                }
                var authorsLength = $(book.volumeInfo.authors).length;
                if (authorsLength > 0) {
                    markup += "<div class='select-book-author'><strong>{t}Authors:{/t}</strong> ";
                    var lastIndex = authorsLength - 1;
                    for (i = 0; i < authorsLength; i++) {
                        markup += book.volumeInfo.authors[i];
                        if (lastIndex != i) {
                            markup += ", ";
                        }
                    }
                    markup += "</div>";
                }
                markup += "</div>";
                markup += "<div class='select-book-link col-lg-12'><a href='#' data-id='" + book.id + "' class='btn btn-info btn-block select-book-by-isbn'>{t}Select Book{/t}</a></div>";
                markup += "</div>";
                return markup;
            }

            $('.drop-zone').on('dragover', function() {
                $(this).addClass('hover');
            });
            $('.drop-zone').on('dragleave', function() {
                $(this).removeClass('hover');
            });
            var coverUploadUrl = '{$routes->getRouteString("coverUpload",[])}';
            var imageUploadUrl = '{$routes->getRouteString("bookImageUpload",[])}';
            var imageDeleteUrl = '{$routes->getRouteString("imageDelete",[])}';
            var eBookUploadUrl = '{$routes->getRouteString("electronicBookUpload",[])}';
            var eBookGetUrl = '{$routes->getRouteString("electronicBookGet",[])}';
            var eBookDeleteUrl = '{$routes->getRouteString("electronicBookDelete",[])}';

            $('#book-images').on('change', bookImagesUpload);
            function bookImagesUpload(event) {
                if (window.File && window.FileReader && window.FileList && window.Blob) {
                    var files = event.target.files;
                    var hiddenImageIdInput;
                    var bookListElement = $('.book-image-list');

                    function upload() {
                        var imgElem = '#book-img-' + index;
                        var imgData = new FormData();
                        imgData.set('file', file);
                        queue.push($.ajax({
                            dataType: 'json',
                            method: 'POST',
                            processData: false,
                            contentType: false,
                            data: imgData,
                            url: imageUploadUrl,
                            beforeSend: function (data) {
                                app.card.loading.start($(imgElem));
                            },
                            success: function (data) {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    if (data.error) {
                                        app.notification('error', data.error);
                                    } else {
                                        hiddenImageIdInput = '<input type="hidden" name="imageIds[]" value="'+data.imageId+'">';
                                        $(imgElem).find('.remove-book-image').attr('data-id', data.imageId);
                                        $(imgElem).append(hiddenImageIdInput);
                                        app.notification('success', '{t}Image successfully uploaded{/t}');
                                    }
                                }
                            },
                            error: function (jqXHR, exception) {
                                app.notification('error', app.getErrorMessage(jqXHR, exception));
                            },
                            complete: function (data) {
                                app.card.loading.finish($(imgElem));
                            }
                        }));
                    }
                    var index = parseInt($(bookListElement).attr('data-count'));
                    for (var i = 0, queue = []; files[i]; i++) {
                        var file = files[i];
                        if ((/^image\/(gif|png|jpeg)$/i).test(file.type)) {
                            var reader = new FileReader();
                            reader.onload = (function(index) {
                                return function(e) {
                                    var tpl = '<div class="card book-image col-lg-3 text-center" id="book-img-' + index + '">' +
                                            '<button type="button" class="btn btn-info remove-book-image"><i class="far fa-trash-alt"></i></button>' +
                                            '<div class="book-img-wrapper"><img class="img-fluid" src="' + e.target.result + '"></div>' +
                                            '<div class="card-loading-layer"><div class="app-spinner loading loading-primary"></div></div>'+
                                            '</div>';
                                    $(bookListElement).append(tpl);
                                };
                            })(index);
                            reader.readAsDataURL(file);
                            upload(index);
                            index++;
                            $(bookListElement).attr('data-count', index);
                        } else {
                            app.notification('error', '{t}Uploaded file is not a valid image. Only JPG, PNG and GIF files are allowed.{/t}');
                        }
                    }
                } else {
                    app.notification('error', '{t}The File APIs are not fully supported in this browser.{/t}');
                }
                $.when.apply($, queue).then(function () {
                    console.log(queue);
                    console.log(queue.length);
                    $('.drop-zone').removeClass('hover');
                    if(queue.length > 0) {
                        $('.book-form').attr('data-changed', true);
                        saveBook();
                    }
                });
            }
            $(document).on('click', '.remove-book-image', function () {
                $('.book-form').attr('data-changed', true);
                var elem = $(this).closest('.card');
                var imgId = $(this).attr('data-id');
                var imgCount = $('.book-image-list').attr('data-count');
                if (imgId != undefined && imgId != null && imgId > 0) {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        url: imageDeleteUrl.replace("[imageId]", imgId),
                        beforeSend: function (data) {
                            app.card.loading.start(elem);
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $(elem).remove();
                                    $('.book-image-list').attr('data-count', imgCount - 1);
                                    saveBook();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish(elem);
                        }
                    });
                }
            });

            $('#book-cover').on('change', bookCoverUpload);
            function bookCoverUpload(event) {
                var dropZone = $('.cover-drop-zone');
                function upload() {
                    var coverId = $('.coverId').val();
                    var imgData = new FormData();
                    imgData.set('file', file);
                    if (coverId) {
                        imgData.set('coverId', coverId);
                    }
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        data: imgData,
                        url: coverUploadUrl,
                        beforeSend: function (data) {
                            app.card.loading.start('#cover-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $('.remove-book-cover').attr('data-id', data.imageId);
                                    $('.coverId').val(data.imageId);
                                    app.notification('success', '{t}Cover successfully uploaded{/t}');
                                    saveBook();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#cover-block');
                        }
                    });
                }
                if (window.File && window.FileReader && window.FileList && window.Blob) {
                    var file = event.target.files[0];
                    if ((/^image\/(gif|png|jpeg)$/i).test(file.type)) {
                        var reader = new FileReader();
                        reader.readAsDataURL(file);
                        reader.onload = function (e) {
                            var img = '<img src="' + e.target.result + '" class="img-fluid">';
                            $(dropZone).find('img').remove();
                            $(dropZone).addClass('cover-exist').append(img);
                            $('.remove-book-cover').removeClass('d-none');
                            upload();
                        };
                    } else {
                        app.notification('error', '{t}Uploaded file is not a valid image. Only JPG, PNG and GIF files are allowed.{/t}');
                    }
                } else {
                    app.notification('error', '{t}The File APIs are not fully supported in this browser.{/t}');
                }
            }
            $(document).on('click', '.remove-book-cover', function () {
                $('.book-form').attr('data-changed', true);
                var imgId = $(this).attr('data-id');
                if (imgId != undefined && imgId != null && imgId > 0) {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        url: imageDeleteUrl.replace("[imageId]", imgId),
                        beforeSend: function (data) {
                            app.card.loading.start('#cover-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $('.cover-drop-zone').removeClass('cover-exist').find('img').remove();
                                    $('.remove-book-cover').addClass('d-none');
                                    $('.coverId').val('');
                                    app.notification('success', data.success);
                                    saveBook();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#cover-block');
                        }
                    });
                }
            });

            function basename(path) {
                return path.replace(/\\/g,'/').replace( /.*\//, '' );
            }
            $('#book-eBook').on('change', bookEBookUpload);
            function bookEBookUpload(event) {
                function upload() {
                    var eBookId = $('.eBookId').val();
                    var eBookData = new FormData();
                    eBookData.set('file', file);
                    if (eBookId) {
                        eBookData.set('eBookId', eBookId);
                    }
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        data: eBookData,
                        url: eBookUploadUrl,
                        beforeSend: function (data) {
                            app.card.loading.start('#eBook-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $('.remove-book-eBook').attr('data-id', data.eBookId).removeClass('d-none');
                                    $('.eBookId').val(data.eBookId);
                                    $('.download-eBook').removeClass('d-none').attr('href', eBookGetUrl.replace('[electronicBookId]', data.eBookId));
                                    $('.eBook-drop-zone .filename').text(basename(data.path));
                                    $('.eBook-drop-zone').addClass('eBook-exist');
                                    app.notification('success', '{t}eBook successfully uploaded{/t}');
                                    saveBook();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#eBook-block');
                        }
                    });
                }
                if (window.File && window.FileReader && window.FileList && window.Blob) {
                    var file = event.target.files[0];
                    upload();
                } else {
                    app.notification('error', '{t}The File APIs are not fully supported in this browser.{/t}');
                }
            }
            $(document).on('click', '.remove-book-eBook', function () {
                $('.book-form').attr('data-changed', true);
                var eBookId = $(this).attr('data-id');
                if (eBookId != undefined && eBookId != null && eBookId > 0) {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        url: eBookDeleteUrl.replace("[electronicBookId]", eBookId),
                        beforeSend: function (data) {
                            app.card.loading.start('#eBook-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $('.eBook-drop-zone').removeClass('eBook-exist').find('.filename').text('');
                                    $('.remove-book-eBook').addClass('d-none');
                                    $('.eBookId').val('');
                                    $('.download-eBook').addClass('d-none');
                                    app.notification('success', data.success);
                                    saveBook();
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#eBook-block');
                        }
                    });
                }
            });

            {if !$siteViewOptions->getOptionValue("bookUrlType")}
            $('.gen-url').on('click', function (e) {
                e.preventDefault();
                var bookTitle = $('#book-title').val();
                $('#urlPath').val(app.generateSlug(bookTitle));
            });
            {/if}

            $('#content-box').summernote().on('summernote.change', function () {
                $('.book-form').attr('data-changed', true);
                $('.save-book').removeClass('btn-outline-secondary disabled').addClass('btn-outline-success');
            });
            $('.year-picker').datepicker({
                format: "yyyy",
                startView: 2,
                minViewMode: 2,
                maxViewMode: 2,
                keepOpen: true
            });
            $('.isbn-code-10, .isbn-code-13').on('change', function () {
                onlyDigits($(this));
            });
            var genreSearchUrl = '{$routes->getRouteString("genreSearchPublic",[])}';
            $("#genres").select2({
                ajax: {
                    url: genreSearchUrl,
                    dataType: 'json',
                    type: 'POST',
                    data: function (params) {
                        return {
                            searchText: params.term
                        };
                    },
                    processResults: function (data, params) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                return {
                                    results: $.map(data, function (item) {
                                        return {
                                            text: item.name,
                                            id: item.id,
                                            term: params.term
                                        }
                                    })
                                };
                            }
                        }
                    },
                    cache: false
                },
                templateResult: function (item) {
                    if (item.loading) {
                        return item.text;
                    }
                    return app.markMatch(item.text, item.term);
                },
                minimumInputLength: 2
            });
            var tagSearchUrl = '{$routes->getRouteString("tagSearchPublic",[])}';
            $("#tags").select2({
                ajax: {
                    url: tagSearchUrl,
                    dataType: 'json',
                    type: 'POST',
                    data: function (params) {
                        return {
                            searchText: params.term
                        };
                    },
                    processResults: function (data, params) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {

                                return {
                                    results: $.map(data, function (item) {
                                        return {
                                            text: item.name,
                                            id: item.id,
                                            term: params.term
                                        }
                                    })
                                };
                            }
                        }
                    },
                    cache: false
                },
                templateResult: function (item) {
                    if (item.loading) {
                        return item.text;
                    }
                    return app.markMatch(item.text, item.term);
                },
                minimumInputLength: 2
            });
            var storeSearchUrl = '{$routes->getRouteString("storeSearchPublic",[])}';
            $("#stores").select2({
                ajax: {
                    url: storeSearchUrl,
                    dataType: 'json',
                    type: 'POST',
                    data: function (params) {
                        return {
                            searchText: params.term
                        };
                    },
                    processResults: function (data, params) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                return {
                                    results: $.map(data, function (item) {
                                        return {
                                            text: item.name,
                                            id: item.id,
                                            term: params.term
                                        }
                                    })
                                };
                            }
                        }
                    },
                    cache: false
                },
                templateResult: function (item) {
                    if (item.loading) {
                        return item.text;
                    }
                    return app.markMatch(item.text, item.term);
                },
                minimumInputLength: 2
            });
            var locationSearchUrl = '{$routes->getRouteString("locationSearchPublic",[])}';
            $("#locations").select2({
                ajax: {
                    url: locationSearchUrl,
                    dataType: 'json',
                    type: 'POST',
                    data: function (params) {
                        var datas = $("#stores").serialize() + '&searchText=' + params.term;
                        return datas;
                    },
                    processResults: function (data, params) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                return {
                                    results: $.map(data, function (item) {
                                        return {
                                            text: item.name + ' (' + item.store.name + ')',
                                            id: item.id,
                                            term: params.term
                                        }
                                    })
                                };
                            }
                        }
                    },
                    cache: false
                },
                templateResult: function (item) {
                    if (item.loading) {
                        return item.text;
                    }
                    return app.markMatch(item.text, item.term);
                },
                minimumInputLength: 2
            });
            var authorSearchUrl = '{$routes->getRouteString("authorSearchPublic",[])}';
            $("#authors").select2({
                ajax: {
                    url: authorSearchUrl,
                    dataType: 'json',
                    type: 'POST',
                    data: function (params) {
                        return {
                            searchText: params.term
                        };
                    },
                    processResults: function (data, params) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                return {
                                    results: $.map(data, function (item) {
                                        if (item.firstName && item.lastName) {
                                            var text = item.firstName + ' ' + item.lastName;
                                        } else if (item.firstName) {
                                            text = item.firstName;
                                        } else if (item.lastName) {
                                            text = item.lastName;
                                        }
                                        return {
                                            text: text,
                                            id: item.id,
                                            term: params.term
                                        }
                                    })
                                };
                            }
                        }
                    },
                    cache: false
                },
                templateResult: function (item) {
                    if (item.loading) {
                        return item.text;
                    }
                    return app.markMatch(item.text, item.term);
                },
                minimumInputLength: 2
            });
            var publisherSearchUrl = '{$routes->getRouteString("publisherSearchPublic",[])}';
            $('#publisherId').select2({
                ajax: {
                    url: function () {
                        return publisherSearchUrl;
                    },
                    dataType: 'json',
                    type: 'POST',
                    data: function (params) {
                        return {
                            searchText: params.term
                        };
                    },
                    processResults: function (data, params) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                return {
                                    results: $.map(data, function (item) {
                                        return {
                                            text: item.name,
                                            id: item.id,
                                            term: params.term
                                        }
                                    })
                                };
                            }
                        }
                    },
                    cache: true
                },
                templateResult: function (item) {
                    if (item.loading) {
                        return item.text;
                    }
                    return app.markMatch(item.text, item.term);
                },
                minimumInputLength: 2
            });
            var seriesSearchUrl = '{$routes->getRouteString("seriesSearchPublic",[])}';
            $('#seriesId').select2({
                ajax: {
                    url: function () {
                        return seriesSearchUrl;
                    },
                    dataType: 'json',
                    type: 'POST',
                    data: function (params) {
                        return {
                            searchText: params.term
                        };
                    },
                    processResults: function (data, params) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                return {
                                    results: $.map(data, function (item) {
                                        return {
                                            text: item.name,
                                            id: item.id,
                                            term: params.term
                                        }
                                    })
                                };
                            }
                        }
                    },
                    cache: true
                },
                templateResult: function (item) {
                    if (item.loading) {
                        return item.text;
                    }
                    return app.markMatch(item.text, item.term);
                },
                minimumInputLength: 2
            });
            $(document).on('click', '.ajax-page', function (e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: $(this).attr('href'),
                    beforeSend: function () {
                        app.card.loading.start('#logs');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $("#issueLogList").html(data.html);
                            }
                        }
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    },
                    complete: function () {
                        app.card.loading.finish('#logs');
                    }
                });
            });
            function tooltipsterInit() {
                $('.validate input:not(disabled),.validate select:not(disabled),.validate textarea:not(disabled)').tooltipster({
                    trigger: 'custom',
                    onlyOne: false,
                    position: 'bottom',
                    offsetY: -5,
                    theme: 'tooltipster-kaa'
                });
            }
            tooltipsterInit();
            $('.validate').validate({
                errorPlacement: function (error, element) {
                    if (element != undefined) {
                        $(element).tooltipster('update', $(error).text());
                        $(element).tooltipster('show');
                    }
                },
                success: function (label, element) {
                    $(element).tooltipster('hide');
                },
                messages: {
                    url: {
                        remote: jQuery.validator.format("<strong>{literal}{0}{/literal}</strong> {t}is already exist. Please use another URL{/t}.")
                    }
                },
                rules: {
                    title: {
                        required: true
                    },
                    pages: {
                        number: true
                    },
                    price: {
                        number: true
                    },
                    publishingYear: {
                        number: true
                    },
                    ISBN10: {
                        digits: true,
                        maxlength: 10,
                        minlength: 10
                    },
                    ISBN13: {
                        digits: true,
                        maxlength: 13,
                        minlength: 13
                    }{if !$siteViewOptions->getOptionValue("bookUrlType")},
                    url: {
                        urlpath: true,
                        remote: {
                            param: {
                                delay: 500,
                                url: '{$routes->getRouteString("bookUrlCheck",[])}',
                                type: "post",
                                data: {
                                    email: function () {
                                        return $("#urlPath").val();
                                    }
                                },
                                error: function (jqXHR, exception) {
                                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                                }
                            },
                            depends: function (element) {
                                return ($(element).val() !== $("#urlPath").attr('data-url'));
                            }
                        }
                    }
                    {/if}
                }
            });

            function bookCopyValidation(elem) {
                $(elem).rules("add", {
                    required:true,
                    remote: {
                        param: function(element) {
                            return {
                                delay: 500,
                                url: '{$routes->getRouteString("bookSNCheck",[])}',
                                type: "post",
                                data: {
                                    bookSN: function () {
                                        return $(element).val();
                                    }
                                },
                                error: function (jqXHR, exception) {
                                    app.notification('error', app.getErrorMessage(jqXHR, exception));
                                }
                            }
                        },
                        depends: function (element) {
                            return ($(element).val() !== $(element).attr('data-sn'));
                        }
                    },
                    messages: {
                        remote: jQuery.validator.format("<strong>{literal}{0}{/literal}</strong> {t}is already exist. Please use another Book Id{/t}.")
                    }
                });
            }
            $('.copy-sn').each(function (index, element) {
                bookCopyValidation(element);
            });

            $('#meta-key').select2({
                multiple: true,
                tags: true,
                allowClear: true,
                language: {
                    noResults: function () {
                        return "{t}Please enter keywords{/t}";
                    }
                }
            }).on('change.select2', function () {
                $("#metaKeyList").val($(this).val());
            });
            $(document).on('change', 'input,textarea,select', function () {
                $(this).closest('form').attr('data-changed', true);
                $('.save-book').removeClass('btn-outline-secondary disabled').addClass('btn-outline-success');
            });

            function onlyDigits(e) {
                var value = $(e).val().replace(/\D/g, '');
                return $(e).val(value);
            }

            var bookEditUrl = '{$routes->getRouteString("bookEdit",[])}';
            var bookCopyDeleteUrl = '{$routes->getRouteString("bookCopyDelete",[])}';
            var cloneBookUrl = '{$routes->getRouteString("bookClone",[])}';
            $('.save-book').on('click', function (e) {
                e.preventDefault();
                saveBook();
            });
            function saveBook() {
                var form = $('.book-form');
                var dataEdit = form.attr('data-edit');
                var dataChanged = form.attr('data-changed');
                if (dataChanged == 'true') {
                    if ($(form).valid()) {
                        $.ajax({
                            dataType: 'json',
                            method: 'POST',
                            data: form.serialize(),
                            url: form.attr('action'),
                            beforeSend: function (data) {
                                app.card.loading.start('.tab-pane');
                            },
                            success: function (data) {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    if (data.error) {
                                        app.notification('error', data.error);
                                    } else {
                                        if(data.bookCopyIds && Object.keys(data.bookCopyIds).length > 0) {
                                            for (var key in data.bookCopyIds) {
                                                $('#book-copy-id-'+key).val(data.bookCopyIds[key]);
                                                $('#book-copy-id-'+key).closest('tr').find('.delete-table-row').attr('data-delete',bookCopyDeleteUrl.replace("[bookCopyId]", data.bookCopyIds[key]));
                                            }
                                        }
                                        form.attr('action', bookEditUrl.replace("[bookId]", data.bookId)).attr('data-changed', false);
                                        $('.clone-this-book').attr('href', cloneBookUrl.replace("[bookId]", data.bookId));
                                        app.notification('success', '{t}Data has been saved successfully{/t}');
                                        $('.save-book').removeClass('btn-outline-success').addClass('btn-outline-secondary disabled');
                                        if (dataEdit == 'false') {
                                            $('.page-title h3').text('{t}Edit Book{/t}');
                                            history.pushState(null, '', bookEditUrl.replace("[bookId]", data.bookId));
                                        }
                                        $('.book-form').attr('data-edit', true);
                                    }
                                }
                            },
                            error: function (jqXHR, exception) {
                                app.notification('error', app.getErrorMessage(jqXHR, exception));
                            },
                            complete: function (data) {
                                app.card.loading.finish('.tab-pane');
                            }
                        });
                    } else {
                        app.notification('information', '{t}Validation errors occurred. Please confirm the fields and submit it again.{/t}');
                    }
                } else {
                    app.notification('information', '{t}Nothing to save.{/t}');
                }
            }
        });
    </script>
{/block}