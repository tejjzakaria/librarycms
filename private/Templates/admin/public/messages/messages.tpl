{extends file='admin/admin.tpl'}
{block name=title}{t}Messages{/t}{/block}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="messages">
                <div class="table-responsive-sm">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th width="60">{t}Viewed{/t}</th>
                            <th style="width: 200px">{t}Name{/t}</th>
                            <th style="width: 225px">{t}Subject{/t}</th>
                            <th>{t}Message{/t}</th>
                            <th style="width: 165px" class="text-center">{t}Submission Date{/t}</th>
                            <th style="width: 75px" class="text-center">{t}Actions{/t}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {if isset($userMessages) and $userMessages != null}
                            {foreach from=$userMessages item=message name=message}
                                <tr{if !$message->isViewed()} class="new-message"{/if}>
                                    <td class="text-center">
                                        <label class="switch switch-sm margin-0">
                                            <input type="checkbox" name="status" {if $message->isViewed()} checked disabled{/if} class="viewed-message" data-id="{$message->getId()}">
                                        </label>
                                    </td>
                                    <td>
                                        {$message->getName()}
                                        <div class="book-list-info">
                                            <strong class="text-uppercase">{t}Email{/t}:</strong>
                                            <a href="mailto:{$message->getEmail()}">{$message->getEmail()}</a>
                                        </div>
                                    </td>
                                    <td>
                                        {$message->getSubject()}
                                    </td>
                                    <td>
                                        {$message->getMessage()}
                                    </td>
                                    <td class="text-center">
                                        {$message->getCreationDate()|date_format:$siteViewOptions->getOptionValue("dateTimeFormat")}
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                                            <button class="btn btn-outline-info btn-sm no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                            <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                                <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                                <li class="divider"></li>
                                                <li class="text-center">
                                                    <button class="btn btn-outline-danger delete-message" data-url="{$routes->getRouteString("userMessageDelete",["messageId"=>$message->getId()])}">
                                                        <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                        {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {include "admin/general/pagination.tpl"}
    </div>
{/block}
{block name=footerPageJs append}{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $('.viewed-message').on('change', function (e) {
                var $this = $(this);
                var id = $(this).attr('data-id');
                var url = '{$routes->getRouteString("userMessageSetViewed")}';
                $.ajax({
                    dataType: 'json',
                    method: 'POST',
                    url: url,
                    data: {
                        messageId: id
                    },
                    beforeSend: function () {
                        app.card.loading.start($("#messages"));
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $this.closest('tr').removeClass('new-message');
                                $this.attr('disabled', true);
                            }
                        }
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    },
                    complete: function () {
                        app.card.loading.finish($("#messages"));
                    }
                });
            });

            $(document).on('click', '.delete-message', function (e) {
                var url = $(this).attr('data-url');
                var row = $(this).closest('tr');
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: url,
                    beforeSend: function () {
                        app.card.loading.start('#messages');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                app.notification('success', data.success);
                                $(row).remove();
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish('#messages');
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
        });
    </script>
{/block}