<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Tag;

    use KAASoft\Controller\DatabaseHelper;
    use KAASoft\Database\Entity\Util\Tag;
    use KAASoft\Database\KAASoftDatabase;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class TagDatabaseHelper
     * @package KAASoft\Controller\Admin\Tag
     */
    class TagDatabaseHelper extends DatabaseHelper {
        /**
         * @param null $tagIds
         * @param null $offset
         * @param null $perPage
         * @param null $sortColumn
         * @param null $sortOrder
         * @return array|null
         */
        public function getTags($tagIds = null, $offset = null, $perPage = null, $sortColumn = null, $sortOrder = null) {
            $queryParams = [];
            if (!ValidationHelper::isArrayEmpty($tagIds)) {
                $queryParams = array_merge($queryParams,
                                           [ KAASoftDatabase::$TAGS_TABLE_NAME . ".id" => $tagIds ]);
            }
            if ($offset !== null && $perPage !== null) {
                $queryParams = array_merge($queryParams,
                                           [ "ORDER" => ( $sortColumn === null ? [ "id" => "ASC" ] : ( $sortOrder === null ? [ $sortColumn => "ASC" ] : [ $sortColumn => $sortOrder ] ) ),
                                             "LIMIT" => [ (int)$offset,
                                                          (int)$perPage ] ]);
            }

            $tagsQueryResult = $this->kaaSoftDatabase->select(KAASoftDatabase::$TAGS_TABLE_NAME,
                                                              array_merge(Tag::getDatabaseFieldNames(),
                                                                          [ KAASoftDatabase::$TAGS_TABLE_NAME . ".id" ]),
                                                              $queryParams);

            if ($tagsQueryResult !== false) {
                $tags = [];

                foreach ($tagsQueryResult as $tagRow) {
                    $tag = Tag::getObjectInstance($tagRow);
                    $tags[] = $tag;
                }

                return $tags;
            }

            return null;
        }

        /**
         * @return bool|int
         */
        public function getTagsCount() {
            return $this->kaaSoftDatabase->count(KAASoftDatabase::$TAGS_TABLE_NAME);
        }

        /**
         * @param $tagId
         * @return Tag|null
         */
        public function getTag($tagId) {
            $queryResult = $this->kaaSoftDatabase->get(KAASoftDatabase::$TAGS_TABLE_NAME,
                                                       array_merge(Tag::getDatabaseFieldNames(),
                                                                   [ KAASoftDatabase::$TAGS_TABLE_NAME . ".id" ]),
                                                       [ "id" => $tagId ]);
            if ($queryResult !== false) {
                $tag = Tag::getObjectInstance($queryResult);

                return $tag;

            }

            return null;
        }

        /**
         * @param array $tags
         * @return bool|int
         */
        public function saveTags($tags) {
            $data = [];
            foreach ($tags as $tag) {
                if ($tag instanceof Tag) {
                    $data[] = $tag->getDatabaseArray();
                }
            }

            return $this->kaaSoftDatabase->insert(KAASoftDatabase::$TAGS_TABLE_NAME,
                                                  $data);
        }

        /**
         * @param $tag Tag
         * @return array|bool|int
         */
        public function saveTag($tag) {
            $data = $tag->getDatabaseArray();
            if ($tag->getId() === null) {
                return $this->kaaSoftDatabase->insert(KAASoftDatabase::$TAGS_TABLE_NAME,
                                                      $data);
            }
            else {
                return $this->kaaSoftDatabase->update(KAASoftDatabase::$TAGS_TABLE_NAME,
                                                      $data,
                                                      [ "id" => $tag->getId() ]);
            }
        }

        /**
         * @param $tagId
         * @return bool
         */
        public function deleteTag($tagId) {
            $tag = $this->getTag($tagId);
            if ($tag !== null) {
                return $this->kaaSoftDatabase->delete(KAASoftDatabase::$TAGS_TABLE_NAME,
                                                      [ "id" => $tagId ]);
            }

            return false;
        }

        /**+
         * @param $tagId
         * @return bool
         */
        public function isTagExist($tagId) {
            return $this->kaaSoftDatabase->has(KAASoftDatabase::$TAGS_TABLE_NAME,
                                               [ "id" => $tagId ]);
        }

    }