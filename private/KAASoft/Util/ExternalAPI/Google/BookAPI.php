<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util\ExternalAPI\Google;


    use KAASoft\Controller\Controller;
    use KAASoft\Environment\GoogleSettings;
    use KAASoft\Util\BookSearcher;
    use KAASoft\Util\HTTP\HttpClient;

    /**
     * Class BookAPI
     * @package KAASoft\Util\ExternalAPI\Google
     */
    class BookAPI implements BookSearcher {
        const BOOK_SEARCH_QUERY_BASE = "https://www.googleapis.com/books/v1/volumes";
        const ISBN_SEARCH_PREFIX     = "isbn:";

        /**
         * @var GoogleSettings
         */
        private $settings;

        /**
         * @var HttpClient
         */
        private $httpClient;

        /**
         * BookAPI constructor.
         */
        public function __construct() {
            $settings = new GoogleSettings();
            $settings->loadSettings();
            $this->settings = $settings;

            $this->initEngine();
        }

        protected function initEngine() {
            $this->httpClient = new HttpClient();
        }

        protected function buildGetBookUrl($googleBookId) {
            $paramArray = [ "key" => $this->settings->getApiKey() ];

            return BookAPI::BOOK_SEARCH_QUERY_BASE . HttpClient::HTTP_PATH_SEPARATOR . $googleBookId . HttpClient::HTTP_QUERY_PREFIX . http_build_query($paramArray);
        }

        protected function buildSearchUrl($searchText, $maxResults = null) {
            $paramArray = [ "key"        => $this->settings->getApiKey(),
                            "country"    => $this->settings->getCountry(),
                            "q"          => $searchText,
                            "maxResults" => $maxResults === null ? $this->settings->getMaxSearchResults() : $maxResults ];

            return BookAPI::BOOK_SEARCH_QUERY_BASE . HttpClient::HTTP_QUERY_PREFIX . http_build_query($paramArray);

        }

        /**
         * @param     $searchText string
         * @param int $maxResults
         * @return string JSON(book list) or error message
         */
        public function searchBook($searchText, $maxResults = 5) {
            $url = $this->buildSearchUrl($searchText,
                                         $maxResults);
            if ($this->httpClient->fetch($url)) {
                $results = $this->httpClient->getResults();

                return $results;
            }
            else {
                return json_encode([ Controller::AJAX_PARAM_NAME_ERROR => $this->httpClient->getError() ]);
            }
        }

        /**
         * @param     $searchText
         * @param int $maxResults
         * @return string
         */
        public function searchBookByIsbn($searchText, $maxResults = 5) {
            return $this->searchBook(BookAPI::ISBN_SEARCH_PREFIX . $searchText,
                                     $maxResults);
        }

        /**
         * @param $googleBookId
         * @return string JSON(specific book) or error message
         */
        public function getBook($googleBookId) {
            $url = $this->buildGetBookUrl($googleBookId);
            if ($this->httpClient->fetch($url)) {
                $results = $this->httpClient->getResults();

                return $results;
            }
            else {
                return json_encode([ Controller::AJAX_PARAM_NAME_ERROR => $this->httpClient->getError() ]);
            }
        }

        /**
         * @return mixed
         */
        public function getHttpClient() {
            return $this->httpClient;
        }

        /**
         * @param mixed $httpClient
         */
        public function setHttpClient($httpClient) {
            $this->httpClient = $httpClient;
        }
    }