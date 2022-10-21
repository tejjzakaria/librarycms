<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;


    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Environment\RssSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\RSS\Channel;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class RssSettingsEditAction
     * @package KAASoft\Controller\Admin\Util
     */
    class RssSettingsEditAction extends AdminActionBase {
        /**
         * RssSettingsEditAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws \Exception
         */
        protected function action($args) {
            $rssSettings = new RssSettings();
            if (Helper::isPostRequest()) {
                $channels = [];
                if (!ValidationHelper::isArrayEmpty($_POST["channels"])) {
                    foreach ($_POST["channels"] as $channel) {
                        $channels [] = Channel::getInstance($channel);
                    }
                }
                $rssSettings->setChannels($channels);
                $rssSettings->saveSettings();
            }
            else {
                $rssSettings->loadSettings();
            }

            $this->smarty->assign("rssSettings",
                                  $rssSettings);

            return new DisplaySwitch("admin/rssSettings.tpl");
        }
    }