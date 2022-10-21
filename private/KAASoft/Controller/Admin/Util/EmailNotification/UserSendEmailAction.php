<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\EmailNotification;

    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Environment\EmailSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Emailer;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class UserSendEmailAction
     * @package KAASoft\Controller\Admin\Util\EmailNotification
     */
    class UserSendEmailAction extends AdminActionBase {
        /**
         * UserSendEmailAction constructor.
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
            if (Helper::isAjaxRequest()) {
                $userId = $args["userId"];
                $emailSettings = new EmailSettings();
                $emailSettings->loadSettings();

                if (Helper::isPostRequest()) {
                    $userHelper = new UserDatabaseHelper($this);
                    $user = $userHelper->getUser($userId);

                    if ($user === null) {
                        $message = sprintf("There is no requested user(%d) in database.",
                                           $userId);
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $message ]);

                        return new DisplaySwitch();
                    }

                    //$bookId = ValidationHelper::getInt($_POST["bookId"]);
                    $subject = ValidationHelper::getString($_POST["subject"]);
                    $content = ValidationHelper::getString($_POST["content"]);

                    $emailContent = str_replace("[" . EmailSettings::SHORT_CODE_DYNAMIC_CONTENT . "]",
                                                $content,
                                                $emailSettings->getStaticEmailTemplate());

                    $shortCodes = $this->getStaticShortCodes();
                    $shortCodes = array_merge($shortCodes,
                                              $this->getCustomStaticCodes($user));

                    $emailContent = $this->replaceShortCodes($emailContent,
                                                             $shortCodes);
                    $subject = $this->replaceShortCodes($subject,
                                                        $shortCodes);


                    $emailer = new Emailer($emailSettings->getSendMethod(),
                                           $subject,
                                           $emailContent);
                    $emailer->ClearAllRecipients();
                    $emailer->FromName = $emailSettings->getDefaultFromEmailName();
                    $emailer->From = $emailSettings->getDefaultFromEmailAddress();
                    $emailer->AddAddress($user->getEmail(),
                                         $user->getFirstName() . " " . $user->getLastName());

                    $result = $emailer->sendEmail();
                    if ($result === true) {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Email is successfully sent.") ]);
                    }
                    else {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => sprintf(_("Email sending is failed: %s"),
                                                                                                     $result) ]);
                    }
                }
                else {
                    $this->putArrayToAjaxResponse([ "dynamicEmailTemplate" => $emailSettings->getDynamicEmailTemplate() ]);
                }
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }

        /**
         * @param $user User
         * @return array
         */
        private function getCustomStaticCodes($user/*, $bookId*/) {
            $result = [];

            if ($user !== null and $user instanceof User) {
                $result[EmailSettings::SHORT_CODE_USER_FIRST_NAME] = $user->getFirstName();
                $result[EmailSettings::SHORT_CODE_USER_LAST_NAME] = $user->getLastName();

                $bookHelper = new BookDatabaseHelper($this);
                $books = $bookHelper->getUserIssuedBooks($user->getId());
                $this->smarty->assign("books",
                                      $books);
                $activeTheme = ControllerBase::getThemeSettings()->getActiveTheme();
                $issueTemplate = $this->smarty->fetch(FileHelper::getEmailNotificationTemplateDirectory($activeTheme) . DIRECTORY_SEPARATOR . "issues.tpl");

                $result[EmailSettings::SHORT_CODE_BOOK] = $issueTemplate;
            }

            return $result;
        }
    }