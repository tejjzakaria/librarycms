<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */
    namespace KAASoft\Environment;

    use KAASoft\Environment\Routes\Admin\AuthorRoutes;
    use KAASoft\Environment\Routes\Admin\BookCopyRoutes;
    use KAASoft\Environment\Routes\Admin\BookFieldRoutes;
    use KAASoft\Environment\Routes\Admin\BookRoutes;
    use KAASoft\Environment\Routes\Admin\CategoryRoutes;
    use KAASoft\Environment\Routes\Admin\ElectronicBookRoutes;
    use KAASoft\Environment\Routes\Admin\EmailNotificationRoutes;
    use KAASoft\Environment\Routes\Admin\GenreRoutes;
    use KAASoft\Environment\Routes\Admin\ImageRoutes;
    use KAASoft\Environment\Routes\Admin\IssueRoutes;
    use KAASoft\Environment\Routes\Admin\LocationRoutes;
    use KAASoft\Environment\Routes\Admin\PageRoutes;
    use KAASoft\Environment\Routes\Admin\PostRoutes;
    use KAASoft\Environment\Routes\Admin\PublisherRoutes;
    use KAASoft\Environment\Routes\Admin\RequestRoutes;
    use KAASoft\Environment\Routes\Admin\ReviewRoutes;
    use KAASoft\Environment\Routes\Admin\SeriesRoutes;
    use KAASoft\Environment\Routes\Admin\SessionRoutes;
    use KAASoft\Environment\Routes\Admin\StoreRoutes;
    use KAASoft\Environment\Routes\Admin\TagRoutes;
    use KAASoft\Environment\Routes\Admin\UserMessageRoutes;
    use KAASoft\Environment\Routes\Admin\UserRoutes;
    use KAASoft\Environment\Routes\Admin\UtilRoutes;
    use KAASoft\Environment\Routes\Pub\BookPublicRoutes;
    use KAASoft\Environment\Routes\Pub\EnvatoRoutes;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Routes\Pub\IssuePublicRoutes;
    use KAASoft\Environment\Routes\Pub\PostAndPageRoutes;
    use KAASoft\Environment\Routes\Route;
    use KAASoft\Environment\Routes\RouteControllerBase;

    /**
     * Class Routes
     * @package KAASoft\Environment
     */
    class RouteController extends RouteControllerBase {

        private static $ROUTES;

        /**
         * @return RouteController
         */
        public static function getInstance() {
            if (self::$ROUTES === null) {
                self::$ROUTES = new RouteController();
            }

            return self::$ROUTES;
        }

        protected function initRoutes() {
            $routes = array_merge(GeneralPublicRoutes::getRoutes(),
                                  SessionRoutes::getRoutes(),
                                  BookRoutes::getRoutes(),
                                  BookCopyRoutes::getRoutes(),
                                  BookFieldRoutes::getRoutes(),
                                  IssueRoutes::getRoutes(),
                                  IssuePublicRoutes::getRoutes(),
                                  EmailNotificationRoutes::getRoutes(),
                                  RequestRoutes::getRoutes(),
                                  ReviewRoutes::getRoutes(),
                                  ImageRoutes::getRoutes(),
                                  UserRoutes::getRoutes(),
                                  UtilRoutes::getRoutes(),
                                  PostRoutes::getRoutes(),
                                  PageRoutes::getRoutes(),
                                  UserMessageRoutes::getRoutes(),
                                  CategoryRoutes::getRoutes(),
                                  AuthorRoutes::getRoutes(),
                                  PublisherRoutes::getRoutes(),
                                  SeriesRoutes::getRoutes(),
                                  BookPublicRoutes::getRoutes(),
                                  EnvatoRoutes::getRoutes(),
                                  GenreRoutes::getRoutes(),
                                  TagRoutes::getRoutes(),
                                  ElectronicBookRoutes::getRoutes(),
                                  StoreRoutes::getRoutes(),
                                  LocationRoutes::getRoutes(),
                //this route should be last
                                  PostAndPageRoutes::getRoutes());

            return $routes;
        }

        /**
         * @param Route $firstRoute
         * @param Route $secondRoute
         * @return int
         */
        public function compareRoutesByTitle(Route $firstRoute, Route $secondRoute) {
            return strcmp($firstRoute->getTitle(),
                          $secondRoute->getTitle());
        }
    }

    ?>