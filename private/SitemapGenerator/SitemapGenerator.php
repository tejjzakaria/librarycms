<?php
    /**
     * Copyright (c) 2015 - 2016 by KAA Soft.  All rights reserved.
     */

    namespace SitemapGenerator;

    use BadMethodCallException;
    use DOMDocument;
    use InvalidArgumentException;
    use KAASoft\Util\FileHelper;
    use KAASoft\Util\Helper;
    use KAASoft\Util\HTTP\HttpClient;
    use LengthException;

    class SitemapGenerator {

        const CHANGE_FREQUENCY_ALWAYS  = "always";
        const CHANGE_FREQUENCY_HOURLY  = "hourly";
        const CHANGE_FREQUENCY_DAILY   = "daily";
        const CHANGE_FREQUENCY_WEEKLY  = "weekly";
        const CHANGE_FREQUENCY_MONTHLY = "monthly";
        const CHANGE_FREQUENCY_YEARLY  = "yearly";
        const CHANGE_FREQUENCY_NEVER   = "never";

        /**
         * Name of sitemap file
         * @var string
         * @access public
         */
        public $sitemapFileName = "sitemap.xml";

        /**
         * Name of sitemap index file
         * @var string
         * @access public
         */
        public $sitemapIndexFileName = "sitemap-index.xml";

        /**
         * Robots file name
         * @var string
         * @access public
         */
        public $robotsFileName = "robots.txt";

        /**
         * Quantity of URLs per single sitemap file.
         * According to specification max value is 50.000.
         * If Your links are very long, sitemap file can be bigger than 10MB,
         * in this case use smaller value.
         * @var int
         * @access public
         */
        public $maxURLsPerSitemap = 50000;

        /**
         * If true, two sitemap files (.xml and .xml.gz) will be created and added to robots.txt.
         * If true, .gz file will be submitted to search engines.
         * If quantity of URLs will be bigger than 50.000, option will be ignored,
         * all sitemap files except sitemap index will be compressed.
         * @var bool
         * @access public
         */
        public $createGZipFile = true;

        /**
         * URL to Your site (without trailing slash).
         * Script will use it to send sitemaps to search engines.
         * @var string
         * @access private
         */
        private $baseURL;

        /**
         * Base path. Relative to script location.
         * Use this if Your sitemap and robots files should be stored in other
         * directory then script.
         * @var string
         * @access private
         */
        private $basePath;

        /**
         * Array with urls
         * @var array of strings
         * @access private
         */
        private $urls;

        /**
         * Sitemap Array
         * @var array Sitemap
         * @access private
         */
        private $sitemaps;

        /**
         * @var Sitemap
         * @access private
         */
        private $sitemapIndex;

        /**
         * Current sitemap full URL
         * @var string
         * @access private
         */
        private $sitemapFullURL;

        /**
         * Constructor.
         * @param string      $baseURL  You site URL, with / at the end.
         * @param string|null $basePath Relative path where sitemap and robots should be stored.
         */
        public function __construct($baseURL, $basePath = "") {
            $this->baseURL = $baseURL;
            $this->basePath = $basePath;
        }

        /**
         * Use this to add many URL at one time.
         * Each inside array can have 1 to 4 fields.
         * @param $url SitemapUrl
         */
        public function addUrlObject($url) {
            if (!$url instanceof SitemapUrl) {
                throw new InvalidArgumentException("SitemapUrl as argument should be given.");
            }

            $this->urls[] = $url;
        }

        /**
         * @param $urls array SitemapUrl
         */
        public function addUrls($urls) {
            if (!is_array($urls)) {
                throw new InvalidArgumentException("SitemapUrl as argument should be given.");
            }
            foreach ($urls as $url) {
                if ($url instanceof SitemapUrl) {
                    $this->urls[] = $url;
                }
            }
        }

        /**
         * Use this to add single URL to sitemap.
         * @param string $url             URL
         * @param string $lastModified    When it was modified, use ISO 8601
         * @param string $changeFrequency How often search engines should revisit this URL
         * @param string $priority        Priority of URL on You site
         * @param null   $baseUrl         Site domain
         * @see http://en.wikipedia.org/wiki/ISO_8601
         * @see http://php.net/manual/en/function.date.php
         */
        public function addUrl($url, $lastModified = null, $changeFrequency = null, $priority = null, $baseUrl = null) {
            if ($url == null) {
                throw new InvalidArgumentException("URL is mandatory. At least one argument should be given.");
            }
            $urlLength = extension_loaded('mbstring') ? mb_strlen($url) : strlen($url);
            if ($urlLength > 2048) {
                throw new InvalidArgumentException("URL length can't be bigger than 2048 characters.
                                                Note, that precise url length check is guaranteed only using mb_string extension.
                                                Make sure Your server allow to use mbstring extension.");
            }
            $tmpUrl = new SitemapUrl();
            $tmpUrl->setLocation(( $baseUrl === null ? $this->baseURL : $baseUrl ) . $url);
            if (isset( $lastModified )) {
                $tmpUrl->setLastModification($lastModified);
            }
            if (isset( $changeFrequency )) {
                $tmpUrl->setChangeFrequency($changeFrequency);
            }
            if (isset( $priority )) {
                $tmpUrl->setPriority($priority);
            }

            $this->urls[] = $tmpUrl;
        }

        /**
         * Create sitemap in memory.
         */
        public function createSitemap() {
            if (!isset( $this->urls )) {
                throw new BadMethodCallException("To create sitemap, call addUrl or addUrls function first.");
            }
            if ($this->maxURLsPerSitemap > 50000) {
                throw new InvalidArgumentException("More than 50,000 URLs per single sitemap is not allowed.");
            }

            $generatorInfo = ' Generated on ' . Helper::getDateString() . ' ';

            foreach (array_chunk($this->urls,
                                 $this->maxURLsPerSitemap) as $sitemap) {
                $sitemapDocument = new DomDocument('1.0',
                                                   'UTF-8');
                $sitemapDocument->formatOutput = true;
                $sitemapDocument->preserveWhiteSpace = false;
                $comment = $sitemapDocument->createComment($generatorInfo);
                $sitemapDocument->appendChild($comment);
                $root = $sitemapDocument->createElement('urlset');
                $root->setAttribute('xmlns',
                                    'http://www.sitemaps.org/schemas/sitemap/0.9');
                $root = $sitemapDocument->appendChild($root);

                foreach ($sitemap as $url) {
                    if ($url instanceof SitemapUrl) {
                        $urlElement = $sitemapDocument->createElement('url');
                        $row = $root->appendChild($urlElement);

                        $locElement = $sitemapDocument->createElement('loc',
                                                                      htmlspecialchars($url->getLocation(),
                                                                                       ENT_QUOTES,
                                                                                       'UTF-8'));
                        $row->appendChild($locElement);

                        if ($url->getLastModification() !== null) {
                            $lastModElement = $sitemapDocument->createElement('lastmod',
                                                                              $url->getLastModification());
                            $row->appendChild($lastModElement);
                        }
                        if ($url->getChangeFrequency() !== null) {
                            $changeFreqElement = $sitemapDocument->createElement('changefreq',
                                                                                 $url->getChangeFrequency());
                            $row->appendChild($changeFreqElement);
                        }
                        if ($url->getPriority() !== null) {
                            $priorityElement = $sitemapDocument->createElement('priority',
                                                                               $url->getPriority());
                            $row->appendChild($priorityElement);
                        }
                    }
                }
                if (strlen($sitemapDocument->saveXML()) > 10485760) {
                    throw new LengthException("Sitemap size is more than 10MB (10,485,760), please decrease maxURLsPerSitemap variable.");
                }
                $sitemap = new Sitemap();
                $sitemap->setSitemapContent($sitemapDocument->saveXML());
                $this->sitemaps[] = $sitemap;

            }
            if (sizeof($this->sitemaps) > 1000) {
                throw new LengthException("Sitemap index can contains 1000 single sitemaps. Perhaps You trying to submit too many URLs.");
            }
            if (sizeof($this->sitemaps) > 1) {
                for ($i = 0; $i < sizeof($this->sitemaps); $i++) {
                    $sitemap = $this->sitemaps[$i];
                    if ($sitemap instanceof Sitemap) {
                        $sitemap->setFileName(str_replace(".xml",
                                                          ( $i + 1 ) . ".xml.gz",
                                                          $this->sitemapFileName));
                    }
                }

                $sitemapIndexDocument = new DomDocument('1.0',
                                                        'UTF-8');
                $sitemapIndexDocument->formatOutput = true;
                $sitemapIndexDocument->preserveWhiteSpace = false;

                $comment = $sitemapIndexDocument->createComment($generatorInfo);
                $sitemapIndexDocument->appendChild($comment);

                $root = $sitemapIndexDocument->createElement('sitemapindex');
                $root->setAttribute('xmlns',
                                    'http://www.sitemaps.org/schemas/sitemap/0.9');

                $root = $sitemapIndexDocument->appendChild($root);

                foreach ($this->sitemaps as $sitemap) {
                    if ($sitemap instanceof Sitemap) {
                        $sitemapElement = $sitemapIndexDocument->createElement('sitemap');
                        $row = $root->appendChild($sitemapElement);

                        $locElement = $sitemapIndexDocument->createElement('loc',
                                                                           $this->baseURL . HttpClient::HTTP_PATH_SEPARATOR . htmlentities($sitemap->getFileName()));
                        $row->appendChild($locElement);

                        $lastModElement = $sitemapIndexDocument->createElement('lastmod',
                                                                               Helper::getDateString());
                        $row->appendChild($lastModElement);

                    }
                }
                $this->sitemapFullURL = $this->baseURL . HttpClient::HTTP_PATH_SEPARATOR . $this->sitemapIndexFileName;
                $this->sitemapIndex = new Sitemap($this->sitemapIndexFileName,
                                                  $sitemapIndexDocument->saveXML());
            }
            else {
                if ($this->createGZipFile) {
                    $this->sitemapFullURL = $this->baseURL . HttpClient::HTTP_PATH_SEPARATOR . $this->sitemapFileName . ".gz";
                }
                else {
                    $this->sitemapFullURL = $this->baseURL . HttpClient::HTTP_PATH_SEPARATOR . $this->sitemapFileName;
                }
                $sitemap = $this->sitemaps[0];
                if ($sitemap instanceof Sitemap) {
                    $sitemap->setFileName($this->sitemapFileName);
                }
            }
        }

        /**
         * Will write sitemaps as files.
         * @access public
         */
        public function writeSitemap() {
            if (!isset( $this->sitemaps )) {
                throw new BadMethodCallException("To write sitemap, call createSitemap function first.");
            }
            if (isset( $this->sitemapIndex )) {
                FileHelper::saveStringToFile($this->sitemapIndex->getSitemapContent(),
                                             $this->basePath . $this->sitemapIndex->getFileName());
                if ($this->createGZipFile) {
                    foreach ($this->sitemaps as $sitemap) {
                        if ($sitemap instanceof Sitemap) {
                            FileHelper::saveGZipFile($sitemap->getSitemapContent(),
                                                     $this->basePath,
                                                     $sitemap->getFileName());
                        }
                    }
                }
            }
            else {
                $sitemap = $this->sitemaps[0];
                if ($sitemap instanceof Sitemap) {
                    FileHelper::saveStringToFile($sitemap->getSitemapContent(),
                                                 $this->basePath . $sitemap->getFileName());
                    FileHelper::saveGZipFile($sitemap->getSitemapContent(),
                                             $this->basePath,
                                             $sitemap->getFileName() . ".gz");
                }
            }
        }

        /**
         * If robots.txt file exist, will update information about newly created sitemaps.
         * If there is no robots.txt will, create one and put into it information about sitemaps.
         * @access public
         */
        public function updateRobots() {
            if (!isset( $this->sitemaps )) {
                throw new BadMethodCallException("To update robots.txt, call createSitemap function first.");
            }
            $sampleRobotsFile = "User-agent: *\nAllow: /";
            if (file_exists($this->basePath . $this->robotsFileName)) {
                $robotsFile = explode("\n",
                                      file_get_contents($this->basePath . $this->robotsFileName));
                $robotsFileContent = "";
                foreach ($robotsFile as $key => $value) {
                    if (substr($value,
                               0,
                               8) == 'Sitemap:'
                    ) {
                        unset( $robotsFile[$key] );
                    }
                    else {
                        $robotsFileContent .= $value . "\n";
                    }
                }
                $robotsFileContent .= "Sitemap: $this->sitemapFullURL";
                if ($this->createGZipFile && !isset( $this->sitemapIndex )) {
                    $robotsFileContent .= "\nSitemap: " . $this->sitemapFullURL . ".gz";
                }
                file_put_contents($this->basePath . $this->robotsFileName,
                                  $robotsFileContent);
            }
            else {
                $sampleRobotsFile = $sampleRobotsFile . "\n\nSitemap: " . $this->sitemapFullURL;
                if ($this->createGZipFile && !isset( $this->sitemapIndex )) {
                    $sampleRobotsFile .= "\nSitemap: " . $this->sitemapFullURL . ".gz";
                }
                file_put_contents($this->basePath . $this->robotsFileName,
                                  $sampleRobotsFile);
            }
        }
    }

?>
