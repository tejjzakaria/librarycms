<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-06-01
     */


    namespace KAASoft\Controller\Admin\Book;

    use Exception;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Environment\BookLayoutSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Layout\LayoutContainer;
    use KAASoft\Util\Layout\LayoutElement;

    /**
     * Class BookLayoutAction
     * @package KAASoft\Controller\Admin\Book
     */
    class BookLayoutAction extends AdminActionBase {

        /**
         * BookLayoutAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $bookLayoutSettings = new BookLayoutSettings();

            if (Helper::isPostRequest()) {
                try {
                    $containers = [];
                    foreach ($_POST as $containerName => $contentJson) {
                        $layoutContainer = new LayoutContainer();
                        $layoutContainer->setName($containerName);

                        $contentArray = json_decode($contentJson,
                                                    true);

                        foreach ($contentArray as $elementArray) {
                            $element = LayoutElement::getObjectInstance($elementArray);
                            $layoutContainer->addElement($element);
                        }

                        $layoutContainer->sortElements();

                        $containers [] = $layoutContainer;
                    }

                    $bookLayoutSettings->setLayoutContainers($containers);
                    $bookLayoutSettings->saveSettings();
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Book layout settings are successfully saved.") ]);
                }
                catch (Exception $e) {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $e->getMessage() ]);
                }

                return new DisplaySwitch();
            }
            else {
                $bookLayoutSettings->loadSettings();

                $this->smarty->assign("bookVisibleFieldList",
                                      Book::getFieldListPrivate());

                $this->smarty->assign("bookLayoutSettings",
                                      $bookLayoutSettings);

                return new DisplaySwitch('admin/books/book-layout.tpl');
            }
        }
    }