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

    class Instagram extends AbstractProvider {
        const NAME = 'instagram';

        /**
         * {@inheritdoc}
         */
        public function getBaseUri() {
            return 'https://api.instagram.com/v1/';
        }

        /**
         * {@inheritdoc}
         */
        public function getAuthorizeUri() {
            return 'https://api.instagram.com/oauth/authorize';
        }

        /**
         * {@inheritdoc}
         */
        public function getRequestTokenUri() {
            return 'https://api.instagram.com/oauth/access_token';
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
            $result = json_decode($body,
                                  true);
            if ($result) {
                return new AccessToken($result);
            }

            throw new InvalidAccessToken('AccessToken is not a valid JSON');
        }

        /**
         * {@inheritdoc}
         */
        public function getIdentity(AccessTokenInterface $accessToken) {
            $response = $this->httpClient->request($this->getBaseUri() . 'users/self',
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

            $hydrator = new ObjectMap([ 'id'              => 'id',
                                        'username'        => 'username',
                                        'bio'             => 'bio',
                                        'website'         => 'website',
                                        'profile_picture' => 'pictureURL',
                                        'full_name'       => 'fullname' ]);

            return $hydrator->hydrate(new User(),
                                      $result->data);
        }
    }
