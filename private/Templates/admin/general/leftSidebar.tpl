<aside class="left-sidebar">
    <div class="scrollable-sidebar">
        {if isset($user)}
            <div class="user-profile">
                <div class="profile-img">
                    {if $user->getPhoto() and strcmp($user->getPhoto()->getWebPath(),$siteViewOptions->getOptionValue("noImageFilePath")) !== 0}
                        <img src="{$user->getPhoto()->getWebPath()}" alt="{$user->getFirstName()} {$user->getLastName()}" style="border-radius: 0"/>
                    {else}
                        <img src="{$siteViewOptions->getOptionValue("noUserImageFilePath")}" alt="{$user->getFirstName()}  {$user->getLastName()}"/>
                    {/if}
                </div>
                <div class="profile-text">
                    <p class="name m-0">{$user->getFirstName()} {$user->getLastName()}</p>
                    <p class="designation">{$user->getRole()->getName()}</p>
                </div>
            </div>
        {/if}
        {include file='admin/general/sidebarMenu.tpl'}
    </div>
</aside>