{assign var="labelWidth" value=$barCodePrintSettings->getLabelWidth()}
{assign var="labelHeight" value=$barCodePrintSettings->getLabelHeight()}

{assign var="cornerRadius" value=$barCodePrintSettings->getCornerRadius()}
{assign var="gapAcross" value=$barCodePrintSettings->getGapAcross()}
{assign var="gapDown" value=$barCodePrintSettings->getGapDown()}

{assign var="primaryFontSize" value=$barCodePrintSettings->getPrimaryFontSize()}
{assign var="secondaryFontSize" value=$barCodePrintSettings->getSecondaryFontSize()}

{assign var="pageMarginTop" value=$barCodePrintSettings->getPageMarginTop()}
{assign var="pageMarginBottom" value=$barCodePrintSettings->getPageMarginBottom()}

{assign var="pageMarginLeft" value=$barCodePrintSettings->getPageMarginLeft()}
{assign var="pageMarginRight" value=$barCodePrintSettings->getPageMarginRight()}

{assign var="maxPageWidth" value=210-$pageMarginLeft-$pageMarginRight-$gapAcross}
{assign var="maxPageHeight" value=300-$pageMarginTop-$pageMarginBottom}

{math equation="floor(maxPageWidth/labelWidth)" maxPageWidth=$maxPageWidth labelWidth=$labelWidth assign=countLabelsByWidth}
{math equation="floor(maxPageHeight/labelHeight)" maxPageHeight=$maxPageHeight labelHeight=$labelHeight assign=countLabelsByHeight}

{assign var="labelsPerPage" value=$countLabelsByHeight * $countLabelsByWidth}

{if isset($books) and $books != null and isset($barCodePrintSettings)}
    <div class="a4-page" style="padding-top: {$pageMarginTop}mm;padding-bottom: {$pageMarginBottom}mm; padding-left: {$pageMarginLeft}mm; padding-right: {$pageMarginRight}mm">
    {assign var="index" value="0"}
    {foreach from=$books item=book name=book}
        {for $foo=1 to $book->getQuantity()}
            {if ($index % $labelsPerPage) == 0 and $index != 0}
                <div class="a4-page" style="padding-top: {$pageMarginTop}mm;padding-bottom: {$pageMarginBottom}mm; padding-left: {$pageMarginLeft}mm; padding-right: {$pageMarginRight}mm">
            {/if}
            <div class="barcode-card" style="width: {$labelWidth}mm;height: {$labelHeight}mm; border-radius: {$cornerRadius}mm;margin-bottom: {$gapDown}mm;margin-left: {$gapAcross/($countLabelsByWidth*2)}mm; margin-right: {$gapAcross/($countLabelsByWidth*2)}mm;">
                <div class="row">
                    <div class="col-sm-12">
                        <h4 class="book-title" style="font-size: {$primaryFontSize}em;">{$book->getTitle()}</h4>
                    </div>
                    {if $barCodePrintSettings->isPrintBookCover() or $barCodePrintSettings->isPrintBookPrice()}
                        <div class="col-sm-{if $labelsPerPage == 8}3{elseif $labelsPerPage == 16}2{else}4{/if} left-part">
                            <div class="cover-container {if !$barCodePrintSettings->isPrintBookCover()}no-cover{/if}">
                                {if $barCodePrintSettings->isPrintBookCover()}
                                    {if $book->getCover() != null}
                                        <img src="{$book->getCover()->getWebPath()}" class="img-fluid book-cover">
                                    {else}
                                        <img src="{$siteViewOptions->getOptionValue("noBookImageFilePath")}" class="img-fluid book-cover">
                                    {/if}
                                {/if}

                                {if $barCodePrintSettings->isPrintBookPrice() and $book->getPrice() != null}
                                    <div class="book-price" style="font-size: {$primaryFontSize}em;">{$book->getPrice()} {$siteViewOptions->getOptionValue("priceCurrency")}</div>
                                {/if}
                            </div>
                        </div>
                    {/if}
                    {if $labelsPerPage != 16}
                        <div class="col-sm-{if !$barCodePrintSettings->isPrintBookCover() and !$barCodePrintSettings->isPrintBookPrice()}12 text-center{elseif $labelsPerPage == 8}9{elseif $labelsPerPage == 16}10{else}8{/if} right-part">
                            <!--<div class="book-meta">2014</div>-->
                            {if $barCodePrintSettings->isPrintBookISBN()}
                                {if $book->getISBN13() != null}
                                    <div class="book-meta" style="font-size: {$secondaryFontSize}em;">{$book->getISBN13()}</div>
                                {else}
                                    <div class="book-meta" style="font-size: {$secondaryFontSize}em;">{$book->getISBN10()}</div>
                                {/if}
                            {/if}
                            {if $book->getPublisher() != null and $barCodePrintSettings->isPrintBookPublisher()}
                                <div class="book-meta" style="font-size: {$secondaryFontSize}em;">{$book->getPublisher()->getName()}</div>
                            {/if}

                            {if $book->getGenres() !== null and is_array($book->getGenres()) and count($book->getGenres()) > 0 and $barCodePrintSettings->isPrintBookGenre()}
                                <div class="book-meta" style="font-size: {$secondaryFontSize}em;">
                                    {foreach from=$book->getGenres() item=genre name=genre}
                                        {$genre->getName()}{if $smarty.foreach.genre.last}{else},{/if}
                                    {/foreach}
                                </div>
                            {/if}
                            {if $book->getAuthors() !== null and is_array($book->getAuthors()) and count($book->getAuthors()) > 0 and $barCodePrintSettings->isPrintBookAuthor()}
                                <div class="book-meta" style="font-size: {$secondaryFontSize}em;">
                                    {foreach from=$book->getAuthors() item=author name=author}
                                        {$author->getLastName()} {$author->getFirstName()}{if $smarty.foreach.author.last}{else},{/if}
                                    {/foreach}
                                </div>
                            {/if}
                        </div>
                    {/if}
                    <div class="col-sm-{if $labelsPerPage == 16}10{else}12{/if} text-center" style="{if $labelsPerPage == 12}margin: 4mm 0 0;{/if}{if $labelsPerPage == 16}padding-top: 3mm;{/if}">
                        {assign var=rawISBN10 value=substr($book->getISBN10(),0,9)}
                        {assign var="prefix" value=978}
                        {assign var="isbnnum" value=$prefix|cat:$rawISBN10}
                        {assign var="total" value=0}
                        {for $x=0 to 11}
                            {if ($x % 2) == 0}
                                {assign var="y" value=1}
                            {else}
                                {assign var="y" value=3}
                            {/if}
                            {assign var="total" value=$total+(substr($isbnnum,$x,1) * $y)}
                        {/for}
                        {assign var=mod10 value=(10 - ($total % 10)) % 10}
                        <svg class="barcode"
                             jsbarcode-format="EAN13"
                             jsbarcode-value="{if $book->getISBN13() != null}{$book->getISBN13()}{else}978{$rawISBN10}{$mod10}{/if}"
                             jsbarcode-textmargin="0"
                             jsbarcode-fontsize="14"
                             jsbarcode-height="{if $labelsPerPage == 40}40{else}50{/if}"
                             {if $labelsPerPage == 40}jsbarcode-width="1"{/if}
                             jsbarcode-margin="0">
                        </svg>
                    </div>
                    {if $barCodePrintSettings->getCustomText() != null}
                        <div class="col-sm-12" style="font-size: {$secondaryFontSize}em;">
                            <div class="book-meta custom-text">{$barCodePrintSettings->getCustomText()}</div>
                        </div>
                    {/if}
                </div>
            </div>
            {assign var="index" value=$index+1}
            {if ($index % $labelsPerPage) == 0}
                </div>
            {/if}
        {/for}
    {/foreach}
    {if ($index % $labelsPerPage) != 0}
        </div>
    {/if}
{/if}