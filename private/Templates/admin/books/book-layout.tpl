{extends file='admin/admin.tpl'}
{block name=title}{t}Book Layout{/t}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/js/plugins/jquery/jquery-ui.min.css" rel="stylesheet"/>
    <link href="{$resourcePath}assets/js/plugins/gridstack/gridstack.css" rel="stylesheet"/>
    <link href="{$resourcePath}assets/js/plugins/gridstack/gridstack-extra.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    {if $isDemoMode === true}
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-info text-center">In the demo version you can't change book layout.</div>
            </div>
        </div>
    {/if}
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
    <div class="card p-l-10 p-t-30 p-b-10 p-r-10">
        <div class="book-layout-additional-elements">
            <span class="title">{t}Not Used Elements{/t}</span>
            {foreach from=$tempBookVisibleFieldList item=title key=key}
                <div class="grid-stack-item" data-gs-min-width="2" data-gs-max-height="1" data-gs-x="0" data-gs-y="0" data-gs-width="4" data-gs-height="1" data-gs-title="{$title}" data-gs-name="{$key}">
                    <div class="grid-stack-item-content form-element">
                        <div class="form-element-header">{t}{$title}{/t}</div>
                        <button class="form-element-remove"><i class="ti-trash"></i></button>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
    {if isset($bookLayoutSettings) and $bookLayoutSettings->getLayoutContainers() != null}
        <div class="book-layout">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="grid-stack grid-stack-12 content-block">
                            {if $bookLayoutSettings->getContainer('content')->getElements() != null}
                                {foreach from=$bookLayoutSettings->getContainer('content')->getElements() item=element name=element}
                                    <div class="grid-stack-item" data-gs-min-width="3" data-gs-max-height="1" data-gs-x="{$element->getX()}" data-gs-y="{$element->getY()}" data-gs-width="{$element->getWidth()}" data-gs-height="{$element->getHeight()}" data-gs-title="{$element->getTitle()}" data-gs-name="{$element->getName()}">
                                        <div class="grid-stack-item-content form-element">
                                            <div class="form-element-header">
                                                {t}{$element->getTitle()}{/t}
                                            </div>
                                            <button class="form-element-remove"><i class="ti-trash"></i></button>
                                        </div>
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="grid-stack grid-stack-4 sidebar-block">
                            {if $bookLayoutSettings->getContainer('sidebar')->getElements() != null}
                                {foreach from=$bookLayoutSettings->getContainer('sidebar')->getElements() item=element name=element}
                                    <div class="grid-stack-item" data-gs-min-width="2" data-gs-max-height="1" data-gs-x="{$element->getX()}" data-gs-y="{$element->getY()}" data-gs-width="{$element->getWidth()}" data-gs-height="{$element->getHeight()}" data-gs-title="{$element->getTitle()}" data-gs-name="{$element->getName()}">
                                        <div class="grid-stack-item-content form-element">
                                            <div class="form-element-header">
                                                {t}{$element->getTitle()}{/t}
                                            </div>
                                            <button class="form-element-remove"><i class="ti-trash"></i></button>
                                        </div>
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    <div class="row">
        <div class="col-lg-12">
            <button class="btn btn-primary m-t-20 pull-right" id="save-layout"{if $isDemoMode === true} disabled{/if}>
                <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
            </button>
        </div>
    </div>

{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery/jquery-ui.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/lodash/lodash.min.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/gridstack/gridstack.all.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/gridstack/gridstack.jQueryUI.min.js"></script>
{/block}

{block name=footerCustomJs append}

    <script>
        $(document).ready(function () {
            var options = {
                width: 12,
                float: false,
                cellHeight: 80,
                verticalMargin: 0,
                acceptWidgets: '.grid-stack-item',
                resizable: {
                    handles: 'se, sw'
                }
            };
            $('.content-block').gridstack(options);
            $('.sidebar-block').gridstack(_.defaults({
                float: false,
                width: 4
            }, options));

            $(document).on('click', '.form-element-remove', function (e) {
                e.preventDefault();
                var item = $(this).closest('.grid-stack-item');
                var itemClone = $(this).closest('.grid-stack-item').clone();
                $('.book-layout-additional-elements').append(itemClone);
                $(item).remove();
                $('.book-layout-additional-elements .grid-stack-item').draggable({
                    refreshPositions: true,
                    revert: 'invalid',
                    handle: '.grid-stack-item-content',
                    scroll: false,
                    appendTo: 'body'
                });
            });

            $('.book-layout-additional-elements .grid-stack-item').draggable({
                refreshPositions: true,
                revert: 'invalid',
                handle: '.grid-stack-item-content',
                scroll: false,
                appendTo: 'body'
            });

            function getGridLayout(searchBlock) {
                var items = [];
                $(searchBlock).each(function () {
                    var $this = $(this);
                    items.push({
                        x: $this.attr('data-gs-x'),
                        y: $this.attr('data-gs-y'),
                        width: $this.attr('data-gs-width'),
                        height: $this.attr('data-gs-height'),
                        title: $this.attr('data-gs-title'),
                        name: $this.attr('data-gs-name')
                    });
                });

                return JSON.stringify(items);
            }
            {if $isDemoMode === false}
            var bookLayoutUrl = '{$routes->getRouteString("bookLayout",[])}';
            $('#save-layout').on('click', function (e) {
                e.preventDefault();
                console.log();
                var contentBlock = getGridLayout($('.content-block .grid-stack-item.ui-draggable'));
                var sidebarBlock = getGridLayout($('.sidebar-block .grid-stack-item.ui-draggable'));
                $.ajax({
                    dataType: 'json',
                    method: 'POST',
                    data: {
                        content: contentBlock,
                        sidebar: sidebarBlock
                    },
                    url: bookLayoutUrl,
                    beforeSend: function (data) {
                        app.card.loading.start('.card');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                app.notification('success', data.success);
                            }
                        }
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    },
                    complete: function (data) {
                        app.card.loading.finish('.card');
                    }
                });
            });
            {/if}
        });
    </script>

{/block}