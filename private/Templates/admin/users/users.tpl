{extends file='admin/admin.tpl'}
{block name=title}{t}Users{/t}{if isset($userRole)}: {$userRole->getName()}{/if}{/block}
{block name=toolbar}
    <div class="heading-elements">
        <a href="{$routes->getRouteString("userCreate")}" class="btn btn-success btn-icon-fixed" target="_blank">
            <span class="btn-icon"><i class="fas fa-plus"></i></span> {t}Add User{/t}
        </a>
    </div>
{/block}
{block name=content}
    <div class="row">
        <div class="col-12">
            <div class="card" id="userList">
                {include "admin/users/user-list.tpl"}
            </div>
        </div>
    </div>
{/block}
{block name=footerPageJs append}
    <script type="text/javascript" src="{$resourcePath}assets/js/plugins/select2/select2.full.min.js"></script>
{/block}
{block name=footerCustomJs append}
    <script>
        $(document).ready(function () {
            $(document).on('click', '#role-filter', function (e) {
                e.preventDefault();
                var roleId = $('#roleId').val();
                var url = '{$routes->getRouteString("roleUserListView",[])}';
                if (roleId) {
                    $.ajax({
                        dataType: 'json',
                        //data: form.serialize(),
                        type: 'POST',
                        url: url.replace('[roleId]', roleId),
                        beforeSend: function () {
                            app.card.loading.start($("#userList"));
                        },
                        success: function (data) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.error) {
                                    app.notification('error', data.error);
                                } else {
                                    $("#userList").html(data.html);
                                    searchRole();
                                }
                            }
                        },
                        complete: function () {
                            app.card.loading.finish($("#userList"));
                        },
                        error: function (jqXHR, exception) {
                            app.notification('error', app.getErrorMessage(jqXHR, exception));
                        }
                    });
                } else {
                    app.notification('warning', 'Please Select Role');
                }
            });
            function searchRole() {
                var roleSearchUrl = '{$routes->getRouteString("roleSearch",[])}';
                $("#roleId").select2({
                    ajax: {
                        url: roleSearchUrl,
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
                    width: '260px',
                    minimumInputLength: 2
                });
            }
            searchRole();

            $(document).on('click', '.delete-user', function (e) {
                var url = $(this).attr('data-url');
                var row = $(this).closest('tr');
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: url,
                    beforeSend: function () {
                        app.card.loading.start('#userList');
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
                        app.card.loading.finish('#userList');
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
            $(document).on('click', '.ajax-page', function (e) {
                e.preventDefault();
                $.ajax({
                    dataType: 'json',
                    url: $(this).attr('href'),
                    beforeSend: function () {
                        app.card.loading.start($("#userList"));
                    },
                    success: function (data) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            if (data.error) {
                                app.notification('error', data.error);
                            } else {
                                $("#userList").html(data.html);
                            }
                        }
                    },
                    complete: function () {
                        app.card.loading.finish($("#userList"));
                    },
                    error: function (jqXHR, exception) {
                        app.notification('error', app.getErrorMessage(jqXHR, exception));
                    }
                });
            });
        });
    </script>
{/block}