<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\EmailNotification;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Database\Entity\Util\EmailNotification;
    use KAASoft\Environment\EmailSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\EmailAddress;
    use KAASoft\Util\Emailer;

    /**
     * Class EmailNotificationTestAction
     * @package KAASoft\Controller\Admin\Util\EmailNotification
     */
    class EmailNotificationTestAction extends AdminActionBase {
        /**
         * EmailNotificationTestAction constructor.
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
            $testEmail = $_POST["email"];

            $emailSettings = new EmailSettings();
            $emailSettings->loadSettings();
            $emailNotification = EmailNotification::getObjectInstance($_POST);

            $emailer = new Emailer($emailSettings->getSendMethod(),
                                   $emailNotification->getSubject(),
                                   $emailNotification->getContent());

            if ($emailNotification->getFrom() === null and $emailSettings->getDefaultFromEmailAddress() !== null) {
                $fromEmail = new EmailAddress($emailSettings->getDefaultFromEmailAddress());
                if ($emailSettings->getDefaultFromEmailName() !== null) {
                    $fromEmail->setName($emailSettings->getDefaultFromEmailName());
                }
                $emailNotification->setFrom($fromEmail);
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
    }