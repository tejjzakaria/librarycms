<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Database\Entity\General;

    use JsonSerializable;
    use KAASoft\Database\Entity\DatabaseEntity;
    use KAASoft\Database\Entity\Util\FieldOptions;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class Book
     * @package KAASoft\Database\Entity\General
     */
    class Book extends DatabaseEntity implements JsonSerializable {
        const BOOK_URL_FORMAT_1 = "%s"; // bookName
        // const BOOK_URL_FORMAT_2 = "%s-%s"; // bookName-serialNumber
        const BOOK_URL_FORMAT_3 = "%s-%s"; // bookName-ISBN13

        const BOOK_URL_REPLACEMENT = [ " "  => "-",
                                       "/"  => "-",
                                       "!"  => "-",
                                       "*"  => "-",
                                       "'"  => "-",
                                       "\"" => "-",
                                       "("  => "-",
                                       ")"  => "-",
                                       "["  => "-",
                                       "]"  => "-",
                                       ";"  => "-",
                                       ":"  => "-",
                                       "@"  => "-",
                                       "&"  => "-",
                                       "="  => "-",
                                       "+"  => "-",
                                       "$"  => "-",
                                       ","  => "-",
                                       "?"  => "-",
                                       "#"  => "-" ];
        /**
         * @var DatabaseField[]
         */
        private static $CUSTOM_FIELDS = [];

        /**
         * @var array IssueLog
         */
        private $logs;
        /**
         * @var ElectronicBook
         */
        private $eBook;
        /**
         * @var Issue
         */
        private $issue;
        /**
         * @var array Author
         */
        private $authors;
        /**
         * @var Publisher
         */
        private $publisher;
        /**
         * @var Series
         */
        private $series;
        /**
         * @var Image
         */
        private $cover;

        /**
         * @var array of Image
         */
        private $images;

        /**
         * @var array Genre
         */
        private $genres;

        /**
         * @var array Tag
         */
        private $tags;

        /**
         * @var array Store
         */
        private $stores;
        /**
         * @var array Location
         */
        private $locations;
        /**
         * @var array Review
         */
        private $reviews;
        /**
         * @var array BookCopy
         */
        private $bookCopies;
        /**
         * @var int Number of votes in book rating
         */
        private $bookRatingVotesNumber;
        private $quantity;
        private $issuedQuantity;

        private $eBookId;
        private $title;
        private $subtitle;
        private $seriesId;
        private $publisherId;
        private $publishingYear;
        private $pages;
        private $binding;
        private $coverId;
        private $description;
        private $ownerId;
        private $rating;
        private $ISBN10;
        private $ISBN13;
        private $notes;
        private $edition;
        private $physicalForm;
        private $size;
        private $price;
        private $language;
        private $type;
        private $updateDateTime;
        private $creationDateTime;
        private $metaTitle;
        private $metaKeywords;
        private $metaDescription;
        private $url;

        //private $externalBuyLink;
        //private $externalPreview;

        /**
         * Book constructor.
         * @param null $id
         */
        function __construct($id = null) {
            parent::__construct($id);
        }

        /**
         * @return array
         */
        public function getDatabaseArray() {
            $customFields = Book::getCustomFields();

            $customFieldKeyValuePairs = [];
            foreach ($customFields as $customField) {
                if ($customField instanceof DatabaseField) {
                    $fieldName = $customField->getName();
                    $customFieldKeyValuePairs[$fieldName] = $this->$fieldName;
                }
            }

            return array_merge(parent::getDatabaseArray(),
                               $customFieldKeyValuePairs,
                               [ "title"            => $this->title,
                                 "eBookId"          => $this->eBookId,
                                 "subtitle"         => $this->subtitle,
                                 "seriesId"         => $this->seriesId,
                                 "publisherId"      => $this->publisherId,
                                 "publishingYear"   => $this->publishingYear,
                                 "pages"            => $this->pages,
                                 "binding"          => $this->binding,
                                 "ISBN10"           => $this->ISBN10,
                                 "ISBN13"           => $this->ISBN13,
                                 "coverId"          => $this->coverId,
                                 "description"      => $this->description,
                                 "notes"            => $this->notes,
                                 "rating"           => $this->rating,
                                 "edition"          => $this->edition,
                                 "physicalForm"     => $this->physicalForm,
                                 "size"             => $this->size,
                                 "price"            => $this->price,
                                 "language"         => $this->language,
                                 "type"             => $this->type,
                                 "updateDateTime"   => $this->updateDateTime,
                                 "creationDateTime" => $this->creationDateTime,
                                 "metaTitle"        => $this->metaTitle,
                                 "metaKeywords"     => $this->metaKeywords,
                                 "metaDescription"  => $this->metaDescription,
                                 //"externalBuyLink"  => $this->externalBuyLink,
                                 //"externalPreview"  => $this->externalPreview,
                                 "url"              => $this->url ]);
        }

        /**
         * @param array $databaseArray
         * @return Book to restore form databaseArray
         */
        public static function getObjectInstance(array $databaseArray) {
            $book = new Book(ValidationHelper::getNullableInt($databaseArray["id"]));
            $book->setTitle(ValidationHelper::getString($databaseArray["title"]));
            $book->setEBookId(ValidationHelper::getNullableInt($databaseArray["eBookId"]));
            $book->setSubtitle(ValidationHelper::getString($databaseArray["subtitle"]));
            $book->setSeriesId(ValidationHelper::getNullableInt($databaseArray["seriesId"]));
            $book->setPublisherId(ValidationHelper::getNullableInt($databaseArray["publisherId"]));
            $book->setPublishingYear(ValidationHelper::getNullableInt($databaseArray["publishingYear"]));
            $book->setPages(ValidationHelper::getNullableInt($databaseArray["pages"]));
            $book->setBinding(ValidationHelper::getString($databaseArray["binding"]));
            $book->setISBN10(ValidationHelper::getString($databaseArray["ISBN10"]));
            $book->setISBN13(ValidationHelper::getString($databaseArray["ISBN13"]));
            $book->setCoverId(ValidationHelper::getNullableInt($databaseArray["coverId"]));
            $book->setDescription(ValidationHelper::getString($databaseArray["description"]));
            $book->setNotes(ValidationHelper::getString($databaseArray["notes"]));
            $book->setRating(ValidationHelper::getFloat($databaseArray["rating"]));
            $book->setEdition(ValidationHelper::getString($databaseArray["edition"]));
            $book->setPhysicalForm(ValidationHelper::getString($databaseArray["physicalForm"]));
            $book->setSize(ValidationHelper::getString($databaseArray["size"]));
            $book->setPrice(ValidationHelper::getFloat($databaseArray["price"]));
            $book->setLanguage(ValidationHelper::getString($databaseArray["language"]));
            $book->setType(ValidationHelper::getString($databaseArray["type"]));
            $book->setUpdateDateTime(ValidationHelper::getString($databaseArray["updateDateTime"]));
            $book->setCreationDateTime(ValidationHelper::getString($databaseArray["creationDateTime"]));
            $book->setMetaTitle(ValidationHelper::getString($databaseArray["metaTitle"]));
            $book->setMetaKeywords(ValidationHelper::getString($databaseArray["metaKeywords"]));
            $book->setMetaDescription(ValidationHelper::getString($databaseArray["metaDescription"]));
            //$book->setExternalBuyLink(ValidationHelper::getString($databaseArray["externalBuyLink"]));
            //$book->setExternalPreview(ValidationHelper::getString($databaseArray["externalPreview"]));
            $book->setUrl(ValidationHelper::getString($databaseArray["url"]));


            $customFields = Book::getCustomFields();

            foreach ($customFields as $customField) {
                if ($customField instanceof DatabaseField) {
                    $fieldName = $customField->getName();
                    $book->$fieldName = ValidationHelper::getFieldValue($customField,
                                                                        $databaseArray[$fieldName]);
                }
            }

            return $book;
        }

        /**
         * @param $fieldName
         * @return null
         */
        public function getCustomFieldValue($fieldName) {
            return isset( $this->$fieldName ) ? $this->$fieldName : null;
        }

        /**
         * @return array
         */
        public static function getDatabaseFieldNames() {
            $customFields = Book::getCustomFields();

            $customFieldNames = [];
            foreach ($customFields as $customField) {
                if ($customField instanceof DatabaseField) {
                    $customFieldNames[] = KAASoftDatabase::$BOOKS_TABLE_NAME . "." . $customField->getName();
                }
            }

            return array_merge(parent::getDatabaseFieldNames(),
                               $customFieldNames,
                               [ KAASoftDatabase::$BOOKS_TABLE_NAME . ".title",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".subtitle",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".eBookId",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".ISBN10",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".seriesId",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".publisherId",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".publishingYear",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".pages",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".binding",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".ISBN13",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".coverId",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".description",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".notes",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".edition",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".physicalForm",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".size",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".price",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".rating",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".language",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".type",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".updateDateTime",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".creationDateTime",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".metaTitle",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".metaKeywords",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".metaDescription",
                                // KAASoftDatabase::$BOOKS_TABLE_NAME . ".externalBuyLink",
                                // KAASoftDatabase::$BOOKS_TABLE_NAME . ".externalPreview",
                                 KAASoftDatabase::$BOOKS_TABLE_NAME . ".url" ]);
        }

        /**
         * @return array
         */
        public static function getFieldListPrivate() {
            $customFields = Book::getCustomFields();

            $customFieldNamesPairs = [];
            foreach ($customFields as $customField) {
                if ($customField instanceof DatabaseField) {
                    $customFieldNamesPairs[$customField->getName()] = $customField->getTitle();
                }
            }

            return array_merge([ "title"           => _("Title"),
                                 "subtitle"        => _("Subtitle"),
                                 "ISBN10"          => _("ISBN10"),
                                 "ISBN13"          => _("ISBN13"),
                                 "eBook"           => _("eBook"),
                                 "series"          => _("Series"),
                                 "publisher"       => _("Publisher"),
                                 "genre"           => _("Genre"),
                                 "author"          => _("Author"),
                                 "store"           => _("Store"),
                                 "location"        => _("Location"),
                                 "tag"             => _("Tag"),
                                 //"review"         => _("Review"),
                                 "publishingYear"  => _("Publishing Year"),
                                 "pages"           => _("Pages"),
                                 "binding"         => _("Binding"),
                                 "cover"           => _("Cover"),
                                 //"image"          => _("Image"),
                                 "description"     => _("Description"),
                                 //"quantity"       => _("Quantity"),
                                 "edition"         => _("Edition"),
                                 "physicalForm"    => _("Physical Form"),
                                 "size"            => _("Size"),
                                 "price"           => _("Price"),
                                 //"rating"         => _("Rating"),
                                 "language"        => _("Language"),
                                 "type"            => _("Type"),
                                 "url"             => _("URL"),
                                 /*"externalBuyLink" => _("External Book Buy URL"),
                                 "externalPreview" => _("External Book Preview")*/ ],
                               $customFieldNamesPairs);
        }

        /**
         * @return array
         */
        public static function getVisibleFieldListPublic() {
            $customFields = Book::getCustomFields();

            $customFieldNamesPairs = [];
            foreach ($customFields as $customField) {
                if ($customField instanceof DatabaseField) {
                    $customFieldNamesPairs[$customField->getName()] = new FieldOptions($customField->getTitle(),
                                                                                       true);
                }
            }

            return array_merge([ "title"           => new FieldOptions(_("Title"),
                                                                       false),
                                 "subtitle"        => new FieldOptions(_("Subtitle"),
                                                                       true),
                                 "ISBN10"          => new FieldOptions(_("ISBN10"),
                                                                       true),
                                 "ISBN13"          => new FieldOptions(_("ISBN13"),
                                                                       true),
                                 /*"eBook"          => new FieldOptions(_("eBook"),
                                                                      true),*/
                                 "series"          => new FieldOptions(_("Series"),
                                                                       true),
                                 "publisher"       => new FieldOptions(_("Publisher"),
                                                                       true),
                                 "genre"           => new FieldOptions(_("Genre"),
                                                                       true),
                                 "author"          => new FieldOptions(_("Author"),
                                                                       true),
                                 /*"store"          => new FieldOptions(_("Store"),
                                                                      true),
                                 "location"       => new FieldOptions(_("Location"),
                                                                      true),*/
                                 "tag"             => new FieldOptions(_("Tag"),
                                                                       true),
                                 /*"review"         => new FieldOptions(_("Review"),
                                                                      true),*/
                                 "publishingYear"  => new FieldOptions(_("Publishing Year"),
                                                                       true),
                                 "pages"           => new FieldOptions(_("Pages"),
                                                                       true),
                                 "binding"         => new FieldOptions(_("Binding"),
                                                                       true),
                                 "cover"           => new FieldOptions(_("Cover"),
                                                                       false),
                                 "images"          => new FieldOptions(_("Images"),
                                                                       true),
                                 "description"     => new FieldOptions(_("Description"),
                                                                       true),
                                 "edition"         => new FieldOptions(_("Edition"),
                                                                       true,
                                                                       false),
                                 "physicalForm"    => new FieldOptions(_("Physical Form"),
                                                                       true,
                                                                       false),
                                 "size"            => new FieldOptions(_("Size"),
                                                                       true,
                                                                       false),
                                 /*"price"          => new FieldOptions(_("Price"),
                                                                      true),*/
                                 "rating"          => new FieldOptions(_("Rating"),
                                                                       true),
                                 "language"        => new FieldOptions(_("Language"),
                                                                       true,
                                                                       false),
                                 "type"            => new FieldOptions(_("Type"),
                                                                       true,
                                                                       false),
                                 /*"externalBuyLink" => new FieldOptions(_("External Book Buy URL"),
                                                                       true,
                                                                       false),
                                 "externalPreview" => new FieldOptions(_("External Book Preview"),
                                                                       true,
                                                                       false),*/ ],
                               $customFieldNamesPairs);
        }

        /**
         * (PHP 5 &gt;= 5.4.0)<br/>
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         */
        function jsonSerialize() {
            return array_merge($this->getDatabaseArray(),
                               [ "cover"      => $this->cover,
                                 "bookCopies" => $this->bookCopies,
                                 "quantity"   => $this->quantity,
                                 "images"     => $this->images,
                                 "publisher"  => $this->publisher,
                                 "authors"    => $this->authors,
                                 "series"     => $this->series,
                                 "genres"     => $this->genres,
                                 "tags"       => $this->tags ]);
        }

        /**
         * @param $formatNumber int 1,2 or 3
         * @return string
         */
        public function generateUrl($formatNumber = 1) {
            // const BOOK_URL_FORMAT_1 = "%s"; // bookName
            // const BOOK_URL_FORMAT_2 = "%s-%s"; // bookName-serialNumber
            // const BOOK_URL_FORMAT_3 = "%s-%s"; // bookName-ISBN13
            $url = null;
            switch ($formatNumber) {
                /*case 2:
                    $url = sprintf(Book::BOOK_URL_FORMAT_2,
                                   $this->title,
                                   $this->bookSN);
                    break;*/
                case 3:
                    $url = sprintf(Book::BOOK_URL_FORMAT_3,
                                   $this->title,
                                   $this->ISBN13);
                    break;
                default:
                    $url = sprintf(Book::BOOK_URL_FORMAT_1,
                                   $this->title);
            }

            return strtolower(str_replace(array_keys(Book::BOOK_URL_REPLACEMENT),
                                          array_values(Book::BOOK_URL_REPLACEMENT),
                                          $url));
        }

        /**
         * @return mixed
         */
        public function getCreationDateTime() {
            return $this->creationDateTime;
        }

        /**
         * @param mixed $creationDateTime
         */
        public function setCreationDateTime($creationDateTime) {
            $this->creationDateTime = $creationDateTime;
        }

        /**
         * @return mixed
         */
        public function getTitle() {
            return $this->title;
        }

        /**
         * @param mixed $title
         */
        public function setTitle($title) {
            $this->title = $title;
        }


        /**
         * @return mixed
         */
        public function getSeriesId() {
            return $this->seriesId;
        }

        /**
         * @param mixed $seriesId
         */
        public function setSeriesId($seriesId) {
            $this->seriesId = $seriesId;
        }


        /**
         * @return mixed
         */
        public function getPublisherId() {
            return $this->publisherId;
        }

        /**
         * @param mixed $publisherId
         */
        public function setPublisherId($publisherId) {
            $this->publisherId = $publisherId;
        }

        /**
         * @return mixed
         */
        public function getPublishingYear() {
            return $this->publishingYear;
        }

        /**
         * @param mixed $publishingYear
         */
        public function setPublishingYear($publishingYear) {
            $this->publishingYear = $publishingYear;
        }

        /**
         * @return mixed
         */
        public function getPages() {
            return $this->pages;
        }

        /**
         * @param mixed $pages
         */
        public function setPages($pages) {
            $this->pages = $pages;
        }

        /**
         * @return mixed
         */
        public function getBinding() {
            return $this->binding;
        }

        /**
         * @param mixed $binding
         */
        public function setBinding($binding) {
            $this->binding = $binding;
        }

        /**
         * @return mixed
         */
        public function getCoverId() {
            return $this->coverId;
        }

        /**
         * @param mixed $coverId
         */
        public function setCoverId($coverId) {
            $this->coverId = $coverId;
        }

        /**
         * @return mixed
         */
        public function getDescription() {
            return $this->description;
        }

        /**
         * @param mixed $description
         */
        public function setDescription($description) {
            $this->description = $description;
        }


        /**
         * @return Publisher
         */
        public function getPublisher() {
            return $this->publisher;
        }

        /**
         * @param Publisher $publisher
         */
        public function setPublisher($publisher) {
            $this->publisher = $publisher;
        }

        /**
         * @return Series
         */
        public function getSeries() {
            return $this->series;
        }

        /**
         * @param Series $series
         */
        public function setSeries($series) {
            $this->series = $series;
        }

        /**
         * @return Image
         */
        public function getCover() {
            return $this->cover;
        }

        /**
         * @param Image $cover
         */
        public function setCover($cover) {
            $this->cover = $cover;
        }

        /**
         * @return mixed
         */
        public function getOwnerId() {
            return $this->ownerId;
        }

        /**
         * @param mixed $ownerId
         */
        public function setOwnerId($ownerId) {
            $this->ownerId = $ownerId;
        }

        /**
         * @return array
         */
        public function getAuthors() {
            return $this->authors;
        }

        /**
         * @param array $authors
         */
        public function setAuthors($authors) {
            $this->authors = $authors;
        }

        /**
         * @return array
         */
        public function getGenres() {
            return $this->genres;
        }

        /**
         * @param array $genres
         */
        public function setGenres($genres) {
            $this->genres = $genres;
        }

        /**
         * @return mixed
         */
        public function getRating() {
            return $this->rating;
        }

        /**
         * @param mixed $rating
         */
        public function setRating($rating) {
            $this->rating = $rating;
        }

        /**
         * @return mixed
         */
        public function getSubtitle() {
            return $this->subtitle;
        }

        /**
         * @param mixed $subtitle
         */
        public function setSubtitle($subtitle) {
            $this->subtitle = $subtitle;
        }

        /**
         * @return mixed
         */
        public function getISBN10() {
            return $this->ISBN10;
        }

        /**
         * @param mixed $ISBN10
         */
        public function setISBN10($ISBN10) {
            $this->ISBN10 = $ISBN10;
        }

        /**
         * @return mixed
         */
        public function getISBN13() {
            return $this->ISBN13;
        }

        /**
         * @param mixed $ISBN13
         */
        public function setISBN13($ISBN13) {
            $this->ISBN13 = $ISBN13;
        }

        /**
         * @return mixed
         */
        public function getNotes() {
            return $this->notes;
        }

        /**
         * @param mixed $notes
         */
        public function setNotes($notes) {
            $this->notes = $notes;
        }

        /**
         * @return mixed
         */
        public function getQuantity() {
            return $this->quantity;
        }

        /**
         * @param mixed $quantity
         */
        public function setQuantity($quantity) {
            $this->quantity = $quantity;
        }

        /**
         * @return mixed
         */
        public function getEdition() {
            return $this->edition;
        }

        /**
         * @param mixed $edition
         */
        public function setEdition($edition) {
            $this->edition = $edition;
        }

        /**
         * @return mixed
         */
        public function getPhysicalForm() {
            return $this->physicalForm;
        }

        /**
         * @param mixed $physicalForm
         */
        public function setPhysicalForm($physicalForm) {
            $this->physicalForm = $physicalForm;
        }

        /**
         * @return mixed
         */
        public function getSize() {
            return $this->size;
        }

        /**
         * @param mixed $size
         */
        public function setSize($size) {
            $this->size = $size;
        }

        /**
         * @return mixed
         */
        public function getPrice() {
            return $this->price;
        }

        /**
         * @param mixed $price
         */
        public function setPrice($price) {
            $this->price = $price;
        }

        /**
         * @return mixed
         */
        public function getLanguage() {
            return $this->language;
        }

        /**
         * @param mixed $language
         */
        public function setLanguage($language) {
            $this->language = $language;
        }

        /**
         * @return mixed
         */
        public function getType() {
            return $this->type;
        }

        /**
         * @param mixed $type
         */
        public function setType($type) {
            $this->type = $type;
        }

        /**
         * @return mixed
         */
        public function getUpdateDateTime() {
            return $this->updateDateTime;
        }

        /**
         * @param mixed $updateDateTime
         */
        public function setUpdateDateTime($updateDateTime) {
            $this->updateDateTime = $updateDateTime;
        }

        /**
         * @return Issue
         */
        public function getIssue() {
            return $this->issue;
        }

        /**
         * @param Issue $issue
         */
        public function setIssue($issue) {
            $this->issue = $issue;
        }

        /**
         * @return array
         */
        public function getStores() {
            return $this->stores;
        }

        /**
         * @param array $stores
         */
        public function setStores($stores) {
            $this->stores = $stores;
        }

        /**
         * @return array
         */
        public function getLocations() {
            return $this->locations;
        }

        /**
         * @param array $locations
         */
        public function setLocations($locations) {
            $this->locations = $locations;
        }

        /**
         * @return mixed
         */
        public function getEBookId() {
            return $this->eBookId;
        }

        /**
         * @param mixed $eBookId
         */
        public function setEBookId($eBookId) {
            $this->eBookId = $eBookId;
        }

        /**
         * @return ElectronicBook
         */
        public function getEBook() {
            return $this->eBook;
        }

        /**
         * @param ElectronicBook $eBook
         */
        public function setEBook($eBook) {
            $this->eBook = $eBook;
        }

        /**
         * @return mixed
         */
        public function getMetaTitle() {
            return $this->metaTitle;
        }

        /**
         * @param mixed $metaTitle
         */
        public function setMetaTitle($metaTitle) {
            $this->metaTitle = $metaTitle;
        }

        /**
         * @return mixed
         */
        public function getMetaKeywords() {
            return $this->metaKeywords;
        }

        /**
         * @param mixed $metaKeywords
         */
        public function setMetaKeywords($metaKeywords) {
            $this->metaKeywords = $metaKeywords;
        }

        /**
         * @return mixed
         */
        public function getMetaDescription() {
            return $this->metaDescription;
        }

        /**
         * @param mixed $metaDescription
         */
        public function setMetaDescription($metaDescription) {
            $this->metaDescription = $metaDescription;
        }

        /**
         * @return array
         */
        public function getReviews() {
            return $this->reviews;
        }

        /**
         * @param array $reviews
         */
        public function setReviews($reviews) {
            $this->reviews = $reviews;
        }

        /**
         * @return int
         */
        public function getBookRatingVotesNumber() {
            return $this->bookRatingVotesNumber;
        }

        /**
         * @param int $bookRatingVotesNumber
         */
        public function setBookRatingVotesNumber($bookRatingVotesNumber) {
            $this->bookRatingVotesNumber = $bookRatingVotesNumber;
        }

        /**
         * @return mixed
         */
        public function getUrl() {
            return $this->url;
        }

        /**
         * @param mixed $url
         */
        public function setUrl($url) {
            $this->url = $url;
        }

        /**
         * @return array
         */
        public function getImages() {
            return $this->images;
        }

        /**
         * @param array $images
         */
        public function setImages($images) {
            $this->images = $images;
        }

        /**
         * @return array
         */
        public function getTags() {
            return $this->tags;
        }

        /**
         * @param array $tags
         */
        public function setTags($tags) {
            $this->tags = $tags;
        }

        /**
         * @return array
         */
        public function getLogs() {
            return $this->logs;
        }

        /**
         * @param array $logs
         */
        public function setLogs($logs) {
            $this->logs = $logs;
        }

        /**
         * @return DatabaseField[]
         */
        public static function getCustomFields() {
            return self::$CUSTOM_FIELDS;
        }

        /**
         * @param DatabaseField[] $customFields
         */
        public static function setCustomFields($customFields) {
            self::$CUSTOM_FIELDS = $customFields;
        }

        /**
         * @param $fieldName
         * @return DatabaseField|null
         */
        public static function getCustomField($fieldName) {
            foreach (self::$CUSTOM_FIELDS as $customField) {
                if (strcmp($fieldName,
                           $customField->getName()) === 0
                ) {
                    return $customField;
                }
            }

            return null;
        }

        /**
         * @return array
         */
        public function getBookCopies() {
            return $this->bookCopies;
        }

        /**
         * @param array $bookCopies
         */
        public function setBookCopies($bookCopies) {
            $this->bookCopies = $bookCopies;
        }

        /**
         * @return mixed
         */
        public function getIssuedQuantity() {
            return $this->issuedQuantity;
        }

        /**
         * @param mixed $issuedQuantity
         */
        public function setIssuedQuantity($issuedQuantity) {
            $this->issuedQuantity = $issuedQuantity;
        }
    }