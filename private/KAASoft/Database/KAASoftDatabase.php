<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database;

    use Exception;
    use KAASoft\Database\Entity\General\Binding;
    use KAASoft\Database\Entity\General\BookSize;
    use KAASoft\Database\Entity\General\BookType;
    use KAASoft\Database\Entity\General\PhysicalForm;
    use KAASoft\Database\Entity\Post\Page;
    use KAASoft\Database\Entity\Util\DatabaseVersion;
    use KAASoft\Database\Entity\Util\Language;
    use KAASoft\Database\Entity\Util\Permission;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Environment\Config;
    use KAASoft\Util\Exception\DatabaseConnectException;
    use PDO;

    /**
     * Class KAASoftDatabase
     * @package KAASoft\Database
     */
    class KAASoftDatabase extends Database {

        //public static $USER_ROLE_LIST = [ "administrator" => "администратор" ];
        const INSERT_PACKET_SIZE = 1000;

        public static $USERS_TABLE_NAME            = "Users";
        public static $ROLES_TABLE_NAME            = "Roles";
        public static $PERMISSIONS_TABLE_NAME      = "Permissions";
        public static $ROLE_PERMISSIONS_TABLE_NAME = "RolePermissions";
        public static $LANGUAGES_TABLE_NAME        = "Languages";

        public static $POSTS_TABLE_NAME           = "Posts";
        public static $PAGES_TABLE_NAME           = "Pages";
        public static $POST_CATEGORIES_TABLE_NAME = "PostCategories";
        public static $CATEGORIES_TABLE_NAME      = "Categories";
        public static $TAGS_TABLE_NAME            = "Tags";

        public static $DATABASE_VERSION_TABLE_NAME    = "DatabaseVersion";
        public static $ELECTRONIC_BOOKS_TABLE_NAME    = "ElectronicBooks";
        public static $BOOKS_TABLE_NAME               = "Books";
        public static $BOOK_FIELDS_TABLE_NAME         = "BookFields";
        public static $LIST_VALUES_TABLE_NAME         = "ListValues";
        public static $BOOK_COPIES_TABLE_NAME         = "BookCopies";
        public static $BOOK_AUTHORS_TABLE_NAME        = "BookAuthors";
        public static $BOOK_GENRES_TABLE_NAME         = "BookGenres";
        public static $BOOK_STORES_TABLE_NAME         = "BookStores";
        public static $BOOK_LOCATIONS_TABLE_NAME      = "BookLocations";
        public static $BOOK_IMAGES_TABLE_NAME         = "BookImages";
        public static $BOOK_RATINGS_TABLE_NAME        = "BookRatings";
        public static $BOOK_TAGS_TABLE_NAME           = "BookTags";
        public static $ISSUES_TABLE_NAME              = "Issues";
        public static $ISSUE_LOGS_TABLE_NAME          = "IssueLogs";
        public static $REQUESTS_TABLE_NAME            = "Requests";
        public static $REVIEWS_TABLE_NAME             = "Reviews";
        public static $BOOK_REVIEWS_TABLE_NAME        = "BookReviews";
        public static $AUTHORS_TABLE_NAME             = "Authors";
        public static $PUBLISHERS_TABLE_NAME          = "Publishers";
        public static $GENRES_TABLE_NAME              = "Genres";
        public static $STORES_TABLE_NAME              = "Stores";
        public static $LOCATIONS_TABLE_NAME           = "Locations";
        public static $BINDINGS_TABLE_NAME            = "Bindings";
        public static $BOOK_SIZES_TABLE_NAME          = "BookSizes";
        public static $BOOK_TYPES_TABLE_NAME          = "BookTypes";
        public static $PHYSICAL_FORMS_TABLE_NAME      = "PhysicalForms";
        public static $SERIES_TABLE_NAME              = "Series";
        public static $IMAGES_TABLE_NAME              = "Images";
        public static $IMAGE_RESOLUTIONS_TABLE_NAME   = "ImageResolutions";
        public static $USER_MESSAGES_TABLE_NAME       = "UserMessages";
        public static $MENU_ITEMS_TABLE_NAME          = "MenuItems";
        public static $MENUS_TABLE_NAME               = "Menus";
        public static $EMAIL_NOTIFICATIONS_TABLE_NAME = "EmailNotifications";
        public static $STATIC_SHORT_CODES_TABLE_NAME  = "StaticShortCodes";
        public static $DYNAMIC_SHORT_CODES_TABLE_NAME = "DynamicShortCodes";

        public static $PASSWORD_CONFIRMATION_TABLE_NAME = "PasswordConfirmation";


        protected static $INSTANCE = null;

        final  protected function __clone() {
        }

        final protected function __construct($logger = null) {
            $databaseConnection = Config::getDatabaseConnection();
            $this->logger = $logger;
            try {
                parent::__construct([ 'databaseType' => $databaseConnection->getDatabaseType(),
                                      'databaseName' => $databaseConnection->getDatabaseName(),
                                      'server'       => $databaseConnection->getHost(),
                                      'username'     => $databaseConnection->getUsername(),
                                      'password'     => $databaseConnection->getPassword(),
                                      'charset'      => $databaseConnection->getCharset(),
                                      // optional
                                      'port'         => $databaseConnection->getPort() ]);
            }
            catch (Exception $exception) {
                throw new DatabaseConnectException(sprintf(_("Couldn't connect to database: %s"),
                                                           $exception->getMessage()));
            }
            //uncomment  if need database debugging only
            $this->debugMode = Config::DEBUG_DATABASE_MODE;
        }

        /**
         * @param null $logger
         * @return KAASoftDatabase
         */
        public static function getInstance($logger = null) {
            if (KAASoftDatabase::$INSTANCE === null) {
                KAASoftDatabase::$INSTANCE = new KAASoftDatabase($logger);
            }

            return KAASoftDatabase::$INSTANCE;
        }

        /**
         * @return PDO
         */
        public function getPDO() {
            return $this->pdo;
        }

        /**
         * @param Role $role
         * @return array|null
         */
        public function getPermissions(Role $role) {
            return $this->getRolePermissions($role->getId());
        }

        /**
         * @param $roleId
         * @return array|null
         */
        public function getRolePermissions($roleId) {

            $queryResult = $this->select(KAASoftDatabase::$ROLE_PERMISSIONS_TABLE_NAME,
                                         [ "[><]" . KAASoftDatabase::$PERMISSIONS_TABLE_NAME => [ "permissionId" => "id" ] ],
                                         array_merge(Permission::getDatabaseFieldNames(),
                                                     [ KAASoftDatabase::$PERMISSIONS_TABLE_NAME . ".id" ]),
                                         [ KAASoftDatabase::$ROLE_PERMISSIONS_TABLE_NAME . ".roleId" => $roleId ]);

            if ($queryResult !== false) {
                $permissions = [];

                foreach ($queryResult as $permission) {
                    $permissions[] = Permission::getObjectInstance($permission);
                }

                return $permissions;
            }

            return null;
        }

        /**
         * @return array
         */
        public function getPageUrls() {
            $queryResult = $this->select(KAASoftDatabase::$PAGES_TABLE_NAME,
                                         [ "id",
                                           "url" ]);
            $result = [];
            if ($queryResult !== false) {

                foreach ($queryResult as $row) {
                    $result[$row["id"]] = $row["url"];
                }

            }

            return $result;
        }

        /**
         * @return array
         */
        public function getPostUrls() {
            $queryResult = $this->select(KAASoftDatabase::$POSTS_TABLE_NAME,
                                         [ "id",
                                           "url" ]);
            $result = [];
            if ($queryResult !== false) {

                foreach ($queryResult as $row) {
                    $result[$row["id"]] = $row["url"];
                }

            }

            return $result;
        }


        /**
         * @return array
         */
        public function getIndexMenuPages() {
            $queryResult = $this->select(KAASoftDatabase::$PAGES_TABLE_NAME,
                                         "*",
                                         [ "isIndexMenu" => true,
                                           "ORDER"       => [ "parentId ASC",
                                                              "title ASC" ] ]);
            $result = [];
            if ($queryResult !== false) {

                foreach ($queryResult as $row) {
                    $page = Page::getObjectInstance($row);
                    $result[$page->getId()] = $page;
                }

            }

            return $result;
        }

        /**
         * @param $languageCode
         * @return Language|null
         */
        public function getLanguage($languageCode) {
            if ($languageCode !== null) {
                $queryResult = $this->get(KAASoftDatabase::$LANGUAGES_TABLE_NAME,
                                          array_merge(Language::getDatabaseFieldNames(),
                                                      [ KAASoftDatabase::$LANGUAGES_TABLE_NAME . ".id" ]),
                                          [ "code" => $languageCode ]);
                if ($queryResult !== false) {
                    return Language::getObjectInstance($queryResult);
                }
            }

            return null;
        }

        /**
         * @return array|bool
         */
        public function getLanguages() {
            $queryResult = $this->select(KAASoftDatabase::$LANGUAGES_TABLE_NAME,
                                         array_merge(Language::getDatabaseFieldNames(),
                                                     [ KAASoftDatabase::$LANGUAGES_TABLE_NAME . ".id" ]));
            if ($queryResult !== false) {
                $languages = [];
                foreach ($queryResult as $languageRow) {
                    $language = Language::getObjectInstance($languageRow);

                    $languages [] = $language;
                }

                return $languages;
            }

            return false;
        }

        /**
         * @return array|bool
         */
        public function getBindings() {
            $queryResult = $this->select(KAASoftDatabase::$BINDINGS_TABLE_NAME,
                                         array_merge(Binding::getDatabaseFieldNames(),
                                                     [ KAASoftDatabase::$BINDINGS_TABLE_NAME . ".id" ]));
            if ($queryResult !== false) {
                $bindings = [];
                foreach ($queryResult as $bindingRow) {
                    $binding = Binding::getObjectInstance($bindingRow);

                    $bindings [] = $binding;
                }

                return $bindings;
            }

            return false;
        }

        /**
         * @return array|bool
         */
        public function getBookTypes() {
            $queryResult = $this->select(KAASoftDatabase::$BOOK_TYPES_TABLE_NAME,
                                         array_merge(BookType::getDatabaseFieldNames(),
                                                     [ KAASoftDatabase::$BOOK_TYPES_TABLE_NAME . ".id" ]));
            if ($queryResult !== false) {
                $bookTypes = [];
                foreach ($queryResult as $bookTypeRow) {
                    $bookType = BookType::getObjectInstance($bookTypeRow);

                    $bookTypes [] = $bookType;
                }

                return $bookTypes;
            }

            return false;
        }

        /**
         * @return array|bool
         */
        public function getBookSizes() {
            $queryResult = $this->select(KAASoftDatabase::$BOOK_SIZES_TABLE_NAME,
                                         array_merge(BookSize::getDatabaseFieldNames(),
                                                     [ KAASoftDatabase::$BOOK_SIZES_TABLE_NAME . ".id" ]));
            if ($queryResult !== false) {
                $bookSizes = [];
                foreach ($queryResult as $bookSizeRow) {
                    $bookSize = BookSize::getObjectInstance($bookSizeRow);

                    $bookSizes [] = $bookSize;
                }

                return $bookSizes;
            }

            return false;
        }

        /**
         * @return array|bool
         */
        public function getPhysicalForms() {
            $queryResult = $this->select(KAASoftDatabase::$PHYSICAL_FORMS_TABLE_NAME,
                                         array_merge(PhysicalForm::getDatabaseFieldNames(),
                                                     [ KAASoftDatabase::$PHYSICAL_FORMS_TABLE_NAME . ".id" ]));
            if ($queryResult !== false) {
                $physicalForms = [];
                foreach ($queryResult as $physicalFormRow) {
                    $physicalForm = PhysicalForm::getObjectInstance($physicalFormRow);

                    $physicalForms [] = $physicalForm;
                }

                return $physicalForms;
            }

            return false;
        }

        /**
         * @return DatabaseVersion|null
         */
        public function getDatabaseVersion() {
            $queryResult = $this->max(KAASoftDatabase::$DATABASE_VERSION_TABLE_NAME,
                                      KAASoftDatabase::$DATABASE_VERSION_TABLE_NAME . ".version");
            if ($queryResult !== false) {
                $dbVersion = new DatabaseVersion();
                $dbVersion->setVersion($queryResult);

                return $dbVersion;
            }

            return null;
        }

        /**
         * @param $version
         * @return array|bool
         */
        public function saveDatabaseVersion($version) {
            return $this->insert(KAASoftDatabase::$DATABASE_VERSION_TABLE_NAME,
                                 [ "version" => $version ]);
        }
    }