<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\EmailNotification;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Environment\Config;
    use KAASoft\Environment\EmailSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\EmailAddress;
    use KAASoft\Util\Emailer;
    use KAASoft\Util\Helper;

    /**
     * Class EmailSendAction
     * @package KAASoft\Controller\Admin\Util\EmailNotification
     */
    class EmailSendAction extends AdminActionBase {
        /**
         * EmailSendAction constructor.
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
            if (Helper::isPostRequest()) {
                $testEmail = $_POST["email"];

                $emailSettings = new EmailSettings();
                $emailSettings->loadSettings();

                $emailSubject = "This is test email.";
                $emailContent = "<html><body><h1><b>This is email <i>content</i>!</b></h1></body></html>";
                $emailer = new Emailer($emailSettings->getSendMethod(),
                                       $emailSubject,
                                       $emailContent);

                if ($emailSettings->getDefaultFromEmailAddress() !== null) {
                    $fromEmail = new EmailAddress($emailSettings->getDefaultFromEmailAddress(),
                                                  $emailSettings->getDefaultFromEmailName());

                    $emailer->SetFrom($fromEmail->getEmail(),
                                      $fromEmail->getName());
                }
                else {
                    $emailer->SetFrom("no-replay@" . Config::getSiteDomain(),
                                      "Library CMS - No Replay");
                }
                $emailer->ClearAllRecipients();
                $emailer->AddAddress($testEmail);

                $result = $emailer->sendEmail();
                if ($result === true) {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Email is successfully sent.") ]);
                }
                else {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => sprintf(_("Couldn't send email: %s"),
                                                                                                 $result) ]);
                }

                return new DisplaySwitch();
            }
            else {
                return new DisplaySwitch('admin/emailSend.tpl');
            }
        }
    }