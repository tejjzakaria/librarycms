<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    /**
     * Class BarCodePrintSettings
     * @package KAASoft\Util
     */
    class BarCodePrintSettings {

        private $labelWidth;
        private $labelHeight;

        private $cornerRadius;

        private $pageMarginTop;
        private $pageMarginBottom;
        private $pageMarginLeft;
        private $pageMarginRight;

        private $gapAcross;
        private $gapDown;

        private $isPrintBookTitle;
        private $isPrintBookCover;
        private $isPrintBookISBN;
        private $isPrintBookPublisher;
        private $isPrintBookGenre;
        private $isPrintBookAuthor;
        private $isPrintBookPrice;

        private $customText;

        private $primaryFontSize;
        private $secondaryFontSize;

        /**
         * BarCodePrintSettings constructor.
         */
        public function __construct() {
        }

        /**
         * @param array $databaseArray
         * @return BarCodePrintSettings
         */
        public static function getObjectInstance(array $databaseArray) {
            $barCodePrintSettings = new BarCodePrintSettings();

            $barCodePrintSettings->setLabelWidth(ValidationHelper::getFloat($databaseArray["labelWidth"]));
            $barCodePrintSettings->setLabelHeight(ValidationHelper::getFloat($databaseArray["labelHeight"]));
            $barCodePrintSettings->setCornerRadius(ValidationHelper::getFloat($databaseArray["cornerRadius"]));
            $barCodePrintSettings->setPageMarginTop(ValidationHelper::getFloat($databaseArray["pageMarginTop"]));
            $barCodePrintSettings->setPageMarginBottom(ValidationHelper::getFloat($databaseArray["pageMarginBottom"]));
            $barCodePrintSettings->setPageMarginLeft(ValidationHelper::getFloat($databaseArray["pageMarginLeft"]));
            $barCodePrintSettings->setPageMarginRight(ValidationHelper::getFloat($databaseArray["pageMarginRight"]));
            $barCodePrintSettings->setGapAcross(ValidationHelper::getFloat($databaseArray["gapAcross"]));
            $barCodePrintSettings->setGapDown(ValidationHelper::getFloat($databaseArray["gapDown"]));

            $barCodePrintSettings->setIsPrintBookTitle(ValidationHelper::getBool($databaseArray["isPrintBookTitle"]));
            $barCodePrintSettings->setIsPrintBookCover(ValidationHelper::getBool($databaseArray["isPrintBookCover"]));
            $barCodePrintSettings->setIsPrintBookISBN(ValidationHelper::getBool($databaseArray["isPrintBookISBN"]));
            $barCodePrintSettings->setIsPrintBookPublisher(ValidationHelper::getBool($databaseArray["isPrintBookPublisher"]));
            $barCodePrintSettings->setIsPrintBookGenre(ValidationHelper::getBool($databaseArray["isPrintBookGenre"]));
            $barCodePrintSettings->setIsPrintBookAuthor(ValidationHelper::getBool($databaseArray["isPrintBookAuthor"]));
            $barCodePrintSettings->setIsPrintBookPrice(ValidationHelper::getBool($databaseArray["isPrintBookPrice"]));

            $barCodePrintSettings->setCustomText(ValidationHelper::getString($databaseArray["customText"]));

            $barCodePrintSettings->setPrimaryFontSize(ValidationHelper::getFloat($databaseArray["primaryFontSize"]));
            $barCodePrintSettings->setSecondaryFontSize(ValidationHelper::getFloat($databaseArray["secondaryFontSize"]));

            return $barCodePrintSettings;
        }

        /**
         * @return mixed
         */
        public function getLabelWidth() {
            return $this->labelWidth;
        }

        /**
         * @param mixed $labelWidth
         */
        public function setLabelWidth($labelWidth) {
            $this->labelWidth = $labelWidth;
        }

        /**
         * @return mixed
         */
        public function getLabelHeight() {
            return $this->labelHeight;
        }

        /**
         * @param mixed $labelHeight
         */
        public function setLabelHeight($labelHeight) {
            $this->labelHeight = $labelHeight;
        }

        /**
         * @return mixed
         */
        public function getCornerRadius() {
            return $this->cornerRadius;
        }

        /**
         * @param mixed $cornerRadius
         */
        public function setCornerRadius($cornerRadius) {
            $this->cornerRadius = $cornerRadius;
        }

        /**
         * @return mixed
         */
        public function getPageMarginTop() {
            return $this->pageMarginTop;
        }

        /**
         * @param mixed $pageMarginTop
         */
        public function setPageMarginTop($pageMarginTop) {
            $this->pageMarginTop = $pageMarginTop;
        }

        /**
         * @return mixed
         */
        public function getPageMarginBottom() {
            return $this->pageMarginBottom;
        }

        /**
         * @param mixed $pageMarginBottom
         */
        public function setPageMarginBottom($pageMarginBottom) {
            $this->pageMarginBottom = $pageMarginBottom;
        }

        /**
         * @return mixed
         */
        public function getPageMarginLeft() {
            return $this->pageMarginLeft;
        }

        /**
         * @param mixed $pageMarginLeft
         */
        public function setPageMarginLeft($pageMarginLeft) {
            $this->pageMarginLeft = $pageMarginLeft;
        }

        /**
         * @return mixed
         */
        public function getPageMarginRight() {
            return $this->pageMarginRight;
        }

        /**
         * @param mixed $pageMarginRight
         */
        public function setPageMarginRight($pageMarginRight) {
            $this->pageMarginRight = $pageMarginRight;
        }

        /**
         * @return mixed
         */
        public function getGapAcross() {
            return $this->gapAcross;
        }

        /**
         * @param mixed $gapAcross
         */
        public function setGapAcross($gapAcross) {
            $this->gapAcross = $gapAcross;
        }

        /**
         * @return mixed
         */
        public function getGapDown() {
            return $this->gapDown;
        }

        /**
         * @param mixed $gapDown
         */
        public function setGapDown($gapDown) {
            $this->gapDown = $gapDown;
        }

        /**
         * @return mixed
         */
        public function isPrintBookTitle() {
            return $this->isPrintBookTitle;
        }

        /**
         * @param mixed $isPrintBookTitle
         */
        public function setIsPrintBookTitle($isPrintBookTitle) {
            $this->isPrintBookTitle = $isPrintBookTitle;
        }

        /**
         * @return mixed
         */
        public function isPrintBookCover() {
            return $this->isPrintBookCover;
        }

        /**
         * @param mixed $isPrintBookCover
         */
        public function setIsPrintBookCover($isPrintBookCover) {
            $this->isPrintBookCover = $isPrintBookCover;
        }

        /**
         * @return mixed
         */
        public function isPrintBookISBN() {
            return $this->isPrintBookISBN;
        }

        /**
         * @param mixed $isPrintBookISBN
         */
        public function setIsPrintBookISBN($isPrintBookISBN) {
            $this->isPrintBookISBN = $isPrintBookISBN;
        }

        /**
         * @return mixed
         */
        public function isPrintBookPublisher() {
            return $this->isPrintBookPublisher;
        }

        /**
         * @param mixed $isPrintBookPublisher
         */
        public function setIsPrintBookPublisher($isPrintBookPublisher) {
            $this->isPrintBookPublisher = $isPrintBookPublisher;
        }

        /**
         * @return mixed
         */
        public function isPrintBookGenre() {
            return $this->isPrintBookGenre;
        }

        /**
         * @param mixed $isPrintBookGenre
         */
        public function setIsPrintBookGenre($isPrintBookGenre) {
            $this->isPrintBookGenre = $isPrintBookGenre;
        }

        /**
         * @return mixed
         */
        public function isPrintBookAuthor() {
            return $this->isPrintBookAuthor;
        }

        /**
         * @param mixed $isPrintBookAuthor
         */
        public function setIsPrintBookAuthor($isPrintBookAuthor) {
            $this->isPrintBookAuthor = $isPrintBookAuthor;
        }

        /**
         * @return mixed
         */
        public function isPrintBookPrice() {
            return $this->isPrintBookPrice;
        }

        /**
         * @param mixed $isPrintBookPrice
         */
        public function setIsPrintBookPrice($isPrintBookPrice) {
            $this->isPrintBookPrice = $isPrintBookPrice;
        }

        /**
         * @return mixed
         */
        public function getCustomText() {
            return $this->customText;
        }

        /**
         * @param mixed $customText
         */
        public function setCustomText($customText) {
            $this->customText = $customText;
        }

        /**
         * @return mixed
         */
        public function getPrimaryFontSize() {
            return $this->primaryFontSize;
        }

        /**
         * @param mixed $primaryFontSize
         */
        public function setPrimaryFontSize($primaryFontSize) {
            $this->primaryFontSize = $primaryFontSize;
        }

        /**
         * @return mixed
         */
        public function getSecondaryFontSize() {
            return $this->secondaryFontSize;
        }

        /**
         * @param mixed $secondaryFontSize
         */
        public function setSecondaryFontSize($secondaryFontSize) {
            $this->secondaryFontSize = $secondaryFontSize;
        }
    }