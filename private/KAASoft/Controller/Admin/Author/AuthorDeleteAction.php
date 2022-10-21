<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Author;

    use KAASoft\Controller\Admin\Image\ImageDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class AuthorDeleteAction
     * @package KAASoft\Controller\Admin\Author
     */
    class AuthorDeleteAction extends AdminActionBase {
        /**
         * AuthorDeleteAction constructor.
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
            $authorId = $args["authorId"];
            $authorDatabaseHelper = new AuthorDatabaseHelper($this);
            try {
                if (Helper::isAjaxRequest()) {
                    if ($this->startDatabaseTransaction()) {
                        $author = $authorDatabaseHelper->getAuthor($authorId);
                        if ($author !== null) {
                            $result = $authorDatabaseHelper->deleteAuthor($authorId);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Couldn't delete Author '%d' for some reason."),
                                                        $authorId);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }
                            if ($author->getPhoto() !== null) {
                                $imageHelper = new ImageDatabaseHelper($this);
                                if ($imageHelper->deleteImage($author->getPhoto()->getId()) === false) {
                                    $this->rollbackDatabaseChanges();
                                    $errorMessage = _("Couldn't delete Author's Photo.");
                                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                    return new DisplaySwitch();
                                }

                                $photoPath = $author->getPhoto()->getAbsolutePath();
                                FileHelper::deleteFolder(dirname($photoPath));
                            }
                        }
                        else {
                            $this->kaaSoftDatabase->rollbackTransaction();
                            $errorMessage = sprintf(_("There is no Author with Id '%d' in database table \"%s\"."),
                                                    $authorId,
                                                    KAASoftDatabase::$AUTHORS_TABLE_NAME);
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                            return new DisplaySwitch();
                        }

                        $this->commitDatabaseChanges();
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Author is deleted successfully.") ]);
                    }
                    else {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Database transaction couldn't be created.") ]);
                    }
                }
                else {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
                }
            }
            catch (PDOException $e) {
                $this->rollbackDatabaseChanges();
                ControllerBase::getLogger()->error($e->getMessage(),
                                                   $e);
                $errorMessage = sprintf(_("Couldn't delete Author '%d'.%s%s"),
                                        $authorId,
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
            }

            return new DisplaySwitch();

        }
    }