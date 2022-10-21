<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util\CSV;

    use KAASoft\Util\ValidationHelper;

    /**
     * Class CSVReader
     * @package KAASoft\Util\CSV
     */
    class CSVReader {

        private $filePointer;
        private $parseHeader;
        private $header;
        private $delimiterSymbol;
        private $length;
        private $enclosureSymbol;
        private $escapeSymbol;
        private $currentLine;
        private $lineNumber = 0;

        /**
         * CSVReader constructor.
         * @param string $fileName
         * @param bool   $parseHeader
         * @param string $delimiter
         * @param int    $length
         * @param string $enclosure
         * @param string $escape
         */
        function __construct($fileName, $parseHeader = false, $delimiter = ",", $length = 10000, $enclosure = '"', $escape = "\\") {
            $this->filePointer = fopen($fileName,
                                       "r");
            $this->parseHeader = $parseHeader;
            $this->length = $length;
            $this->delimiterSymbol = strlen($delimiter) === 1 ? $delimiter : strlen($delimiter) > 1 ? substr($delimiter,
                                                                                                             0,
                                                                                                             1) : ',';
            $this->enclosureSymbol = strlen($enclosure) === 1 ? $enclosure : strlen($enclosure) > 1 ? substr($enclosure,
                                                                                                             0,
                                                                                                             1) : '"';
            $this->escapeSymbol = strlen($escape) === 1 ? $escape : strlen($escape) > 1 ? substr($escape,
                                                                                                 0,
                                                                                                 1) : "\\";
            // fix for MAC
            ini_set('auto_detect_line_endings',
                    true);

            if ($this->parseHeader) {
                //setlocale(LC_ALL, 'en_US.UTF-8');
                $headerString = fgets($this->filePointer);
                // fix for BOM
                $headerString = $this->removeBOM($headerString);

                $this->header = str_getcsv($headerString,
                                        $this->delimiterSymbol,
                                        $this->enclosureSymbol,
                                        $this->escapeSymbol);
                // utf-8 fix
                if (isset( $this->header[0] )) {
                    $this->header[0] = preg_replace('/\x{FEFF}/u',
                                                    "",
                                                    $this->header[0]);
                }
                $this->lineNumber++;
            }
        }

        /**
         *
         */
        function __destruct() {
            if ($this->filePointer) {
                fclose($this->filePointer);
            }
        }

        /**
         * Removing BOM from string
         * @param string $str - source string
         * @return string $str - string without BOM
         */
        function removeBOM($str="") {
            if(substr($str, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
                $str = substr($str, 3);
            }
            return $str;
        }

        /**
         * @param int $maxLines
         *            if $maxLines is set to 0, then get all the data
         * @return array
         */
        function read($maxLines = 0) {
            $data = [];

            if ($maxLines > 0) {
                $lineCount = 0;
            }
            else {
                $lineCount = -1;
            } // so loop limit is ignored

            while ($lineCount < $maxLines && ( $row = fgetcsv($this->filePointer,
                                                              $this->length,
                                                              $this->delimiterSymbol) ) !== false) {
                if ($this->parseHeader) {
                    $newRow = [];
                    foreach ($this->header as $i => $columnName) {
                        $newRow[$columnName] = $row[$i];
                    }
                    $data[] = $newRow;
                    $this->currentLine = $newRow;
                }
                else {
                    $data[] = $row;
                    $this->currentLine = $row;
                }

                $this->lineNumber++;

                if ($maxLines > 0) {
                    $lineCount++;
                }
            }

            return $data;
        }

        /**
         * @return array|bool|null
         */
        function readLine() {
            $row = fgetcsv($this->filePointer,
                           $this->length,
                           $this->delimiterSymbol);

            if ($row === false) {
                return false; // end of file or error
            }
            if ($row === null || ( count($row) === 1 and ValidationHelper::isEmpty($row[0]) )) {
                $this->currentLine = null;

                return null;
            }


            if ($this->parseHeader) {
                $newRow = [];
                foreach ($this->header as $i => $columnName) {
                    $newRow[$columnName] = $row[$i];
                }
                $row = $newRow;
            }
            $this->currentLine = $row;
            $this->lineNumber++;

            return $row;
        }

        /**
         * @return array
         */
        public function getHeader() {
            return $this->header;
        }

        /**
         * @return array|null
         */
        public function getCurrentLine() {
            return $this->currentLine;
        }

        /**
         * @return int
         */
        public function getLineNumber() {
            return $this->lineNumber;
        }
    }

    ?>