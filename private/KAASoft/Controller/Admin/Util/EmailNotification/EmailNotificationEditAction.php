<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util\EmailNotification;

    use Exception;
    use KAASoft\Controller\Admin\Util\UtilDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Util\EmailNotification;
    use KAASoft\Environment\Routes\Admin\EmailNotificationRoutes;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\EmailAddress;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;


    /**
     * Class EmailNotificationEditAction
     * @package KAASoft\Controller\Admin\Util\EmailNotification
     */
    class EmailNotificationEditAction extends AdminActionBase {
        /**
         * EmailNotificationEditAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        protected function sanitizeUserInput($excludeKeys = []) {
            parent::sanitizeUserInput(array_merge($excludeKeys,
                                                  ["content"]));
        }


        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $route = $args["route"];
            $utilDatabaseHelper = new UtilDatabaseHelper($this);
            if (Helper::isPostRequest()) { // POST request
                try {
                    if ($this->startDatabaseTransaction()) {
                        $fromName = isset( $_POST["fromName"] ) ? $_POST["fromName"] : null;
                        $emailNotification = EmailNotification::getObjectInstance(array_merge($_POST,
                                                                                              [ "route" => $route ]));
                        $emailNotification->setUserId($this->session->getUser()->getId());
                        $emailNotification->setFrom(new EmailAddress($emailNotification->getFrom(),
                                                                     $fromName));
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
                                                     $this->routeController->getRouteString(EmailNotificationRoutes::EMAIL_NOTIFICATION_EDIT_ROUTE,
                                                                                            [ 'route' => $route ]));
                        }


                        $result = $utilDatabaseHelper->saveEmailNotification($emailNotification);
                        if ($result === false) {
                            $this->rollbackDatabaseChanges();
                            $errorMessage = _("EmailNotification saving is failed for some reason.");
                            $this->session->addSessionMessage($errorMessage,
                                                              Message::MESSAGE_STATUS_ERROR);
                        }
                        else {
                            $this->commitDatabaseChanges();
                            $errorMessage = _("EmailNotification is successfully saved.");
                            $this->session->addSessionMessage($errorMessage,
                                                              Message::MESSAGE_STATUS_INFO);
                        }
                    }
                }
                catch (Exception $e) {
                    $this->rollbackDatabaseChanges();
                    ControllerBase::getLogger()->error($e->getMessage(),
                                                       $e);
                    $errorMessage = sprintf(_("Couldn't save EmailNotification.%s%s"),
                                            Helper::HTML_NEW_LINE,
                                            $e->getMessage());
                    $this->session->addSessionMessage($errorMessage,
                                                      Message::MESSAGE_STATUS_ERROR);

                }

                return new DisplaySwitch(null,
                                         $this->routeController->getRouteString(EmailNotificationRoutes::EMAIL_NOTIFICATION_EDIT_ROUTE,
                                                                                [ 'route' => $route ]));

            }
            else {
                // important
                $this->smarty->unregister_outputfilter([ $this,
                                                         "smartyOutputFilter" ]);

                $emailNotification = $utilDatabaseHelper->getEmailNotification($route);

                if ($emailNotification === null) {
                    $this->session->addSessionMessage(sprintf(_("EmailNotification with ID = '%s' is not found."),
                                                              $route),
                                                      Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                }

                $this->smarty->assign("action",
                                      "edit");
                $this->smarty->assign("emailNotification",
                                      $emailNotification);

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