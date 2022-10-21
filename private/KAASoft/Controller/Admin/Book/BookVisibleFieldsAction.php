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
    use KAASoft\Database\Entity\Util\FieldOptions;
    use KAASoft\Environment\BookFieldSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class BookVisibleFieldsAction
     * @package KAASoft\Controller\Admin\Book
     */
    class BookVisibleFieldsAction extends AdminActionBase {

        /**
         * BookVisibleFieldsAction constructor.
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
            $bookFieldSettings = new BookFieldSettings();
            $bookFieldSettings->loadSettings();

            if (Helper::isPostRequest()) {
                try {
                    $fieldNames = ValidationHelper::getArray($_POST["bookFields"]);
                    if ($fieldNames !== null) {
                        foreach ($bookFieldSettings->getBookFields() as $fieldName => $fieldOptions) {

                            if ($fieldOptions instanceof FieldOptions) {
                                $fieldOptions->setIsVisible(array_key_exists($fieldName,
                                                                             $fieldNames));
                            }
                        }

                        $bookFieldSettings->saveSettings();
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Book setting is successfully saved.") ]);
                    }
                }
                catch (Exception $e) {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $e->getMessage() ]);
                }

                return new DisplaySwitch();
            }
            else {
                $this->smarty->assign("bookFieldSettings",
                                      $bookFieldSettings);

                return new DisplaySwitch("admin/books/book-visible-fields.tpl");
            }
        }
    }