<div class="table-responsive-sm">
    <table class="table table-striped">
        <thead class="table-vertical-header" style="height: 40px;">
        <tr class="text-center">
            <th>{t}Book{/t}</th>
            <th>{t}Book Copy Id{/t}</th>
            <th>{t}Request Date{/t}</th>
            <th>{t}Issue Date{/t}</th>
            <th>{t}Expiry Date{/t}</th>
            <th>{t}Return Date{/t}</th>
            <th style="width: 140px;">{t}Last Action{/t}</th>
            <th style="width: 180px;">{t}Action Time{/t}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$issueLogs item=log name=log}
            <tr class="text-center">
                <td>
                    <a href="{$routes->getRouteString("bookEdit",["bookId"=>$log->getBookId()])}">{$log->getBookTitle()}</a>
                </td>
                <td>
                    {$log->getBookSN()}
                </td>
                <td>
                    {if $log->getRequestId() != null}
                        <a href="{$routes->getRouteString("requestEdit",["requestId"=>$log->getRequestId()])}">{$log->getRequestDateTime()}</a>
                    {else}
                        {$log->getRequestDateTime()}
                    {/if}
                </td>
                <td>
                    {if $log->getRequestId() != null}
                        <a href="{$routes->getRouteString("issueEdit",["issueId"=>$log->getIssueId()])}"> {$log->getIssueDate()}</a>
                    {else}
                        {$log->getIssueDate()}
                    {/if}
                </td>
                <td>
                    {$log->getExpiryDate()}
                </td>
                <td>
                    {$log->getReturnDate()}
                </td>
                <td>
                    <span class="badge {if $log->getIssueDate() != null}badge-success{else}badge-info{/if}">{if $log->getIssueDate() != null}{t}Issue{/t}{else}{t}Request{/t}{/if}</span>
                </td>
                <td>
                    {$log->getUpdateDateTime()}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
{include "admin/general/pagination.tpl"}