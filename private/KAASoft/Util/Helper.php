<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    use DateTime;
    use DateTimeZone;
    use Exception;
    use KAASoft\Controller\Controller;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Environment\Config;
    use KAASoft\Environment\RouteController;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\Session;
    use Throwable;

    /**
     * Class Helper
     * @package KAASoft\Util
     */
    class Helper {
        /******* CONSTANTS ***********************/
        const DATABASE_DATE_TIME_FORMAT         = "Y-m-d H:i:s";
        const DATABASE_DATE_FORMAT              = "Y-m-d";
        const DATABASE_YEAR_MONTH_FORMAT        = "Y-m";
        const DATABASE_YEAR_FORMAT              = "Y";
        const HTML_NEW_LINE                     = "<br/>";
        const NEW_LINE                          = "\r\n";
        const RECOVERY_PASSWORD_HASH_VALID_TIME = 60 * 60 * 12; //12 hours
        /******* COMMON METHODS *************/
        /**
         *
         */
        function printDiagnoseInfo() {
            echo '<br/>';
            echo "PHP version: " . phpversion();
            echo '<br/>';
            echo "DOC_ROOT: " . FileHelper::getSiteRoot();
            echo '<br/>';
        }

        /**
         * @param $json
         * @return string
         */
        public static function prettyPrintJSON($json) {
            $result = "";
            $level = 0;
            $in_quotes = false;
            $in_escape = false;
            $ends_line_level = null;
            $json_length = strlen($json);

            for ($i = 0; $i < $json_length; $i++) {
                $char = $json[$i];
                $new_line_level = null;
                $post = "";
                if ($ends_line_level !== null) {
                    $new_line_level = $ends_line_level;
                    $ends_line_level = null;
                }
                if ($in_escape) {
                    $in_escape = false;
                }
                else {
                    if ($char === '"') {
                        $in_quotes = !$in_quotes;
                    }
                    else {
                        if (!$in_quotes) {
                            switch ($char) {
                                case '}':
                                case ']':
                                    $level--;
                                    $ends_line_level = null;
                                    $new_line_level = $level;
                                    break;
                                case '{':
                                case '[':
                                    $level++;
                                    $ends_line_level = $level;
                                    break;
                                case ',':
                                    $ends_line_level = $level;
                                    break;

                                case ':':
                                    $post = " ";
                                    break;

                                case " ":
                                case "\t":
                                case "\n":
                                case "\r":
                                    $char = "";
                                    $ends_line_level = $new_line_level;
                                    $new_line_level = null;
                                    break;
                            }
                        }
                        else {
                            if ($char === '\\') {
                                $in_escape = true;
                            }
                        }
                    }
                }
                if ($new_line_level !== null) {
                    $result .= "\n" . str_repeat("\t",
                                                 $new_line_level);
                }
                $result .= $char . $post;
            }

            return $result;
        }

        public static function printLineSeparator() {
            echo Helper::HTML_NEW_LINE . "<hr/>";
        }

        /**
         * @param        $array
         * @param string $title
         */
        public static function printArray($array, $title = "") {
            if (!empty( $title )) {
                Helper::printHtmlLine($title);
            }
            Helper::printObject($array);
        }

        /**
         * @param $string
         */
        public static function printHtmlLine($string) {
            echo $string . Helper::HTML_NEW_LINE;
        }

        /**
         * @param $string
         */
        public static function printLine($string) {
            echo $string . Helper::NEW_LINE;
        }

        /**
         * @param $object
         */
        public static function printObject($object) {
            echo "<pre>";
            echo print_r($object);
            echo "</pre>";
        }

        /**
         * @param Throwable $e
         * @param string    $message
         */
        public static function printException(Throwable $e, $message = "") {
            Helper::printString("<!DOCTYPE html>");
            Helper::printHtmlLine("<html>");
            Helper::printHtmlLine("<header>");
            Helper::printHtmlLine('<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>');
            Helper::printHtmlLine("</header>");
            Helper::printHtmlLine(utf8_encode($message));
            Helper::printHtmlLine("<hr/>");
            Helper::printString("<table border='0'>");
            Helper::printTableRow([ _("Message:"),
                                    $e->getMessage() ]);
            Helper::printTableRow([ _("Code:"),
                                    $e->getCode() ]);
            Helper::printTableRow([ _("File:"),
                                    utf8_encode($e->getFile()) ]);
            Helper::printTableRow([ _("Line:"),
                                    $e->getLine() ]);
            Helper::printTableRow([ _("Trace:"),
                                    utf8_encode(Helper::getExceptionTraceAsHtmlString($e)) ]);
            Helper::printString("</table>");
            Helper::printHtmlLine("<hr/>");
            Helper::printHtmlLine("<html>");
        }

        /**
         * @param Throwable $e
         * @param string    $message
         * @return string
         */
        public static function printExceptionAsHtmlString(Throwable $e, $message = "") {
            $exceptionString = utf8_encode($message);

            $exceptionString .= "<table border='0'>";
            $exceptionString .= Helper::printTableRowAsString([ _("Message:"),
                                                                $e->getMessage() ]);
            $exceptionString .= Helper::printTableRowAsString([ _("Code:"),
                                                                $e->getCode() ]);
            $exceptionString .= Helper::printTableRowAsString([ _("File:"),
                                                                utf8_encode($e->getFile()) ]);
            $exceptionString .= Helper::printTableRowAsString([ _("Line:"),
                                                                $e->getLine() ]);
            $exceptionString .= Helper::printTableRowAsString([ _("Trace:"),
                                                                utf8_encode(Helper::getExceptionTraceAsHtmlString($e)) ]);
            $exceptionString .= "</table>";

            return $exceptionString;
        }

        /**
         * @param Throwable $e
         * @return string
         */
        public static function printExceptionAsString(Throwable $e) {
            $exceptionString = Helper::NEW_LINE;

            $exceptionString .= "Message:" . $e->getMessage() . Helper::NEW_LINE;
            $exceptionString .= "Code:   " . $e->getCode() . Helper::NEW_LINE;
            $exceptionString .= "File:   " . utf8_encode($e->getFile()) . Helper::NEW_LINE;
            $exceptionString .= "Line:   " . $e->getLine() . Helper::NEW_LINE;
            $exceptionString .= "Trace:  " . utf8_encode(Helper::getExceptionTraceAsString($e)) . Helper::NEW_LINE;

            return $exceptionString;
        }

        /**
         * @param $string
         */
        public static function printString($string) {
            echo $string;
        }

        /**
         * @param $object
         */
        public static function printAsJSON($object) {
            Helper::printString(json_encode($object));
        }

        /**
         * @param int $code
         */
        public static function setResponseCode($code = 200) {
            http_response_code($code);
        }

        /**
         * @param string $cacheControlOption
         */
        public static function setCacheControl($cacheControlOption = "no-cache") {
            header("Cache-Control: " . $cacheControlOption);
        }

        /**
         * @param $newLocation
         */
        public static function redirectTo($newLocation) {
            header("Location: " . $newLocation);
            exit;
        }

        public static function setHtmlContentType() {
            self::setContentType("text/html");
        }

        public static function setJsonContentType() {
            self::setContentType("application/json");
        }

        public static function setEventStreamContentType() {
            self::setContentType("text/event-stream");
        }

        /**
         * @param string $contentType : "text/html", "application/json", etc.
         * @param string $charset
         * @param bool   $override
         * @param int    $responseCode
         */
        public static function setContentType($contentType, $charset = "utf-8", $override = true, $responseCode = 200) {
            header("Content-Type: " . $contentType . "; charset=" . $charset,
                   $override,
                   $responseCode);
        }

        /**
         * @param $array
         * @return string
         */
        public static function arrayToString($array) {
            $resultString = "";
            $array_count = count($array);
            for ($i = 0; $i < $array_count; $i++) {
                $resultString .= $array[$i];
                if ($i < $array_count - 1) {
                    $resultString .= ", ";
                }
            }

            return $resultString;
        }

        /**
         * @param        $var
         * @param bool   $scope
         * @param string $prefix
         * @param string $suffix
         * @return bool|int|string
         */
        public static function getVariableName(&$var, $scope = false, $prefix = 'UNIQUE', $suffix = 'VARIABLE') {
            if ($scope) {
                $values = $scope;
            }
            else {
                $values = $GLOBALS;
            }
            $old = $var;
            $var = $new = $prefix . rand() . $suffix;
            $varName = false;
            foreach ($values as $key => $val) {
                if ($val === $new) {
                    $varName = $key;
                }
            }
            $var = $old;

            return $varName;
        }

        /**
         * @param int $length
         * @return string
         */
        public static function generateRandomPassword($length = 8) {
            $chars = "abcdefghijklmnopqrstuvwxyz!@#$%^&*()_+=<>?/ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $count = mb_strlen($chars);

            for ($i = 0, $result = ''; $i < $length; $i++) {
                $index = rand(0,
                              $count - 1);
                $result .= mb_substr($chars,
                                     $index,
                                     1);
            }

            return $result;
        }

        /**
         * @param $password
         * @return string
         */
        public static function encryptPassword($password) {
            $hashFormat = "$2y$10$";                // Tells PHP to use Blowfish with a "cost" of 10
            $saltLength = 22;                       // Blowfish salts should be 22-characters or more
            $salt = Helper::generatePasswordSalt($saltLength);
            $formatAndSalt = $hashFormat . $salt;
            $hash = crypt($password,
                          $formatAndSalt);

            return $hash;
        }

        /**
         * @param $length
         * @return string
         */
        public static function generatePasswordSalt($length) {
            // Not 100% unique, not 100% random, but good enough for a salt
            // MD5 returns 32 characters
            $uniqueRandomString = md5(uniqid(mt_rand(),
                                             true));

            // Valid characters for a salt are [a-zA-Z0-9./]
            $base64String = base64_encode($uniqueRandomString);

            // But not '+' which is valid in base64 encoding
            $modifiedBase64String = str_replace('+',
                                                '.',
                                                $base64String);

            // Truncate string to the correct length
            $salt = substr($modifiedBase64String,
                           0,
                           $length);

            return $salt;
        }

        /**
         * @param $password
         * @param $existingHash
         * @return bool
         */
        public static function checkPassword($password, $existingHash) {
            // existing hash contains format and salt at start
            $hash = crypt($password,
                          $existingHash);
            //print_line($password."___".$existing_hash);
            //print_line($hash."___".$existing_hash);
            if ($hash === $existingHash) {
                return true;
            }
            else {
                return false;
            }
        }

        /**
         * @param string $htmlString
         * @return mixed|string
         */
        public static function brToNewLine($htmlString = "") {
            if ($htmlString === null) {
                return null;
            }
            $htmlString = preg_replace('#<br[^>]*>#si',
                                       "\n",
                                       $htmlString);
            $htmlString = trim($htmlString);

            return $htmlString;
        }

        /**
         * @param string $string
         * @return mixed|string
         */
        public static function newLineToBr($string = "") {
            if ($string === null) {
                return null;
            }
            $string = preg_replace('#\\n\\r#si',
                                   Helper::HTML_NEW_LINE,
                                   $string);
            $string = preg_replace('#\\r\\n#si',
                                   Helper::HTML_NEW_LINE,
                                   $string);
            $string = preg_replace('#\\n#si',
                                   Helper::HTML_NEW_LINE,
                                   $string);
            $string = preg_replace('#\\r#si',
                                   Helper::HTML_NEW_LINE,
                                   $string);
            $string = trim($string);

            return $string;

        }

        /**
         * @param array $array
         */
        private static function printTableRow(array $array) {
            Helper::printString("<tr>");
            foreach ($array as $value) {
                Helper::printString("<td>");
                Helper::printString($value);
                Helper::printString("</td>");
            }

            Helper::printString("</tr>");
        }

        /**
         * @param array $array
         * @return string
         */
        private static function printTableRowAsString(array $array) {
            $tableRow = "<tr>";
            foreach ($array as $value) {
                $tableRow .= "<td>";
                $tableRow .= $value;
                $tableRow .= "</td>";
            }

            $tableRow .= "</tr>";

            return $tableRow;
        }

        /**
         * @param Throwable $e
         * @return string
         */
        private static function getExceptionTraceAsHtmlString(Throwable $e) {
            $trace = "";
            $index = 0;
            foreach ($e->getTrace() as $traceLine) {
                $trace .= "#" . $index . " " . $traceLine["file"] . "(" . $traceLine["line"] . "): " . $traceLine["class"] . $traceLine["type"] . $traceLine["function"];
                if (count($e->getTrace()) - 1 > $index) {
                    $trace .= Helper::HTML_NEW_LINE;
                }
                $index++;
            }

            return $trace;
        }

        /**
         * @param Throwable $e
         * @return string
         */
        private static function getExceptionTraceAsString(Throwable $e) {
            $trace = "";
            $index = 0;
            foreach ($e->getTrace() as $traceLine) {
                $trace .= "#" . $index . " " . $traceLine["file"] . "(" . $traceLine["line"] . "): " . $traceLine["class"] . $traceLine["type"] . $traceLine["function"];
                if (count($e->getTrace()) - 1 > $index) {
                    $trace .= Helper::NEW_LINE;
                }
                $index++;
            }

            return $trace;
        }

        /**
         * @param null   $time
         * @param string $format
         * @return bool|string
         */
        public static function getDateTimeString($time = null, $format = Helper::DATABASE_DATE_TIME_FORMAT) {
            return date($format,
                        $time == null ? time() : $time);
        }

        /**
         * @param string $birthDate
         * @param string $timeZone
         * @param string $dateFormat
         * @return int|null
         */
        public static function getAge($birthDate, $timeZone = "UTC", $dateFormat = Helper::DATABASE_DATE_FORMAT) {
            if ($birthDate == null) {
                return null;
            }
            $tz = new DateTimeZone($timeZone);
            $age = DateTime::createFromFormat($dateFormat,
                                              $birthDate,
                                              $tz)->diff(new DateTime('now',
                                                                      $tz))->y;

            return $age;
        }

        /**
         * @param  string $dateTimeString
         * @param string  $timeZone
         * @param string  $dateFormat
         * @return DateTime|null
         */
        public static function getDateTimeFromString($dateTimeString, $timeZone = "UTC", $dateFormat = Helper::DATABASE_DATE_FORMAT) {
            $dateTimeString = ValidationHelper::getString($dateTimeString);
            if ($dateTimeString === null) {
                return null;
            }
            $tz = new DateTimeZone($timeZone);

            return DateTime::createFromFormat($dateFormat,
                                              $dateTimeString,
                                              $tz);
        }

        /**
         * @param        $dateTimeString
         * @param        $destinationFormat
         * @param string $sourceFormat
         * @return string
         */
        public static function reformatDateTimeString($dateTimeString, $destinationFormat, $sourceFormat = Helper::DATABASE_DATE_TIME_FORMAT) {
            if ($dateTimeString === '0000-00-00 00:00:00' or $dateTimeString === '0000-00-00') {
                return "";
            }
            if ($dateTimeString === null or empty( $dateTimeString ) or $dateTimeString === true or $dateTimeString === false) {
                return $dateTimeString;
            }
            $dateTime = new DateTime();
            $dateTime = $dateTime->createFromFormat($sourceFormat,
                                                    $dateTimeString);

            return $dateTime->format($destinationFormat);
        }

        public static function reformatDateString($dateTimeString, $destinationFormat, $sourceFormat = Helper::DATABASE_DATE_FORMAT) {
            return Helper::reformatDateTimeString($dateTimeString,
                                                  $destinationFormat,
                                                  $sourceFormat);
        }

        /**
         * @param null   $time
         * @param string $format
         * @return bool|string
         */
        public static function getDateString($time = null, $format = Helper::DATABASE_DATE_FORMAT) {
            return Helper::getDateTimeString($time,
                                             $format);
        }

        /**
         * @return string
         */
        public static function getDefaultTimeZone() {
            return date_default_timezone_get();
        }

        /**
         * @return bool
         */
        public static function isAjaxRequest() {
            return !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        }

        /**
         * @return bool
         */
        public static function isPostRequest() {
            return $_SERVER["REQUEST_METHOD"] == "POST";
        }

        /**
         * @return bool
         */
        public static function isGetRequest() {
            return $_SERVER["REQUEST_METHOD"] == "GET";
        }

        /**
         * @param Throwable|Exception $e
         * @param RouteController     $routeController
         * @param bool                $isOnEpicFailPage
         */
        public static function processFatalException($e, RouteController $routeController, $isOnEpicFailPage = false) {
            $errorMessage = _("Oops!!! Critical error is occurred. See details below:");
            Session::addSessionMessage(Config::DEBUG_MODE ? Helper::printExceptionAsHtmlString($e,
                                                                                               $errorMessage) : $e->getMessage(),
                                       Message::MESSAGE_STATUS_ERROR);

            if (!$isOnEpicFailPage) {
                ControllerBase::getLogger()->error(_("Something very bad is happened."),
                                                   $e);
                $epicFailRoute = null;

                if ($routeController === null) {
                    $epicFailRoute = FileHelper::getSiteRelativeLocation() . "/epicFail";
                }
                else {
                    $epicFailRoute = $routeController->getRouteString(GeneralPublicRoutes::EPIC_FAIL_ROUTE_NAME);
                }
                if (Helper::isAjaxRequest()) {
                    if (Config::DEBUG_MODE) {
                        Helper::printAsJSON([ Controller::AJAX_PARAM_NAME_REDIRECT => $epicFailRoute ]);
                    }
                    else {
                        Helper::printAsJSON([ Controller::AJAX_PARAM_NAME_REDIRECT => $epicFailRoute ]);
                    }
                    die( 1 );
                }
                else {
                    if (Config::DEBUG_MODE) {
                        Helper::printException($e,
                                               $errorMessage);
                        die( 1 );
                    }
                    else {
                        Helper::redirectTo($epicFailRoute);
                    }
                }
            }
            else {
                ControllerBase::getLogger()->error(_("Something terrible is happened."),
                                                   $e);
                $fileName = FileHelper::getDefaultTemplateDirectory() . "admin/epicEpicFail.html";
                Helper::setResponseCode(500);
                if (file_exists($fileName)) {
                    $htmlCode = file_get_contents($fileName);
                }
                else {
                    $htmlCode = Helper::getEpicEpicFailHtml();
                }

                $htmlCode = str_replace("%error_message%",
                                        $e->getMessage(),
                                        $htmlCode);
                if (Helper::isAjaxRequest()) {
                    Helper::printAsJSON([ Controller::AJAX_PARAM_NAME_HTML => $htmlCode ]);

                }
                else {
                    Helper::printString($htmlCode);
                }
                die( 1 );
            }
        }

        private static function getEpicEpicFailHtml() {
            return "<html>
                        <head>
                            <title>Server error</title>
                        </head>
                        <body>
                            <h1>Server error is detected:</h1>
                            <h2>%error_message%</h2>
                            <br/>
                            Please contact site administrator: <strong>library@cms.com</strong>
                        </body>
                    </html>";
        }

        /**
         * @param $sourceString
         * @param $searchString
         * @return bool
         */
        public static function startsWith($sourceString, $searchString) {
            return strpos($sourceString,
                          $searchString) === 0;
        }

        /**
         * @param $sourceString
         * @param $searchString
         * @return bool
         */
        public static function endsWith($sourceString, $searchString) {
            return strrpos($sourceString,
                           $searchString) + strlen($searchString) === strlen($sourceString);
        }


        /**
         * Output message with flushing.
         *
         * @param      $message Message
         */
        public static function outputMessage($message) {
            $class = "default";
            switch ($message->getStatus()) {
                case Message::MESSAGE_STATUS_ERROR:
                    $class = "error";
                    break;
                case Message::MESSAGE_STATUS_INFO:
                    $class = "default";
                    break;
                case Message::MESSAGE_STATUS_WARNING:
                    $class = "warning";
                    break;
                case Message::MESSAGE_STATUS_SUCCESS:
                    $class = "success";
                    break;
            }
            echo "<p class='$class'>" . $message->getMessage() . "</p>";
            Helper::outputBuffer();
        }

        /**
         * Flush output buffer
         */
        public static function outputBuffer() {
            echo( str_repeat(' ',
                             256) );
            if (@ob_get_contents()) {
                @ob_end_flush();
            }
            flush();
        }
    }

    ?>