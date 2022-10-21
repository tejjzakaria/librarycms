<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller;
    /**
     * Class AdminActionBase
     * @package KAASoft\Controller
     */
    abstract class AdminActionBase extends ActionBase {
        /**
         * AdminActionBase constructor.
         * @param null $activeRoute
         * @param bool $isInitDataBase
         */
        public function __construct($activeRoute = null, $isInitDataBase = true) {
            parent::__construct($activeRoute,
                                $isInitDataBase);
        }
    }