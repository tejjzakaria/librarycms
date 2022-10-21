<?php
    /**
     * SocialConnect project
     * @author: Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
     */

    namespace SocialConnect\OAuth1\Exception;


    use Exception;

    class InvalidRequestToken extends Exception {
        public function __construct($message = 'Invalid request token token') {
            parent::__construct($message);
        }
    }
