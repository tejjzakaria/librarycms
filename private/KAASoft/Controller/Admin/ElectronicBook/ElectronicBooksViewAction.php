<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-01-02
     */

    namespace KAASoft\Controller\Admin\ElectronicBook;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Util\DisplaySwitch;

    /**
     * Class ElectronicBooksViewAction
     * @package KAASoft\Controller\Admin\ElectronicBook
     */
    class ElectronicBooksViewAction extends AdminActionBase {

        /**
         * @param $args        array
         * @return DisplaySwitch
         */
        protected function action($args) {
            return new DisplaySwitch();
        }
    }