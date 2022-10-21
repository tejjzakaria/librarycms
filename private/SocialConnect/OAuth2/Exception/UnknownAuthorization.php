<?php
    /**
     * SocialConnect project
     * @author: Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
     */

    namespace SocialConnect\OAuth2\Exception;

    use SocialConnect\Provider\Exception\AuthFailed;

    class UnknownAuthorization extends AuthFailed {
        public function __construct($message = 'Unknown authorization') {
            parent::__construct($message);
        }
    }
