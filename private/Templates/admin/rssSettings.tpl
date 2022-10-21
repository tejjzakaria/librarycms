{extends file='admin/admin.tpl'}
{block name=title}{t}RSS Settings{/t}{/block}
{block name=headerCss append}
    <link href="{$resourcePath}assets/css/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
{/block}
{block name=content}
    {*$rssSettings|var_dump*}
    {if $rssSettings != null}
    {assign var=bookChannel value=$rssSettings->getChannel('bookRssChannel')}
        {*$bookChannel|var_dump*}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{$routes->getRouteString("rssSettings")}" method="post" class="validate">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{t}Title{/t} <i class="icon-info" data-toggle="tooltip" data-html="true" data-placement="top" title="{t}The name of the channel.{/t}"></i></label>
                                    <input type="text" name="channels[0][title]" class="form-control" value="{if $bookChannel != null}{$bookChannel->getTitle()}{/if}" required>
                                    <input type="hidden" name="channels[0][name]" value="bookRssChannel">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{t}Link{/t} <i class="icon-info" data-toggle="tooltip" data-html="true" data-placement="top" title="{t}The URL to the HTML website corresponding to the channel.{/t}"></i></label>
                                    <input type="text" name="channels[0][link]" class="form-control" value="{if $bookChannel != null}{$bookChannel->getLink()}{/if}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label">{t}Description{/t} <i class="icon-info" data-toggle="tooltip" data-html="true" data-placement="top" title="{t}Phrase or sentence describing the channel.{/t}"></i></label>
                                    <textarea name="channels[0][description]" class="form-control" rows="3" required>{if $bookChannel != null}{$bookChannel->getDescription()}{/if}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{t}Language{/t} <i class="icon-info" data-toggle="tooltip" data-html="true" data-placement="top" title="{t}The language the channel is written in. For example 'en' or 'en-US'.{/t}"></i></label>
                                    <input type="text" name="channels[0][language]" class="form-control" value="{if $bookChannel != null}{$bookChannel->getLanguage()}{/if}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{t}Copyright{/t} <i class="icon-info" data-toggle="tooltip" data-html="true" data-placement="top" title="{t}Copyright notice for content in the channel.{/t}"></i></label>
                                    <input type="text" name="channels[0][copyright]" class="form-control" value="{if $bookChannel != null}{$bookChannel->getCopyright()}{/if}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">{t}Managing Editor{/t} <i class="icon-info" data-toggle="tooltip" data-html="true" data-placement="top" title="{t}Email address for person responsible for editorial content.{/t}"></i></label>
                                    <input type="text" name="channels[0][managingEditor]" class="form-control" value="{if $bookChannel != null}{$bookChannel->getManagingEditor()}{/if}">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">{t}WebMaster{/t} <i class="icon-info" data-toggle="tooltip" data-html="true" data-placement="top" title="{t}Email address for person responsible for technical issues relating to channel.{/t}"></i></label>
                                    <input type="text" name="channels[0][webMaster]" class="form-control" value="{if $bookChannel != null}{$bookChannel->getWebMaster()}{/if}">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">{t}Image{/t} <i class="icon-info" data-toggle="tooltip" data-html="true" data-placement="top" title="{t}Specifies a GIF, JPEG or PNG image that can be displayed with the channel.{/t}"></i></label>
                                    <input type="text" name="channels[0][image]" class="form-control" value="{if $bookChannel != null and $bookChannel->getImage() != null}{$bookChannel->getImage()}{/if}">
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success pull-right mt-2">
                                <span class="btn-icon"><i class="far fa-save"></i></span> {t}Save{/t}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {/if}
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
                }
            });
        });
    </script>
{/block}