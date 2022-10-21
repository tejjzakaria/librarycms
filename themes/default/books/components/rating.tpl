    {*<ul class="rating-stars {if $readOnly}readOnly{/if} d-inline-block" {if $showTooltip}data-toggle="tooltip" data-placement="top" title="{$tooltipMessage}"{/if}>
        {for $ratingIndex=1 to 5}
            {if $rating >= $ratingIndex}
                <li class="star selected" data-value="{$ratingIndex}">
                    <i class="fas fa-star"></i>
                </li>
            {elseif ($ratingIndex - $rating) <= 0.5 and ($ratingIndex - $rating) < 1}
                <li class="star selected" data-value="{$ratingIndex}">
                    <i class="fa  fa-star-half"></i>
                </li>
            {else}
                <li class="star" data-value="{$ratingIndex}">
                    <i class="fas fa-star"></i>
                </li>
            {/if}
        {/for}
    </ul>*}


    <div class="book-rating-box">
        <div class="rating" style="width:{$rating*20}%;"></div>
    </div>