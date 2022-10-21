<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Genre;

    use Exception;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\General\Genre;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;

    /**
     * Class GenreEditAction
     * @package KAASoft\Controller\Admin\Genre
     */
    class GenreEditAction extends AdminActionBase {
        /**
         * GenreEditAction constructor.
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
            $genreId = $args["genreId"];
            $genreDatabaseHelper = new GenreDatabaseHelper($this);
            if (Helper::isAjaxRequest()) {
                if (Helper::isPostRequest()) { // POST request
                    try {
                        if ($this->startDatabaseTransaction()) {
                            //$genreId = $_POST["genreId"];
                            $genre = Genre::getObjectInstance($_POST);
                            $genre->setId($genreId);

                            $result = $genreDatabaseHelper->saveGenre($genre);
                            if ($result === false) {
                                $this->rollbackDatabaseChanges();
                                $errorMessage = _("Genre saving is failed for some reason.");
                                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ "genreId" => $genreId ]);
                        }
                    }
                    catch (Exception $e) {
                        $this->rollbackDatabaseChanges();
                        ControllerBase::getLogger()->error($e->getMessage(),
                                                           $e);
                        $errorMessage = sprintf(_("Couldn't save Genre.%s%s"),
                                                Helper::HTML_NEW_LINE,
                                                $e->getMessage());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ]);
                    }
                }

                return new DisplaySwitch();
            }
            else {
                $genre = null;
                if ($genreId !== null) {
                    $genre = $genreDatabaseHelper->getGenre($genreId);

                    if ($genre === null) {
                        $this->session->addSessionMessage(sprintf(_("Genre with id = '%d' is not found."),
                                                                  $genreId),
                                                          Message::MESSAGE_STATUS_ERROR);

                        return new DisplaySwitch(null,
                                                 $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                    }
                }
                $this->smarty->assign("action",
                                      "edit");
                $this->smarty->assign("genre",
                                      $genre);

                return new DisplaySwitch('admin/genres/genre.tpl');
            }
        }
    }