{extends file='admin/admin.tpl'}
{block name=title}{if $action == "create"}{t}Add Tag{/t}{else}{t}Edit Tag{/t}{/if}{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="tag-block">
                <div class="card-body">
                    {if $action == "create"}
                        {assign var=route value=$routes->getRouteString("tagCreate")}
                    {elseif $action == "edit" and isset($tag)}
                        {assign var=route value=$routes->getRouteString("tagEdit",["tagId"=>$tag->getId()])}
                    {elseif $action == "delete"}
                        {assign var=route value=""}
                    {/if}
                    <form action="{$route}" method="post" data-edit="{if $action == "create"}false{else}true{/if}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="control-label">{t}Title{/t}</label>
                                    <input type="text" class="form-control" autocomplete="off" name="name" value="{if $action == "edit"}{$tag->getName()}{/if}">
                                </div>
                            </div>
                        </div>
                        <div class="row margin-top-20">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-outline-secondary disabled pull-right save-tag">
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
                $('.save-tag').removeClass('btn-outline-secondary  disabled').addClass('btn-outline-success');
            });
            var tagEditUrl = '{$routes->getRouteString("tagEdit",[])}';
            $('.save-tag').on('click', function (e) {
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
                            app.card.loading.start('#tag-block');
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    form.attr('action', tagEditUrl.replace("[tagId]", data.tagId)).attr('data-changed', false);
                                    app.notification('success', '{t}Data has been saved successfully{/t}');
                                    $('.save-tag').removeClass('btn-outline-success').addClass('btn-outline-secondary disabled');
                                    if (dataEdit == 'false') {
                                        $('.page-title h3').text('{t}Edit Tag{/t}');
                                        history.pushState(null, '', tagEditUrl.replace("[tagId]", data.tagId));
                                    }
                                    $(form).attr('data-edit', true);
                                }
                            }
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        },
                        complete: function (data) {
                            app.card.loading.finish('#tag-block');
                        }
                    });
                } else {
                    app.notification('information', '{t}There are no changes{/t}');
                }
            });
        });
    </script>
{/block}