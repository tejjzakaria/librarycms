<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;

    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\Locale;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class LanguageChangeAction
     * @package KAASoft\Controller\Admin\Util
     */
    class LanguageChangeAction extends PublicActionBase {

        /**
         * LanguageChangeAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute,
                                true);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $languageCode = $args["languageCode"];

            if (isset( $_SERVER["HTTP_REFERER"] )) {
                Session::addSessionValue(Locale::ACTIVE_LANGUAGE,
                                         $this->kaaSoftDatabase->getLanguage($languageCode));
                Helper::redirectTo($_SERVER["HTTP_REFERER"]);
            }
            else {
                Session::addSessionMessage(_("Direct access is not allowed. Language won't be changed."),
                                           Message::MESSAGE_STATUS_ERROR);
                Helper::redirectTo($this->routeController->getRouteString(GeneralPublicRoutes::PUBLIC_INDEX_ROUTE));
            }

            return new DisplaySwitch();
        }
    }