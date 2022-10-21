<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes\Admin;


    use KAASoft\Environment\Routes\AdminRoute;
    use KAASoft\Environment\Routes\PublicRoute;
    use KAASoft\Environment\Routes\RoutesInterface;

    /**
     * Class UtilRoutes
     * @package KAASoft\Environment\Routes\Admin
     */
    class UtilRoutes implements RoutesInterface {

        const OPTION_LIST_VIEW_ROUTE        = "optionListView";
        const GOOGLE_SETTINGS_ROUTE         = "googleSettings";
        const FILTER_SETTINGS_ROUTE         = "filterSettings";
        const LDAP_SETTINGS_ROUTE           = "ldapSettings";
        const LDAP_TEST_ROUTE               = "ldapTest";
        const DATABASE_SETTINGS_ROUTE       = "databaseSettings";
        const BINDING_LIST_VIEW_ROUTE       = "bindingListView";
        const BOOK_SIZE_LIST_VIEW_ROUTE     = "bookSizeListView";
        const BOOK_TYPE_LIST_VIEW_ROUTE     = "bookTypeListView";
        const PHYSICAL_FORM_LIST_VIEW_ROUTE = "physicalFormListView";
        const LANGUAGE_LIST_VIEW_ROUTE      = "languageListView";
        const LANGUAGE_CHANGE_ROUTE         = "languageChange";
        const ROLE_LIST_VIEW_ROUTE          = "roleListView";
        const ROLE_CREATE_ROUTE             = "roleCreate";
        const ROLE_EDIT_ROUTE               = "roleEdit";
        const ROLE_DELETE_ROUTE             = "roleDelete";
        const ROLE_SEARCH_ROUTE             = "roleSearch";
        const PERMISSION_LIST_UPDATE_ROUTE  = "permissionListUpdate";
        const IMPORT_CSV_ROUTE              = "importCSV";
        const EXPORT_CSV_ROUTE              = "exportCSV";
        const IMPORT_EXPORT_ROUTE           = "importExport";
        const MENU_LIST_VIEW_ROUTE          = "menuListView";
        const MENU_CREATE_ROUTE             = "menuCreate";
        const MENU_EDIT_ROUTE               = "menuEdit";
        const MENU_DELETE_ROUTE             = "menuDelete";
        const MENU_ITEMS_EDIT_ROUTE         = "menuItemsEdit";
        const SOCIAL_NETWORK_SETTINGS_ROUTE = "socialNetworkSettings";
        const SMS_SETTINGS_ROUTE            = "smsSettings";
        const SMS_SEND_ROUTE                = "smsSend";
        const SMS_GET_BALANCE_ROUTE         = "smsGetBalance";
        const RSS_SETTINGS_ROUTE            = "rssSettings";


        const STATIC_SHORT_CODE_LIST_VIEW_ROUTE  = "staticShortCodeListView";
        const DYNAMIC_SHORT_CODE_LIST_VIEW_ROUTE = "dynamicShortCodeListView";
        const SITE_VIEW_OPTION_FILE_UPLOAD_ROUTE = "siteViewOptionFileUpload";
        const SITE_VIEW_OPTION_FILE_DELETE_ROUTE = "siteViewOptionFileDelete";
        const SITEMAP_GENERATE_ROUTE             = "sitemapGenerate";

        const THEMES_LIST_VIEW_ROUTE      = "themesListView";
        const THEME_ACTIVATE_ROUTE        = "themeActivate";
        const COLOR_SCHEMA_ACTIVATE_ROUTE = "colorSchemaActivate";


        public static function getRoutes() {
            $routes = [];
            /*************************************  UTIL **************************************************************/
            $routes[UtilRoutes::THEMES_LIST_VIEW_ROUTE] = new AdminRoute(_("Themes"),
                                                                         "/themes[/]??",
                                                                         "Admin\\Theme\\ThemesAction",
                                                                         "/themes");

            $routes[UtilRoutes::THEME_ACTIVATE_ROUTE] = new AdminRoute(_("Theme Activate"),
                                                                       "/theme/activate[/]??",
                                                                       "Admin\\Theme\\ThemeActivateAction",
                                                                       "/theme/activate");

            $routes[UtilRoutes::COLOR_SCHEMA_ACTIVATE_ROUTE] = new AdminRoute(_("Color Schema Activate"),
                                                                              "/color-schema/activate[/]??",
                                                                              "Admin\\Theme\\ColorSchemaActivateAction",
                                                                              "/color-schema/activate");

            $routes[UtilRoutes::SITEMAP_GENERATE_ROUTE] = new AdminRoute(_("Generate Sitemap"),
                                                                         "/sitemap/generate[/]??",
                                                                         "Admin\\Util\\SitemapGenerateAction",
                                                                         "/sitemap/generate");

            $routes[UtilRoutes::GOOGLE_SETTINGS_ROUTE] = new AdminRoute(_("Google Settings"),
                                                                        "/google-settings[/]??",
                                                                        "Admin\\Util\\GoogleSettingsEditAction",
                                                                        "/google-settings");

            $routes[UtilRoutes::FILTER_SETTINGS_ROUTE] = new AdminRoute(_("Filter Settings"),
                                                                        "/filter-settings[/]??",
                                                                        "Admin\\Util\\FilterSettingsEditAction",
                                                                        "/filter-settings");

            $routes[UtilRoutes::LDAP_SETTINGS_ROUTE] = new AdminRoute(_("LDAP Settings"),
                                                                      "/ldap-settings[/]??",
                                                                      "Admin\\Util\\LdapSettingsEditAction",
                                                                      "/ldap-settings");

            $routes[UtilRoutes::LDAP_TEST_ROUTE] = new AdminRoute(_("LDAP Test"),
                                                                  "/ldap-test[/]??",
                                                                  "Admin\\Util\\LdapTestAction",
                                                                  "/ldap-test");

            $routes[UtilRoutes::OPTION_LIST_VIEW_ROUTE] = new AdminRoute(_("Site View Option List View"),
                                                                         "/options[/]??",
                                                                         "Admin\\Util\\SiteViewOptionsEditAction",
                                                                         "/options");

            $routes[UtilRoutes::LANGUAGE_LIST_VIEW_ROUTE] = new AdminRoute(_("Language List View"),
                                                                           "/languages[/]??",
                                                                           "Admin\\Util\\LanguagesViewAction",
                                                                           "/languages");

            $routes[UtilRoutes::BINDING_LIST_VIEW_ROUTE] = new AdminRoute(_("Binding List View"),
                                                                          "/bindings[/]??",
                                                                          "Admin\\Util\\BindingsViewAction",
                                                                          "/bindings");

            $routes[UtilRoutes::BOOK_SIZE_LIST_VIEW_ROUTE] = new AdminRoute(_("Book Sizes List View"),
                                                                            "/book-sizes[/]??",
                                                                            "Admin\\Util\\BookSizesViewAction",
                                                                            "/book-sizes");

            $routes[UtilRoutes::BOOK_TYPE_LIST_VIEW_ROUTE] = new AdminRoute(_("Book Type List View"),
                                                                            "/book-types[/]??",
                                                                            "Admin\\Util\\BookTypesViewAction",
                                                                            "/book-types");

            $routes[UtilRoutes::PHYSICAL_FORM_LIST_VIEW_ROUTE] = new AdminRoute(_("Physical Form List View"),
                                                                                "/physical-forms[/]??",
                                                                                "Admin\\Util\\PhysicalFormsViewAction",
                                                                                "/physical-forms");

            $routes[UtilRoutes::LANGUAGE_CHANGE_ROUTE] = new PublicRoute(_("Language Change"),
                                                                         "/languageChange/([a-z]{2}_[A-Z]{2})[/]??",
                                                                         "Admin\\Util\\LanguageChangeAction",
                                                                         "/languageChange/[languageCode]",
                                                                         [ "languageCode" ]);

            $routes[UtilRoutes::ROLE_SEARCH_ROUTE] = new AdminRoute(_("Role Search"),
                                                                    "/role/search[/]??",
                                                                    "Admin\\Util\\Role\\RoleSearchAction",
                                                                    "/role/search");

            $routes[UtilRoutes::ROLE_LIST_VIEW_ROUTE] = new AdminRoute(_("Role List View"),
                                                                       "/roles[/]??",
                                                                       "Admin\\Util\\Role\\RolesAction",
                                                                       "/roles");

            $routes[UtilRoutes::ROLE_CREATE_ROUTE] = new AdminRoute(_("Role Create"),
                                                                    "/role/create[/]??",
                                                                    "Admin\\Util\\Role\\RoleCreateAction",
                                                                    "/role/create/");

            $routes[UtilRoutes::ROLE_EDIT_ROUTE] = new AdminRoute(_("Role Edit"),
                                                                  "/role/(\\d+)/edit[/]??",
                                                                  "Admin\\Util\\Role\\RoleEditAction",
                                                                  "/role/[roleId]/edit",
                                                                  [ "roleId" ]);

            $routes[UtilRoutes::ROLE_DELETE_ROUTE] = new AdminRoute(_("Role Delete"),
                                                                    "/role/(\\d+)/delete[/]??",
                                                                    "Admin\\Util\\Role\\RoleDeleteAction",
                                                                    "/role/[roleId]/delete",
                                                                    [ "roleId" ]);

            $routes[UtilRoutes::PERMISSION_LIST_UPDATE_ROUTE] = new AdminRoute(_("Permission List Update"),
                                                                               "/permissions/update[/]??",
                                                                               "Admin\\Util\\Permission\\PermissionListUpdateAction",
                                                                               "/permissions/update");

            $routes[UtilRoutes::IMPORT_CSV_ROUTE] = new AdminRoute(_("Import CSV"),
                                                                   "/import/csv[/]??",
                                                                   "Admin\\Util\\Import\\ImportCsvAction",
                                                                   "/import/csv");

            $routes[UtilRoutes::EXPORT_CSV_ROUTE] = new AdminRoute(_("Export CSV"),
                                                                   "/export/csv[/]??",
                                                                   "Admin\\Util\\Export\\ExportCsvAction",
                                                                   "/export/csv");
            $routes[UtilRoutes::IMPORT_EXPORT_ROUTE] = new AdminRoute(_("Import/Export"),
                                                                      "/import-export[/]??",
                                                                      "Admin\\Util\\ImportExportAction",
                                                                      "/import-export");

            $routes[UtilRoutes::MENU_LIST_VIEW_ROUTE] = new AdminRoute(_('Menu List View'),
                                                                       '/menus(?:/page/(\d+))?[/]??',
                                                                       'Admin\\Menu\\MenusAction',
                                                                       '/menus',
                                                                       [ 'page' ]);

            $routes[UtilRoutes::MENU_CREATE_ROUTE] = new AdminRoute(_('Menu Create'),
                                                                    '/menu/create[/]??',
                                                                    'Admin\\Menu\\MenuCreateAction',
                                                                    '/menu/create');

            $routes[UtilRoutes::MENU_EDIT_ROUTE] = new AdminRoute(_('Menu Edit'),
                                                                  '/menu/edit/(\d+)[/]??',
                                                                  'Admin\\Menu\\MenuEditAction',
                                                                  '/menu/edit/[menuId]',
                                                                  [ 'menuId' ]);

            $routes[UtilRoutes::MENU_DELETE_ROUTE] = new AdminRoute(_('Menu Delete'),
                                                                    '/menu/delete/(\d+)[/]??',
                                                                    'Admin\\Menu\\MenuDeleteAction',
                                                                    '/menu/delete/[menuId]',
                                                                    [ 'menuId' ]);

            $routes[UtilRoutes::MENU_ITEMS_EDIT_ROUTE] = new AdminRoute(_('Menu Items Edit'),
                                                                        '/menu/(\d+)/menu-items[/]??',
                                                                        'Admin\\Menu\\MenuItemsAction',
                                                                        '/menu/[menuId]/menu-items',
                                                                        [ 'menuId' ]);

            $routes[UtilRoutes::SITE_VIEW_OPTION_FILE_UPLOAD_ROUTE] = new AdminRoute(_("Site View Option File Upload"),
                                                                                     "/site-view-option-file/upload[/]??",
                                                                                     "Admin\\Util\\SiteViewOptions\\SiteViewOptionUploadAction",
                                                                                     "/site-view-option-file/upload");

            $routes[UtilRoutes::SITE_VIEW_OPTION_FILE_DELETE_ROUTE] = new AdminRoute(_("Site View Option File Delete"),
                                                                                     "/site-view-option-file/delete[/]??",
                                                                                     "Admin\\Util\\SiteViewOptions\\SiteViewOptionDeleteAction",
                                                                                     "/site-view-option-file/delete");

            $routes[UtilRoutes::DATABASE_SETTINGS_ROUTE] = new AdminRoute(_("Database Settings"),
                                                                          "/database-settings[/]??",
                                                                          "Admin\\Util\\DatabaseSettingsEditAction",
                                                                          "/database-settings");

            $routes[UtilRoutes::SOCIAL_NETWORK_SETTINGS_ROUTE] = new AdminRoute(_("Social Network Settings"),
                                                                                "/social-network-settings[/]??",
                                                                                "Admin\\Util\\SocialNetworkSettingsEditAction",
                                                                                "/social-network-settings");

            $routes[UtilRoutes::SMS_SETTINGS_ROUTE] = new AdminRoute(_("SMS Settings"),
                                                                     "/sms-settings[/]??",
                                                                     "Admin\\Util\\SmsSettingsEditAction",
                                                                     "/sms-settings");

            $routes[UtilRoutes::RSS_SETTINGS_ROUTE] = new AdminRoute(_("RSS Settings"),
                                                                     "/rss-settings[/]??",
                                                                     "Admin\\Util\\RssSettingsEditAction",
                                                                     "/rss-settings");

            $routes[UtilRoutes::SMS_SEND_ROUTE] = new AdminRoute(_("SMS Send"),
                                                                 "/user/(\\d+)/sms/send[/]??",
                                                                 "Admin\\Util\\SmsSendAction",
                                                                 "/user/[userId]/sms/send",
                                                                 [ "userId" ]);


            $routes[UtilRoutes::SMS_GET_BALANCE_ROUTE] = new AdminRoute(_("SMS Get Balance"),
                                                                        "/sms/balance[/]??",
                                                                        "Admin\\Util\\SmsGetBalanceAction",
                                                                        "/sms/balance");
            /*************************************  UTIL END  *********************************************************/
            /*************************************  SHORT CODES *******************************************************/
            $routes[UtilRoutes::STATIC_SHORT_CODE_LIST_VIEW_ROUTE] = new AdminRoute(_('Static Short Code List View'),
                                                                                    '/short-codes/static[/]??',
                                                                                    'Admin\\Util\\ShortCode\\StaticShortCodesAction',
                                                                                    '/short-codes/static');

            $routes[UtilRoutes::DYNAMIC_SHORT_CODE_LIST_VIEW_ROUTE] = new AdminRoute(_('Dynamic Short Code List View'),
                                                                                     '/short-codes/dynamic[/]??',
                                                                                     'Admin\\Util\\ShortCode\\DynamicShortCodesAction',
                                                                                     '/short-codes/dynamic');

            /*************************************  SHORT CODES END ***************************************************/
            return $routes;
        }
    }