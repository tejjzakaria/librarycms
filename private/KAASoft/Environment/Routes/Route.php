<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment\Routes;

    use KAASoft\Util\FileHelper;

    /**
     * Class Route
     * @package KAASoft\Environment\Routes
     */
    abstract class Route {
        private $name;
        private $title;
        private $pattern;
        private $class;
        private $routeString;
        private $parameters;

        /**
         * Route constructor.
         * @param      $title
         * @param null $pattern
         * @param null $class
         * @param null $route
         * @param null $parameters
         */
        function __construct($title = null, $pattern = null, $class = null, $route = null, $parameters = null) {
            $this->title = $title;
            $this->pattern = "~^" . FileHelper::getSiteRelativeLocation() . $pattern . "$~";
            $this->class = $class;
            $this->routeString = $route;
            $this->parameters = $parameters;
            $this->routeString = FileHelper::getSiteRelativeLocation() . $route;
        }

        /**
         * @return mixed
         */
        public function getPattern() {
            return $this->pattern;
        }

        /**
         * @param mixed $pattern
         */
        public function setPattern($pattern) {
            $this->pattern = $pattern;
        }

        /**
         * @return mixed
         */
        public function getClass() {
            return $this->class;
        }

        /**
         * @param mixed $class
         */
        public function setClass($class) {
            $this->class = $class;
        }

        /**
         * @return mixed
         */
        public function getRouteString() {
            return $this->routeString;
        }

        /**
         * @param mixed $routeString
         */
        public function setRouteString($routeString) {
            $this->routeString = $routeString;
        }

        /**
         * @return null
         */
        public function getParameters() {
            return $this->parameters;
        }

        /**
         * @param null $parameters
         */
        public function setParameters($parameters) {
            $this->parameters = $parameters;
        }

        /**
         * @return mixed
         */
        public function getName() {
            return $this->name;
        }

        /**
         * @param mixed $name
         */
        public function setName($name) {
            $this->name = $name;
        }

        /**
         * @return mixed
         */
        public function getTitle() {
            return $this->title;
        }

        /**
         * @param mixed $title
         */
        public function setTitle($title) {
            $this->title = $title;
        }
    }