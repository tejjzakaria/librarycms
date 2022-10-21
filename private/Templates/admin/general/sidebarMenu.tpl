<nav class="sidebar-nav">
    <ul id="sidebar-menu">
        {if isset($user) and $user->getRole() != null and $user->getRole()->getPriority() > 100}
            <li class="{if $activeRoute->getName() == 'adminIndex'}active{/if}">
                <a href="{$routes->getRouteString("adminIndex")}" aria-expanded="false"><img src="{$resourcePath}assets/images/icons/dashboard.png" alt=""><span class="hide-menu">{t}Dashboard{/t}
                </a>
            </li>
            <li class="{if $activeRoute->getName() == 'bookListView' or $activeRoute->getName() == 'bookCreate' or $activeRoute->getName() == 'bookEdit'}active{/if}">
                <a class="has-arrow " href="#" aria-expanded="false"><img src="{$resourcePath}assets/images/icons/books.png" alt=""><span class="hide-menu">{t}Books{/t}</span></a>
                <ul aria-expanded="false" class="collapse">
                    <li class="{if $activeRoute->getName() == 'bookListView'}active{/if}">
                        <a href="{$routes->getRouteString("bookListView")}" class="{if $activeRoute->getName() == 'bookListView'}active{/if}">{t}All Books{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'bookCreate'}active{/if}">
                        <a href="{$routes->getRouteString("bookCreate")}" class="{if $activeRoute->getName() == 'bookCreate'}active{/if}">{t}Add Book{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'publisherListView' or $activeRoute->getName() == 'publisherCreate' or $activeRoute->getName() == 'publisherEdit'}active{/if}">
                        <a class="has-arrow " href="#" aria-expanded="false">{t}Publishers{/t}</a>
                        <ul aria-expanded="false" class="collapse">
                            <li class="{if $activeRoute->getName() == 'publisherListView'}active{/if}">
                                <a href="{$routes->getRouteString("publisherListView")}" class="{if $activeRoute->getName() == 'publisherListView'}active{/if}">{t}All Publishers{/t}</a>
                            </li>
                            <li class="{if $activeRoute->getName() == 'publisherCreate'}active{/if}">
                                <a href="{$routes->getRouteString("publisherCreate")}" class="{if $activeRoute->getName() == 'publisherCreate'}active{/if}">{t}Add Publisher{/t}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{if $activeRoute->getName() == 'seriesListView' or $activeRoute->getName() == 'seriesCreate' or $activeRoute->getName() == 'seriesEdit'}active{/if}">
                        <a class="has-arrow " href="#" aria-expanded="false">{t}Series{/t}</a>
                        <ul aria-expanded="false" class="collapse">
                            <li class="{if $activeRoute->getName() == 'seriesListView'}active{/if}">
                                <a href="{$routes->getRouteString("seriesListView")}" class="{if $activeRoute->getName() == 'seriesListView'}active{/if}">{t}All Series{/t}</a>
                            </li>
                            <li class="{if $activeRoute->getName() == 'seriesCreate'}active{/if}">
                                <a href="{$routes->getRouteString("seriesCreate")}" class="{if $activeRoute->getName() == 'seriesCreate'}active{/if}">{t}Add Series{/t}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{if $activeRoute->getName() == 'authorListView' or $activeRoute->getName() == 'authorCreate' or $activeRoute->getName() == 'authorEdit'}active{/if}">
                        <a class="has-arrow " href="#" aria-expanded="false">{t}Authors{/t}</a>
                        <ul aria-expanded="false" class="collapse">
                            <li class="{if $activeRoute->getName() == 'authorListView'}active{/if}">
                                <a href="{$routes->getRouteString("authorListView")}" class="{if $activeRoute->getName() == 'authorListView'}active{/if}">{t}All Authors{/t}</a>
                            </li>
                            <li class="{if $activeRoute->getName() == 'authorCreate'}active{/if}">
                                <a href="{$routes->getRouteString("authorCreate")}" class="{if $activeRoute->getName() == 'authorCreate'}active{/if}">{t}Add Author{/t}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{if $activeRoute->getName() == 'genreListView' or $activeRoute->getName() == 'genreCreate' or $activeRoute->getName() == 'genreEdit'}active{/if}">
                        <a class="has-arrow " href="#" aria-expanded="false">{t}Genres{/t}</a>
                        <ul aria-expanded="false" class="collapse">
                            <li class="{if $activeRoute->getName() == 'genreListView'}active{/if}">
                                <a href="{$routes->getRouteString("genreListView")}" class="{if $activeRoute->getName() == 'genreListView'}active{/if}">{t}All Genres{/t}</a>
                            </li>
                            <li class="{if $activeRoute->getName() == 'genreCreate'}active{/if}">
                                <a href="{$routes->getRouteString("genreCreate")}" class="{if $activeRoute->getName() == 'genreCreate'}active{/if}">{t}Add Genre{/t}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{if $activeRoute->getName() == 'tagListView' or $activeRoute->getName() == 'tagCreate' or $activeRoute->getName() == 'tagEdit'}active{/if}">
                        <a class="has-arrow " href="#" aria-expanded="false">{t}Tags{/t}</a>
                        <ul aria-expanded="false" class="collapse">
                            <li class="{if $activeRoute->getName() == 'tagListView'}active{/if}">
                                <a href="{$routes->getRouteString("tagListView")}" class="{if $activeRoute->getName() == 'tagListView'}active{/if}">{t}All Tags{/t}</a>
                            </li>
                            <li class="{if $activeRoute->getName() == 'tagCreate'}active{/if}">
                                <a href="{$routes->getRouteString("tagCreate")}" class="{if $activeRoute->getName() == 'tagCreate'}active{/if}">{t}Add Tag{/t}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{if $activeRoute->getName() == 'storeListView' or $activeRoute->getName() == 'storeCreate' or $activeRoute->getName() == 'locationListView' or $activeRoute->getName() == 'locationCreate'}active{/if}">
                        <a class="has-arrow " href="#" aria-expanded="false">{t}Stores & Locations{/t}</a>
                        <ul aria-expanded="false" class="collapse">
                            <li class="{if $activeRoute->getName() == 'storeListView'}active{/if}">
                                <a href="{$routes->getRouteString("storeListView")}" class="{if $activeRoute->getName() == 'storeListView'}active{/if}">{t}All Stores{/t}</a>
                            </li>
                            <li class="{if $activeRoute->getName() == 'storeCreate'}active{/if}">
                                <a href="{$routes->getRouteString("storeCreate")}" class="{if $activeRoute->getName() == 'storeCreate'}active{/if}">{t}Add Store{/t}</a>
                            </li>
                            <li class="{if $activeRoute->getName() == 'locationListView'}active{/if}">
                                <a href="{$routes->getRouteString("locationListView")}" class="{if $activeRoute->getName() == 'locationListView'}active{/if}">{t}All Locations{/t}</a>
                            </li>
                            <li class="{if $activeRoute->getName() == 'locationCreate'}active{/if}">
                                <a href="{$routes->getRouteString("locationCreate")}" class="{if $activeRoute->getName() == 'locationCreate'}active{/if}">{t}Add Location{/t}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{if $activeRoute->getName() == 'reviewListView' or $activeRoute->getName() == 'reviewCreate' or $activeRoute->getName() == 'reviewEdit'}active{/if}">
                        <a class="has-arrow " href="#" aria-expanded="false">{t}Reviews{/t}</a>
                        <ul aria-expanded="false" class="collapse">
                            <li class="{if $activeRoute->getName() == 'reviewListView'}active{/if}">
                                <a href="{$routes->getRouteString("reviewListView")}" class="{if $activeRoute->getName() == 'reviewListView'}active{/if}">{t}All Reviews{/t}</a>
                            </li>
                            <li class="{if $activeRoute->getName() == 'reviewCreate'}active{/if}">
                                <a href="{$routes->getRouteString("reviewCreate")}" class="{if $activeRoute->getName() == 'reviewCreate'}active{/if}">{t}Add Review{/t}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{if $activeRoute->getName() == 'bookBulkBarCodeGenerate'}active{/if}">
                        <a href="{$routes->getRouteString("bookBulkBarCodeGenerate")}" class="{if $activeRoute->getName() == 'bookBulkBarCodeGenerate'}active{/if}">{t}Book Barcode Generation{/t}</a>
                    </li>
                    {if isset($user) and $user->getRole() != null and $user->getRole()->getPriority() >= 255}
                        <li class="{if $activeRoute->getName() == 'importExport'}active{/if}">
                            <a href="{$routes->getRouteString("importExport")}" class="{if $activeRoute->getName() == 'importExport'}active{/if}">{t}Import & Export CSV{/t}</a>
                        </li>
                    {/if}
                </ul>
            </li>
            {if $siteViewOptions->getOptionValue("enableBookIssue")}
            <li class="{if $activeRoute->getName() == 'issueListView'}active{/if}">
                <a href="{$routes->getRouteString("issueListView")}" aria-expanded="false"><img src="{$resourcePath}assets/images/icons/issue-book.png" alt=""><span class="hide-menu">{t}Issued Books{/t}</span></a>
            </li>
            {/if}
            {if $siteViewOptions->getOptionValue("enableBookRequest")}
            <li class="{if $activeRoute->getName() == 'requestListView'}active{/if}">
                <a href="{$routes->getRouteString("requestListView")}" aria-expanded="false"><img src="{$resourcePath}assets/images/icons/req-book.png" alt=""><span class="hide-menu">{t}Requested Books{/t}</span></a>
            </li>
            {/if}
            <li class="{if $activeRoute->getName() == 'postListView' or $activeRoute->getName() == 'createPost'}active{/if}">
                <a class="has-arrow " href="#" aria-expanded="false"><img src="{$resourcePath}assets/images/icons/public.png" alt=""><span class="hide-menu">{t}Public{/t}</span></a>
                <ul aria-expanded="false" class="collapse">
                    <li class="{if $activeRoute->getName() == 'postListView' or $activeRoute->getName() == 'postCreate' or $activeRoute->getName() == 'postEdit'}active{/if}">
                        <a class="has-arrow " href="#" aria-expanded="false">{t}Posts{/t}</a>
                        <ul aria-expanded="false" class="collapse">
                            <li class="{if $activeRoute->getName() == 'postListView'}active{/if}">
                                <a href="{$routes->getRouteString("postListView")}" class="{if $activeRoute->getName() == 'postListView'}active{/if}">{t}All Posts{/t}</a>
                            </li>
                            <li class="{if $activeRoute->getName() == 'postCreate'}active{/if}">
                                <a href="{$routes->getRouteString("postCreate")}" class="{if $activeRoute->getName() == 'postCreate'}active{/if}">{t}Add Post{/t}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{if $activeRoute->getName() == 'pageListView' or $activeRoute->getName() == 'pageCreate' or $activeRoute->getName() == 'pageEdit'}active{/if}">
                        <a class="has-arrow " href="#" aria-expanded="false">{t}Pages{/t}</a>
                        <ul aria-expanded="false" class="collapse">
                            <li class="{if $activeRoute->getName() == 'pageListView'}active{/if}">
                                <a href="{$routes->getRouteString("pageListView")}" class="{if $activeRoute->getName() == 'pageListView'}active{/if}">{t}All Pages{/t}</a>
                            </li>
                            <li class="{if $activeRoute->getName() == 'pageCreate'}active{/if}">
                                <a href="{$routes->getRouteString("pageCreate")}" class="{if $activeRoute->getName() == 'pageCreate'}active{/if}">{t}Add Page{/t}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{if $activeRoute->getName() == 'categoryListView'}active{/if}">
                        <a href="{$routes->getRouteString("categoryListView")}" class="{if $activeRoute->getName() == 'categoryListView'}active{/if}">{t}Categories{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'userMessageListView'}active{/if}">
                        <a href="{$routes->getRouteString("userMessageListView")}" class="{if $activeRoute->getName() == 'userMessageListView'}active{/if}">{t}Messages{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'menuListView' or $activeRoute->getName() == 'menuCreate' or $activeRoute->getName() == 'menuEdit'}active{/if}">
                        <a class="has-arrow " href="#" aria-expanded="false">{t}Menus{/t}</a>
                        <ul aria-expanded="false" class="collapse">
                            <li class="{if $activeRoute->getName() == 'menuListView'}active{/if}">
                                <a href="{$routes->getRouteString("menuListView")}" class="{if $activeRoute->getName() == 'menuListView'}active{/if}">{t}All Menus{/t}</a>
                            </li>
                            <li class="{if $activeRoute->getName() == 'menuCreate'}active{/if}">
                                <a href="{$routes->getRouteString("menuCreate")}" class="{if $activeRoute->getName() == 'menuCreate'}active{/if}">{t}Add Menu{/t}</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="{if $activeRoute->getName() == 'userListView' or $activeRoute->getName() == 'userCreate' or $activeRoute->getName() == 'userEdit' or $activeRoute->getName() == 'roleListView' or $activeRoute->getName() == 'roleCreate' or $activeRoute->getName() == 'roleEdit'}active{/if}">
                <a class="has-arrow " href="#" aria-expanded="false"><img src="{$resourcePath}assets/images/icons/users.png" alt=""><span class="hide-menu">{t}Users{/t}</span></a>
                <ul aria-expanded="false" class="collapse">
                    <li class="{if $activeRoute->getName() == 'userListView'}active{/if}">
                        <a href="{$routes->getRouteString("userListView")}" class="{if $activeRoute->getName() == 'userListView'}active{/if}">{t}All Users{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'userCreate'}active{/if}">
                        <a href="{$routes->getRouteString("userCreate")}" class="{if $activeRoute->getName() == 'userCreate'}active{/if}">{t}Add User{/t}</a>
                    </li>
                    {if isset($user) and $user->getRole() != null and $user->getRole()->getPriority() >= 255}
                        <li class="{if $activeRoute->getName() == 'roleListView'}active{/if}">
                            <a href="{$routes->getRouteString("roleListView")}" class="{if $activeRoute->getName() == 'roleListView'}active{/if}">{t}All Roles{/t}</a>
                        </li>
                        <li class="{if $activeRoute->getName() == 'roleCreate'}active{/if}">
                            <a href="{$routes->getRouteString("roleCreate")}" class="{if $activeRoute->getName() == 'roleCreate'}active{/if}">{t}Add Role{/t}</a>
                        </li>
                    {/if}
                </ul>
            </li>
        {/if}
        {if isset($user) and $user->getRole() != null and $user->getRole()->getPriority() >= 255}
            <li class="{if $activeRoute->getName() == 'permissionListUpdate' or $activeRoute->getName() == 'optionListView'}active{/if}">
                <a class="has-arrow " href="#" aria-expanded="false"><img src="{$resourcePath}assets/images/icons/settings.png" alt=""><span class="hide-menu">{t}Settings{/t}</span></a>
                <ul aria-expanded="false" class="collapse">
                    <li class="{if $activeRoute->getName() == 'themesListView'}active{/if}">
                        <a href="{$routes->getRouteString("themesListView")}" class="{if $activeRoute->getName() == 'themesListView'}active{/if}">{t}Themes{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'emailNotificationListView' or $activeRoute->getName() == 'emailNotificationCreate' or $activeRoute->getName() == 'staticShortCodeListView' or $activeRoute->getName() == 'dynamicShortCodeListView'}active{/if}">
                        <a class="has-arrow " href="#" aria-expanded="false">{t}Notifications{/t}</a>
                        <ul aria-expanded="false" class="collapse">
                            <li class="{if $activeRoute->getName() == 'emailNotificationListView'}active{/if}">
                                <a href="{$routes->getRouteString("emailNotificationListView")}" class="{if $activeRoute->getName() == 'emailNotificationListView'}active{/if}">{t}All Notifications{/t}</a>
                            </li>
                            {*<li class="{if $activeRoute->getName() == 'emailNotificationCreate'}active{/if}">
                                <a href="{$routes->getRouteString("emailNotificationCreate")}" class="{if $activeRoute->getName() == 'emailNotificationCreate'}active{/if}">{t}Add Notification{/t}</a>
                            </li>*}
                            <li class="{if $activeRoute->getName() == 'staticShortCodeListView'}active{/if}">
                                <a href="{$routes->getRouteString("staticShortCodeListView")}" class="{if $activeRoute->getName() == 'staticShortCodeListView'}active{/if}">{t}Static ShortCodes{/t}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{if $activeRoute->getName() == 'googleSettings'}active{/if}">
                        <a href="{$routes->getRouteString("googleSettings")}" class="{if $activeRoute->getName() == 'googleSettings'}active{/if}">{t}Google Books Settings{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'emailSettings'}active{/if}">
                        <a href="{$routes->getRouteString("emailSettings")}" class="{if $activeRoute->getName() == 'emailSettings'}active{/if}">{t}Email Settings{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'smtpSettings'}active{/if}">
                        <a href="{$routes->getRouteString("smtpSettings")}" class="{if $activeRoute->getName() == 'smtpSettings'}active{/if}">{t}SMTP Settings{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'smsSettings'}active{/if}">
                        <a href="{$routes->getRouteString("smsSettings")}" class="{if $activeRoute->getName() == 'smsSettings'}active{/if}">{t}SMS Settings{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'socialNetworkSettings'}active{/if}">
                        <a href="{$routes->getRouteString("socialNetworkSettings")}" class="{if $activeRoute->getName() == 'socialNetworkSettings'}active{/if}">{t}Social Network Settings{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'languageListView'}active{/if}">
                        <a href="{$routes->getRouteString("languageListView")}" class="{if $activeRoute->getName() == 'languageListView'}active{/if}">{t}Languages{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'permissionListUpdate'}active{/if}">
                        <a href="{$routes->getRouteString("permissionListUpdate")}" class="{if $activeRoute->getName() == 'permissionListUpdate'}active{/if}">{t}Permissions{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'rssSettings'}active{/if}">
                        <a href="{$routes->getRouteString("rssSettings")}" class="{if $activeRoute->getName() == 'rssSettings'}active{/if}">{t}RSS Settings{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'ldapSettings'}active{/if}">
                        <a href="{$routes->getRouteString("ldapSettings")}" class="{if $activeRoute->getName() == 'ldapSettings'}active{/if}">{t}LDAP Settings{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'databaseSettings'}active{/if}">
                        <a href="{$routes->getRouteString("databaseSettings")}" class="{if $activeRoute->getName() == 'databaseSettings'}active{/if}">{t}Database Setting{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'bookFieldListView' or $activeRoute->getName() == 'bookFieldCreate' or $activeRoute->getName() == 'bookFieldEdit' or $activeRoute->getName() == 'bindingListView' or $activeRoute->getName() == 'bookSizeListView' or $activeRoute->getName() == 'bookTypeListView' or $activeRoute->getName() == 'physicalFormListView' or $activeRoute->getName() == 'bookVisibleFieldsForPublic'}active{/if}">
                        <a class="has-arrow" href="#" aria-expanded="false">{t}Book Fields{/t}</a>
                        <ul aria-expanded="false" class="collapse">
                            <li class="{if $activeRoute->getName() == 'bookFieldListView' or $activeRoute->getName() == 'bookFieldCreate' or $activeRoute->getName() == 'bookFieldEdit'}active{/if}">
                                <a class="has-arrow" href="#" aria-expanded="false">{t}Custom Fields{/t}</a>
                                <ul aria-expanded="false" class="collapse">
                                    <li class="{if $activeRoute->getName() == 'bookFieldListView'}active{/if}">
                                        <a href="{$routes->getRouteString("bookFieldListView")}" class="{if $activeRoute->getName() == 'bookFieldListView'}active{/if}">{t}All Custom Fields{/t}</a>
                                    </li>
                                    <li class="{if $activeRoute->getName() == 'bookFieldCreate'}active{/if}">
                                        <a href="{$routes->getRouteString("bookFieldCreate")}" class="{if $activeRoute->getName() == 'bookFieldCreate'}active{/if}">{t}Add Custom Field{/t}</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="{if $activeRoute->getName() == 'bindingListView' or $activeRoute->getName() == 'bookSizeListView' or $activeRoute->getName() == 'bookTypeListView' or $activeRoute->getName() == 'physicalFormListView'}active{/if}">
                                <a class="has-arrow" href="#" aria-expanded="false">{t}Default Fields{/t}</a>
                                <ul aria-expanded="false" class="collapse">
                                    <li class="{if $activeRoute->getName() == 'bindingListView'}active{/if}">
                                        <a href="{$routes->getRouteString("bindingListView")}" class="{if $activeRoute->getName() == 'bindingListView'}active{/if}">{t}Bindings{/t}</a>
                                    </li>
                                    <li class="{if $activeRoute->getName() == 'bookSizeListView'}active{/if}">
                                        <a href="{$routes->getRouteString("bookSizeListView")}" class="{if $activeRoute->getName() == 'bookSizeListView'}active{/if}">{t}Sizes{/t}</a>
                                    </li>
                                    <li class="{if $activeRoute->getName() == 'bookTypeListView'}active{/if}">
                                        <a href="{$routes->getRouteString("bookTypeListView")}" class="{if $activeRoute->getName() == 'bookTypeListView'}active{/if}">{t}Types{/t}</a>
                                    </li>
                                    <li class="{if $activeRoute->getName() == 'physicalFormListView'}active{/if}">
                                        <a href="{$routes->getRouteString("physicalFormListView")}" class="{if $activeRoute->getName() == 'physicalFormListView'}active{/if}">{t}Physical Forms{/t}</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="{if $activeRoute->getName() == 'bookVisibleFieldsForPublic'}active{/if}">
                                <a href="{$routes->getRouteString("bookVisibleFieldsForPublic")}" class="{if $activeRoute->getName() == 'bookVisibleFieldsForPublic'}active{/if}">{t}Visible Fields{/t}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{if $activeRoute->getName() == 'bookLayout'}active{/if}">
                        <a href="{$routes->getRouteString("bookLayout")}" class="{if $activeRoute->getName() == 'bookLayout'}active{/if}">{t}Book Layout{/t}</a>
                    </li>
                    <li class="{if $activeRoute->getName() == 'filterSettings'}active{/if}">
                        <a href="{$routes->getRouteString("filterSettings")}" class="{if $activeRoute->getName() == 'filterSettings'}active{/if}">{t}Book Filter Settings{/t}</a>
                    </li>

                    <li class="{if $activeRoute->getName() == 'bookUrlGenerate'}active{/if}">
                        <a href="{$routes->getRouteString("bookUrlGenerate")}" class="{if $activeRoute->getName() == 'bookUrlGenerate'}active{/if}">{t}Book URL Generate{/t}</a>
                    </li>

                    <li class="{if $activeRoute->getName() == 'optionListView'}active{/if}">
                        <a href="{$routes->getRouteString("optionListView")}" class="{if $activeRoute->getName() == 'optionListView'}active{/if}">{t}Site View Setting{/t}</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{$resourcePath}docs" target="_blank" aria-expanded="false"><img src="{$resourcePath}assets/images/icons/docs.png" alt=""><span class="hide-menu">{t}Documentation{/t}</span></a>
            </li>
        {/if}
    </ul>
</nav>