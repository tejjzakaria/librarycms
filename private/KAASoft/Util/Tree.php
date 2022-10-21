<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    /**
     * Class Tree
     * @package KAASoft\Util
     */
    class Tree {
        protected $root; // array of root elements

        /**
         * @param array $root
         */
        public function __construct(array $root = null) {
            $this->root = $root;
        }

        /**
         * @param TreeNode $root
         */
        public function addRootNode($root) {
            if ($this->isEmpty()) {
                $this->root = [];
            }
            $this->root[] = $root;
        }

        /**
         * @return bool
         */
        public function isEmpty() {
            return $this->root === null || count($this->root) === 0;
        }

        /**
         * @return array TreeNode
         */
        public function getRootNodes() {
            return $this->root;
        }

        /**
         * @param array TreeNode $root
         */
        public function setRootNodes($root) {
            $this->root = $root;
        }

    }