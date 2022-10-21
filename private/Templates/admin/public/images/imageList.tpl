<div class="row mb-3" id="image-list">
    {if isset($images) and $images != null}
        {foreach $images as $image}
            <div class="col-sm-4 col-md-3 col-lg-2 text-center gallery-img">
                <div class="tile-basic">
                    <div class="tile-image">
                        {if $image != null}
                            <img src="{$image->getWebPath('small')}" alt="" class="small-img img-fluid">
                        {else}
                            <img src="{$siteViewOptions->getOptionValue("noBookImageFilePath")}" alt="" class="small-img img-fluid">
                        {/if}
                        <div class="tile-image-title">
                            <p>{if $image != null}{$image->getWebPath()|basename}{/if}</p>
                        </div>
                        <div class="tile-image-hover">
                            <button type="button" class="btn btn-danger delete-img gallery-img-delete" data-id="{$image->getId()}">
                                <i class="far fa-trash-alt"></i>
                            </button>
                            <div class="tile-image-container-vertical text-center">
                                <button type="button" class="btn btn-info btn-sm btn-icon-fixed copy-path original-path" data-clipboard-text="{$image->getWebPath()}">
                                    <i class="far fa-copy" aria-hidden="true"></i> {t}Copy Image URL{/t}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/foreach}
    {/if}
    <div class="col-sm-4 col-md-3 col-lg-2 text-center gallery-img gallery-clone">
        <div class="tile-basic">
            <div class="tile-image">
                <img src="" alt="" class="thumb-img">
                <div class="tile-image-title">
                    <p></p>
                </div>
                <div class="tile-image-hover">
                    <button type="button" class="btn btn-danger delete-img gallery-img-delete" data-id="">
                        <i class="far fa-trash-alt"></i>
                    </button>
                    <div class="tile-image-container-vertical text-center">
                        <button type="button" class="btn btn-info btn-sm btn-icon-fixed copy-path original-path" data-clipboard-text="">
                            <span class="far fa-copy" aria-hidden="true"></span> {t}Copy Image URL{/t}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{include "admin/general/pagination.tpl"}