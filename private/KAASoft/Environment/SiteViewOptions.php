<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use Exception;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Util\Language;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\Currency;
    use KAASoft\Util\Enum\Control;
    use KAASoft\Util\FileHelper;

    /**
     * Class SiteViewOptions
     * @package KAASoft\Environment
     */
    class SiteViewOptions {
        const OPTION_GROUPS                 = [];
        const OptionsDefinitionFileNameJSON = "SiteViewOptions.json";

        CONST BOOK_VIEW_TYPE_LIST      = "list";
        CONST BOOK_VIEW_TYPE_MINI_LIST = "miniList";
        CONST BOOK_VIEW_TYPE_GRID      = "grid";

        const ADMIN_COLOR_SCHEMA                          = "adminColorSchema";
        const ENABLE_BOOK_LOGS                            = "enableBookLogs";
        const ENABLE_BOOK_ISSUE                           = "enableBookIssue";
        const ENABLE_BOOK_REQUEST                         = "enableBookRequest";
        const ENABLE_BOOK_ISSUE_BY_USER                   = "enableBookIssueByUser";
        const ENABLE_BOOK_RSS                             = "enableBookRSS";
        const LIGHT_LOGO_FILE_PATH                        = "lightLogoFilePath";
        const USERS_PER_PAGE                              = "usersPerPage";
        const BOOKS_PER_PAGE_ADMIN                        = "booksPerPageAdmin";
        const BOOKS_PER_PAGE_PUBLIC                       = "booksPerPagePublic";
        const BOOKS_PER_PAGE_PUBLIC_FILTER                = "booksPerPagePublicFilter";
        const LAST_BOOKS_NUMBER_INDEX                     = "booksPerPageIndex";
        const RSS_BOOK_NUMBER                             = "rssBooksNumber";
        const TOP_RATED_BOOKS_NUMBER_INDEX                = "topRatedBooksNumberIndex";
        const POSTS_PER_PAGE_INDEX                        = "postPerPageIndex";
        const AUTHORS_PER_PAGE_INDEX                      = "authorPerPageIndex";
        const ISSUES_PER_PAGE                             = "issuesPerPage";
        const ISSUE_LOGS_PER_PAGE                         = "issueLogsPerPage";
        const REQUESTS_PER_PAGE                           = "requestsPerPage";
        const REVIEW_PER_PAGE                             = "reviewsPerPage";
        const REVIEW_PER_PAGE_PUBLIC                      = "reviewsPerPagePublic";
        const AUTHORS_PER_PAGE                            = "authorsPerPage";
        const AUTHORS_PER_PAGE_PUBLIC                     = "authorsPerPagePublic";
        const PUBLISHERS_PER_PAGE                         = "publishersPerPage";
        const SERIES_PER_PAGE                             = "seriesPerPage";
        const GENRES_PER_PAGE                             = "genresPerPage";
        const TAGS_PER_PAGE                               = "tagsPerPage";
        const STORES_PER_PAGE                             = "storesPerPage";
        const LOCATIONS_PER_PAGE                          = "locationsPerPage";
        const POSTS_PER_PAGE                              = "postsPerPage";
        const POSTS_PER_PAGE_PUBLIC                       = "postsPerPagePublic";
        const PAGES_PER_PAGE                              = "pagesPerPage";
        const MAX_SEARCH_RESULT_NUMBER                    = "maxSearchResultNumber";
        const DATE_FORMAT                                 = "dateFormat";
        const DATE_TIME_FORMAT                            = "dateTimeFormat";
        const INPUT_DATE_FORMAT                           = "inputDateFormat";
        const INPUT_DATE_FORMAT_JS                        = "inputDateFormatJS";
        const INPUT_DATE_TIME_FORMAT                      = "inputDateTimeFormat";
        const INPUT_DATE_TIME_FORMAT_JS                   = "inputDateTimeFormatJS";
        const PUBLISHING_DATE_FORMAT                      = "publishingDateFormat";
        const PUBLISHING_DATE_FORMAT_JS                   = "publishingDateFormatJS";
        const BACKGROUND_COLOR                            = "backgroundColor";
        const FOREGROUND_COLOR                            = "foregroundColor";
        const IMAGES_PER_PAGE                             = "imagesPerPage";
        const NO_IMAGE_FILE_PATH                          = "noImageFilePath";
        const NO_BOOK_IMAGE_FILE_PATH                     = "noBookImageFilePath";
        const NO_USER_IMAGE_FILE_PATH                     = "noUserImageFilePath";
        const MENUS_PER_PAGE                              = "menusPerPage";
        const LOGO_FILE_PATH                              = "logoFilePath";
        const FAV_ICON_FILE_PATH                          = "favIconFilePath";
        const DEFAULT_LANGUAGE                            = "defaultLanguage";
        const PRICE_CURRENCY                              = "priceCurrency";
        const SITE_NAME                                   = "siteName";
        const FOOTER_CREDITS                              = "footerCredits";
        const BLOG_URL                                    = "blogURL";
        const ADMIN_URL                                   = "adminURL";
        const ADMIN_EMAIL                                 = "adminEmail";
        const USER_MESSAGES_PER_PAGE                      = "userMessagesPerPage";
        const BOOK_VIEW_TYPE                              = "bookViewType";
        const SHOW_CAROUSEL                               = "showCarousel";
        const SHOW_EBOOK_DOWNLOAD_LINK                    = "showDownloadLink";
        const SHOW_EBOOK_READ_LINK                        = "showReadLink";
        const SHOW_SHARE_LINKS                            = "showShareLinks";
        const SHOW_EBOOK_DOWNLOAD_LINK_TO_REGISTERED_ONLY = "showDownloadLinkToRegisteredOnly";
        const SHOW_EBOOK_READ_LINK_TO_REGISTERED_ONLY     = "showReadLinkToRegisteredOnly";
        const PUBLIC_FOR_REGISTERED_USERS_ONLY            = "publicForRegisteredUsersOnly";
        const ENABLE_REGISTRATION                         = "enableRegistration";
        const MODERATE_REVIEWS                            = "moderateReviews";
        const USE_GET_REQUEST_BOOK_SEARCH                 = "useGetRequestBookSearch";
        const REVIEW_CREATOR                              = "reviewCreator";

        const REVIEW_CREATOR_NOBODY    = "Nobody";
        const REVIEW_CREATOR_USER      = "User";
        const REVIEW_CREATOR_EVERYBODY = "Everybody";

        const GENERATE_SITEMAP     = "generateSiteMap";
        const BOOK_URL_TYPE        = "bookUrlType";
        const BOOK_FIELDS_PER_PAGE = "bookFieldsPerPage";

        const MAIN_PAGE_AUTHOR_LIST_TYPE = "mainPageAuthorListType";
        const MAIN_PAGE_BOOK_LIST_TYPE   = "mainPageBookListType";

        const MAIN_PAGE_AUTHOR_LIST_TYPE_RANDOM = "RandomAuthors";
        const MAIN_PAGE_AUTHOR_LIST_TYPE_TOP    = "TopAuthors";
        const MAIN_PAGE_BOOK_LIST_TYPE_RANDOM   = "RandomBooks";
        const MAIN_PAGE_BOOK_LIST_TYPE_TOP      = "TopBooks";

        const BOOK_FIELD_ENABLED_TEMPLATE = "%sBookFieldEnabled";

        public static $REVIEW_CREATOR_NOBODY;
        public static $REVIEW_CREATOR_USER;
        public static $REVIEW_CREATOR_EVERYBODY;

        public static $MAIN_PAGE_BOOK_LIST_TYPE_RANDOM;
        public static $MAIN_PAGE_BOOK_LIST_TYPE_TOP;

        public static $MAIN_PAGE_AUTHOR_LIST_TYPE_RANDOM;
        public static $MAIN_PAGE_AUTHOR_LIST_TYPE_TOP;

        protected static $INSTANCE = null;

        private $options;

        final function __construct($isInstaller = false) {
            $this->options = [];
            self::$REVIEW_CREATOR_EVERYBODY = _("Everybody");
            self::$REVIEW_CREATOR_NOBODY = _("Nobody");
            self::$REVIEW_CREATOR_USER = _("User");

            self::$MAIN_PAGE_AUTHOR_LIST_TYPE_RANDOM = _("Random");
            self::$MAIN_PAGE_AUTHOR_LIST_TYPE_TOP = _("Top Rated");

            self::$MAIN_PAGE_BOOK_LIST_TYPE_RANDOM = _("Random");
            self::$MAIN_PAGE_BOOK_LIST_TYPE_TOP = _("Top Rated");

            $languageSelectValues = [];
            if (Config::getDatabaseConnection() !== null and !$isInstaller) {
                try {
                    $kaasoftDatabase = KAASoftDatabase::getInstance(ControllerBase::getLogger());
                    $languageList = ControllerBase::getLanguages($kaasoftDatabase);
                    foreach ($languageList as $lang) {
                        if ($lang instanceof Language and $lang->isActive()) {
                            $languageSelectValues [$lang->getCode()] = $lang->getName();
                        }
                    }
                }
                catch (Exception $exception) {
                    // ignore this exception
                }
            }

            $this->options[SiteViewOptions::GENERATE_SITEMAP] = new Option(SiteViewOptions::GENERATE_SITEMAP,
                                                                           _("Generate Sitemap"),
                                                                           Control::BUTTON,
                                                                           _("Press to generate sitemap"),
                                                                           "/sitemap/generate",
                                                                           "/sitemap/generate",
                                                                           null,
                                                                           OptionGroup::OPTION_GROUP_SITEMAP);

            $this->options[SiteViewOptions::ADMIN_EMAIL] = new Option(SiteViewOptions::ADMIN_EMAIL,
                                                                      _("Administrator's Email"),
                                                                      Control::INPUT,
                                                                      _("Type here administrator's email"),
                                                                      'admin@library-cms.com',
                                                                      'admin@library-cms.com');

            $this->options[SiteViewOptions::DEFAULT_LANGUAGE] = new Option(SiteViewOptions::DEFAULT_LANGUAGE,
                                                                           _("Default language"),
                                                                           Control::SELECT,
                                                                           _("Select default language"),
                                                                           "en_US",
                                                                           "en_US",
                                                                           $languageSelectValues);

            $this->options[SiteViewOptions::NO_IMAGE_FILE_PATH] = new Option(SiteViewOptions::NO_IMAGE_FILE_PATH,
                                                                             _("No Image"),
                                                                             Control::FILE,
                                                                             _("This image will be displayed if image is not specified"),
                                                                             '/resources/images/comingsoon.png',
                                                                             '/resources/images/comingsoon.png',
                                                                             null,
                                                                             OptionGroup::OPTION_GROUP_IMAGES);
            $this->options[SiteViewOptions::NO_BOOK_IMAGE_FILE_PATH] = new Option(SiteViewOptions::NO_BOOK_IMAGE_FILE_PATH,
                                                                                  _("No Book"),
                                                                                  Control::FILE,
                                                                                  _("This image will be displayed if book image in not specified"),
                                                                                  '/resources/images/comingsoon.png',
                                                                                  '/resources/images/comingsoon.png',
                                                                                  null,
                                                                                  OptionGroup::OPTION_GROUP_IMAGES);

            $this->options[SiteViewOptions::NO_USER_IMAGE_FILE_PATH] = new Option(SiteViewOptions::NO_USER_IMAGE_FILE_PATH,
                                                                                  _("No User"),
                                                                                  Control::FILE,
                                                                                  _("This image will be displayed if user photo in not specified"),
                                                                                  '/resources/images/noavatar.png',
                                                                                  '/resources/images/noavatar.png',
                                                                                  null,
                                                                                  OptionGroup::OPTION_GROUP_IMAGES);

            $this->options[SiteViewOptions::LOGO_FILE_PATH] = new Option(SiteViewOptions::LOGO_FILE_PATH,
                                                                         _("Logo"),
                                                                         Control::FILE,
                                                                         _("This image will be displayed as site logo image"),
                                                                         '/resources/images/logo.png',
                                                                         '/resources/images/logo.png',
                                                                         null,
                                                                         OptionGroup::OPTION_GROUP_IMAGES);

            $this->options[SiteViewOptions::FAV_ICON_FILE_PATH] = new Option(SiteViewOptions::FAV_ICON_FILE_PATH,
                                                                             _("FavIcon"),
                                                                             Control::FILE,
                                                                             _("This image will be displayed as site favicon"),
                                                                             '/resources/images/favicon.png',
                                                                             '/resources/images/favicon.png',
                                                                             null,
                                                                             OptionGroup::OPTION_GROUP_IMAGES);

            $this->options[SiteViewOptions::BLOG_URL] = new Option(SiteViewOptions::BLOG_URL,
                                                                   _("Blog URL"),
                                                                   Control::INPUT,
                                                                   _("Location of blog with leading slash"),
                                                                   "/blog",
                                                                   "/blog");
            $this->options[SiteViewOptions::ADMIN_URL] = new Option(SiteViewOptions::ADMIN_URL,
                                                                    _("Admin URL"),
                                                                    Control::INPUT,
                                                                    _("Location of admin part of site with leading slash"),
                                                                    "/admin",
                                                                    "/admin");
            $this->options[SiteViewOptions::SITE_NAME] = new Option(SiteViewOptions::SITE_NAME,
                                                                    _("Site Name"),
                                                                    Control::INPUT,
                                                                    _("Type here name of site"),
                                                                    "Library CMS",
                                                                    "Library CMS");
            $this->options[SiteViewOptions::FOOTER_CREDITS] = new Option(SiteViewOptions::FOOTER_CREDITS,
                                                                         _("Footer Credits"),
                                                                         Control::INPUT,
                                                                         _("Type here footer credits"),
                                                                         "&copy; " . date("Y") . " Library CMS.",
                                                                         "&copy; " . date("Y") . " Library CMS.");

            $this->options[SiteViewOptions::IMAGES_PER_PAGE] = new Option(SiteViewOptions::IMAGES_PER_PAGE,
                                                                          _("Images Per Page"),
                                                                          Control::SELECT,
                                                                          _("Number of images per page"),
                                                                          12,
                                                                          12,
                                                                          [ 6  => 6,
                                                                            12 => 12,
                                                                            18 => 18,
                                                                            24 => 24,
                                                                            30 => 30 ]);
            $this->options[SiteViewOptions::USERS_PER_PAGE] = new Option(SiteViewOptions::USERS_PER_PAGE,
                                                                         _("Users Per Page"),
                                                                         Control::SELECT,
                                                                         _("Number of users per page"),
                                                                         20,
                                                                         20,
                                                                         [ 10 => 10,
                                                                           20 => 20,
                                                                           30 => 30,
                                                                           40 => 40,
                                                                           50 => 50 ]);
            $this->options[SiteViewOptions::USER_MESSAGES_PER_PAGE] = new Option(SiteViewOptions::USER_MESSAGES_PER_PAGE,
                                                                                 _("User Messages Per Page"),
                                                                                 Control::SELECT,
                                                                                 _("Number of user messages per page"),
                                                                                 20,
                                                                                 20,
                                                                                 [ 10 => 10,
                                                                                   20 => 20,
                                                                                   30 => 30,
                                                                                   40 => 40,
                                                                                   50 => 50 ]);
            $this->options[SiteViewOptions::MENUS_PER_PAGE] = new Option(SiteViewOptions::MENUS_PER_PAGE,
                                                                         _("Menus Per Page"),
                                                                         Control::SELECT,
                                                                         _("Number of menus per page"),
                                                                         20,
                                                                         20,
                                                                         [ 10 => 10,
                                                                           20 => 20,
                                                                           30 => 30,
                                                                           40 => 40,
                                                                           50 => 50 ]);

            $this->options[SiteViewOptions::BOOK_VIEW_TYPE] = new Option(SiteViewOptions::BOOK_VIEW_TYPE,
                                                                         _("Book View Type [Public]"),
                                                                         Control::SELECT,
                                                                         _("Select book view type"),
                                                                         SiteViewOptions::BOOK_VIEW_TYPE_GRID,
                                                                         SiteViewOptions::BOOK_VIEW_TYPE_GRID,
                                                                         [ SiteViewOptions::BOOK_VIEW_TYPE_GRID      => _("Grid"),
                                                                           SiteViewOptions::BOOK_VIEW_TYPE_LIST      => _("List"),
                                                                           SiteViewOptions::BOOK_VIEW_TYPE_MINI_LIST => _("Mini List"), ],
                                                                         OptionGroup::OPTION_GROUP_BOOKS);

            $this->options[SiteViewOptions::BOOKS_PER_PAGE_ADMIN] = new Option(SiteViewOptions::BOOKS_PER_PAGE_ADMIN,
                                                                               _("Books Per Page [Admin]"),
                                                                               Control::SELECT,
                                                                               _("Number of books per page in admin part"),
                                                                               20,
                                                                               20,
                                                                               [ 10 => 10,
                                                                                 20 => 20,
                                                                                 30 => 30,
                                                                                 40 => 40,
                                                                                 50 => 50 ],
                                                                               OptionGroup::OPTION_GROUP_BOOKS);
            $this->options[SiteViewOptions::BOOKS_PER_PAGE_PUBLIC] = new Option(SiteViewOptions::BOOKS_PER_PAGE_PUBLIC,
                                                                                _("Books Per Page [Public]"),
                                                                                Control::SELECT,
                                                                                _("Number of books per page on public pages"),
                                                                                24,
                                                                                24,
                                                                                [ 6  => 6,
                                                                                  12 => 12,
                                                                                  24 => 24,
                                                                                  36 => 36,
                                                                                  48 => 48 ],
                                                                                OptionGroup::OPTION_GROUP_BOOKS);
            $this->options[SiteViewOptions::BOOKS_PER_PAGE_PUBLIC_FILTER] = new Option(SiteViewOptions::BOOKS_PER_PAGE_PUBLIC_FILTER,
                                                                                       _("Books Per Page With Filter [Public]"),
                                                                                       Control::SELECT,
                                                                                       _("Number of filtered books per page on public pages"),
                                                                                       24,
                                                                                       24,
                                                                                       [ 8  => 8,
                                                                                         16 => 16,
                                                                                         24 => 24,
                                                                                         32 => 32,
                                                                                         48 => 48 ],
                                                                                       OptionGroup::OPTION_GROUP_BOOKS);
            $this->options[SiteViewOptions::LAST_BOOKS_NUMBER_INDEX] = new Option(SiteViewOptions::LAST_BOOKS_NUMBER_INDEX,
                                                                                  _("Last Books Number [Index]"),
                                                                                  Control::SELECT,
                                                                                  _("Number of last created books on main (index) page"),
                                                                                  24,
                                                                                  24,
                                                                                  [ 6  => 6,
                                                                                    12 => 12,
                                                                                    24 => 24,
                                                                                    36 => 36,
                                                                                    48 => 48 ],
                                                                                  OptionGroup::OPTION_GROUP_BOOKS);
            $this->options[SiteViewOptions::RSS_BOOK_NUMBER] = new Option(SiteViewOptions::RSS_BOOK_NUMBER,
                                                                          _("RSS Books Number"),
                                                                          Control::SELECT,
                                                                          _("Number of last created books for RSS"),
                                                                          10,
                                                                          10,
                                                                          [ 5  => 5,
                                                                            10 => 10,
                                                                            15 => 15,
                                                                            20 => 20,
                                                                            25 => 25 ],
                                                                          OptionGroup::OPTION_GROUP_BOOKS);
            $this->options[SiteViewOptions::TOP_RATED_BOOKS_NUMBER_INDEX] = new Option(SiteViewOptions::TOP_RATED_BOOKS_NUMBER_INDEX,
                                                                                       _("Top Rated Books Number [Index]"),
                                                                                       Control::SELECT,
                                                                                       _("Number of top rated books on main (index) page"),
                                                                                       6,
                                                                                       6,
                                                                                       [ 3  => 3,
                                                                                         6  => 6,
                                                                                         9  => 9,
                                                                                         12 => 12,
                                                                                         15 => 15 ],
                                                                                       OptionGroup::OPTION_GROUP_BOOKS);
            $this->options[SiteViewOptions::AUTHORS_PER_PAGE_INDEX] = new Option(SiteViewOptions::AUTHORS_PER_PAGE_INDEX,
                                                                                 _("Authors Per Page [Index]"),
                                                                                 Control::SELECT,
                                                                                 _("Number of authors on main (index) page"),
                                                                                 6,
                                                                                 6,
                                                                                 [ 6  => 6,
                                                                                   9  => 9,
                                                                                   12 => 12,
                                                                                   15 => 15,
                                                                                   18 => 18 ],
                                                                                 OptionGroup::OPTION_GROUP_BOOKS);
            $this->options[SiteViewOptions::POSTS_PER_PAGE_INDEX] = new Option(SiteViewOptions::POSTS_PER_PAGE_INDEX,
                                                                               _("Posts Per Page [Index]"),
                                                                               Control::SELECT,
                                                                               _("Number of posts on main (index) page"),
                                                                               24,
                                                                               24,
                                                                               [ 6  => 6,
                                                                                 12 => 12,
                                                                                 24 => 24,
                                                                                 36 => 36,
                                                                                 48 => 48 ],
                                                                               OptionGroup::OPTION_GROUP_BOOKS);
            $this->options[SiteViewOptions::REVIEW_PER_PAGE] = new Option(SiteViewOptions::REVIEW_PER_PAGE,
                                                                          _("Reviews Per Page"),
                                                                          Control::SELECT,
                                                                          _("Number of reviews per page in admin part"),
                                                                          20,
                                                                          20,
                                                                          [ 10 => 10,
                                                                            20 => 20,
                                                                            30 => 30,
                                                                            40 => 40,
                                                                            50 => 50 ],
                                                                          OptionGroup::OPTION_GROUP_BOOKS);
            $this->options[SiteViewOptions::REVIEW_PER_PAGE_PUBLIC] = new Option(SiteViewOptions::REVIEW_PER_PAGE_PUBLIC,
                                                                                 _("Reviews Per Page [Public]"),
                                                                                 Control::SELECT,
                                                                                 _("Number of reviews per page on public pages"),
                                                                                 20,
                                                                                 20,
                                                                                 [ 10 => 10,
                                                                                   20 => 20,
                                                                                   30 => 30,
                                                                                   40 => 40,
                                                                                   50 => 50 ],
                                                                                 OptionGroup::OPTION_GROUP_BOOKS);
            $this->options[SiteViewOptions::ISSUES_PER_PAGE] = new Option(SiteViewOptions::ISSUES_PER_PAGE,
                                                                          _("Issues Per Page"),
                                                                          Control::SELECT,
                                                                          _("Number of issues per page in admin part"),
                                                                          20,
                                                                          20,
                                                                          [ 10 => 10,
                                                                            20 => 20,
                                                                            30 => 30,
                                                                            40 => 40,
                                                                            50 => 50 ],
                                                                          OptionGroup::OPTION_GROUP_BOOKS);
            $this->options[SiteViewOptions::ISSUE_LOGS_PER_PAGE] = new Option(SiteViewOptions::ISSUE_LOGS_PER_PAGE,
                                                                              _("Issue Logs Per Page"),
                                                                              Control::SELECT,
                                                                              _("Number of issue logs per page in admin part"),
                                                                              20,
                                                                              20,
                                                                              [ 10 => 10,
                                                                                20 => 20,
                                                                                30 => 30,
                                                                                40 => 40,
                                                                                50 => 50 ],
                                                                              OptionGroup::OPTION_GROUP_BOOKS);
            $this->options[SiteViewOptions::REQUESTS_PER_PAGE] = new Option(SiteViewOptions::REQUESTS_PER_PAGE,
                                                                            _("Requests Per Page"),
                                                                            Control::SELECT,
                                                                            _("Number of requests per page in admin part"),
                                                                            20,
                                                                            20,
                                                                            [ 10 => 10,
                                                                              20 => 20,
                                                                              30 => 30,
                                                                              40 => 40,
                                                                              50 => 50 ],
                                                                            OptionGroup::OPTION_GROUP_BOOKS);
            $this->options[SiteViewOptions::AUTHORS_PER_PAGE] = new Option(SiteViewOptions::AUTHORS_PER_PAGE,
                                                                           _("Authors Per Page [Admin]"),
                                                                           Control::SELECT,
                                                                           _("Number of authors per page in admin part"),
                                                                           20,
                                                                           20,
                                                                           [ 10 => 10,
                                                                             20 => 20,
                                                                             30 => 30,
                                                                             40 => 40,
                                                                             50 => 50 ]);
            $this->options[SiteViewOptions::AUTHORS_PER_PAGE_PUBLIC] = new Option(SiteViewOptions::AUTHORS_PER_PAGE_PUBLIC,
                                                                                  _("Authors Per Page [Public]"),
                                                                                  Control::SELECT,
                                                                                  _("Number of authors per page on public pages"),
                                                                                  24,
                                                                                  24,
                                                                                  [ 6  => 6,
                                                                                    12 => 12,
                                                                                    24 => 24,
                                                                                    36 => 36,
                                                                                    48 => 48 ]);
            $this->options[SiteViewOptions::POSTS_PER_PAGE] = new Option(SiteViewOptions::POSTS_PER_PAGE,
                                                                         _("Posts Per Page [Admin]"),
                                                                         Control::SELECT,
                                                                         _("Number of posts per page in admin part"),
                                                                         20,
                                                                         20,
                                                                         [ 10 => 10,
                                                                           20 => 20,
                                                                           30 => 30,
                                                                           40 => 40,
                                                                           50 => 50 ]);
            $this->options[SiteViewOptions::PAGES_PER_PAGE] = new Option(SiteViewOptions::PAGES_PER_PAGE,
                                                                         _("Pages Per Page [Admin]"),
                                                                         Control::SELECT,
                                                                         _("Number of pages per page in admin part"),
                                                                         20,
                                                                         20,
                                                                         [ 10 => 10,
                                                                           20 => 20,
                                                                           30 => 30,
                                                                           40 => 40,
                                                                           50 => 50 ]);
            $this->options[SiteViewOptions::POSTS_PER_PAGE_PUBLIC] = new Option(SiteViewOptions::POSTS_PER_PAGE_PUBLIC,
                                                                                _("Posts Per Page [Public]"),
                                                                                Control::SELECT,
                                                                                _("Number of posts per page on public pages"),
                                                                                20,
                                                                                10,
                                                                                [ 10 => 10,
                                                                                  20 => 20,
                                                                                  30 => 30,
                                                                                  40 => 40,
                                                                                  50 => 50 ]);


            $this->options[SiteViewOptions::PUBLISHERS_PER_PAGE] = new Option(SiteViewOptions::PUBLISHERS_PER_PAGE,
                                                                              _("Publishers Per Page"),
                                                                              Control::SELECT,
                                                                              _("Number of publishers per page in admin part"),
                                                                              20,
                                                                              20,
                                                                              [ 10 => 10,
                                                                                20 => 20,
                                                                                30 => 30,
                                                                                40 => 40,
                                                                                50 => 50 ]);
            $this->options[SiteViewOptions::SERIES_PER_PAGE] = new Option(SiteViewOptions::SERIES_PER_PAGE,
                                                                          _("Series Per Page"),
                                                                          Control::SELECT,
                                                                          _("Number of series per page in admin part"),
                                                                          20,
                                                                          20,
                                                                          [ 10 => 10,
                                                                            20 => 20,
                                                                            30 => 30,
                                                                            40 => 40,
                                                                            50 => 50 ]);
            $this->options[SiteViewOptions::GENRES_PER_PAGE] = new Option(SiteViewOptions::GENRES_PER_PAGE,
                                                                          _("Genres Per Page"),
                                                                          Control::SELECT,
                                                                          _("Number of genres per page in admin part"),
                                                                          20,
                                                                          20,
                                                                          [ 10 => 10,
                                                                            20 => 20,
                                                                            30 => 30,
                                                                            40 => 40,
                                                                            50 => 50 ]);

            $this->options[SiteViewOptions::TAGS_PER_PAGE] = new Option(SiteViewOptions::TAGS_PER_PAGE,
                                                                        _("Tags Per Page"),
                                                                        Control::SELECT,
                                                                        _("Number of tags per page in admin part"),
                                                                        20,
                                                                        20,
                                                                        [ 10 => 10,
                                                                          20 => 20,
                                                                          30 => 30,
                                                                          40 => 40,
                                                                          50 => 50 ]);

            $this->options[SiteViewOptions::STORES_PER_PAGE] = new Option(SiteViewOptions::STORES_PER_PAGE,
                                                                          _("Stores Per Page"),
                                                                          Control::SELECT,
                                                                          _("Number of stores per page in admin part"),
                                                                          20,
                                                                          20,
                                                                          [ 10 => 10,
                                                                            20 => 20,
                                                                            30 => 30,
                                                                            40 => 40,
                                                                            50 => 50 ]);
            $this->options[SiteViewOptions::LOCATIONS_PER_PAGE] = new Option(SiteViewOptions::LOCATIONS_PER_PAGE,
                                                                             _("Locations Per Page"),
                                                                             Control::SELECT,
                                                                             _("Number of locations per page in admin part"),
                                                                             20,
                                                                             20,
                                                                             [ 10 => 10,
                                                                               20 => 20,
                                                                               30 => 30,
                                                                               40 => 40,
                                                                               50 => 50 ]);
            $this->options[SiteViewOptions::BOOK_FIELDS_PER_PAGE] = new Option(SiteViewOptions::BOOK_FIELDS_PER_PAGE,
                                                                               _("Custom Book Fields Per Page"),
                                                                               Control::SELECT,
                                                                               _("Number of custom book fields per page in admin part"),
                                                                               20,
                                                                               20,
                                                                               [ 10 => 10,
                                                                                 20 => 20,
                                                                                 30 => 30,
                                                                                 40 => 40,
                                                                                 50 => 50 ]);
            $this->options[SiteViewOptions::MAX_SEARCH_RESULT_NUMBER] = new Option(SiteViewOptions::MAX_SEARCH_RESULT_NUMBER,
                                                                                   _("Max Search Result Number"),
                                                                                   Control::INPUT,
                                                                                   _("Max number items in search response"),
                                                                                   10,
                                                                                   10);
            $this->options[SiteViewOptions::DATE_FORMAT] = new Option(SiteViewOptions::DATE_FORMAT,
                                                                      _("Date Format"),
                                                                      Control::INPUT,
                                                                      _("Date format"),
                                                                      'M d, Y',
                                                                      'M d, Y',
                                                                      null,
                                                                      OptionGroup::OPTION_GROUP_DATE_TIME_FORMATS);
            $this->options[SiteViewOptions::PUBLISHING_DATE_FORMAT] = new Option(SiteViewOptions::PUBLISHING_DATE_FORMAT,
                                                                                 _("Publishing Date Format"),
                                                                                 Control::INPUT,
                                                                                 _("Publishing date format"),
                                                                                 'M/Y',
                                                                                 'M/Y',
                                                                                 null,
                                                                                 OptionGroup::OPTION_GROUP_DATE_TIME_FORMATS);
            $this->options[SiteViewOptions::PUBLISHING_DATE_FORMAT_JS] = new Option(SiteViewOptions::PUBLISHING_DATE_FORMAT_JS,
                                                                                    _("Publishing Date Format [JavaScript]"),
                                                                                    Control::INPUT,
                                                                                    _("Publishing date format for JS"),
                                                                                    'mm/yyyy',
                                                                                    'mm/yyyy',
                                                                                    null,
                                                                                    OptionGroup::OPTION_GROUP_DATE_TIME_FORMATS);
            $this->options[SiteViewOptions::DATE_TIME_FORMAT] = new Option(SiteViewOptions::DATE_TIME_FORMAT,
                                                                           _("Date/Time Format"),
                                                                           Control::INPUT,
                                                                           _("Date/Time format"),
                                                                           'M d, Y H:i:s',
                                                                           'M d, Y H:i:s',
                                                                           null,
                                                                           OptionGroup::OPTION_GROUP_DATE_TIME_FORMATS);
            $this->options[SiteViewOptions::INPUT_DATE_FORMAT] = new Option(SiteViewOptions::INPUT_DATE_FORMAT,
                                                                            _("Input Date Format"),
                                                                            Control::INPUT,
                                                                            _("Input date format"),
                                                                            'm/d/Y',
                                                                            'm/d/Y',
                                                                            null,
                                                                            OptionGroup::OPTION_GROUP_DATE_TIME_FORMATS);
            $this->options[SiteViewOptions::INPUT_DATE_FORMAT_JS] = new Option(SiteViewOptions::INPUT_DATE_FORMAT_JS,
                                                                               _("Input Date Format [JavaScript]"),
                                                                               Control::INPUT,
                                                                               _("Input date format for JS"),
                                                                               'mm/dd/yyyy',
                                                                               'mm/dd/yyyy',
                                                                               null,
                                                                               OptionGroup::OPTION_GROUP_DATE_TIME_FORMATS);

            $this->options[SiteViewOptions::INPUT_DATE_TIME_FORMAT] = new Option(SiteViewOptions::INPUT_DATE_TIME_FORMAT,
                                                                                 _("Input Date/Time Format"),
                                                                                 Control::INPUT,
                                                                                 _("Input date/time format"),
                                                                                 'm/d/Y H:i:s',
                                                                                 'm/d/Y H:i:s',
                                                                                 null,
                                                                                 OptionGroup::OPTION_GROUP_DATE_TIME_FORMATS);
            $this->options[SiteViewOptions::INPUT_DATE_TIME_FORMAT_JS] = new Option(SiteViewOptions::INPUT_DATE_TIME_FORMAT_JS,
                                                                                    _("Input Date/Time Format [JavaScript]"),
                                                                                    Control::INPUT,
                                                                                    _("Input date/time format for JS"),
                                                                                    'MM/DD/YYYY HH:mm:ss',
                                                                                    'MM/DD/YYYY HH:mm:ss',
                                                                                    null,
                                                                                    OptionGroup::OPTION_GROUP_DATE_TIME_FORMATS);


            $this->options[SiteViewOptions::ADMIN_COLOR_SCHEMA] = new Option(SiteViewOptions::ADMIN_COLOR_SCHEMA,
                                                                             _("Admin Color Schema"),
                                                                             Control::SELECT,
                                                                             _("Select Admin Color Schema"),
                                                                             "Light",
                                                                             "Light",
                                                                             [ "Light" => _("Light"),
                                                                               "Dark"  => _("Dark") ]);

            $this->options[SiteViewOptions::PRICE_CURRENCY] = new Option(SiteViewOptions::PRICE_CURRENCY,
                                                                         _("Price Currency"),
                                                                         Control::SELECT,
                                                                         _("Default price currency"),
                                                                         Currency::DEFAULT_CURRENCY,
                                                                         Currency::DEFAULT_CURRENCY,
                                                                         Currency::CURRENCY_LIST);

            $this->options[SiteViewOptions::MAIN_PAGE_AUTHOR_LIST_TYPE] = new Option(SiteViewOptions::MAIN_PAGE_AUTHOR_LIST_TYPE,
                _("Author List Type [Home Page]"),
                Control::SELECT,
                _("Select author list type on home page"),
                SiteViewOptions::MAIN_PAGE_AUTHOR_LIST_TYPE_TOP,
                SiteViewOptions::MAIN_PAGE_AUTHOR_LIST_TYPE_TOP,
                [SiteViewOptions::MAIN_PAGE_AUTHOR_LIST_TYPE_TOP => self::$MAIN_PAGE_AUTHOR_LIST_TYPE_TOP,
                    SiteViewOptions::MAIN_PAGE_AUTHOR_LIST_TYPE_RANDOM => self::$MAIN_PAGE_AUTHOR_LIST_TYPE_RANDOM],
                OptionGroup::OPTION_GROUP_GENERAL);

            $this->options[SiteViewOptions::MAIN_PAGE_BOOK_LIST_TYPE] = new Option(SiteViewOptions::MAIN_PAGE_BOOK_LIST_TYPE,
                _("Book List Type [Home Page]"),
                Control::SELECT,
                _("Select book list type on home page"),
                SiteViewOptions::MAIN_PAGE_BOOK_LIST_TYPE_TOP,
                SiteViewOptions::MAIN_PAGE_BOOK_LIST_TYPE_TOP,
                [SiteViewOptions::MAIN_PAGE_BOOK_LIST_TYPE_TOP => self::$MAIN_PAGE_BOOK_LIST_TYPE_TOP,
                    SiteViewOptions::MAIN_PAGE_BOOK_LIST_TYPE_RANDOM => self::$MAIN_PAGE_BOOK_LIST_TYPE_RANDOM],
                OptionGroup::OPTION_GROUP_GENERAL);

            $this->options[SiteViewOptions::SHOW_SHARE_LINKS] = new Option(SiteViewOptions::SHOW_SHARE_LINKS,
                                                                           _("Show share links"),
                                                                           Control::CHECKBOX,
                                                                           _("Check to show share links"),
                                                                           true,
                                                                           true);

            $this->options[SiteViewOptions::SHOW_EBOOK_DOWNLOAD_LINK] = new Option(SiteViewOptions::SHOW_EBOOK_DOWNLOAD_LINK,
                                                                                   _("Show eBook download link"),
                                                                                   Control::CHECKBOX,
                                                                                   _("Check to show eBook download link"),
                                                                                   true,
                                                                                   true);

            $this->options[SiteViewOptions::SHOW_EBOOK_DOWNLOAD_LINK_TO_REGISTERED_ONLY] = new Option(SiteViewOptions::SHOW_EBOOK_DOWNLOAD_LINK_TO_REGISTERED_ONLY,
                                                                                                      _("Show eBook download link to registered users only"),
                                                                                                      Control::CHECKBOX,
                                                                                                      _("Check to show eBook download link to registered users only"),
                                                                                                      false,
                                                                                                      false);

            $this->options[SiteViewOptions::SHOW_EBOOK_READ_LINK] = new Option(SiteViewOptions::SHOW_EBOOK_READ_LINK,
                                                                               _("Show eBook read link"),
                                                                               Control::CHECKBOX,
                                                                               _("Check to show eBook read link"),
                                                                               true,
                                                                               true);


            $this->options[SiteViewOptions::SHOW_EBOOK_READ_LINK_TO_REGISTERED_ONLY] = new Option(SiteViewOptions::SHOW_EBOOK_READ_LINK_TO_REGISTERED_ONLY,
                                                                                                  _("Show eBook read link to registered users only"),
                                                                                                  Control::CHECKBOX,
                                                                                                  _("Check to show eBook read link to registered users only"),
                                                                                                  false,
                                                                                                  false);

            $this->options[SiteViewOptions::SHOW_CAROUSEL] = new Option(SiteViewOptions::SHOW_CAROUSEL,
                                                                        _("Show book carousel"),
                                                                        Control::CHECKBOX,
                                                                        _("Check to show book carousel on main (index) page"),
                                                                        true,
                                                                        true);


            $this->options[SiteViewOptions::PUBLIC_FOR_REGISTERED_USERS_ONLY] = new Option(SiteViewOptions::PUBLIC_FOR_REGISTERED_USERS_ONLY,
                                                                                           _("Show public pages for registered users only"),
                                                                                           Control::CHECKBOX,
                                                                                           _("Check to show public pages for registered users only"),
                                                                                           false,
                                                                                           false);

            $this->options[SiteViewOptions::ENABLE_REGISTRATION] = new Option(SiteViewOptions::ENABLE_REGISTRATION,
                                                                              _("Enable user registration"),
                                                                              Control::CHECKBOX,
                                                                              _("Check to enable user registration"),
                                                                              true,
                                                                              true);

            $this->options[SiteViewOptions::REVIEW_CREATOR] = new Option(SiteViewOptions::REVIEW_CREATOR,
                                                                         _("Book review creator"),
                                                                         Control::SELECT,
                                                                         _("Select who can create book reviews"),
                                                                         SiteViewOptions::REVIEW_CREATOR_EVERYBODY,
                                                                         SiteViewOptions::REVIEW_CREATOR_EVERYBODY,
                                                                         [ SiteViewOptions::REVIEW_CREATOR_EVERYBODY => self::$REVIEW_CREATOR_EVERYBODY,
                                                                           SiteViewOptions::REVIEW_CREATOR_USER      => self::$REVIEW_CREATOR_USER,
                                                                           SiteViewOptions::REVIEW_CREATOR_NOBODY    => self::$REVIEW_CREATOR_NOBODY ],
                                                                         OptionGroup::OPTION_GROUP_BOOKS);

            $this->options[SiteViewOptions::MODERATE_REVIEWS] = new Option(SiteViewOptions::MODERATE_REVIEWS,
                                                                           _("Moderate book reviews"),
                                                                           Control::CHECKBOX,
                                                                           _("Enable/disable moderation before review publishing"),
                                                                           true,
                                                                           true,
                                                                           null,
                                                                           OptionGroup::OPTION_GROUP_BOOKS);

            $this->options[SiteViewOptions::USE_GET_REQUEST_BOOK_SEARCH] = new Option(SiteViewOptions::USE_GET_REQUEST_BOOK_SEARCH,
                                                                                      _("Use GET book search"),
                                                                                      Control::CHECKBOX,
                                                                                      _("Check to use book search via GET request"),
                                                                                      false,
                                                                                      false,
                                                                                      null,
                                                                                      OptionGroup::OPTION_GROUP_BOOKS);

            $this->options[SiteViewOptions::BOOK_URL_TYPE] = new Option(SiteViewOptions::BOOK_URL_TYPE,
                                                                        _("Choose Book Url Type"),
                                                                        Control::CHECKBOX,
                                                                        _("Check to use book ID in url like <b>/book/123</b>.<br/>Uncheck to use stored book URLs like <b>/book/lord-of-the-rings</b>."),
                                                                        true,
                                                                        true,
                                                                        null,
                                                                        OptionGroup::OPTION_GROUP_BOOKS);

            $this->options[SiteViewOptions::ENABLE_BOOK_LOGS] = new Option(SiteViewOptions::ENABLE_BOOK_LOGS,
                                                                           _("Enable Book Request/Issue Logs"),
                                                                           Control::CHECKBOX,
                                                                           _("Check to enable book request/issue logging."),
                                                                           true,
                                                                           true,
                                                                           null,
                                                                           OptionGroup::OPTION_GROUP_BOOKS);

            $this->options[SiteViewOptions::ENABLE_BOOK_ISSUE] = new Option(SiteViewOptions::ENABLE_BOOK_ISSUE,
                                                                            _("Enable book issue"),
                                                                            Control::CHECKBOX,
                                                                            _("Check to enable book issue ability."),
                                                                            true,
                                                                            true,
                                                                            null,
                                                                            OptionGroup::OPTION_GROUP_BOOKS);

            $this->options[SiteViewOptions::ENABLE_BOOK_REQUEST] = new Option(SiteViewOptions::ENABLE_BOOK_REQUEST,
                                                                              _("Enable book request"),
                                                                              Control::CHECKBOX,
                                                                              _("Check to enable book request ability."),
                                                                              true,
                                                                              true,
                                                                              null,
                                                                              OptionGroup::OPTION_GROUP_BOOKS);

            $this->options[SiteViewOptions::LIGHT_LOGO_FILE_PATH] = new Option(SiteViewOptions::LIGHT_LOGO_FILE_PATH,
                                                                               _("Light Logo"),
                                                                               Control::FILE,
                                                                               _("This image will be displayed as site light logo image"),
                                                                               '/resources/images/logo-light.png',
                                                                               '/resources/images/logo-light.png',
                                                                               null,
                                                                               OptionGroup::OPTION_GROUP_IMAGES);

            $this->options[SiteViewOptions::ENABLE_BOOK_ISSUE_BY_USER] = new Option(SiteViewOptions::ENABLE_BOOK_ISSUE_BY_USER,
                                                                                    _("Enable book issue by users (without librarian)"),
                                                                                    Control::CHECKBOX,
                                                                                    _("Check to enable book issue by any user."),
                                                                                    false,
                                                                                    false,
                                                                                    null,
                                                                                    OptionGroup::OPTION_GROUP_BOOKS);

            $this->options[SiteViewOptions::ENABLE_BOOK_RSS] = new Option(SiteViewOptions::ENABLE_BOOK_RSS,
                                                                          _("Enable book RSS"),
                                                                          Control::CHECKBOX,
                                                                          _("Check to enable book RSS."),
                                                                          false,
                                                                          false,
                                                                          null,
                                                                          OptionGroup::OPTION_GROUP_BOOKS);

        }

        final function __clone() {
        }

        /**
         * @param bool $isInstaller
         * @return SiteViewOptions
         */
        public static function getInstance($isInstaller = false) {
            if (SiteViewOptions::$INSTANCE === null) {
                SiteViewOptions::$INSTANCE = new SiteViewOptions($isInstaller);
            }

            return SiteViewOptions::$INSTANCE;
        }

        public function splitOptionsByGroup() {
            $result = [];
            foreach ($this->options as $option) {
                if ($option instanceof Option) {
                    $groupId = $option->getGroup();
                    if (!array_key_exists($groupId,
                                          $result)
                    ) {
                        $result[$groupId] = [];
                    }
                    $result[$groupId][] = $option;
                }
            }

            uksort($result,
                   [ $this,
                     "groupComparison" ]);

            return $result;
        }

        public function groupComparison($firstGroupId, $secondGroupId) {
            $firstOptionGroup = OptionGroup::getOptionGroup($firstGroupId);
            $secondOptionGroup = OptionGroup::getOptionGroup($secondGroupId);

            return $firstOptionGroup->getPriority() > $secondOptionGroup->getPriority() ? 1 : -1;
        }

        /**
         * @param $optionGroupId
         * @return OptionGroup
         */
        public function getOptionGroup($optionGroupId) {
            return OptionGroup::getOptionGroup($optionGroupId);
        }

        /**
         *
         */
        public static function loadSiteViewOptions() {
            $instance = SiteViewOptions::getInstance();
            $jsonFileName = SiteViewOptions::getJsonFileName();

            if (file_exists($jsonFileName)) {
                $optionObjects = json_decode(file_get_contents($jsonFileName),
                                             true);

                $options = $instance->getOptions();
                foreach ($optionObjects as $optionObject) {
                    if (array_key_exists($optionObject["name"],
                                         $options)) {
                        $listValues = $optionObject["listValues"];
                        // fix for default language list values
                        if (strcmp($optionObject["name"],
                                   SiteViewOptions::DEFAULT_LANGUAGE) === 0
                        ) {
                            $listValues = $instance->getOption(SiteViewOptions::DEFAULT_LANGUAGE)->getListValues();
                        }
                        // end of fix for default language list values

                        $options[$optionObject["name"]] = new Option($optionObject["name"],
                                                                     $optionObject["title"],
                                                                     $optionObject["control"],
                                                                     $optionObject["description"],
                                                                     $optionObject["value"],
                                                                     $optionObject["defaultValue"],
                                                                     $listValues,
                                                                     $optionObject["group"]);
                    }
                }
                $instance->setOptions($options);
            }
        }

        /**
         * @param null $options
         * @throws Exception
         */
        public static function saveSiteViewOptions($options = null) {
            $instance = SiteViewOptions::getInstance();

            if ($options != null) {
                $requiredOptions = array_keys($instance->getOptions());
                foreach ($options as $key => $value) {
                    if (in_array($key,
                                 $requiredOptions)) {
                        $instance->setOptionValue($key,
                                                  $value);
                    }

                }
                // fix for bool vars only
                foreach ($instance->getOptions() as $key => $option) {
                    if ($option !== null) {
                        if (strcmp($option->getControl(),
                                   Control::CHECKBOX) === 0
                        ) {
                            // if control is checkbox and specified in POST request - it should be set true
                            // if control is checkbox and not specified in POST request - it should be set false
                            $instance->setOptionValue($key,
                                isset( $options[$key] ));
                        }
                    }
                }
            }

            $jsonFileName = SiteViewOptions::getJsonFileName();

            if (file_exists($jsonFileName) and !@rename($jsonFileName,
                                                        $jsonFileName . ".bak")
            ) {
                throw new Exception(sprintf(_("Couldn't create backup of file \"%s\"."),
                                            basename($jsonFileName)));
            }
            FileHelper::saveStringToFile(json_encode($instance->getOptions(),
                                                     JSON_PRETTY_PRINT),
                                         $jsonFileName);
        }

        public static function getJsonFileName() {
            return FileHelper::getConfigFolderLocation() . DIRECTORY_SEPARATOR . SiteViewOptions::OptionsDefinitionFileNameJSON;
        }

        /**
         * @return array
         */
        public function getOptions() {
            return $this->options;
        }

        /**
         * @param array $options
         */
        public function setOptions($options) {
            $this->options = $options;
        }

        /**
         * @return array
         */
        /*private function getOptionsAsKeyValuePairs() {
            $result = [];
            $instance = SiteViewOptions::getInstance();
            foreach ($instance->getOptions() as $key => $value) {
                if ($value instanceof Option) {
                    $result[$key] = $value->getValue();
                }
            }

            return $result;
        }*/

        /**
         * @param $optionName
         * @return Option
         */
        public function getOption($optionName) {
            return $this->options[$optionName];
        }

        /**
         * @param $optionName
         * @return mixed
         */
        public function getOptionValue($optionName) {
            $option = $this->getOption($optionName);
            if ($option !== null) {
                return $option->getValue();
            }
            else {
                return null;
            }
        }

        /**
         * @param $optionName
         * @param $optionValue
         */
        public function setOptionValue($optionName, $optionValue) {
            $option = $this->getOption($optionName);
            if ($option !== null) {
                $option->setValue($optionValue);
            }
        }
    }