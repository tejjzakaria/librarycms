{switch $control}
{case "INPUT"}
    <input type="hidden" autocomplete="off" name="{$name}" value="{if $action == "edit"}{$book->getCustomFieldValue($name)}{/if}">
{/case}
{case "TEXTAREA"}
    <input type="hidden" autocomplete="off" name="{$name}" value="{if $action == "edit"}{$book->getCustomFieldValue($name)}{/if}">
{/case}
{case "CHECKBOX"}
    <input type="hidden" autocomplete="off" name="{$name}" value="{if $action == "edit" and $book->getCustomFieldValue($name)}1{else}0{/if}">
{/case}
{case "SELECT"}
    <input type="hidden" autocomplete="off" name="{$name}" value="{if $action == "edit"}{$book->getCustomFieldValue($name)}{/if}">
{/case}
{/switch}