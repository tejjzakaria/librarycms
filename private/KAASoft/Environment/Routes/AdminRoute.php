<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes;

    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\FileHelper;

    /**
     * Class AdminRoute
     * @package KAASoft\Environment\Routes
     */
    class AdminRoute extends Route {

        function __construct($title, $pattern, $class, $route = null, $parameters = null) {
            parent::__construct();
            $adminRoutePrefix = SiteViewOptions::getInstance()->getOptionValue(SiteViewOptions::ADMIN_URL);
            parent::setTitle($title);
            parent::setPattern("~^" . FileHelper::getSiteRelativeLocation() . $adminRoutePrefix . $pattern . "$~");
            parent::setClass($class);
            parent::setRouteString( FileHelper::getSiteRelativeLocation().$adminRoutePrefix . $route);
            parent::setParameters($parameters);
        }

    }