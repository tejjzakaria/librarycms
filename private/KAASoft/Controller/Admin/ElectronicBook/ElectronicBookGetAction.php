<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-01-02
     */

    namespace KAASoft\Controller\Admin\ElectronicBook;

    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Session;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\FileStreamer;
    use KAASoft\Util\Message;

    class ElectronicBookGetAction extends PublicActionBase {
        /**
         * ElectronicBookGetAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute,
                                true);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {
            $electronicBookId = $args["electronicBookId"];
            $electronicBookDatabaseHelper = new ElectronicBookDatabaseHelper($this);
            $electronicBook = $electronicBookDatabaseHelper->getElectronicBook($electronicBookId);
            if ($electronicBook !== null) {
                $path = $electronicBook->getPath();

                $fileName = FileHelper::getElectronicBookRootLocation() . $path;
                if (file_exists($fileName)) {

                    $mimeType = FileHelper::getFileMimeType($fileName);
                    if ($mimeType !== null) {
                        header('Content-Type: ' . $mimeType);
                    }

                    //header('Content-Disposition: attachment; filename="' . basename($electronicBook->getPath()) . '"');
                    //readfile($fileName);

                    $fileStreamer = new FileStreamer($fileName);
                    $fileStreamer->send();
                }
                else {
                    Session::addSessionMessage(_("Couldn't find requested eBook."),
                                               Message::MESSAGE_STATUS_ERROR);

                    return new DisplaySwitch(null,
                                             $this->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                }
            }

            return new DisplaySwitch();
        }
    }