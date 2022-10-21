<?php
    /**
 * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
 */

    namespace KAASoft\Controller\Admin\Tag;

    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use PDOException;

    /**
     * Class TagDeleteAction
     * @package KAASoft\Controller\Admin\Tag
     */
    class TagDeleteAction extends AdminActionBase {
        /**
         * TagDeleteAction constructor.
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
            $tagId = $args["tagId"];
            $tagDatabaseHelper = new TagDatabaseHelper($this);
            try {
                if (Helper::isAjaxRequest()) {
                    if ($this->startDatabaseTransaction()) {
                        if ($tagDatabaseHelper->isTagExist($tagId)) {
                            $result = $tagDatabaseHelper->deleteTag($tagId);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = sprintf(_("Couldn't delete Tag '%d' for some reason."),
                                                        $tagId);
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                                return new DisplaySwitch();
                            }
                        }
                        else {
                            $this->kaaSoftDatabase->rollbackTransaction();
                            $errorMessage = sprintf(_("There is no Tag with Id '%d' in database table \"%s\"."),
                                                    $tagId,
                                                    KAASoftDatabase::$TAGS_TABLE_NAME);
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);

                            return new DisplaySwitch();
                        }

                        $this->commitDatabaseChanges();
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Tag is deleted successfully.") ]);
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
                $errorMessage = sprintf(_("Couldn't delete Tag '%d'.%s%s"),
                                        $tagId,
                                        Helper::HTML_NEW_LINE,
                                        $e->getMessage());
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
            }

            return new DisplaySwitch();

        }
    }