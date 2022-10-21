<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub;

    use Exception;
    use KAASoft\Environment\Routes\Admin\SessionRoutes;
    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Database\Entity\Util\Role;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\EmailAddress;
    use KAASoft\Util\Helper;

    /**
     * Class RegistrationAction
     * @package KAASoft\Controller\Pub
     */
    class RegistrationAction extends PublicActionBase {
        /**
         * RegistrationAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute,
                                true);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            $userDatabaseHelper = new UserDatabaseHelper($this);

                            $user = User::getObjectInstance($_POST);
                            $user->setPassword(Helper::encryptPassword($user->getPassword()));
                            $user->setIsActive(false);
                            $user->setIsLdapUser(false);
                            $user->setRoleId(Role::BUILTIN_USER_ROLES[Role::READER_ROLE_ID]);
                            $user->setCreationDateTime(Helper::getDateTimeString());
                            $user->setUpdateDateTime($user->getCreationDateTime());

                            $userId = $userDatabaseHelper->saveUser($user);
                            if ($userId === false) {
                                $errorMessage = _("User saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ "error" => $errorMessage ]);

                                return new DisplaySwitch(null,
                                                         null,
                                                         true);
                            }

                            $this->addEmailNotificationVariable("user",
                                                                $user);

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "creationDateTime" => Helper::reformatDateTimeString($user->getCreationDateTime(),
                                                                                                                 $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_TIME_FORMAT)) ]);

                            $this->addShortCode("FIRST_NAME",
                                                $user->getFirstName());
                            $this->addShortCode("LAST_NAME",
                                                $user->getLastName());

                            $utilHelper = new UtilDatabaseHelper($this);

                            $email = $user->getEmail();
                            $hash = Helper::encryptPassword($email);

                            $result = $userDatabaseHelper->deleteRecoveryByEmail($email);
                            if ($result !== false) {
                                $result = $userDatabaseHelper->saveRecovery($email,
                                                                            $hash,
                                                                            Helper::RECOVERY_PASSWORD_HASH_VALID_TIME);

                                if ($result !== false) {
                                    $emailConfirmationLink = "http://" . $_SERVER['HTTP_HOST'] . $this->getRouteString(SessionRoutes::EMAIL_CONFIRMATION_ROUTE,
                                                                                                                       [ "token" => $hash ]);

                                    $this->addShortCode("CONFIRMATION_LINK",
                                                        $emailConfirmationLink);
                                }
                                else {
                                    $errorMessage = _("Couldn't create confirmation record in database.");
                                    $this->putArrayToAjaxResponse([ "error" => $errorMessage ]);

                                    return new DisplaySwitch(null,
                                                             null,
                                                             true);
                                }
                            }
                            else {
                                $errorMessage = _("Couldn't delete existing email.");
                                $this->putArrayToAjaxResponse([ "error" => $errorMessage ]);

                                return new DisplaySwitch(null,
                                                         null,
                                                         true);
                            }


                            $emailNotification = $utilHelper->getEmailNotification($this->activeRoute->getName());
                            $emailNotification->setTo([ new EmailAddress($user->getEmail(),
                                                                         $user->getLastName() . " " . $user->getFirstName()) ]);

                            $this->setEmailNotification($emailNotification);
                        }
                    }
                    catch (Exception $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save User.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                        return new DisplaySwitch(null,
                                                 null,
                                                 true);
                    }
                }

                return new DisplaySwitch();
            }
            else {

                return new DisplaySwitch('registration.tpl');
            }
        }
    }