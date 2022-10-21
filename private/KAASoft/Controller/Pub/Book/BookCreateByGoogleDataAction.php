<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub\Book;

    use Exception;
    use KAASoft\Controller\ActionBase;
    use KAASoft\Controller\Admin\Author\AuthorDatabaseHelper;
    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\Image\ImageDatabaseHelper;
    use KAASoft\Controller\Admin\Image\ImageUploadBaseAction;
    use KAASoft\Controller\Admin\Publisher\PublisherDatabaseHelper;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Database\Entity\General\Author;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\General\Publisher;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\ExternalAPI\Google\BookAPI;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\HTTP\HttpClient;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class BookCreateByGoogleDataAction
     * @package KAASoft\Controller\Admin\Book
     */
    class BookCreateByGoogleDataAction extends PublicActionBase {
        /**
         * BookCreateByGoogleDataAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute = null) {
            parent::__construct($activeRoute,
                                true);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws \Exception
         */
        protected function action($args) {
            if (Helper::isAjaxRequest()) {
                $googleBookId = $args["googleBookId"];
                //$googleBookId = "bXS4rQEACAAJ";


                $bookApi = new BookAPI();
                $googleBookJSON = $bookApi->getBook($googleBookId);

                ActionBase::getLogger()->info($googleBookJSON);

                $bookObject = json_decode($googleBookJSON,
                                          true);

                if (isset( $bookObject[Controller::AJAX_PARAM_NAME_ERROR] )) {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $bookObject[Controller::AJAX_PARAM_NAME_ERROR] ]);

                    return new DisplaySwitch();
                }
                else {
                    if (isset( $bookObject["id"] ) and isset( $bookObject["volumeInfo"] )) {
                        $volumeInfo = $bookObject["volumeInfo"];
                        if ($this->startDatabaseTransaction()) {
                            //cover
                            $cover = $this->processBookCover($volumeInfo);
                            if ($cover === false) {
                                return new DisplaySwitch();
                            }

                            //authors
                            $authors = $this->processBookAuthors($volumeInfo);
                            if ($authors === false) {
                                return new DisplaySwitch();
                            }

                            //publisher
                            $publisher = $this->processBookPublisher($volumeInfo);
                            if ($publisher === false) {
                                return new DisplaySwitch();
                            }

                            //book
                            $book = $this->processBook($volumeInfo,
                                                       $cover,
                                                       $publisher,
                                                       $authors);
                            if ($book === false) {
                                return new DisplaySwitch();
                            }

                            $this->commitDatabaseChanges();
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("Book is successfully created."),
                                                            "bookId"                            => $book->getId() ]);

                            return new DisplaySwitch();
                        }
                        else {
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Database transaction couldn't be created.") ]);

                            return new DisplaySwitch();
                        }
                    }
                    else {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Google provided wrong data. Please try again.") ]);

                        return new DisplaySwitch();
                    }
                }
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }

        /**
         * @param           $volumeInfo
         * @param Image     $cover
         * @param Publisher $publisher
         * @param array     $authors
         * @return bool|Book
         */
        private function processBook($volumeInfo, $cover, $publisher, $authors) {
            $bookDatabaseHelper = new BookDatabaseHelper($this);
            $book = new Book();
            $book->setCreationDateTime(Helper::getDateTimeString());
            $book->setUpdateDateTime($book->getCreationDateTime());
            //$book->setQuantity();
            //$book->setActualQuantity(0);
            $book->setType("Standard");
            $book->setBinding("Hardcover");
            $book->setPhysicalForm("Book");
            $book->setSize("Medium");

            if (isset( $volumeInfo["title"] )) {
                $book->setTitle(ValidationHelper::getString($volumeInfo["title"]));
            }
            if (isset( $volumeInfo["publishedDate"] )) {
                $pubYear = 0;
                $pubDate = ValidationHelper::getString($volumeInfo["publishedDate"]);
                $dateLength = strlen($pubDate);
                $dateFormat = null;
                switch ($dateLength) {
                    case 4:
                        $dateFormat = Helper::DATABASE_YEAR_FORMAT;
                        break;
                    case 7:
                        $dateFormat = Helper::DATABASE_YEAR_MONTH_FORMAT;
                        break;
                    case 10:
                        $dateFormat = Helper::DATABASE_DATE_FORMAT;
                        break;
                }
                if ($dateFormat !== null) {
                    try {
                        $pubYear = Helper::reformatDateString($pubDate,
                                                              Helper::DATABASE_YEAR_FORMAT,
                                                              $dateFormat);
                    }
                    catch (Exception $e) {
                        // couldn't parse date
                        $pubYear = 0;
                    }
                }
                $book->setPublishingYear($pubYear);
            }
            if (isset( $volumeInfo["description"] )) {
                $book->setDescription(ValidationHelper::getString($volumeInfo["description"]));
            }

            if (isset( $volumeInfo["industryIdentifiers"] )) {
                foreach ($volumeInfo["industryIdentifiers"] as $identifier) {
                    if (strcmp($identifier["type"],
                               "ISBN_10") === 0
                    ) {
                        $book->setISBN10(ValidationHelper::getString($identifier["identifier"]));
                    }
                    elseif (strcmp($identifier["type"],
                                   "ISBN_13") === 0
                    ) {
                        $book->setISBN13(ValidationHelper::getString($identifier["identifier"]));
                    }
                }
            }
            if (isset( $volumeInfo["pageCount"] )) {
                $book->setPages(ValidationHelper::getNullableInt($volumeInfo["pageCount"]));
            }
            if (isset( $volumeInfo["printType"] )) {
                // todo: check values
                //$book->setPhysicalForm(ValidationHelper::getString($volumeInfo["printType"]));
            }
            if (isset( $volumeInfo["language"] )) {
                $book->setLanguage(ValidationHelper::getString($volumeInfo["language"]));
            }

            if ($cover !== null) {
                $book->setCoverId($cover->getId());
            }
            if ($publisher !== null) {
                $book->setPublisherId($publisher->getId());
            }

            $bookId = $bookDatabaseHelper->saveBook($book);
            if ($bookId === false) {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't save book.") ]);

                return false;
            }
            $book->setId($bookId);

            $authorCount = count($authors);
            if ($authorCount > 0) {
                $bookAuthorIds = [];
                foreach ($authors as $author) {
                    $bookAuthorIds[] = [ "bookId"   => $bookId,
                                         "authorId" => $author->getId() ];
                }
                $result = $bookDatabaseHelper->saveBookAuthorsArray($bookAuthorIds);
                if ($result === false) {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't save book authors.") ]);

                    return false;
                }
            }

            return $book;
        }

        /**
         * @param $volumeInfo
         * @return bool|Publisher|null
         */
        private function processBookPublisher($volumeInfo) {
            $publisher = null;
            if (isset( $volumeInfo["publisher"] )) {
                $publisherHelper = new PublisherDatabaseHelper($this);
                $publisherName = ValidationHelper::getString($volumeInfo["publisher"]);

                $existingPublisher = $publisherHelper->getPublisherByName($publisherName);
                if ($existingPublisher !== null and $existingPublisher instanceof Publisher) {
                    return $existingPublisher;
                }

                $publisher = new Publisher();
                $publisher->setName($publisherName);

                $publisherId = $publisherHelper->savePublisher($publisher);
                if ($publisherId === false) {
                    $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't save book publisher.") ]);

                    return false;
                }
                $publisher->setId($publisherId);
            }

            return $publisher;
        }

        /**
         * @param $volumeInfo
         * @return array|bool
         */
        private function processBookAuthors($volumeInfo) {
            $authors = [];
            if (isset( $volumeInfo["authors"] )) {
                $authorHelper = new AuthorDatabaseHelper($this);

                foreach ($volumeInfo["authors"] as $authorName) {
                    $authorNameV = ValidationHelper::getString($authorName);

                    $existingAuthor = $authorHelper->getAuthorByName($authorNameV);
                    if ($existingAuthor !== null and $existingAuthor instanceof Author) {
                        $authors [] = $existingAuthor;
                        continue;
                    }

                    $author = new Author();
                    $author->setLastName($authorNameV);
                    $authors [] = $author;
                }
                $authorCount = count($authors);
                if ($authorCount > 0) {
                    $result = $authorHelper->saveAuthors($authors);
                    if ($result === false or ( $authorCount !== count($result) and $result !== true )) {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't save authors.") ]);

                        return false;
                    }
                        for ($i = 0; $i < $authorCount; $i++) {
                            if ($authors[$i]->getId() === null) {
                                $authors[$i]->setId($result[$i]);
                            }
                        }
                }
            }

            return $authors;
        }

        /**
         * @param $volumeInfo
         * @return bool|Image
         */
        private function processBookCover($volumeInfo) {
            $cover = null;
            if (isset( $volumeInfo["imageLinks"]["thumbnail"] )) {
                $image = $this->saveBookCover($volumeInfo["imageLinks"]["thumbnail"]);
                if ($image !== false) {
                    $cover = $image;
                    $imageHelper = new ImageDatabaseHelper($this);
                    $coverId = $imageHelper->saveImage($cover);
                    if ($coverId === false) {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't save book cover.") ]);

                        return false;
                    }
                    $cover->setId($coverId);
                }
            }

            return $cover;
        }

        /**
         * @param $remoteImage
         * @return bool|Image
         */
        private function saveBookCover($remoteImage) {
            $image = new Image();

            $destFolder = FileHelper::getUniqueFolderName(FileHelper::getImageCurrentMonthLocation(FileHelper::getCoverLocation()));

            FileHelper::createDirectory($destFolder);
            $localFileName = FileHelper::getUniqueFileName($destFolder,
                                                           time() . ".jpg");

            $httpClient = new HttpClient();
            $httpClient->addHeader("User-Agent",
                                   "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:41.0) Gecko/20100101 Firefox/41.0");
            $downloadResult = $httpClient->fetch($remoteImage);
            if ($downloadResult === false) {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't download cover.") ]);

                return false;
            }
            $downloadResult = $httpClient->getResults();
            //$downloadResult = file_get_contents($remoteImage);
            $result = file_put_contents($localFileName,
                                        $downloadResult);

            ControllerBase::getLogger()->trace("[GoogleCreateBook] getting book cover: " . $remoteImage . "[download result: " . $downloadResult . "; result of saving: " . $result . "]");

            if ($result === false) {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Couldn't copy cover to the database.") ]);

                return false;
            }

            $image->setPath(FileHelper::getRelativePath(FileHelper::getImageRootLocation(),
                                                        $localFileName));
            $image->setUploadingDateTime(Helper::getDateTimeString());
            $image->setIsGallery(true);

            ImageUploadBaseAction::resizeImages($localFileName,
                                                ImageUploadBaseAction::getCoverResolutions());


            return $image;
        }
    }