{extends file='admin/admin.tpl'}
{block name=title}{if $action == "create"}{t}Add Genre{/t}{else}{t}Edit Genre{/t}{/if}{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="genre-block">
                <div class="card-body">
                    {if $action == "create"}
                        {assign var=route value=$routes->getRouteString("genreCreate")}
                    {elseif $action == "edit" and isset($genre)}
                        {assign var=route value=$routes->getRouteString("genreEdit",["genreId"=>$genre->getId()])}
                    {elseif $action == "delete"}
                        {assign var=route value=""}
                    {/if}
                    <form action="{$route}" method="post" data-edit="{if $action == "create"}false{else}true{/if}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="control-label">{t}Title{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" name="name" value="{if $action == "edit"}{$genre->getName()}{/if}">
                                </div>
                            </div>
                        </div>
                        <div class="row margin-top-20">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-outline-secondary disabled pull-right save-genre">
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
                $('.save-genre').removeClass('btn-outline-secondary  disabled').addClass('btn-outline-success');
            });
            var genreEditUrl = '{$routes->getRouteString("genreEdit",[])}';
            $('.save-genre').on('click', function (e) {
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
                            app.card.loading.start('#genre-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    form.attr('action', genreEditUrl.replace("[genreId]", data.genreId)).attr('data-changed', false);
                                    app.notification('success', '{t}Data has been saved successfully{/t}');
                                    $('.save-genre').removeClass('btn-outline-success').addClass('btn-outline-secondary disabled');
                                    if (dataEdit == 'false') {
                                        $('.page-title h3').text('{t}Edit Genre{/t}');
                                        history.pushState(null, '', genreEditUrl.replace("[genreId]", data.genreId));
                                    }
                                    $(form).attr('data-edit', true);
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#genre-block');
                        }
                    });
                } else {
                    app.notification('information', '{t}There are no changes{/t}');
                }
            });
        });
    </script>
{/block}