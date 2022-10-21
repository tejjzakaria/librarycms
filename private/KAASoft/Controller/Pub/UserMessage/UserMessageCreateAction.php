<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\UserMessage;

    use KAASoft\Controller\ControllerBase;
    use KAASoft\Controller\Pub\PublicDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Database\Entity\Util\UserMessage;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class UserMessageCreateAction
     * @package Citadel\Controller\Pub\ClientMessage
     */
    class UserMessageCreateAction extends PublicActionBase {
        /**
         * UserMessageCreateAction constructor.
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
            try {
                if (Helper::isAjaxRequest()) {
                    if ($this->startDatabaseTransaction()) {
                        $userMessage = UserMessage::getObjectInstance($_POST);
                        $userMessage->setCreationDate(Helper::getDateTimeString());
                        $userMessage->setIsViewed(false);

                        $helper = new PublicDatabaseHelper($this);
                        $userMessageId = $helper->saveUserMessage($userMessage);
                        if ($userMessageId === false) {
                            $this->rollbackDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "error" => _("UserMessage saving is failed for some reason.") ]);

                            return new DisplaySwitch();
                        }
                        $this->commitDatabaseChanges();
                        $this->putArrayToAjaxResponse([ "userMessageId" => $userMessageId,
                                                        "creationDate"  => Helper::reformatDateTimeString($userMessage->getCreationDate(),
                                                                                                          $this->siteViewOptions->getOptionValue(SiteViewOptions::DATE_TIME_FORMAT)) ]);
                        $this->smarty->assign("userMessage",
                                              $userMessage);


                        // $this->setDynamicShortCodeQuery([ "SELECT * FROM __ClientMessages__  WHERE id = " . $userMessageId ]);
                    }
                }
                else {
                    $this->putArrayToAjaxResponse([ "error" => _("AJAX request is required only.") ]);
                }
            }
            catch (PDOException $e) {
                $this->rollbackDatabaseChanges();
                ControllerBase::getLogger()->error($e->getMessage(),
                                                   $e);
                $errorMessage = sprintf(_("Couldn't create UserMessage.%s%s"),
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ "error" => $errorMessage ]);
            }

            return new DisplaySwitch();
        }
    }