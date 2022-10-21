<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\EmailNotification;

    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Util\EmailNotification;
    use KAASoft\Environment\Routes\Admin\EmailNotificationRoutes;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\EmailAddress;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use PDOException;


    /**
     * Class EmailNotificationCreateAction
     * @package KAASoft\Controller\Admin\Util\EmailNotification
     */
    class EmailNotificationCreateAction extends AdminActionBase {
        /**
         * EmailNotificationCreateAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        protected function sanitizeUserInput($excludeKeys = []) {
            parent::sanitizeUserInput(array_merge($excludeKeys,
                                                  [ "content" ]));
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $this->smarty->assign("action",
                                  "create");
            if (Helper::isPostRequest()) { // POST request
                try {
                    if ($this->startDatabaseTransaction()) {
                        $utilDatabaseHelper = new UtilDatabaseHelper($this);
                        $fromName = isset( $_POST["fromName"] ) ? $_POST["fromName"] : null;
                        $emailNotification = EmailNotification::getObjectInstance($_POST);
                        $emailNotification->setUserId($this->session->getUser()->getId());
                        $emailNotification->setCreationDateTime(Helper::getDateTimeString());
                        $emailNotification->setUpdateDateTime($emailNotification->getCreationDateTime());
                        $emailNotification->setFrom(new EmailAddress($emailNotification->getFrom(),
                                                                     $fromName));
                        $emailNotification->setTemplateName($emailNotification->getRoute() . ".tpl");

                        $activeTheme = ControllerBase::getThemeSettings()->getActiveTheme();
                        FileHelper::createDirectory(FileHelper::getEmailNotificationTemplateDirectory($activeTheme));
                        if (false === file_put_contents(FileHelper::getEmailNotificationTemplateDirectory($activeTheme) . DIRECTORY_SEPARATOR . $emailNotification->getTemplateName(),
                                                        $emailNotification->getContent())
                        ) {
                            $this->rollbackDatabaseChanges();
                            $errorMessage = _("EmailNotification saving is failed. Couldn't save template on drive.");
                            $this->session->addSessionMessage($errorMessage,
                                                              Message::MESSAGE_STATUS_ERROR);

                            return new DisplaySwitch(null,
                                                     $this->routeController->getRouteString(EmailNotificationRoutes::EMAIL_NOTIFICATION_CREATE_ROUTE));
                        }

                        $toStringArray = explode(";",
                                                 $_POST["to"]);
                        $toArray = [];
                        foreach ($toStringArray as $toString) {
                            if (!empty( $toString )) {
                                $addressAndName = explode("|",
                                                          $toString);

                                $toArray[] = new EmailAddress($addressAndName[0],
                                                              count($addressAndName) == 2 ? $addressAndName[1] : null);

                            }
                        }
                        $emailNotification->setTo($toArray);
                        $emailNotification->setUpdateDateTime(Helper::getDateTimeString());

                        $emailNotificationId = $utilDatabaseHelper->saveEmailNotification($emailNotification);
                        if ($emailNotificationId === false) {
                            $this->rollbackDatabaseChanges();
                            $errorMessage = _("EmailNotification saving is failed for some reason.");
                            $this->session->addSessionMessage($errorMessage,
                                                              Message::MESSAGE_STATUS_ERROR);

                            return new DisplaySwitch(null,
                                                     $this->routeController->getRouteString(EmailNotificationRoutes::EMAIL_NOTIFICATION_CREATE_ROUTE));

                        }

                        $this->commitDatabaseChanges();
                        $message = _("EmailNotification is saved successfully.");
                        $this->session->addSessionMessage($message,
                                                          Message::MESSAGE_STATUS_INFO);

                        return new DisplaySwitch(null,
                                                 $this->routeController->getRouteString(EmailNotificationRoutes::EMAIL_NOTIFICATION_EDIT_ROUTE,
                                                                                        [ 'route' => $emailNotification->getRoute() ]));
                    }
                }
                catch (PDOException $e) {
                    $this->rollbackDatabaseChanges();
                    ControllerBase::getLogger()->error($e->getMessage(),
                                                       $e);
                    $errorMessage = sprintf(_("Couldn't create EmailNotification.%s%s"),
                                            Helper::HTML_NEW_LINE,
                                            $e->getMessage());
                    $this->session->addSessionMessage($errorMessage,
                                                      Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             $this->routeController->getRouteString(EmailNotificationRoutes::EMAIL_NOTIFICATION_CREATE_ROUTE));
                }

                return new DisplaySwitch('admin/email-notifications/email-notification.tpl');
            }
            else {
                $this->smarty->assign("emailNotification",
                                      null);

                $sortedRoutes = $this->routeController->getRoutes();
                uasort($sortedRoutes,
                       [ $this->routeController,
                         "compareRoutesByTitle" ]);
                $this->smarty->assign("sortedRoutes",
                                      $sortedRoutes);

                return new DisplaySwitch('admin/email-notifications/email-notification.tpl');
            }
        }


    }