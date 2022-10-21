{extends file='admin/admin.tpl'}
{block name=title}{if $action == "create"}{t}Add Menu{/t}{else}{t}Edit Menu{/t}{/if}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
    <link href="{$resourcePath}assets/js/plugins/nestedSortable/jquery.nested-sortable.css" rel="stylesheet"/>
    <link href="{$resourcePath}assets/js/plugins/draganddrop/draganddrop.css" rel="stylesheet"/>
{/block}
{block name=content}
    <div class="row">
        <div class="col-lg-12">
            <div class="card main-card">
                <div class="card-body">
                    {if $action == "create"}
                        {assign var=route value=$routes->getRouteString("menuCreate")}
                    {elseif $action == "edit" and isset($post)}
                        {assign var=route value=$routes->getRouteString("menuEdit",["menuId"=>$menu->getId()])}
                    {elseif $action == "delete"}
                        {assign var=route value=""}
                    {/if}
                    <form action="{$route}" class="menu-name-form" method="post" data-edit="{if $action == "create"}false{else}true{/if}">
                        <div class="form-group">
                            <label for="name">{t}Menu Name{/t}</label>
                            <input class="form-control" type="text" id="menu-name" name="name" value="{if $action == "edit"}{$menu->getName()}{/if}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card main-card">
                <div class="card-header">
                    <button type="button" class="btn btn-outline-info btn-icon-fixed {if $activeLanguage->isRTL()}ml-1{else}mr-1{/if}" id="add-link">
                        <span class="btn-icon"><i class="fas fa-plus" aria-hidden="true"></i></span> {t}Add Custom Link{/t}
                    </button>
                    <button type="button" class="btn btn-outline-info btn-icon-fixed {if $activeLanguage->isRTL()}ml-1{else}mr-1{/if}" id="add-page">
                        <span class="btn-icon"><i class="fas fa-plus" aria-hidden="true"></i></span> {t}Add Page{/t}
                    </button>
                    <button type="button" class="btn btn-outline-info btn-icon-fixed" id="add-post">
                        <span class="btn-icon"><i class="fas fa-plus" aria-hidden="true"></i></span> {t}Add Post{/t}
                    </button>
                    <div class="heading-elements">
                        <i class="fa fa-minus" id="collapse-all" data-trigger="hover" data-toggle="tooltip" title="{t}Collapse All{/t}"></i>
                    </div>
                </div>
                <div class="card-body menu-items">
                    <form action="{if $action == "edit"}{$routes->getRouteString("menuItemsEdit",['menuId'=>{$menu->getId()}])}{/if}" method="post" class="menu-items-form" data-edit="{if $action == "create"}false{else}true{/if}">

                        {function printMenu node=null}
                            {if isset($node) and $node->getValue() !== null}
                                {assign var="menuItem" value=$node->getValue()}
                                <li id="{$menuItem->getId()}">
                                    <div class="card card-border menu-item">
                                        <div class="card-header">
                                            <i class="icon-cursor-move move-item"></i>
                                            <span class="item-title">{$menuItem->getTitle()}</span>
                                            <div class="heading-elements">
                                                <i class="icon-arrow-up collapse-item"></i>
                                                <i class="far fa-trash-alt delete-item"></i>
                                            </div>
                                        </div>
                                        <div class="card-body collapse show">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="title">{t}Title{/t}</label>
                                                        <input class="form-control title" type="text" name="title" value="{$menuItem->getTitle()}">
                                                    </div>
                                                </div>
                                                {if $menuItem->getPage() != null}
                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label for="pageId">{t}Page{/t}</label>
                                                            <select name="pageId" class="form-control page-select">
                                                                <option value="{$menuItem->getPage()->getId()}" selected>{$menuItem->getPage()->getTitle()}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                {elseif $menuItem->getPost() != null}
                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label for="postId">{t}Post{/t}</label>
                                                            <select name="postId" class="form-control post-select">
                                                                <option value="{$menuItem->getPost()->getId()}" selected>{$menuItem->getPost()->getTitle()}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                {else}
                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label for="link">{t}URL{/t}</label>
                                                            <input class="form-control" type="text" name="link" value="{$menuItem->getLink()}">
                                                        </div>
                                                    </div>
                                                {/if}
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="class">{t}Class{/t}</label>
                                                        <input class="form-control" type="text" name="class" value="{$menuItem->getClass()}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <ul>
                                        {foreach $node->getChildren() as $subNode}
                                            {printMenu node=$subNode}
                                        {/foreach}
                                    </ul>
                                </li>
                            {/if}
                        {/function}
                        <ul class="item-list">
                            {if isset($menuItemsTree)}
                                {foreach from=$menuItemsTree->getRootNodes() item=rootNode}
                                    {printMenu node=$rootNode}
                                {/foreach}
                            {/if}
                        </ul>
                        <div class="form-group mt3">
                            <button type="submit" class="btn btn-success pull-right save-menu">
                                <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/select2/select2.full.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/textchange/jquery.textchange.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/draganddrop/draganddrop.js"></script>
{/block}
{block name=footerCustomJs append}
    <script type="text/javascript">
        $(document).ready(function () {
            var itemList = $('ul.item-list');
            itemList.sortable({
                autocreate: true,
                handle: '.move-item',
                update: function (evt) {
                    var i = 1;
                    $('.item-list li').each(function () {
                        $(this).attr('id', i);
                        i++;
                    });
                    console.log(JSON.stringify($(this).sortable('serialize')));
                }
            });
            var customLinkTemplate = "<div class='col-lg-4'>" +
                    "<div class='form-group'>" +
                    "<label for='link'>{t}URL{/t}</label>" +
                    "<input class='form-control' type='text' name='link'>" +
                    "</div>" +
                    "</div>";
            var pageTemplate = "<div class='col-lg-4'>" +
                    "<div class='form-group'>" +
                    "<label for='pageId'>{t}Page{/t}</label>" +
                    "<select name='pageId' class='form-control page-select'></select>" +
                    "</div>" +
                    "</div>";
            var postTemplate = "<div class='col-lg-4'>" +
                    "<div class='form-group'>" +
                    "<label for='postId'>{t}Post{/t}</label>" +
                    "<select name='postId' class='form-control post-select'></select>" +
                    "</div>" +
                    "</div>";

            function template(type) {
                return "<li>" +
                        "<div class='card card-border menu-item'>" +
                        "<div class='card-header'>" +
                        "<i class='icon-cursor-move move-item'></i>" +
                        "<span class='item-title'></span>" +
                        "<div class='heading-elements'>" +
                        "<i class='icon-arrow-down collapse-item'></i>" +
                        "<i class='far fa-trash-alt delete-item'></i>" +
                        "</div>" +
                        "</div>" +
                        "<div class='card-body collapse show'>" +
                        "<div class='row'>" +
                        "<div class='col-lg-4'>" +
                        "<div class='form-group'>" +
                        "<label for='title'>{t}Title{/t}</label>" +
                        "<input class='form-control title' type='text' name='title'>" +
                        "</div>" +
                        "</div>" +
                        type +
                        "<div class='col-lg-4'>" +
                        "<div class='form-group'>" +
                        "<label for='class'>{t}Class{/t}</label>" +
                        "<input class='form-control' type='text' name='class'>" +
                        "</div>" +
                        "</div>" +
                        "</div>" +
                        "</div>" +
                        "</div>" +
                        "</li>";
            }

            $('#add-link').on('click', function () {
                itemList.append(template(customLinkTemplate));
                var i = 1;
                $('.item-list li').each(function () {
                    $(this).attr('id', i);
                    i++;
                });
            });
            $('#add-post').on('click', function () {
                itemList.append(template(postTemplate));
                var i = 1;
                $('.item-list li').each(function () {
                    $(this).attr('id', i);
                    i++;
                });
                pageSearch();
                postSearch();
            });
            $('#add-page').on('click', function () {
                itemList.append(template(pageTemplate));
                var i = 1;
                $('.item-list li').each(function () {
                    $(this).attr('id', i);
                    i++;
                });
                pageSearch();
                postSearch();
            });
            $('#collapse-all').on('click', function () {
                $('.menu-items .card .collapse').collapse('hide');
                $('.collapse-item').removeClass('icon-arrow-up icon-arrow-down').addClass('icon-arrow-down');
            });
            $(document).on('keyup', '.title', function () {
                $(this).closest('.card').find('.item-title').text($(this).val());
            });
            $(document).on('click', '.collapse-item', function () {
                $(this).toggleClass('icon-arrow-down icon-arrow-up').closest('.card').find('.card-body').collapse('toggle');
            });
            $(document).on('click', '.delete-item', function (e) {
                e.preventDefault();
                var el = $(this).closest('li');
                if ($(this).closest('.menu-item').next('ul').length > 0) {
                    if ($(this).closest('.menu-item').next('ul').find('.post-select').length > 0) {
                        $(this).closest('.menu-item').next('ul').find('.post-select').select2('destroy');
                    }
                    if ($(this).closest('.menu-item').next('ul').find('.page-select').length > 0) {
                        $(this).closest('.menu-item').next('ul').find('.page-select').select2('destroy');
                    }
                    var clone = $(this).closest('.menu-item').next('ul').clone();
                    el.before(clone.find('li'));
                    el.remove();
                    pageSearch();
                    postSearch();
                } else {
                    el.remove();
                }
            });
            var pageSearchUrl = '{$routes->getRouteString("pageSearchPublic",[])}';
            var postSearchUrl = '{$routes->getRouteString("postSearchPublic",[])}';

            function pageSearch(element) {
                if (!element) {
                    element = $('.page-select');
                }

                $(element).select2({
                    ajax: {
                        url: function () {
                            return pageSearchUrl;
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
                                                text: item.title,
                                                id: item.id
                                            }
                                        })
                                    };
                                }
                            }
                        },
                        cache: true
                    },
                    minimumInputLength: 2
                });
            }

            function postSearch(element) {
                if (!element) {
                    element = $('.post-select');
                }
                $(element).select2({
                    ajax: {
                        url: function () {
                            return postSearchUrl;
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
                                                text: item.title,
                                                id: item.id
                                            }
                                        })
                                    };
                                }
                            }
                        },
                        cache: true
                    },
                    minimumInputLength: 2
                });
            }

            pageSearch();
            postSearch();
            var menuItemsEditUrl = '{$routes->getRouteString("menuItemsEdit",[])}';
            var menuEditUrl = '{$routes->getRouteString("menuEdit",[])}';
            var menuCreateUrl = '{$routes->getRouteString("menuCreate",[])}';
            $('.save-menu').on('click', function (e) {
                e.preventDefault();
                var menuNameForm = $('.menu-name-form');
                var menuNameVal = $('#menu-name').val();
                var dataEdit = menuNameForm.attr('data-edit');
                var menuNameEdit = $(menuNameForm).attr('data-edit');
                if (menuNameVal) {
                    if (menuNameEdit != 'true') {
                        $(menuNameForm).attr('action', menuCreateUrl);
                    }
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        data: menuNameForm.serialize(),
                        url: menuNameForm.attr('action'),
                        beforeSend: function () {
                            app.card.loading.start('.main-card');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $(menuNameForm).attr('action', menuEditUrl.replace("[menuId]", data.menuId)).attr('data-edit', true);
                                    var menuId = data.menuId;
                                    var treeString = JSON.stringify(itemList.sortable('serialize'));
                                    $.ajax({
                                        dataType: 'json',
                                        method: 'POST',
                                        data: {
                                            'tree': treeString
                                        },
                                        url: menuItemsEditUrl.replace('[menuId]', menuId),
                                        success: function (data) {
                                            if (data.redirect) {
                                                window.location.href = data.redirect;
                                            } else {
                                                if (data.error) {
                                                    app.notification('error', data.error);
                                                } else {
                                                    app.notification('success', '{t}Data has been saved successfully{/t}');
                                                }
                                            }
                                        },
                                        error: function (jqXHR, exception) {
                                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                                        }
                                    });
                                    if (dataEdit == 'false') {
                                        $('.page-title h3').text('{t}Edit Menu{/t}');
                                        history.pushState(null, '', menuEditUrl.replace("[menuId]", data.menuId));
                                    }
                                    $(menuNameForm).attr('data-edit', true);
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        }
                    });
                } else {
                    app.notification('information', '{t}Please Enter Menu Name{/t}');
                }
            });
            $(document).ajaxStop(function () {
                app.card.loading.finish('.main-card');
            });
        });
    </script>
{/block}