<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes;

    /**
     * Class PublicRoute
     * @package KAASoft\Environment\Routes
     */
    class PublicRoute extends Route {

        function __construct($title, $pattern, $class, $route, $parameters = null) {
            parent::__construct($title,
                                $pattern,
                                $class,
                                $route,
                                $parameters);
        }
    }