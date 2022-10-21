<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Review;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Review;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class ReviewCreateAction
     * @package KAASoft\Controller\Admin\Review
     */
    class ReviewCreateAction extends AdminActionBase {
        /**
         * ReviewCreateAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws \Exception
         */
        protected function action($args) {
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            $reviewDatabaseHelper = new ReviewDatabaseHelper($this);

                            $review = Review::getObjectInstance($_POST);
                            $review->setCreationDateTime(Helper::getDateTimeString());
                            $review->setIsPublish(!$this->siteViewOptions->getOptionValue(SiteViewOptions::MODERATE_REVIEWS));
                            $reviewId = $reviewDatabaseHelper->saveReview($review);

                            if ($reviewId === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Review saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "reviewId" => $reviewId ]);
                        }
                    }
                    catch (PDOException $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save Review.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }

                return new DisplaySwitch();
            }
            else {
                $this->smarty->assign("action",
                                      "create");

                return new DisplaySwitch('admin/reviews/review.tpl');
            }
        }
    }