{if isset($smarty.session.messages) and count($smarty.session.messages) > 0}
    <div class="row">
        <div class="col-sm-12">
            {foreach from=$smarty.session.messages item=message}
                <div class="alert {if $message->getStatus() == 'INFO'}alert-success{elseif strcmp($message->getStatus(),'WARNING') === 0}alert-warning{else}alert-danger{/if} alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {$message->getMessage()}
                </div>
            {/foreach}
        </div>
    </div>
{/if}