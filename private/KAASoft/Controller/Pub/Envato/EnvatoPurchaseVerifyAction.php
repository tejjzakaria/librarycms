<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-05-26
     */


    namespace KAASoft\Controller\Pub\Envato;


    use Exception;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Envato\EnvatoClient;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;

    class EnvatoPurchaseVerifyAction extends PublicActionBase {
        /**
         * EnvatoPurchaseVerifyAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            if (Helper::isPostRequest() and Helper::isAjaxRequest()) {
                try {
                    $purchaseCode = ValidationHelper::getString($_POST["purchaseCode"]);

                    $envatoClient = new EnvatoClient();

                    $license = $envatoClient->verifyPurchaseCode($purchaseCode);

                    if ($license === false) {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_WARNING => _("Couldn't verify license for some reason.") ]);
                    }
                    elseif ($license === null) {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Purchase code is INVALID.") ]);
                    }
                    else {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Purchase code is successfully verified.") ]);
                        $this->putArrayToAjaxResponse([ "license" => $license ]);
                        $license->saveSettings();
                    }
                }
                catch (Exception $e) {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_WARNING => sprintf(_("Couldn't verify license for some reason: %s"),
                                                                                                   $e->getMessage()) ]);
                }

                return new DisplaySwitch();
            }
            else {
                return new DisplaySwitch("auth/envatoLicenseVerification.tpl");
            }
        }
    }