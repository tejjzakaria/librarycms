<div class="sidebar">
    <div class="book-filter">
        <h2 class="text-lg-center text-left">{t}Book Filter{/t}</h2>
        <button class="show-filter" data-toggle="collapse" data-target="#book-filter" aria-expanded="false" aria-controls="book-filter">
            <i class="ti-filter"></i></button>
        <form action="{$routes->getRouteString("bookFilterPublic")}" id="book-filter">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <label>{t}Publisher{/t}</label>
                    <select name="publisherIds[]" id="publishers" multiple="multiple"></select>
                </div>
                <div class="col-md-12 mb-2">
                    <label>{t}Genres{/t}</label>
                    <select name="genreIds[]" id="genres" multiple="multiple"></select>
                </div>
                <div class="col-md-12 mb-2">
                    <label>{t}Authors{/t}</label>
                    <select name="authorIds[]" id="authors" multiple="multiple"></select>
                </div>

                <div class="col-md-12 mb-2">
                    <label>{t}Year{/t}</label>
                    <div class="row">
                        <div class="col-md-6">
                            <select name="startYear" class="custom-select">
                                <option value=""></option>
                                {for $foo=1960 to $smarty.now|date_format:'%Y'}
                                    <option value="{$foo}">{$foo}</option>
                                {/for}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="endYear" class="custom-select">
                                <option value=""></option>
                                {for $foo=1960 to $smarty.now|date_format:'%Y'}
                                    <option value="{$foo}">{$foo}</option>
                                {/for}
                            </select>
                        </div>
                    </div>
                </div>
                {if isset($bindings) and $bindings !== null}
                    <div class="col-md-12 mb-2">
                        <label>{t}Bindings{/t}</label>
                        <select name="bindings[]" id="bindings" multiple="multiple">
                            {foreach from=$bindings item=binding name=binding}
                                <option value="{$binding->getName()}">{$binding->getName()}</option>
                            {/foreach}
                        </select>
                    </div>
                {/if}
                {*$bookCustomFields|var_dump*}
                {if isset($bookCustomFields) and $bookCustomFields !== null}
                    {foreach from=$bookCustomFields item=customField name=customField}
                        {if $customField->isFilterable()}
                        <div class="col-md-12 mb-2">
                            {if $customField->getControl() == 'INPUT'}
                                <label>{$customField->getTitle()}</label>
                                <input type="text" name="{$customField->getName()}" class="form-control">
                            {elseif $customField->getControl() == 'SELECT'}
                                <label>{$customField->getTitle()}</label>
                                {if isset($customField->getListValues()) and $customField->getListValues() !== null}
                                    <select name="{$customField->getName()}" class="custom-select">
                                        {foreach from=$customField->getListValues() item=listValue name=listValue}
                                            <option value="{$listValue->getValue()}">{$listValue->getValue()}</option>
                                        {/foreach}
                                    </select>
                                {/if}
                            {elseif $customField->getControl() == 'TEXTAREA'}
                                <label>{$customField->getTitle()}</label>
                                <textarea name="{$customField->getName()}" cols="30" rows="3" class="form-control"></textarea>
                            {elseif $customField->getControl() == 'CHECKBOX'}
                                <div class="custom-control custom-checkbox my-2">
                                    <input type="checkbox" class="custom-control-input" name="{$customField->getName()}" id="custom-control-{$customField->getId()}" value="1">
                                    <label class="custom-control-label" for="custom-control-{$customField->getId()}">{$customField->getTitle()}</label>
                                </div>
                            {else}
                            {$customField->getTitle()}
                            <br>
                        {/if}
                        </div>
                        {/if}
                    {/foreach}
                {/if}

                <div class="col-md-12 text-center">
                    <button class="btn btn-secondary btn-block mt-2" id="filterIt" type="submit">{t}Filter It!{/t}</button>
                </div>
            </div>
        </form>
    </div>
</div>