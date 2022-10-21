<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\General;


    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Environment\Config;
    use KAASoft\Environment\EmailSettings;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\EmailAddress;
    use KAASoft\Util\Emailer;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;

    class PasswordRecoveryAction extends AdminActionBase {


        /**
         * PasswordRecoveryAction constructor.
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
            /*
             * 1. generate hash
             * 2. store hash (+ email + end valid time) to database
             * 3. send email with link for recovering
             * 4. block user till end of recovering
             *
             */
            if (Helper::isPostRequest()) {

                if (!isset( $_POST['email'] )) {
                    $this->putArrayToAjaxResponse([ "error" => "You have to specify email to recover." ]);

                    return new DisplaySwitch();
                }
                $email = ValidationHelper::getString($_POST['email']);
                $helper = new GeneralDatabaseHelper($this);
                $userHelper = new UserDatabaseHelper($this);

                if ($this->startDatabaseTransaction()) {
                    if ($userHelper->isUserEmailExists($email)) {
                        $hash = Helper::encryptPassword($email);

                        //$hash = rawurlencode($hash);
                        $result = $helper->deleteRecoveryByEmail($email);
                        if ($result !== false) {
                            $result = $helper->saveRecovery($email,
                                                            $hash,
                                                            Helper::RECOVERY_PASSWORD_HASH_VALID_TIME);

                            if ($result !== false) {
                                $user = $userHelper->getUserByEmail($email);

                                if ($user !== null and $user instanceof User) {
                                    $result = $userHelper->activateUser($user->getId(),
                                                                        false);
                                    if ($result !== false) {

                                        $emailSettings = new EmailSettings();
                                        $emailSettings->loadSettings();
                                        // user blocked
                                        $recoveryLink = Config::getSiteURL() . $this->routeController->getRouteString("passwordChange",
                                                                                                                      [ "token" => $hash ]);
                                        $this->smarty->assign("user",
                                                              $user);
                                        $this->smarty->assign("recoveryLink",
                                                              $recoveryLink);

                                        $emailContent = $this->smarty->fetch("notifications/passwordRecovery.tpl");

                                        $emailContent = $this->replaceShortCodes($emailContent,
                                                                                 array_merge($this->getStaticShortCodes(),
                                                                                             [ "FIRST_NAME"    => $user->getFirstName(),
                                                                                               "LAST_NAME"     => $user->getLastName(),
                                                                                               "RECOVERY_LINK" => $recoveryLink ]));

                                        $emailer = new Emailer($emailSettings->getSendMethod(),
                                                               _("Password Recovery"),
                                                               $emailContent);

                                        if (ValidationHelper::isEmpty(trim($emailSettings->getDefaultFromEmailAddress()))) {
                                            $this->rollbackDatabaseChanges();
                                            $this->putArrayToAjaxResponse([ "error" => sprintf(_("Couldn't sent email due to from email address. Please contact <a href=\"mailto:%s\">site administrator(%s)</a> and provide this message."),
                                                                                               $this->siteViewOptions->getOptionValue(SiteViewOptions::ADMIN_EMAIL),
                                                                                               $this->siteViewOptions->getOptionValue(SiteViewOptions::ADMIN_EMAIL)) ]);

                                            return new DisplaySwitch();
                                        }
                                        $fromEmail = new EmailAddress($emailSettings->getDefaultFromEmailAddress(),
                                                                      $emailSettings->getDefaultFromEmailName());

                                        //ControllerBase::getLogger()->debug("DefaultFromEmailAddress: ".$emailSettings->getDefaultFromEmailAddress());
                                        //ControllerBase::getLogger()->debug("DefaultFromEmailName: ".$emailSettings->getDefaultFromEmailName());


                                        $emailer->SetFrom($fromEmail->getEmail(),
                                                          $fromEmail->getName());

                                        $emailer->AddAddress($email);

                                        $emailResult = $emailer->sendEmail();
                                        if ($emailResult === true) {
                                            $this->commitDatabaseChanges();
                                            $this->putArrayToAjaxResponse([ "success" => _("Recovery email was sent. Please check your email.") ]);

                                            return new DisplaySwitch();
                                        }
                                        else {
                                            $this->rollbackDatabaseChanges();
                                            $this->putArrayToAjaxResponse([ "error" => sprintf(_("Couldn't send recovery email. Please try to recover again.<br/>%s"),
                                                                                               $emailResult) ]);

                                            return new DisplaySwitch();
                                        }
                                    }
                                    else {
                                        $this->rollbackDatabaseChanges();
                                        $this->putArrayToAjaxResponse([ "error" => _("Couldn't inactivate user.") ]);

                                        return new DisplaySwitch();
                                    }
                                }
                                else {
                                    $this->rollbackDatabaseChanges();
                                    $this->putArrayToAjaxResponse([ "error" => _("Couldn't get user information in database.") ]);

                                    return new DisplaySwitch();
                                }
                            }
                            else {
                                $this->rollbackDatabaseChanges();
                                $this->putArrayToAjaxResponse([ "error" => _("Couldn't save recovery hash in database.") ]);

                                return new DisplaySwitch();
                            }
                        }
                        else {
                            $this->rollbackDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "error" => _("Couldn't delete existing token.") ]);

                            return new DisplaySwitch();
                        }
                    }
                    else {
                        $this->rollbackDatabaseChanges();
                        $this->putArrayToAjaxResponse([ "error" => _("Specified email is not exist in database.") ]);

                        return new DisplaySwitch();
                    }
                }
            }
            elseif (Helper::isGetRequest()) {
                return new DisplaySwitch('auth/passwordRecovery.tpl');
            }

            return new DisplaySwitch();
        }
    }