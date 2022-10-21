<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;


    use Exception;

    /**
     * Class Calculator
     * @package KAASoft\Util
     */
    class Calculator {
        private $arg1;
        private $arg2;
        private $operation;

        public function __construct($arg1, $arg2, $operation) {
            $this->arg1 = $arg1;
            $this->arg2 = $arg2;
            $this->operation = $operation;
        }

        /**
         * @return float - result of operation
         * @throws Exception
         */
        public function getResult() {
            switch ($this->operation) {
                case "+":
                    return $this->add();
                case "-":
                    return $this->subtract();
                case "*":
                    return $this->multiply();
                case "/":
                    return $this->divide();
                default:
                    throw new Exception("Unknown operation: \"" . $this->operation . " \"");
            }
        }

        public function add() {
            return $this->arg1 + $this->arg2;
        }

        public function subtract() {
            return $this->arg1 - $this->arg2;
        }

        public function multiply() {
            return $this->arg1 * $this->arg2;
        }

        public function divide() {
            return $this->arg1 / $this->arg2;
        }

        /**
         * @return mixed
         */
        public function getArg1() {
            return $this->arg1;
        }

        /**
         * @return mixed
         */
        public function getArg2() {
            return $this->arg2;
        }

        /**
         * @return mixed
         */
        public function getOperation() {
            return $this->operation;
        }


    }