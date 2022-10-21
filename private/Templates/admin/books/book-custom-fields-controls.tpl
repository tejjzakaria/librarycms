{switch $control}
{case "INPUT"}
    <div class="form-group">
        <label class="control-label">{t}{$title}{/t}</label>
        <input type="text" class="form-control" autocomplete="off" name="{$name}" value="{if $action == "edit"}{$book->getCustomFieldValue($name)}{/if}">
    </div>
{/case}
{case "TEXTAREA"}
    <div class="form-group">
        <label class="control-label">{t}{$title}{/t}</label>
        <textarea name="{$name}" class="form-control" autocomplete="off">{if $action == "edit"}{$book->getCustomFieldValue($name)}{/if}</textarea>
    </div>
{/case}
{case "CHECKBOX"}
    <div class="form-group">
        <label class="control-label">{t}{$title}{/t}</label><br>
        <label class="switch switch-sm">
            <input type="checkbox" name="{$name}" value="1"{if $action == "edit" and $book->getCustomFieldValue($name)} checked{/if}>
        </label>
    </div>
{/case}
{case "SELECT"}
    <div class="form-group">
        <label class="control-label">{t}{$title}{/t}</label>
        {if $listValues !== null}
            <select name="{$name}" class="form-control custom-select">
                {foreach from=$listValues item=listValue name=listValue}
                    <option value="{$listValue->getValue()}"{if $action == "edit" and $book->getCustomFieldValue($name) == $listValue->getValue()} selected{/if}>{$listValue->getValue()}</option>
                {/foreach}
            </select>
        {/if}
    </div>
{/case}
{/switch}