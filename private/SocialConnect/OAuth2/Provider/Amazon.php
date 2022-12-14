<?php
    /**
     * SocialConnect project
     * @author: Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
     */

    namespace SocialConnect\OAuth2\Provider;

    use SocialConnect\Common\Entity\User;
    use SocialConnect\Common\Hydrator\ObjectMap;
    use SocialConnect\OAuth2\AbstractProvider;
    use SocialConnect\OAuth2\AccessToken;
    use SocialConnect\Provider\AccessTokenInterface;
    use SocialConnect\Provider\Exception\InvalidAccessToken;
    use SocialConnect\Provider\Exception\InvalidResponse;

    class Amazon extends AbstractProvider {
        const NAME = 'amazon';

        /**
         * {@inheritdoc}
         */
        public function getBaseUri() {
            return 'https://api.amazon.com/';
        }

        /**
         * {@inheritdoc}
         */
        public function getAuthorizeUri() {
            return 'https://www.amazon.com/ap/oa';
        }

        /**
         * {@inheritdoc}
         */
        public function getRequestTokenUri() {
            return 'https://api.amazon.com/auth/o2/token';
        }

        /**
         * {@inheritdoc}
         */
        public function getName() {
            return self::NAME;
        }

        /**
         * {@inheritdoc}
         */
        public function parseToken($body) {
            if (empty( $body )) {
                throw new InvalidAccessToken('Provider response with empty body');
            }

            $result = json_decode($body,
                                  true);
            if ($result) {
                return new AccessToken($result);
            }

            throw new InvalidAccessToken('Provider response with not valid JSON');
        }

        /**
         * {@inheritdoc}
         */
        public function getIdentity(AccessTokenInterface $accessToken) {
            $response = $this->httpClient->request($this->getBaseUri() . 'user/profile',
                                                   [ 'access_token' => $accessToken->getToken() ]);

            if (!$response->isSuccess()) {
                throw new InvalidResponse('API response with error code',
                                          $response);
            }

            $result = $response->json();
            if (!$result) {
                throw new InvalidResponse('API response is not a valid JSON object',
                                          $response);
            }

            $hydrator = new ObjectMap([ 'user_id' => 'id',
                                        'name'    => 'firstname', ]);

            return $hydrator->hydrate(new User(),
                                      $result);
        }
    }
