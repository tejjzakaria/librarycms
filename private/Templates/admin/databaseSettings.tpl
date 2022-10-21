{extends file='admin/admin.tpl'}
{block name=title}{t}Database Settings{/t}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    {if $isDemoMode === true}
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-info text-center">In the demo version you can't see or change database settings.</div>
            </div>
        </div>
    {/if}
    {if $isDemoMode === false}
    {assign var=route value=$routes->getRouteString("databaseSettings")}
    <form action="{$route}" method="post" id="connections">
        <div class="repeat-container">
            {if isset($databaseSettings) and $databaseSettings->getDatabaseConnections() !== null}
                {foreach from=$databaseSettings->getDatabaseConnections() item=connection name=connection}
                    <div class="card connection">
                        <div class="card-header pt-0 pb-0">
                            <div class="app-radio round inline">
                                <label><input type="radio" class="form-control" data-name="connectionStatus" name="connectionStatus[{$smarty.foreach.connection.index}]" value="1" {if strcmp($connection->getName(),$databaseSettings->getActiveConnectionName())==0} checked{/if}> {t}Connection Status{/t}
                                </label>
                            </div>
                            <div class="heading-elements pt-1">
                                <div class="dropdown d-inline" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                                    <button class="btn btn-outline-info no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                    <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                        <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                        <li class="divider"></li>
                                        <li class="text-center">
                                            <button class="btn btn-outline-danger delete-connection">
                                                <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="name" class="control-label">{t}Name{/t}</label>
                                        <input class="form-control" data-name="name" type="text" name="name[{$smarty.foreach.connection.index}]" value="{$connection->getName()}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="databaseType" class="control-label">{t}Database Type{/t}</label>
                                        <select name="databaseType[{$smarty.foreach.connection.index}]" data-name="databaseType" class="form-control select-picker">
                                            <option value="mysql"{if strcmp($connection->getDatabaseType(),'mysql') == 0} selected{/if}>mysql</option>
                                            <option value="mariadb" {if strcmp($connection->getDatabaseType(),'mariadb') == 0} selected{/if}>mariadb</option>
                                            <option value="mssql" {if strcmp($connection->getDatabaseType(),'mssql') == 0} selected{/if}>mssql</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="databaseName" class="control-label">{t}Database Name{/t}</label>
                                        <input class="form-control" type="text" data-name="databaseName" name="databaseName[{$smarty.foreach.connection.index}]" value="{$connection->getDatabaseName()}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="host" class="control-label">{t}Hostname{/t}</label>
                                        <input class="form-control" type="text" data-name="host" name="host[{$smarty.foreach.connection.index}]" value="{$connection->getHost()}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="port" class="control-label">{t}Port{/t}</label>
                                        <input class="form-control" type="text" data-name="port" name="port[{$smarty.foreach.connection.index}]" value="{$connection->getPort()}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="username" class="control-label">{t}User Name{/t}</label>
                                        <input class="form-control" type="text" data-name="username" name="username[{$smarty.foreach.connection.index}]" value="{$connection->getUsername()}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="password" class="control-label">{t}Password{/t}</label>
                                        <input class="form-control" type="password" data-name="password" name="password[{$smarty.foreach.connection.index}]" value="{$connection->getPassword()}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="charset" class="control-label">{t}Charset{/t}</label>
                                        <input class="form-control" type="text" data-name="charset" name="charset[{$smarty.foreach.connection.index}]" value="{$connection->getCharset()}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {/foreach}
            {/if}
            <div class="card copy-template connection">
                <div class="card-header pt-0 pb-0">
                    <div class="app-radio round inline">
                        <label>
                            <input type="radio" class="form-control" data-name="connectionStatus" name="connectionStatus[]" value="1" disabled> {t}Connection Status{/t}
                        </label>
                    </div>
                    <div class="heading-elements pt-1">
                        <div class="dropdown d-inline" data-trigger="hover" data-toggle="tooltip" title="{t}Delete{/t}">
                            <button class="btn btn-outline-info no-border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="far fa-trash-alt"></i>
                            </button>
                            <ul class="dropdown-menu delete-dropdown dropdown-menu-right">
                                <li class="text-center">{t}Do you really want to delete?{/t}</li>
                                <li class="divider"></li>
                                <li class="text-center">
                                    <button class="btn btn-outline-danger delete-connection">
                                        <span class="btn-icon"><i class="far fa-trash-alt"></i></span> {t}Delete{/t}
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="name" class="control-label">{t}Name{/t}</label>
                                <input class="form-control" data-name="name" type="text" name="name[]" disabled>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="databaseType" class="control-label">{t}Database Type{/t}</label>
                                <select name="databaseType[]" data-name="databaseType" class="form-control" disabled>
                                    <option value="mysql">mysql</option>
                                    <option value="mariadb">mariadb</option>
                                    <option value="mssql">mssql</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="databaseName" class="control-label">{t}Database Name{/t}</label>
                                <input class="form-control" type="text" data-name="databaseName" name="databaseName[]" disabled>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="host" class="control-label">{t}Hostname{/t}</label>
                                <input class="form-control" type="text" data-name="host" name="host[]" disabled>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="port" class="control-label">{t}Port{/t}</label>
                                <input class="form-control" type="text" data-name="port" name="port[]" disabled>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="username" class="control-label">{t}User Name{/t}</label>
                                <input class="form-control" type="text" data-name="username" name="username[]" disabled>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="password" class="control-label">{t}Password{/t}</label>
                                <input class="form-control" type="password" data-name="password" name="password[]" disabled>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="charset" class="control-label">{t}Charset{/t}</label>
                                <input class="form-control" type="text" data-name="charset" name="charset[]" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <button type="button" class="btn btn-outline-success add-connection">
                    <i class="fas fa-plus"></i> {t}Add Connection{/t}
                </button>
            </div>
            <div class="col-lg-6">
                <button type="submit" class="btn btn-success pull-right save-connections">
                    <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                </button>
            </div>
        </div>
    </form>
    {/if}
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
{/block}
{block name=footerCustomJs append}
    {if $isDemoMode === false}
    <script>
        $(document).ready(function () {
            $('.add-connection').on('click', function (e) {
                e.preventDefault();
                var template = $('.copy-template');
                var container = template.closest('.repeat-container');
                var newRow = template.clone();
                newRow.removeClass('copy-template');
                newRow.find('input, select').removeAttr('disabled');
                newRow.appendTo(container);
                newRow.find('select').selectpicker();
                app.tooltip_popover();
                return false;
            });
            $('#connections').on('submit', function (e) {
                var container = $('.repeat-container');
                var count = 0;
                $('.card',container).each(function (i, element) {
                    $('input, select', element).each(function (i, element) {
                        var name = $(element).attr('data-name');
                        var newName = name + '[' + count + ']';
                        $(element).attr('name', newName);
                    });
                    count++;
                });
            });
            $(document).on('click', '.delete-connection', function (e) {
                e.preventDefault();
                var card = $(this).closest('.card');
                card.remove();
                $('.tooltip.show').remove();
            });
            $(document).on('change', 'input[type=radio]', function () {
                $('input[type=radio]:checked').not(this).prop('checked', false);
            });
        });
    </script>
    {/if}
{/block}