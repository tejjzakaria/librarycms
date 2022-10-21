<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-01-19
     */

    namespace KAASoft\Controller\Admin\Util;

    use Exception;
    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\Page\PageDatabaseHelper;
    use KAASoft\Controller\Admin\Post\PostDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\ControllerBase;
    use KAASoft\Database\Entity\Post\Page;
    use KAASoft\Database\Entity\Post\Post;
    use KAASoft\Environment\Config;
    use KAASoft\Environment\Routes\Pub\BookPublicRoutes;
    use KAASoft\Environment\Routes\Pub\GeneralPublicRoutes;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\Message;
    use RuntimeException;
    use SitemapGenerator\SitemapGenerator;

    /**
     * Class SitemapGenerateAction
     * @package KAASoft\Controller\Admin\Util
     */
    class SitemapGenerateAction extends AdminActionBase {
        /**
         * SitemapGenerateAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws \Exception
         */
        protected function action($args) {
            set_time_limit(2 * 60 * 60); // 2 hours
            $this->generateSitemap();

            return new DisplaySwitch();
        }

        /**
         *
         */
        protected function generateSitemap() {
            try {
                // create object
                $sitemap = new SitemapGenerator(Config::getSiteURL());
                $indexRoute = $this->routeController->getRouteString(GeneralPublicRoutes::PUBLIC_INDEX_ROUTE);

                $postHelper = new PostDatabaseHelper($this);
                $pageHelper = new PageDatabaseHelper($this);
                $bookHelper = new BookDatabaseHelper($this);

                // add urls
                $message = new Message("Put \"index\" page to sitemap...");
                ControllerBase::getLogger()->info($message->getMessage());
                Helper::outputMessage($message);
                $sitemap->addUrl($indexRoute,
                                 Helper::getDateString(),
                                 SitemapGenerator::CHANGE_FREQUENCY_YEARLY,
                                 "1");
                // posts
                $message = new Message("Get posts from database...");
                ControllerBase::getLogger()->info($message->getMessage());
                Helper::outputMessage($message);
                $posts = $postHelper->getPublicPosts();
                $message = new Message("Put posts to sitemap...");
                ControllerBase::getLogger()->info($message->getMessage());
                Helper::outputMessage($message);
                foreach ($posts as $post) {
                    if ($post instanceof Post) {
                        $sitemap->addUrl($post->getUrl(),
                                         Helper::reformatDateString($post->getUpdateDateTime(),
                                                                    Helper::DATABASE_DATE_FORMAT,
                                                                    Helper::DATABASE_DATE_TIME_FORMAT),
                                         SitemapGenerator::CHANGE_FREQUENCY_WEEKLY,
                                         "1");
                    }
                }
                // pages
                $message = new Message("Get pages from database...");
                ControllerBase::getLogger()->info($message->getMessage());
                Helper::outputMessage($message);
                $pages = $pageHelper->getPages();
                $message = new Message("Put pages to sitemap...");
                ControllerBase::getLogger()->info($message->getMessage());
                Helper::outputMessage($message);
                foreach ($pages as $page) {
                    if ($page instanceof Page) {
                        if ($page->getId() !== 0) {
                            $sitemap->addUrl($page->getUrl(),
                                             Helper::reformatDateString($page->getUpdateDateTime(),
                                                                        Helper::DATABASE_DATE_FORMAT,
                                                                        Helper::DATABASE_DATE_TIME_FORMAT),
                                             SitemapGenerator::CHANGE_FREQUENCY_WEEKLY,
                                             "1");
                        }
                    }
                }
                // books
                $bookCount = $bookHelper->getBooksCount();
                $perPage = 5000;
                $bookPages = ceil($bookCount / $perPage);
                for ($i = 0; $i < $bookPages; $i++) {
                    $message = new Message(sprintf("Get books from database: %10d of %10d",
                                                   ( ( $i + 1 ) == $bookPages ) ? $bookCount : ( ( $i + 1 ) * $perPage ),
                                                   $bookCount));
                    ControllerBase::getLogger()->info($message->getMessage());
                    Helper::outputMessage($message);
                    $books = $bookHelper->getSimpleBooks($i * $perPage,
                                                         $perPage);
                    $message = new Message("Put books to sitemap...");
                    ControllerBase::getLogger()->info($message->getMessage());
                    Helper::outputMessage($message);
                    if ($books !== null) {
                        foreach ($books as $book) {
                            $sitemap->addUrl($this->routeController->getRouteString(BookPublicRoutes::BOOK_VIEW_PUBLIC_ROUTE,
                                                                                    [ "bookId" => $book["id"] ]),
                                             Helper::reformatDateString($book["updateDateTime"],
                                                                        Helper::DATABASE_DATE_FORMAT,
                                                                        Helper::DATABASE_DATE_TIME_FORMAT),
                                             SitemapGenerator::CHANGE_FREQUENCY_YEARLY,
                                             "1");
                        }
                    }
                    else {
                        $message = _("Sitemap generation is failed. Couldn't get books from database.");
                        throw new RuntimeException($message);
                    }
                }

                $message = new Message("Saving sitemap file(s)...");
                ControllerBase::getLogger()->info($message->getMessage());
                Helper::outputMessage($message);
                $sitemap->createSitemap();
                $sitemap->writeSitemap();
                $sitemap->updateRobots();
                $message = new Message("Sitemap generation is successfully completed.",
                                       Message::MESSAGE_STATUS_SUCCESS);
                ControllerBase::getLogger()->info($message->getMessage());
                Helper::outputMessage($message);
            }
            catch (Exception $e) {
                ControllerBase::getLogger()->error($e->getMessage());
                Helper::outputMessage(new Message($e->getMessage(),
                                                  Message::MESSAGE_STATUS_ERROR));

            }
        }
    }