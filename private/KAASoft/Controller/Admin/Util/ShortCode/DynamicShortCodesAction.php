<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\ShortCode;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Database\Entity\Util\DynamicShortCode;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Session;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class DynamicShortCodesAction
     * @package KAASoft\Controller\Admin\Util\ShortCode
     */
    class DynamicShortCodesAction extends AdminActionBase {
        /**
         * DynamicShortCodesAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        protected function action($args) {
            $helper = new ShortCodeDatabaseHelper($this);
            if (Helper::isPostRequest()) {
                if ($this->startDatabaseTransaction()) {
                    if (isset( $_POST["code"] )) {
                        $count = count($_POST["code"]);
                        $codes = [];
                        for ($i = 0; $i < $count; $i++) {
                            $code = new DynamicShortCode();
                            $code->setCode($_POST["code"][$i]);
                            $code->setColumnName($_POST["columnName"][$i]);
                            $codes [] = $code;
                        }

                        $result = $helper->deleteAllDynamicCodes();
                        if ($result !== false) {
                            $results = $helper->saveDynamicShortCodes($codes);
                            $isError = false;
                            foreach ($results as $result) {
                                if ($result === false) {
                                    $this->rollbackDatabaseChanges();
                                    Session::addSessionMessage(_("Couldn't save Dynamic Short Codes."),
                                                               Message::MESSAGE_STATUS_ERROR);
                                    $isError = true;
                                    break;
                                }
                            }
                            if (!$isError) {
                                $this->commitDatabaseChanges();
                                Session::addSessionMessage(_("Dynamic Short Codes were saved successfully."),
                                                           Message::MESSAGE_STATUS_INFO);
                            }
                        }
                        else {
                            $this->rollbackDatabaseChanges();
                            Session::addSessionMessage(sprintf(_("Couldn't clean table \"%s\"."),
                                                               KAASoftDatabase::$DYNAMIC_SHORT_CODES_TABLE_NAME),
                                                       Message::MESSAGE_STATUS_ERROR);
                        }
                    }
                }
                else {
                    Session::addSessionMessage(_("Database transaction couldn't be created."),
                                               Message::MESSAGE_STATUS_ERROR);
                }
            }
            $shortCodes = $helper->getDynamicShortCodes();

            $this->smarty->assign("shortCodes",
                                  $shortCodes);

            return new DisplaySwitch('admin/shortcode/dynamicShortCode.tpl');

        }
    }