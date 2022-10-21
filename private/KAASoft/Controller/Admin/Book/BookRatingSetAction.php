<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-01-24
     */


    namespace KAASoft\Controller\Admin\Book;


    use Exception;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\BookRating;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;

    class BookRatingSetAction extends AdminActionBase {
        /**
         * BookEditAction constructor.
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
            $bookId = $args["bookId"];
            $rating = $args["rating"];
            $bookDatabaseHelper = new BookDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        $user = $this->session->getUser();
                        if ($this->startDatabaseTransaction()) {
                            if ($user === null || !( $user instanceof User )) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Registered users can set rating only.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch(null,
                                                         null,
                                                         true);
                            }
                            $userId = $user->getId();
                            $bookRating = $bookDatabaseHelper->getBookRating($bookId,
                                                                                 $userId);
                            if ($bookRating === null) {
                                $bookRating = new BookRating();
                                $bookRating->setUserId($user->getId());
                                $bookRating->setBookId($bookId);
                                $bookRating->setCreationDateTime(Helper::getDateTimeString());
                            }
                            $bookRating->setUpdateDateTime(Helper::getDateTimeString());
                            $bookRating->setRating($rating);

                            $result = $bookDatabaseHelper->saveBookRating($bookRating);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Couldn't save book rating for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch(null,
                                                         null,
                                                         true);
                            }

                            $calculatedRating = $bookDatabaseHelper->getCalculatedBookRating($bookId);
                            if ($calculatedRating !== null) {
                                $result = $bookDatabaseHelper->saveRating($bookId,
                                                                          $calculatedRating);
                                if ($result === false) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("Couldn't update book rating for some reason.");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch(null,
                                                             null,
                                                             true);
                                }
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "bookId"           => $bookId,
                                                            "calculatedRating" => $calculatedRating ]);
                        }
                    }
                    catch (Exception $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't set book rating.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }
            }

            return new DisplaySwitch();
        }
    }