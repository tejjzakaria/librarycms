<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller;


    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\SiteViewOptions;

    abstract class DatabaseHelper {
        /**
         * @var ActionBase
         */
        protected $action;

        /**
         * @var KAASoftDatabase
         */
        protected $kaaSoftDatabase;

        /**
         * @var SiteViewOptions
         */
        protected $siteViewOptions;

        /**
         * MenuDatabaseHelper constructor.
         * @param $action ActionBase
         */
        public function __construct($action) {
            $this->action = $action;
            $this->kaaSoftDatabase = $action->getKaaSoftDatabase();
            $this->siteViewOptions = $action->getSiteViewOptions();
        }

    }