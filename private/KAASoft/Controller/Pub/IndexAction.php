<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Pub;

    use KAASoft\Controller\Admin\Author\AuthorDatabaseHelper;
    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\Page\PageDatabaseHelper;
    use KAASoft\Controller\Admin\Post\PostDatabaseHelper;
    use KAASoft\Controller\PublicActionBase;
    use KAASoft\Environment\SiteViewOptions;
    use KAASoft\Util\DisplaySwitch;

    /**
     * Class IndexAction
     * @package KAASoft\Controller\Pub
     */
    class IndexAction extends PublicActionBase {
        /**
         * IndexAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute,
                                true);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         */
        protected function action($args) {

            // main page
            $bookDatabaseHelper = new BookDatabaseHelper($this);
            $authorDatabaseHelper = new AuthorDatabaseHelper($this);
            $postDatabaseHelper = new PostDatabaseHelper($this);
            $pageDatabaseHelper = new PageDatabaseHelper($this);

            $lastBooksNumber = $this->siteViewOptions->getOptionValue(SiteViewOptions::LAST_BOOKS_NUMBER_INDEX);
            $topRatedBooksNumber = $this->siteViewOptions->getOptionValue(SiteViewOptions::TOP_RATED_BOOKS_NUMBER_INDEX);
            $authorsPerPage = $this->siteViewOptions->getOptionValue(SiteViewOptions::AUTHORS_PER_PAGE_INDEX);
            $postsPerPage = $this->siteViewOptions->getOptionValue(SiteViewOptions::POSTS_PER_PAGE_INDEX);

            $books = $bookDatabaseHelper->getBooks(0,
                                                   $lastBooksNumber,
                                                   "Books.creationDateTime",
                                                   "DESC");

            $bookListType = $this->siteViewOptions->getOptionValue(SiteViewOptions::MAIN_PAGE_BOOK_LIST_TYPE);
            if (strcmp($bookListType,
                       SiteViewOptions::MAIN_PAGE_BOOK_LIST_TYPE_TOP) === 0
            ) {
                $topRatedBooks = $bookDatabaseHelper->getBooks(0,
                                                               $topRatedBooksNumber,
                                                               "Books.rating",
                                                               "DESC");
            }
            else {
                $topRatedBooks = $bookDatabaseHelper->getRandomBooks($topRatedBooksNumber);
            }

            $authorListType = $this->siteViewOptions->getOptionValue(SiteViewOptions::MAIN_PAGE_AUTHOR_LIST_TYPE);
            if (strcmp($authorListType,
                       SiteViewOptions::MAIN_PAGE_AUTHOR_LIST_TYPE_TOP) === 0
            ) {
                $authors = $authorDatabaseHelper->getTopRatedAuthors(0,
                                                                     $authorsPerPage);
            }
            else {
                $authors = $authorDatabaseHelper->getRandomAuthors($authorsPerPage);
            }


            $posts = $postDatabaseHelper->getPublicPosts(0,
                                                         $postsPerPage);

            $this->smarty->assign("books",
                                  $books);

            $this->smarty->assign("topRatedBooks",
                                  $topRatedBooks);

            $this->smarty->assign("authors",
                                  $authors);

            $this->smarty->assign("posts",
                                  $posts);

            $this->smarty->assign("indexPage",
                                  $pageDatabaseHelper->getPage(0));

            return new DisplaySwitch('index.tpl');
        }
    }