<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util\HTTP;

    use Exception;
    use RuntimeException;

    /**
     * Class HttpClient
     * @package KAASoft\Util\HTTP
     */
    class HttpClient {

        /**** Constants *****/
        const HTTP_PROTOCOL           = "http";
        const HTTPS_PROTOCOL          = "https";
        const HTTP_QUERY_PREFIX       = "?";
        const HTTP_PATH_SEPARATOR     = "/";
        const HTTP_PROTOCOL_SEPARATOR = "://";
        /**** Private variables ****/
        private $scheme    = HttpClient::HTTP_PROTOCOL; // http or https
        private $host      = "www.php.net"; // host name we are connecting to
        private $port      = 80; // port we are connecting to
        private $proxyHost = ""; // proxy host to use
        private $proxyPort = ""; // proxy port to use
        private $proxyUser = ""; // proxy user to use
        private $proxyPass = ""; // proxy password to use

        private $agent   = "PHP Agent"; // agent we masquerade as
        private $referer = ""; // referer info to pass
        private $cookies = []; // array of cookies to pass
        // $cookies["username"]="joe";
        private $rawHttpHeaders = []; // array of raw headers to send
        // $rawHttpHeaders["Content-type"]="text/html";

        private $maxRedirects        = 5; // http redirection depth maximum. 0 = disallow
        private $lastRedirectAddress = ""; // contains address of last redirected address
        private $offSiteOk           = true; // allows redirection off-site
        private $maxFrames           = 0; // frame content depth maximum. 0 = disallow
        private $expandLinks         = true; // expand links to fully qualified URLs.
        // this only applies to fetchLinks(), submitLinks(), and submitText()
        private $passCookies = true; // pass set cookies back through redirects
        // NOTE: this currently does not respect
        // dates, domains or paths.

        private $userName = ""; // user for http authentication
        private $password = ""; // password for http authentication

        // http accept types
        private $acceptTypes = "image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, */*";

        private $results = ""; // where the content is put

        private $error         = ""; // error messages sent here
        private $responseCode  = ""; // response code returned from server
        private $headers       = []; // headers returned from server sent here
        private $maxDataLength = 500000; // max return data length (body)
        private $readTimeout   = 0; // timeout on read operations, in seconds
        // supported only since PHP 4 Beta 4
        // set to 0 to disallow timeouts
        private $isReadingTimedOut = false; // if a read operation timed out
        private $status            = 0; // http request status

        //private $tempDir = "/tmp"; // temporary directory that the webserver
        // has permission to write to.
        // under Windows, this should be C:\temp

        // send Accept-encoding: gzip?
        private $useGzip = true;

        // file or directory with CA certificates to verify remote host with
        private $CAFile;
        private $CAPath;

        /**** Private variables ****/

        private $maxHeaderLineLength = 4096; // max line length (headers)

        private $defaultHttpMethod   = "GET"; // default http request method
        private $defaultHttpVersion  = "HTTP/1.0"; // default http request version
        private $defaultSubmitMethod = "POST"; // default submit method
        private $defaultSubmitType   = "application/x-www-form-urlencoded"; // default submit type
        private $defaultMimeBoundary = ""; // MIME boundary for multipart/form-data submit type
        private $isRedirectAddress   = false; // will be set if page fetched is a redirect
        private $redirectDepth       = 0; // increments on an http redirect
        private $frameUrls           = []; // frame src urls
        private $frameDepth          = 0; // increments on frame depth

        private $isProxy       = false; // set if using a proxy server
        private $socketTimeout = 30; // timeout for socket connection

        /**************************************************************************************************************/
        //                                    PUBLIC FUNCTIONS
        /**************************************************************************************************************/
        /**
         * fetch the contents of a web page
         * @param $URI - the location of the page to fetch
         * @return bool - true if success ($this->results    the output text from the fetch). false - if error.
         */
        public function fetch($URI) {

            $URI_PARTS = parse_url($URI);
            if (!empty( $URI_PARTS["user"] )) {
                $this->userName = $URI_PARTS["user"];
            }
            if (!empty( $URI_PARTS["pass"] )) {
                $this->password = $URI_PARTS["pass"];
            }
            if (empty( $URI_PARTS["query"] )) {
                $URI_PARTS["query"] = "";
            }
            if (empty( $URI_PARTS["path"] )) {
                $URI_PARTS["path"] = "";
            }

            $filePointer = null;

            switch (strtolower($URI_PARTS["scheme"])) {
                /** @noinspection PhpMissingBreakStatementInspection */
                case HttpClient::HTTPS_PROTOCOL:
                    if (!extension_loaded('openssl')) {
                        throw new RuntimeException(_("openssl extension is required to use HTTPS protocol."));
                    }
                    $this->port = 443;

                case HttpClient::HTTP_PROTOCOL:
                    $this->scheme = strtolower($URI_PARTS["scheme"]);
                    $this->host = $URI_PARTS["host"];
                    if (!empty( $URI_PARTS["port"] )) {
                        $this->port = $URI_PARTS["port"];
                    }
                    if ($this->connect($filePointer)) {
                        if ($this->isProxy) {
                            // using proxy, send entire URI
                            $this->httpRequest($URI,
                                               $filePointer,
                                               $URI,
                                               $this->defaultHttpMethod);
                        }
                        else {
                            $path = $URI_PARTS["path"] . ( $URI_PARTS["query"] ? "?" . $URI_PARTS["query"] : "" );
                            // no proxy, send only the path
                            $this->httpRequest($path,
                                               $filePointer,
                                               $URI,
                                               $this->defaultHttpMethod);
                        }

                        $this->disconnect($filePointer);

                        if ($this->isRedirectAddress) {
                            /* url was redirected, check if we've hit the max depth */
                            if ($this->maxRedirects > $this->redirectDepth) {
                                // only follow redirect if it's on this site, or offsiteok is true
                                if (preg_match("|^https?://" . preg_quote($this->host) . "|i",
                                               $this->isRedirectAddress) || $this->offSiteOk
                                ) {
                                    /* follow the redirect */
                                    $this->redirectDepth++;
                                    $this->lastRedirectAddress = $this->isRedirectAddress;
                                    $this->fetch($this->isRedirectAddress);
                                }
                            }
                        }

                        if ($this->frameDepth < $this->maxFrames && count($this->frameUrls) > 0) {
                            $frameUrls = $this->frameUrls;
                            $this->frameUrls = [];

                            while (list( , $frameUrl ) = each($frameUrls)) {
                                if ($this->frameDepth < $this->maxFrames) {
                                    $this->fetch($frameUrl);
                                    $this->frameDepth++;
                                }
                                else {
                                    break;
                                }
                            }
                        }
                    }
                    else {
                        return false;
                    }

                    return true;
                default:
                    // not a valid protocol
                    $this->error = sprintf(_('Invalid protocol "%s".'),
                                           $URI_PARTS["scheme"]);

                    return false;
            }
        }

        /**
         * submit an http(s) form
         * @param        $URI       - the location to post the data
         * @param string $formVars  - the $formVars to use. format: $formVars["var"] = "val";
         * @param string $formFiles - an array of files to submit. format: $formFiles["var"] = "/dir/filename.ext";
         * @return bool - true if success ($this->results    the text output from the post). false - if error.
         */
        public function submit($URI, $formVars = "", $formFiles = "") {
            unset( $postData );

            $postData = $this->preparePostBody($formVars,
                                               $formFiles);

            $URI_PARTS = parse_url($URI);
            if (!empty( $URI_PARTS["user"] )) {
                $this->userName = $URI_PARTS["user"];
            }
            if (!empty( $URI_PARTS["pass"] )) {
                $this->password = $URI_PARTS["pass"];
            }
            if (empty( $URI_PARTS["query"] )) {
                $URI_PARTS["query"] = "";
            }
            if (empty( $URI_PARTS["path"] )) {
                $URI_PARTS["path"] = "";
            }

            switch (strtolower($URI_PARTS["scheme"])) {
                /** @noinspection PhpMissingBreakStatementInspection */
                case HttpClient::HTTPS_PROTOCOL:
                    if (!extension_loaded('openssl')) {
                        throw new RuntimeException(_("openssl extension is required to use HTTPS protocol."));
                    }
                    $this->port = 443;
                case HttpClient::HTTP_PROTOCOL:
                    $this->scheme = strtolower($URI_PARTS["scheme"]);
                    $this->host = $URI_PARTS["host"];
                    if (!empty( $URI_PARTS["port"] )) {
                        $this->port = $URI_PARTS["port"];
                    }
                    if ($this->connect($fp)) {
                        if ($this->isProxy) {
                            // using proxy, send entire URI
                            $this->httpRequest($URI,
                                               $fp,
                                               $URI,
                                               $this->defaultSubmitMethod,
                                               $this->defaultSubmitType,
                                               $postData);
                        }
                        else {
                            $path = $URI_PARTS["path"] . ( $URI_PARTS["query"] ? "?" . $URI_PARTS["query"] : "" );
                            // no proxy, send only the path
                            $this->httpRequest($path,
                                               $fp,
                                               $URI,
                                               $this->defaultSubmitMethod,
                                               $this->defaultSubmitType,
                                               $postData);
                        }

                        $this->disconnect($fp);

                        if ($this->isRedirectAddress) {
                            /* url was redirected, check if we've hit the max depth */
                            if ($this->maxRedirects > $this->redirectDepth) {
                                if (!preg_match("|^" . $URI_PARTS["scheme"] . "://|",
                                                $this->isRedirectAddress)
                                ) {
                                    $this->isRedirectAddress = $this->expandLinks($this->isRedirectAddress,
                                                                                  $URI_PARTS["scheme"] . "://" . $URI_PARTS["host"]);
                                }

                                // only follow redirect if it's on this site, or offsiteok is true
                                if (preg_match("|^https?://" . preg_quote($this->host) . "|i",
                                               $this->isRedirectAddress) || $this->offSiteOk
                                ) {
                                    /* follow the redirect */
                                    $this->redirectDepth++;
                                    $this->lastRedirectAddress = $this->isRedirectAddress;
                                    if (strpos($this->isRedirectAddress,
                                               "?") > 0
                                    ) {
                                        $this->fetch($this->isRedirectAddress);
                                    } // the redirect has changed the request method from post to get
                                    else {
                                        $this->submit($this->isRedirectAddress,
                                                      $formVars,
                                                      $formFiles);
                                    }
                                }
                            }
                        }

                        if ($this->frameDepth < $this->maxFrames && count($this->frameUrls) > 0) {
                            $frameUrls = $this->frameUrls;
                            $this->frameUrls = [];

                            while (list( , $frameUrl ) = each($frameUrls)) {
                                if ($this->frameDepth < $this->maxFrames) {
                                    $this->fetch($frameUrl);
                                    $this->frameDepth++;
                                }
                                else {
                                    break;
                                }
                            }
                        }

                    }
                    else {
                        return false;
                    }

                    return true;
                    break;
                default:
                    // not a valid protocol
                    $this->error = sprintf(_('Invalid protocol "%s".'),
                                           $URI_PARTS["scheme"]);

                    return false;
                    break;
            }
        }

        /**
         * fetch the links from a web page
         * @param $URI - where you are fetching from
         * @return bool - true if ok ($this->results - an array of the URLs), false - if fail.
         */
        public function fetchLinks($URI) {
            if ($this->fetch($URI) !== false) {
                if ($this->lastRedirectAddress) {
                    $URI = $this->lastRedirectAddress;
                }
                if (is_array($this->results)) {
                    for ($x = 0; $x < count($this->results); $x++) {
                        $this->results[$x] = $this->stripLinks($this->results[$x]);
                    }
                }
                else {
                    $this->results = $this->stripLinks($this->results);
                }

                if ($this->expandLinks) {
                    $this->results = $this->expandLinks($this->results,
                                                        $URI);
                }

                return true;
            }
            else {
                return false;
            }
        }

        /**
         * fetch the form elements from a web page
         * @param $URI - where you are fetching from
         * @return bool - true if ok ($this->results    the resulting html form), false - if fail.
         */
        public function fetchForm($URI) {
            if ($this->fetch($URI) !== false) {

                if (is_array($this->results)) {
                    for ($x = 0; $x < count($this->results); $x++) {
                        $this->results[$x] = $this->stripForm($this->results[$x]);
                    }
                }
                else {
                    $this->results = $this->stripForm($this->results);
                }

                return true;
            }
            else {
                return false;
            }
        }

        /**
         * fetch the text from a web page, stripping the links
         * @param $URI - where you are fetching from
         * @return bool - true if ok ($this->results    the text from the web page), false - if fail.
         */
        public function fetchText($URI) {
            if ($this->fetch($URI) !== false) {
                if (is_array($this->results)) {
                    for ($x = 0; $x < count($this->results); $x++) {
                        $this->results[$x] = $this->stripText($this->results[$x]);
                    }
                }
                else {
                    $this->results = $this->stripText($this->results);
                }

                return true;
            }
            else {
                return false;
            }
        }

        /**
         * grab links from a form submission
         * @param        $URI - where you are submitting from
         * @param string $formVars
         * @param string $formFiles
         * @return bool - true if ok ($this->results    an array of the links from the post), false - if fail.
         */
        public function submitLinks($URI, $formVars = "", $formFiles = "") {
            if ($this->submit($URI,
                              $formVars,
                              $formFiles) !== false
            ) {
                if ($this->lastRedirectAddress) {
                    $URI = $this->lastRedirectAddress;
                }
                if (is_array($this->results)) {
                    for ($x = 0; $x < count($this->results); $x++) {
                        $this->results[$x] = $this->stripLinks($this->results[$x]);
                        if ($this->expandLinks) {
                            $this->results[$x] = $this->expandLinks($this->results[$x],
                                                                    $URI);
                        }
                    }
                }
                else {
                    $this->results = $this->stripLinks($this->results);
                    if ($this->expandLinks) {
                        $this->results = $this->expandLinks($this->results,
                                                            $URI);
                    }
                }

                return true;
            }
            else {
                return false;
            }
        }

        /**
         * grab text from a form submission
         * @param        $URI - where you are submitting from
         * @param string $formVars
         * @param string $formFiles
         * @return bool - true if ok ($this->results    the text from the web page), false - if fail.
         */
        public function submitText($URI, $formVars = "", $formFiles = "") {
            if ($this->submit($URI,
                              $formVars,
                              $formFiles) !== false
            ) {
                if ($this->lastRedirectAddress) {
                    $URI = $this->lastRedirectAddress;
                }
                if (is_array($this->results)) {
                    for ($x = 0; $x < count($this->results); $x++) {
                        $this->results[$x] = $this->stripText($this->results[$x]);
                        if ($this->expandLinks) {
                            $this->results[$x] = $this->expandLinks($this->results[$x],
                                                                    $URI);
                        }
                    }
                }
                else {
                    $this->results = $this->stripText($this->results);
                    if ($this->expandLinks) {
                        $this->results = $this->expandLinks($this->results,
                                                            $URI);
                    }
                }

                return true;
            }
            else {
                return false;
            }
        }

        /**
         * Set the form submission content type to multipart/form-data
         */
        public function setSubmitMultipart() {
            $this->defaultSubmitType = "multipart/form-data";
        }

        /**
         * Set the form submission content type to application/x-www-form-urlencoded
         */
        public function setSubmitNormal() {
            $this->defaultSubmitType = "application/x-www-form-urlencoded";
        }

        /**************************************************************************************************************/
        //                                    PROTECTED FUNCTIONS
        /**************************************************************************************************************/

        /**
         * strip the hyperlinks from an html document
         * @param $document - document to strip.
         * @return array - an array of the links
         */
        protected function stripLinks($document) {
            preg_match_all("'<\s*a\s.*?href\s*=\s*			# find <a href=
						([\"\'])?					# find single or double quote
						(?(1) (.*?)\\1 | ([^\s\>]+))		# if quote found, match up to next matching
													# quote, otherwise match up to next space
						'isx",
                           $document,
                           $links);

            $result = [];

            // catenate the non-empty matches from the conditional subpattern
            foreach ($links[2] as $link) {
                if (!empty( $link )) {
                    $result[] = $link;
                }
            }

            foreach ($links[3] as $link) {
                if (!empty( $link )) {
                    $result[] = $link;
                }
            }

            // return the links
            return $result;
        }

        /**
         * strip the form elements from an html document
         * @param $document - document to strip
         * @return string - an array of the links
         */
        protected function stripForm($document) {
            preg_match_all("'<\/?(FORM|INPUT|SELECT|TEXTAREA|(OPTION))[^<>]*>(?(2)(.*(?=<\/?(option|select)[^<>]*>[\r\n]*)|(?=[\r\n]*))|(?=[\r\n]*))'Usi",
                           $document,
                           $elements);

            // catenate the matches
            $match = implode("\r\n",
                             $elements[0]);

            // return the links
            return $match;
        }

        /**
         * strip the text from an html document
         * @param $document - document to strip
         * @return mixed - the resulting text
         */
        protected function stripText($document) {

            // I didn't use preg eval (//e) since that is only available in PHP 4.0.
            // so, list your entities one by one here. I included some of the
            // more common ones.

            /** @noinspection BadExpressionStatementJS */
            $search = [ "'<script[^>]*?>.*?</script>'si",
                        // strip out javascript
                        "'<[\/\!]*?[^<>]*?>'si",
                        // strip out html tags
                        "'([\r\n])[\s]+'",
                        // strip out white space
                        "'&(quot|#34|#034|#x22);'i",
                        // replace html entities
                        "'&(amp|#38|#038|#x26);'i",
                        // added hexadecimal values
                        "'&(lt|#60|#060|#x3c);'i",
                        "'&(gt|#62|#062|#x3e);'i",
                        "'&(nbsp|#160|#xa0);'i",
                        "'&(iexcl|#161);'i",
                        "'&(cent|#162);'i",
                        "'&(pound|#163);'i",
                        "'&(copy|#169);'i",
                        "'&(reg|#174);'i",
                        "'&(deg|#176);'i",
                        "'&(#39|#039|#x27);'",
                        "'&(euro|#8364);'i",
                        // europe
                        "'&a(uml|UML);'",
                        // german
                        "'&o(uml|UML);'",
                        "'&u(uml|UML);'",
                        "'&A(uml|UML);'",
                        "'&O(uml|UML);'",
                        "'&U(uml|UML);'",
                        "'&szlig;'i", ];
            $replace = [ "",
                         "",
                         "\\1",
                         "\"",
                         "&",
                         "<",
                         ">",
                         " ",
                         chr(161),
                         chr(162),
                         chr(163),
                         chr(169),
                         chr(174),
                         chr(176),
                         chr(39),
                         chr(128),
                         "ä",
                         "ö",
                         "ü",
                         "Ä",
                         "Ö",
                         "Ü",
                         "ß", ];

            $text = preg_replace($search,
                                 $replace,
                                 $document);

            return $text;
        }

        /**
         * expand each link into a fully qualified URL
         * @param $links - the links to qualify
         * @param $URI   -the full URI to get the base from
         * @return mixed - the expanded links
         */
        protected function expandLinks($links, $URI) {
            preg_match("/^[^\?]+/",
                       $URI,
                       $match);

            $match = preg_replace("|/[^\/\.]+\.[^\/\.]+$|",
                                  "",
                                  $match[0]);
            $match = preg_replace("|/$|",
                                  "",
                                  $match);
            $urlParts = parse_url($match);
            $siteRoot = $urlParts["scheme"] . "://" . $urlParts["host"];

            $search = [ "|^http[s]?://" . preg_quote($this->host) . "|i",
                        "|^(\/)|i",
                        "|^(?!http[s]?://)(?!mailto:)|i",
                        "|/\./|",
                        "|/[^\/]+/\.\./|" ];

            $replace = [ "",
                         $siteRoot . "/",
                         $match . "/",
                         "/",
                         "/" ];

            $expandedLinks = preg_replace($search,
                                          $replace,
                                          $links);

            return $expandedLinks;
        }

        /**
         * go get the http(s) data from the server
         * @param        $url         - the url to fetch
         * @param        $filePointer - the current open file pointer
         * @param        $URI         - the full URI
         * @param        $httpMethod
         * @param string $contentType
         * @param string $body        - body contents to send if any (POST)
         * @return bool - true if success, false - if fail.
         */
        protected function httpRequest($url, $filePointer, $URI, $httpMethod, $contentType = "", $body = "") {
            $cookieHeaders = "";
            if ($this->passCookies && $this->isRedirectAddress) {
                $this->setCookies();
            }

            $URI_PARTS = parse_url($URI);
            if (empty( $url )) {
                $url = "/";
            }
            $headers = $httpMethod . " " . $url . " " . $this->defaultHttpVersion . "\r\n";
            if (!empty( $this->host ) && !isset( $this->rawHttpHeaders['Host'] )) {
                $headers .= "Host: " . $this->host;
                if (!empty( $this->port ) && $this->port != '80') {
                    $headers .= ":" . $this->port;
                }
                $headers .= "\r\n";
            }
            if (!empty( $this->agent )) {
                $headers .= "User-Agent: " . $this->agent . "\r\n";
            }
            if (!empty( $this->acceptTypes )) {
                $headers .= "Accept: " . $this->acceptTypes . "\r\n";
            }
            if ($this->useGzip) {
                // make sure PHP was built with --with-zlib
                // and we can handle gzipp'ed data
                if (function_exists('gzinflate')) {
                    $headers .= "Accept-encoding: gzip\r\n";
                }
                else {
                    throw  new RuntimeException(_("use_gzip is on, but PHP was built without zlib support. Requesting file(s) without gzip encoding."));
                }
            }
            if (!empty( $this->referer )) {
                $headers .= "Referer: " . $this->referer . "\r\n";
            }
            if (!empty( $this->cookies )) {
                if (!is_array($this->cookies)) {
                    $this->cookies = (array)$this->cookies;
                }

                reset($this->cookies);
                if (count($this->cookies) > 0) {
                    $cookieHeaders .= 'Cookie: ';
                    foreach ($this->cookies as $cookieKey => $cookieVal) {
                        $cookieHeaders .= $cookieKey . "=" . $cookieVal . "; ";
                    }
                    $headers .= substr($cookieHeaders,
                                       0,
                                       -2) . "\r\n";
                }
            }
            if (!empty( $this->rawHttpHeaders )) {
                if (!is_array($this->rawHttpHeaders)) {
                    $this->rawHttpHeaders = (array)$this->rawHttpHeaders;
                }
                foreach ($this->rawHttpHeaders as $headerKey => $headerVal) {
                    $headers .= $headerKey . ": " . $headerVal . "\r\n";
                }
            }
            if (!empty( $contentType )) {
                $headers .= "Content-type: $contentType";
                if ($contentType == "multipart/form-data") {
                    $headers .= "; boundary=" . $this->defaultMimeBoundary;
                }
                $headers .= "\r\n";
            }
            if (!empty( $body )) {
                $headers .= "Content-length: " . strlen($body) . "\r\n";
            }
            if (!empty( $this->userName ) || !empty( $this->password )) {
                $headers .= "Authorization: Basic " . base64_encode($this->userName . ":" . $this->password) . "\r\n";
            }

            //add proxy auth headers
            if (!empty( $this->proxyUser )) {
                $headers .= 'Proxy-Authorization: ' . 'Basic ' . base64_encode($this->proxyUser . ':' . $this->proxyPass) . "\r\n";
            }


            $headers .= "\r\n";

            // set the read timeout if needed
            if ($this->readTimeout > 0) {
                socket_set_timeout($filePointer,
                                   $this->readTimeout);
            }
            $this->isReadingTimedOut = false;

            fwrite($filePointer,
                   $headers . $body,
                   strlen($headers . $body));

            $this->isRedirectAddress = false;
            unset( $this->headers );

            // content was returned gzip encoded?
            $is_gzipped = false;

            while ($currentHeader = fgets($filePointer,
                                          $this->maxHeaderLineLength)) {
                if ($this->readTimeout > 0 && $this->isTimeout($filePointer)) {
                    $this->status = -100;

                    return false;
                }

                if ($currentHeader == "\r\n") {
                    break;
                }

                // if a header begins with Location: or URI:, set the redirect
                if (preg_match("/^(Location:|URI:)/i",
                               $currentHeader)) {
                    // get URL portion of the redirect
                    preg_match("/^(Location:|URI:)[ ]+(.*)/i",
                               chop($currentHeader),
                               $matches);
                    // look for :// in the Location header to see if hostname is included
                    if (!preg_match("|\:\/\/|",
                                    $matches[2])
                    ) {
                        // no host in the path, so prepend
                        $this->isRedirectAddress = $URI_PARTS["scheme"] . "://" . $this->host . ":" . $this->port;
                        // eliminate double slash
                        if (!preg_match("|^/|",
                                        $matches[2])
                        ) {
                            $this->isRedirectAddress .= "/" . $matches[2];
                        }
                        else {
                            $this->isRedirectAddress .= $matches[2];
                        }
                    }
                    else {
                        $this->isRedirectAddress = $matches[2];
                    }
                }

                if (preg_match("|^HTTP/|",
                               $currentHeader)) {
                    if (preg_match("|^HTTP/[^\s]*\s(.*?)\s|",
                                   $currentHeader,
                                   $status)) {
                        $this->status = $status[1];
                    }
                    $this->responseCode = $currentHeader;
                }

                if (preg_match("/Content-Encoding: gzip/",
                               $currentHeader)) {
                    $is_gzipped = true;
                }

                $this->headers[] = $currentHeader;
            }

            $results = "";
            do {
                $_data = fread($filePointer,
                               $this->maxDataLength);
                if (strlen($_data) == 0) {
                    break;
                }
                $results .= $_data;
            } while (true);

            // gunzip
            if ($is_gzipped) {
                // per http://www.php.net/manual/en/function.gzencode.php
                $results = substr($results,
                                  10);
                $results = gzinflate($results);
            }

            if ($this->readTimeout > 0 && $this->isTimeout($filePointer)) {
                $this->status = -100;

                return false;
            }

            // check if there is a a redirect meta tag

            if (preg_match("'<meta[\s]*http-equiv[^>]*?content[\s]*=[\s]*[\"\']?\d+;[\s]*URL[\s]*=[\s]*([^\"\']*?)[\"\']?>'i",
                           $results,
                           $match)) {
                $this->isRedirectAddress = $this->expandLinks($match[1],
                                                              $URI);
            }

            // have we hit our frame depth and is there frame src to fetch?
            if (( $this->frameDepth < $this->maxFrames ) && preg_match_all("'<frame\s+.*src[\s]*=[\'\"]?([^\'\"\>]+)'i",
                                                                           $results,
                                                                           $match)
            ) {
                $this->results[] = $results;
                for ($x = 0; $x < count($match[1]); $x++) {
                    $this->frameUrls[] = $this->expandLinks($match[1][$x],
                                                            $URI_PARTS["scheme"] . "://" . $this->host);
                }
            } // have we already fetched framed content?
            elseif (is_array($this->results)) {
                $this->results[] = $results;
            }
            // no framed content
            else {
                $this->results = $results;
            }

            return true;
        }

        /**
         * set cookies for a redirection
         * @return $this
         */
        protected function setCookies() {
            for ($x = 0; $x < count($this->headers); $x++) {
                if (preg_match('/^set-cookie:[\s]+([^=]+)=([^;]+)/i',
                               $this->headers[$x],
                               $match)) {
                    $this->cookies[$match[1]] = $match[2];
                }
            }

            return $this;
        }

        /**
         * checks whether timeout has occurred
         * @param $filePointer
         * @return bool
         */
        protected function isTimeout($filePointer) {
            if ($this->readTimeout > 0) {
                $filePointerStatus = socket_get_status($filePointer);
                if ($filePointerStatus["timed_out"]) {
                    $this->isReadingTimedOut = true;

                    return true;
                }
            }

            return false;
        }

        /**
         * @param $filePointer - file pointer
         * @return bool - true if success ($filePointer has socket pointer in this case), false - if fail.
         * @throws Exception
         */
        protected function connect(&$filePointer) {
            if (!empty( $this->proxyHost ) && !empty( $this->proxyPort )) {
                $this->isProxy = true;

                $host = $this->proxyHost;
                $port = $this->proxyPort;

                if ($this->scheme == HttpClient::HTTPS_PROTOCOL) {
                    throw  new Exception(_("HTTPS connections over proxy are currently not supported"));
                }
            }
            else {
                $host = $this->host;
                $port = $this->port;
            }

            $this->status = 0;

            $contextOptions = [];

            if ($this->scheme == HttpClient::HTTPS_PROTOCOL) {
                // if CAFile or CAPath is specified, enable certificate
                // verification (including name checks)
                if (isset( $this->CAFile ) || isset( $this->CAPath )) {
                    $contextOptions['ssl'] = [ 'verify_peer'         => true,
                                               'CN_match'            => $this->host,
                                               'disable_compression' => true, ];

                    if (isset( $this->CAFile )) {
                        $contextOptions['ssl']['cafile'] = $this->CAFile;
                    }
                    if (isset( $this->CAPath )) {
                        $contextOptions['ssl']['capath'] = $this->CAPath;
                    }
                }

                $host = 'ssl://' . $host;
            }

            $context = stream_context_create($contextOptions);

            if (version_compare(PHP_VERSION,
                                '5.0.0',
                                '>')) {
                if ($this->scheme == HttpClient::HTTP_PROTOCOL) {
                    $host = "tcp://" . $host;
                }
                $filePointer = stream_socket_client("$host:$port",
                                                    $errno,
                                                    $errmsg,
                                                    $this->socketTimeout,
                                                    STREAM_CLIENT_CONNECT,
                                                    $context);
            }
            else {
                $filePointer = fsockopen($host,
                                         $port,
                                         $errno,
                                         $errstr,
                                         $this->socketTimeout/*,$context*/);
            }

            if ($filePointer) {
                // socket connection succeeded
                return true;
            }
            else {
                // socket connection failed
                $this->status = $errno;
                switch ($errno) {
                    case -3:
                        $this->error = _("socket creation failed (-3).");
                        break;
                    case -4:
                        $this->error = _("dns lookup failure (-4).");
                        break;
                    case -5:
                        $this->error = _("connection refused or timed out (-5).");
                        break;
                    default:
                        $this->error = sprintf(_("connection failed (%d)."),
                                               $errno);
                }

                return false;
            }
        }

        /**
         * Disconnect a socket connection
         * @param $filePointer - file pointer
         * @return bool
         */
        protected function disconnect($filePointer) {
            return ( fclose($filePointer) );
        }


        /**
         * Prepare post body according to encoding type
         * @param $formVars  - form variables
         * @param $formFiles - form upload files
         * @return string - post body
         */
        protected function preparePostBody($formVars, $formFiles) {
            settype($formVars,
                    "array");
            settype($formFiles,
                    "array");
            $postData = "";

            if (count($formVars) == 0 && count($formFiles) == 0) {
                return $postData;
            }

            switch ($this->defaultSubmitType) {
                case "application/x-www-form-urlencoded":
                    reset($formVars);
                    while (list( $key, $val ) = each($formVars)) {
                        if (is_array($val) || is_object($val)) {
                            /** @noinspection PhpUnusedLocalVariableInspection */
                            while (list( $currentKey, $curValue ) = each($val)) {
                                $postData .= urlencode($key) . "[]=" . urlencode($curValue) . "&";
                            }
                        }
                        else {
                            $postData .= urlencode($key) . "=" . urlencode($val) . "&";
                        }
                    }
                    break;

                case "multipart/form-data":
                    $this->defaultMimeBoundary = "Snoopy" . md5(uniqid(microtime()));

                    reset($formVars);
                    while (list( $key, $val ) = each($formVars)) {
                        if (is_array($val) || is_object($val)) {
                            /** @noinspection PhpUnusedLocalVariableInspection */
                            while (list( $currentKey, $curValue ) = each($val)) {
                                $postData .= "--" . $this->defaultMimeBoundary . "\r\n";
                                $postData .= "Content-Disposition: form-data; name=\"$key\[\]\"\r\n\r\n";
                                $postData .= "$curValue\r\n";
                            }
                        }
                        else {
                            $postData .= "--" . $this->defaultMimeBoundary . "\r\n";
                            $postData .= "Content-Disposition: form-data; name=\"$key\"\r\n\r\n";
                            $postData .= "$val\r\n";
                        }
                    }

                    reset($formFiles);
                    while (list( $field_name, $file_names ) = each($formFiles)) {
                        settype($file_names,
                                "array");
                        while (list( , $file_name ) = each($file_names)) {
                            if (!is_readable($file_name)) {
                                continue;
                            }

                            $fp = fopen($file_name,
                                        "r");
                            $file_content = fread($fp,
                                                  filesize($file_name));
                            fclose($fp);
                            $base_name = basename($file_name);

                            $postData .= "--" . $this->defaultMimeBoundary . "\r\n";
                            $postData .= "Content-Disposition: form-data; name=\"$field_name\"; filename=\"$base_name\"\r\n\r\n";
                            $postData .= "$file_content\r\n";
                        }
                    }
                    $postData .= "--" . $this->defaultMimeBoundary . "--\r\n";
                    break;
            }

            return $postData;
        }

        /**
         * @param $key
         * @param $value
         */
        public function addHeader($key, $value) {
            $this->rawHttpHeaders[$key] = $value;
        }

        /**************************************************************************************************************/
        //                                    GETTERS
        /**************************************************************************************************************/

        /**
         * return the results of a request
         * @return string
         */
        public function getResults() {
            return $this->results;
        }

        /**
         * return last error
         * @return string
         */
        public function getError() {
            return $this->error;
        }
    }

?>
