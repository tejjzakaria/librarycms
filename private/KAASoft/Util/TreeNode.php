<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    /**
     * Class TreeNode
     * @package KAASoft\Util
     */
    class TreeNode {
        private $value;    // contains the node item
        private $children;
        private $parent;

        /**
         * @param mixed         $value
         * @param TreeNode|null $parent
         * @param array|null    $children
         */
        public function __construct($value, $parent = null, $children = null) {
            $this->value = $value;
            $this->parent = $parent;
            $this->children = $children;
        }

        /**
         * @param TreeNode $child
         */
        public function addChild(TreeNode $child) {
            if ($this->children == null) {
                $this->children = [];
            }
            $child->setParent($this);
            $this->children[] = $child;
        }

        /**
         * @return TreeNode|null
         */
        public function getParent() {
            return $this->parent;
        }

        /**
         * @param TreeNode|null $parent
         */
        public function setParent($parent) {
            $this->parent = $parent;
        }


        /**
         * @return mixed
         */
        public function getValue() {
            return $this->value;
        }

        /**
         * @param mixed $value
         */
        public function setValue($value) {
            $this->value = $value;
        }

        /**
         * @return array
         */
        public function getChildren() {
            return $this->children;
        }

        /**
         * @param array $children
         */
        public function setChildren($children) {
            $this->children = $children;
        }
    }