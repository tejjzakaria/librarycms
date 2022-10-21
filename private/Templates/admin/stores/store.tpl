{extends file='admin/admin.tpl'}
{block name=title}{if $action == "create"}{t}Add Store{/t}{else}{t}Edit Store{/t}{/if}{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="store-block">
                <div class="card-body">
                    {if $action == "create"}
                        {assign var=route value=$routes->getRouteString("storeCreate")}
                    {elseif $action == "edit" and isset($store)}
                        {assign var=route value=$routes->getRouteString("storeEdit",["storeId"=>$store->getId()])}
                    {elseif $action == "delete"}
                        {assign var=route value=""}
                    {/if}
                    <form action="{$route}" method="post" data-edit="{if $action == "create"}false{else}true{/if}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="control-label">{t}Title{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" name="name" value="{if $action == "edit" and $store != null}{$store->getName()}{/if}">
                                </div>
                            </div>
                        </div>
                        <div class="row margin-top-20">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-outline-secondary disabled pull-right save-store">
                                        <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $(document).on('change', 'input,textarea,select', function () {
                $(this).closest('form').attr('data-changed', true);
                $('.save-store').removeClass('btn-outline-secondary  disabled').addClass('btn-outline-success');
            });
            var storeEditUrl = '{$routes->getRouteString("storeEdit",[])}';
            $('.save-store').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var dataEdit = form.attr('data-edit');
                var dataChanged = form.attr('data-changed');
                if (dataChanged == 'true') {
                    $.ajax({
                        dataType: 'json',
                        method: 'POST',
                        data: form.serialize(),
                        url: form.attr('action'),
                        beforeSend: function (data) {
                            app.card.loading.start('#store-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    form.attr('action', storeEditUrl.replace("[genreId]", data.storeId)).attr('data-changed', false);
                                    app.notification('success', '{t}Data has been saved successfully{/t}');
                                    $('.save-genre').removeClass('btn-outline-success').addClass('btn-outline-secondary disabled');
                                    if (dataEdit == 'false') {
                                        $('.page-title h3').text('{t}Edit Store{/t}');
                                        history.pushState(null, '', storeEditUrl.replace("[storeId]", data.storeId));
                                    }
                                    $(form).attr('data-edit', true);
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#store-block');
                        }
                    });
                } else {
                    app.notification('information', '{t}There are no changes{/t}');
                }
            });
        });
    </script>
{/block}