<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes;

    /**
     * Class RouteControllerBase
     * @package KAASoft\Environment\Routes
     */
    class RouteControllerBase {
        /**
         * @var array
         */
        protected $routes;

        /**
         * RoutesBase constructor.
         */
        protected function __construct() {
            $this->updateRoutes();
        }

        /**
         * @return array
         */
        public function getRoutes() {
            return $this->routes;
        }

        public function updateRoutes() {
            $this->routes = $this->initRoutes();
        }

        /**
         * @param       $routeName
         * @param array $params
         * @return mixed|null|string
         */
        public function getRouteString($routeName, array $params = null) {
            $routes = $this->getRoutes();
            if (isset( $routes[$routeName] )) {
                $route = $routes[$routeName];
                if ($route instanceof Route) {

                    $routeString = $route->getRouteString() . "";
                    if ($params != null) {
                        foreach ($params as $key => $value) {
                            $routeString = str_replace("[" . $key . "]",
                                                       $value,
                                                       $routeString);
                        }
                    }

                    return $routeString;
                }
            }

            return null;
        }


        /**
         * @param $routeName
         * @return Route
         */
        public function getRoute($routeName) {
            return $this->getRoutes()[$routeName];
        }

        protected function initRoutes() {
            return [];
        }
    }