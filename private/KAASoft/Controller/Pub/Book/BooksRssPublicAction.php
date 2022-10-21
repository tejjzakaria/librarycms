<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-07-07
     */


    namespace KAASoft\Controller\Pub\Book;


    use DateTime;
    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\Util\Image;
    use KAASoft\Environment\Config;
    use KAASoft\Environment\Routes\Pub\BookPublicRoutes;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Environment\RssSettings;
    use KAASoft\Environment\Session;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use KAASoft\Util\RSS\ChannelItem;
    use KAASoft\Util\RSS\RssFeed;

    /**
     * Class BooksRssPublicAction
     * @package KAASoft\Controller\Pub\Book
     */
    class BooksRssPublicAction extends PublicActionBase {

        const /** @noinspection HtmlUnknownTarget */
            BOOK_DESCRIPTION_HTML = '<table>
                                            <tr>
                                                <td><img xmlns="http://www.w3.org/1999/xhtml" src="%s" width="140" height="220"/></td>
                                                <td>%s</td>
                                            </tr>
                                       </table>';

        /**
         * BooksRssPublicAction constructor.
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
            if ($this->siteViewOptions->getOptionValue(SiteViewOptions::ENABLE_BOOK_RSS)) {

                $rssSettings = new RssSettings();
                $rssSettings->loadSettings();

                $bookChannel = $rssSettings->getChannel(RssFeed::BOOK_RSS_CHANNEL_NAME);

                $rssFeed = new RssFeed();
                if ($bookChannel !== null) {
                    $rssFeed->addChannel($bookChannel);

                    $rssBooksNumber = $this->siteViewOptions->getOptionValue(SiteViewOptions::RSS_BOOK_NUMBER);

                    $bookHelper = new BookDatabaseHelper($this);
                    $books = $bookHelper->getBooks(0,
                                                   $rssBooksNumber,
                                                   "Books.creationDateTime",
                                                   "DESC");

                    $isFirst = true;
                    if ($books !== null) {
                        foreach ($books as $book) {
                            if ($book instanceof Book) {
                                $dateTime = DateTime::createFromFormat(Helper::DATABASE_DATE_TIME_FORMAT,
                                                                       $book->getUpdateDateTime());
                                $pubTime = $dateTime->getTimestamp();

                                if ($isFirst) {
                                    /* $headers = getallheaders();
                                     if (isset( $headers["If-None-Match"] )) {
                                         if ($headers["If-None-Match"] == $time) {
                                             header("HTTP/1.1 304 Not Modified");
                                             exit;
                                         }
                                     }
                                     else {
                                         if (isset( $headers["If-Modified-Since"] )) {
                                             $timeGmt=gmdate("D, d M Y H:i:s", $time)." GMT";
                                             if ($headers["If-Modified-Since"] == $timeGmt) {
                                                 header("HTTP/1.1 304 Not Modified");
                                                 exit;
                                             }
                                         }
                                     }*/

                                    $bookChannel->setPubDate($pubTime);
                                    $bookChannel->setLastBuildDate($pubTime);
                                    $isFirst = false;
                                }
                                $channelItem = new ChannelItem();
                                $channelItem->setTitle($book->getTitle());
                                $link = ( $this->siteViewOptions->getOptionValue(SiteViewOptions::BOOK_URL_TYPE) ? $this->routeController->getRouteString(BookPublicRoutes::BOOK_VIEW_PUBLIC_ROUTE,
                                                                                                                                                          [ "bookId" => $book->getId() ]) : $this->routeController->getRouteString(BookPublicRoutes::BOOK_VIEW_VIA_URL_PUBLIC_ROUTE,
                                                                                                                                                                                                                                   [ "bookUrl" => $book->getUrl() ]) );
                                $channelItem->setLink(Config::getSiteURL() . $link);
                                $bookCover = $book->getCover();
                                $description = null;
                                if ($bookCover !== null and $bookCover instanceof Image and file_exists($bookCover->getAbsolutePath())) {
                                    $description = sprintf(BooksRssPublicAction::BOOK_DESCRIPTION_HTML,
                                                           Config::getSiteURL() . $bookCover->getWebPath(),
                                                           $book->getDescription());
                                }
                                else {
                                    $description = $book->getDescription();
                                }
                                $channelItem->setDescription($description);
                                $channelItem->addCategory("Books");
                                $channelItem->setPubDate($pubTime);

                                $bookChannel->addItem($channelItem);

                            }
                        }


                        $this->setXmlContentType();
                        header("Last-Modified: " . $bookChannel->getPubDate());
                        // header("Etag: \"" . $bookChannel->getPubDate() . "\"");
                        echo $rssFeed->getXml();
                        exit( 0 );
                    }
                }
                else {
                    $message = _("RSS is enabled but not configured.");
                    Session::addSessionMessage($message,
                                               Message::MESSAGE_STATUS_WARNING);
                    ControllerBase::getLogger()->warn($message);

                    return new DisplaySwitch(null,
                                             $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
                }
            }

            $message = _("RSS is not enabled.");
            Session::addSessionMessage($message,
                                       Message::MESSAGE_STATUS_WARNING);
            ControllerBase::getLogger()->warn($message);

            return new DisplaySwitch(null,
                                     $this->routeController->getRouteString(GeneralPublicRoutes::PAGE_IS_NOT_FOUND_ROUTE));
        }
    }