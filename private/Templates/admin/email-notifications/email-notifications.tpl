{extends file='admin/admin.tpl'}
{block name=title}{t}Email Notifications{/t}{/block}
{*block name=toolbar}
    <div class="heading-elements">
        <a href="{$routes->getRouteString("emailNotificationCreate")}" class="btn btn-success btn-icon-fixed" >
            <i class="fas fa-plus"></i> {t}Add Notification{/t}
        </a>
    </div>
{/block*}
{block name=content}
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="notifications">
                <div class="table-responsive-sm">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 75px" class="text-center">{t}Enabled{/t}</th>
                                <th>{t}Trigger{/t}</th>
                                <th>{t}Subject{/t}</th>
                                <th style="width: 70px" class="text-center">{t}Actions{/t}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$emailNotifications item=emailNotification name=emailNotification}
                                <tr>
                                    <td class="text-center">
                                        <label class="switch switch-sm margin-0">
                                            <input type="checkbox" name="isEnabled" class="enabled-flag" value="1"{if $emailNotification->isEnabled()} checked{/if} data-route="{$emailNotification->getRoute()}">
                                        </label>
                                    </td>
                                    <td>
                                        {$routesArray=$routes->getRoutes()}
                                        <a href="{$routes->getRouteString("emailNotificationEdit",["route"=>$emailNotification->getRoute()])}">{$routesArray[$emailNotification->getRoute()]->getTitle()}</a>
                                    </td>
                                    <td>{$emailNotification->getSubject()}</td>
                                    <td class="text-center">
                                        <a href="{$routes->getRouteString("emailNotificationEdit",["route"=>$emailNotification->getRoute()])}" class="btn btn-outline-info btn-sm no-border" data-container="body" data-toggle="tooltip" title="{t}Edit{/t}"><i class="fas fa-pencil-alt"></i></a>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                {include "admin/general/pagination.tpl"}
            </div>
        </div>
    </div>
{/block}
{block name=footerJs append}
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            var changeStatusUrl = '{$routes->getRouteString("emailNotificationEnable",[])}';
            $(document).on('change', '.enabled-flag', function () {
                var status;
                if ($(this).is(':checked')) {
                    status = 'enable';
                } else {
                    status = 'disable';
                }
                var url = $(this).attr('data-route');
                $.ajax({
                    dataType: 'json',
                    method: 'POST',
                    url: changeStatusUrl.replace("[route]", url).replace("[enable]", status),
                    beforeSend: function () {
                        app.card.loading.start('#notifications');
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                if ($(this).is(':checked')) {
                                    app.notification('success', '{t}Email Notification Successfully Enabled.{/t}');
                                } else {
                                    app.notification('success', '{t}Email Notification Successfully Disabled.{/t}');
                                }
                            }
                        }
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    },
                    complete: function () {
                        app.card.loading.finish('#notifications');
                    }
                });
            });
        });
    </script>
{/block}