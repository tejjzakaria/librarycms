<table width="100%" cellpadding="0" cellspacing="0" class="table-bordered" border="0">
    <thead>
        <tr>
            <th>Book</th>
            <th class="aligncenter">Issue Date</th>
            <th class="aligncenter">Expiry Date</th>
            <th class="aligncenter">Penalty</th>
        </tr>
    </thead>
    <tbody>
        {foreach $books as $book}
            {if $book->getIssue()->isExpired()}
                <tr>
                    <td>
                        <a href="{$SiteURL}{if $siteViewOptions->getOptionValue("bookUrlType")}{$routes->getRouteString("bookViewPublic",["bookId"=>$book->getId()])}{else}{$routes->getRouteString("bookViewViaUrlPublic",["bookUrl"=>$book->getUrl()])}{/if}">{$book->getTitle()}</a>
                    </td>
                    <td class="aligncenter">
                        {$book->getIssue()->getIssueDate()|date_format:$siteViewOptions->getOptionValue("dateFormat")}
                    </td>
                    <td class="aligncenter">
                        {$book->getIssue()->getExpiryDate()|date_format:$siteViewOptions->getOptionValue("dateFormat")}
                    </td>
                    <td class="aligncenter">
                        {$book->getIssue()->getPenalty()} {$siteViewOptions->getOptionValue("priceCurrency")}
                    </td>
                </tr>
            {/if}
        {/foreach}
    </tbody>
</table>