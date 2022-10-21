{extends file='admin/admin.tpl'}
{block name=title}{t}LDAP Settings{/t}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    <style>
        .tooltip-inner {
            min-width: 250px; //the minimum width
        }
    </style>
    {if $isDemoMode === true}
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-info text-center">In the demo version you can't change LDAP settings.</div>
            </div>
        </div>
    {/if}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form action="{$routes->getRouteString("ldapSettings")}" method="post" class="validate" id="general-form">
                    <ul class="nav nav-tabs customtab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#general" role="tab" aria-expanded="true">
                                <span class="hidden-xs-down">{t}General{/t}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#connection" role="tab" aria-expanded="false">
                                <span class="hidden-xs-down">{t}Connection{/t}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#roleMapping" role="tab" aria-expanded="false">
                                <span class="hidden-xs-down">{t}Role Mapping{/t}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#attributeMapping" role="tab" aria-expanded="false">
                                <span class="hidden-xs-down">{t}Attribute Mapping{/t}</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane p-20 active" id="general" role="tabpanel" aria-expanded="true">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="control-label">{t}Enable LDAP{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Check to enable LDAP authentication"></i></label>
                                        <br>
                                        <label class="switch switch-sm">
                                            <input type="checkbox" name="isEnabled" value="1"{if $ldapSettings->isEnabled()} checked{/if}{if $isDemoMode === true} disabled{/if}>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="control-label">{t}Enable Auto Registering Users{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Check to enable automatic LDAP users registration to database"></i></label>
                                        <br>
                                        <label class="switch switch-sm">
                                            <input type="checkbox" name="isUserAutoRegistration" value="1"{if $ldapSettings->isUserAutoRegistration()} checked{/if}{if $isDemoMode === true} disabled{/if}>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="control-label">{t}Enable Both Authentication{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Check to use default authentication in the same time with LDAP"></i></label>
                                        <br>
                                        <label class="switch switch-sm">
                                            <input type="checkbox" name="isUseBothAuth" value="1"{if $ldapSettings->isUseBothAuth()} checked{/if}{if $isDemoMode === true} disabled{/if}>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane p-20" id="connection" role="tabpanel" aria-expanded="true">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label">{t}Server{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify LDAP server IP or domain name (e.g. ldap.library-cms.com)"></i></label>
                                        <input type="text" class="form-control" name="server" value="{$ldapSettings->getServer()}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label">{t}Port{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify LDAP server port (default is 389)"></i></label>
                                        <input type="text" class="form-control" name="port" value="{$ldapSettings->getPort()}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label">{t}Service Account DN{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify LDAP DN of user with at least read only access to users and groups. This user will be used to search LDAP users during login (e.g. uid=admin,cn=users,dc=library-cms,dc=com)"></i></label>
                                        <input type="text" class="form-control" name="serviceAccountDN" value="{$ldapSettings->getServiceAccountDN()}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label">{t}Service Account Password{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify password for Service Account DN"></i></label>
                                        <input type="password" class="form-control" name="serviceAccountPassword" value="{$ldapSettings->getServiceAccountPassword()}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label">{t}Search Base(s){/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify user search path (e.g. cn=users,dc=library-cms,dc=com)"></i></label>
                                        <input type="text" class="form-control" name="searchBase" value="{$ldapSettings->getSearchBase()}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane p-20" id="roleMapping" role="tabpanel" aria-expanded="false">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label">{t escape=no}<strong>Admin</strong> Group Name{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify administrator group name (e.g. cn=administrators,cn=groups,dc=library-cms,dc=com). Users from this group will be Library CMS administrators"></i>
                                        </label>
                                        <input type="text" class="form-control" name="adminGroupName" value="{$ldapSettings->getAdminGroupName()}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label">{t escape=no}<strong>Librarian</strong> Group Name{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify librarian group name (e.g. cn=librarians,cn=groups,dc=library-cms,dc=com). Users from this group will be Library CMS librarians"></i></label>
                                        <input type="text" class="form-control" name="librarianGroupName" value="{$ldapSettings->getLibrarianGroupName()}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label">{t escape=no}<strong>Member</strong> Group Name{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify member group name (e.g. cn=users,cn=groups,dc=library-cms,dc=com). Users from this group will be Library CMS members"></i>
                                        </label>
                                        <input type="text" class="form-control" name="userGroupName" value="{$ldapSettings->getUserGroupName()}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane p-20" id="attributeMapping" role="tabpanel" aria-expanded="false">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label">{t escape=no}<strong>Login</strong> Attribute Name{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify login attribute name (e.g. uid)"></i></label>
                                        <input type="text" class="form-control" name="loginAttributeName" value="{$ldapSettings->getLoginAttributeName()}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label">{t escape=no}<strong>DN</strong> Attribute Name{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify DN attribute name (e.g. dn)"></i>
                                        </label>
                                        <input type="text" class="form-control" name="dnAttributeName" value="{$ldapSettings->getDnAttributeName()}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label">{t escape=no}<strong>Email</strong> Attribute Name{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify mail attribute name (e.g. mail)"></i></label>
                                        <input type="text" class="form-control" name="emailAttributeName" value="{$ldapSettings->getEmailAttributeName()}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label">{t escape=no}<strong>First Name</strong> Attribute Name{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify mail attribute name (e.g. fn)"></i></label>
                                        <input type="text" class="form-control" name="firstNameAttributeName" value="{$ldapSettings->getFirstNameAttributeName()}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="control-label">{t escape=no}<strong>Last Name</strong> Attribute Name{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify mail attribute name (e.g. ln)"></i></label>
                                        <input type="text" class="form-control" name="lastNameAttributeName" value="{$ldapSettings->getLastNameAttributeName()}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <button type="submit" class="btn btn-success pull-right mt-1 mb-4"{if $isDemoMode === true} disabled{/if}>
                            <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{$routes->getRouteString("ldapTest")}" method="post" class="validate" id="test-form">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{t}Login{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify LDAP user name to test"></i></label>
                                    <input type="text" class="form-control" name="login" id="testLogin">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{t}Password{/t} <i class="icon-info"  data-toggle="tooltip" data-placement="top" title="Specify LDAP user's password"></i></label>
                                    <input type="password" class="form-control" name="password" id="testPassword">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group text-right">
                                    <button type="button" class="btn btn-primary testConnection">
                                        <span class="btn-icon"><i class="fa fa-rocket" aria-hidden="true"></i></span> {t}Test Connection{/t}
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
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/jquery-validate/jquery.validate.js"></script>
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/bootstrap-select/bootstrap-select.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $('.validate input,.validate select,.validate textarea').tooltipster({
                trigger: 'custom',
                onlyOne: false,
                position: 'bottom',
                offsetY: -5,
                theme: 'tooltipster-kaa'
            });
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
                rules: {
                    server: {
                        required: true
                    },
                    port: {
                        required: true
                    },
                    serviceAccountDN: {
                        required: true
                    },
                    serviceAccountPassword: {
                        required: true
                    },
                    searchBase: {
                        required: true
                    },
                    loginAttributeName: {
                        required: true
                    },
                    dnAttributeName: {
                        required: true
                    }
                }
            });




            $('.testConnection').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                $.ajax({
                    type: "POST",
                    url: form.attr('action'),
                    data: $('#general-form').serialize() + '&' + form.serialize(),
                    dataType: 'json',
                    beforeSend: function () {
                        app.card.loading.start($(form).closest('.card'));
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.warning) {
                                app.notification('warning', data.warning);
                            } else if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                var table = "<hr>";
                                if (data.success.email != null) {
                                    table += "{t}Email/Login{/t}: " + data.success.email + "<br>";
                                }
                                if (data.success.firstName != null) {
                                    table += "{t}First Name{/t}: " + data.success.firstName + "<br>";
                                }
                                if (data.success.lastName != null) {
                                    table += "{t}Last Name{/t}: " + data.success.lastName + "<br>";
                                }
                                if (data.success.role.id == "1") {
                                    table += "{t}Role{/t}: Admin";
                                } else if (data.success.role.id == "2") {
                                    table += "{t}Role{/t}: Librarian";
                                } else {
                                    table += "{t}Role{/t}: User";
                                }
                                app.notification('success', '{t}Congratulations! User is successfully logged and have required permissions to log in Library CMS.{/t}' + table);
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish($(form).closest('.card'));
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });


            $('.add-row').on('click', function (e) {
                e.preventDefault();
                var container = $(this).data('container');
                var template = $(container).find('.copy-template');
                var newRow = template.clone();
                var rowLength = $(container).data('count');
                var count = parseInt(rowLength + 1);
                $('input,select,textarea', newRow).each(function () {
                    $.each(this.attributes, function (index, element) {
                        this.value = this.value.replace('[count]', '[' + count + ']');
                    });
                });
                newRow.removeClass('copy-template');
                newRow.find('input,select,textarea').removeAttr('disabled');
                //newRow.find('select').select2();
                newRow.appendTo(container);
                $(container).data('count', count);
                app.tooltip_popover();
                return false;
            });
            $(document).on('click', '.remove-row', function () {
                var row = $(this).closest('.repeat-row');
                var container = $(this).data('container');
                var rowLength = $(container).data('count');
                row.remove();
                $(container).data('count', rowLength - 1);
                $('.tooltip.show').remove();
            });
        });
    </script>
{/block}