<?php
    /**
     * SocialConnect project
     * @author: Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
     */

    namespace SocialConnect\OAuth2\Exception;

    use SocialConnect\Provider\Exception\AuthFailed;

    class UnknownState extends AuthFailed {
        public function __construct($message = 'Unknown state') {
            parent::__construct($message);
        }
    }
