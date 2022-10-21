<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Issue;

    use Exception;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Issue;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class IssueEditAction
     * @package KAASoft\Controller\Admin\Issue
     */
    class IssueEditAction extends AdminActionBase {
        /**
         * IssueEditAction constructor.
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
            $issueId = $args["issueId"];
            $issueDatabaseHelper = new IssueDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            //$issueId = $_POST["issueId"];
                            $issue = Issue::getObjectInstance($_POST);
                            $issue->setId($issueId);
                            $issue->setIssueDate(Helper::reformatDateString($issue->getIssueDate(),
                                                                            Helper::DATABASE_DATE_FORMAT,
                                                                            $this->siteViewOptions->getOptionValue(SiteViewOptions::INPUT_DATE_FORMAT)));
                            $issue->setExpiryDate(Helper::reformatDateString($issue->getExpiryDate(),
                                                                             Helper::DATABASE_DATE_FORMAT,
                                                                             $this->siteViewOptions->getOptionValue(SiteViewOptions::INPUT_DATE_FORMAT)));

                            $result = $issueDatabaseHelper->saveIssue($issue);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Issue saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "issueId" => $issueId ]);
                        }
                    }
                    catch (Exception $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save Issue.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }

                return new DisplaySwitch();
            }
            else {
                $issue = null;
                if ($issueId !== null) {
                    $issue = $issueDatabaseHelper->getIssue($issueId);

                    if ($issue === null) {
                        $this->session->addSessionMessage(sprintf(_("Issue with id = '%d' is not found."),
                                                                  $issueId),
                                                          Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                    }

                    $issue->setIssueDate(Helper::reformatDateString($issue->getIssueDate(),
                                                                    $this->siteViewOptions->getOptionValue(SiteViewOptions::INPUT_DATE_FORMAT)));
                    $issue->setExpiryDate(Helper::reformatDateString($issue->getExpiryDate(),
                                                                     $this->siteViewOptions->getOptionValue(SiteViewOptions::INPUT_DATE_FORMAT)));
                    $issue->setReturnDate(Helper::reformatDateString($issue->getReturnDate(),
                                                                     $this->siteViewOptions->getOptionValue(SiteViewOptions::INPUT_DATE_FORMAT)));

                }
                $this->smarty->assign("action",
                                      "edit");
                $this->smarty->assign("issue",
                                      $issue);

                return new DisplaySwitch('admin/issues/issue.tpl');
            }
        }
    }