<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\ShortCode;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Database\Entity\Util\StaticShortCode;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Environment\Session;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class StaticShortCodesAction
     * @package KAASoft\Controller\Admin\Util\ShortCode
     */
    class StaticShortCodesAction extends AdminActionBase {
        /**
         * StaticShortCodesAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        protected function sanitizeUserInput($excludeKeys = []) {
            parent::sanitizeUserInput(array_merge($excludeKeys,
                                                  [ "code",
                                                    "value" ]));
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $helper = new ShortCodeDatabaseHelper($this);
            if (Helper::isPostRequest()) {
                if ($this->startDatabaseTransaction()) {
                    if (isset( $_POST["code"] )) {
                        $count = count($_POST["code"]);
                        $codes = [];
                        for ($i = 0; $i < $count; $i++) {
                            $code = new StaticShortCode();
                            $code->setCode($_POST["code"][$i]);
                            $code->setValue($_POST["value"][$i]);
                            $code->setIsLongText(isset( $_POST["isLongText"][$i] ) ? $_POST["isLongText"][$i] : false);
                            $codes [] = $code;
                        }

                        $result = $helper->deleteAllStaticCodes();
                        if ($result !== false) {
                            $results = $helper->saveStaticShortCodes($codes);
                            $isError = false;
                            foreach ($results as $result) {
                                if ($result === false) {
                                    $this->rollbackDatabaseChanges();
                                    Session::addSessionMessage(_("Couldn't save Static Short Codes."),
                                                               Message::MESSAGE_STATUS_ERROR);
                                    $isError = true;
                                    break;
                                }
                            }
                            if (!$isError) {
                                $this->commitDatabaseChanges();
                                Session::addSessionMessage(_("Static Short Codes were saved successfully."),
                                                           Message::MESSAGE_STATUS_INFO);
                            }
                        }
                        else {
                            $this->rollbackDatabaseChanges();
                            Session::addSessionMessage(sprintf(_("Couldn't clean table \"%s\"."),
                                                               KAASoftDatabase::$STATIC_SHORT_CODES_TABLE_NAME),
                                                       Message::MESSAGE_STATUS_ERROR);
                        }
                    }
                }
                else {
                    Session::addSessionMessage(_("Database transaction couldn't be created."),
                                               Message::MESSAGE_STATUS_ERROR);
                }
            }

            $shortCodes = $helper->getStaticShortCodes();
            $this->smarty->assign("shortCodes",
                                  $shortCodes);

            // disable output filter for templates [FIX for code displaying]
            $this->smarty->unregister_outputfilter([ $this,
                                                     "smartyOutputFilter" ]);

            return new DisplaySwitch('admin/shortcode/staticShortCode.tpl');
        }
    }