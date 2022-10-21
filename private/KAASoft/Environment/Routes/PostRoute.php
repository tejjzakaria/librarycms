<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes;

    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\FileHelper;

    class PostRoute extends PublicRoute {

        function __construct($title, $pattern, $class, $route = null, $parameters = null) {
            parent::__construct($title,
                                $pattern,
                                $class,
                                $route,
                                $parameters);


            $postRoutePrefix = SiteViewOptions::getInstance()->getOptionValue(SiteViewOptions::BLOG_URL);

            parent::setTitle($title);
            parent::setPattern("~^" .FileHelper::getSiteRelativeLocation() . $postRoutePrefix . $pattern . "$~");
            parent::setClass($class);
            parent::setRouteString( FileHelper::getSiteRelativeLocation().$postRoutePrefix . $route);
            parent::setParameters($parameters);
        }
    }