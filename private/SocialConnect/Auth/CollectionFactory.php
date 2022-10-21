<?php
    /**
     * SocialConnect project
     * @author: Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
     */

    namespace SocialConnect\Auth;

    use LogicException;
    use SocialConnect\OAuth1;
    use SocialConnect\OAuth2;
    use SocialConnect\Provider\Consumer;

    /**
     * Class Factory
     * @package SocialConnect\Auth\Provider
     */
    class CollectionFactory implements FactoryInterface {
        /**
         * @var array
         */
        protected $providers = [ // OAuth1
                                 OAuth1\Provider\Twitter::NAME   => OAuth1\Provider\Twitter::class,
                                 // OAuth2
                                 OAuth2\Provider\Facebook::NAME  => OAuth2\Provider\Facebook::class,
                                 OAuth2\Provider\Google::NAME    => OAuth2\Provider\Google::class,
                                 OAuth2\Provider\Instagram::NAME => OAuth2\Provider\Instagram::class,
                                 OAuth2\Provider\Vk::NAME        => OAuth2\Provider\Vk::class,
                                 OAuth2\Provider\Amazon::NAME    => OAuth2\Provider\Amazon::class,
                                 OAuth2\Provider\Yandex::NAME    => OAuth2\Provider\Yandex::class,
                                 OAuth2\Provider\Microsoft::NAME => OAuth2\Provider\Microsoft::class,
                                 OAuth2\Provider\LinkedIn::NAME  => OAuth2\Provider\LinkedIn::class,
                                 OAuth2\Provider\Yahoo::NAME     => OAuth2\Provider\Yahoo::class, ];

        /**
         * @param array $providers
         */
        public function __construct(array $providers = null) {
            if ($providers) {
                $this->providers = $providers;
            }
        }

        /**
         * @param $id
         * @return bool
         */
        public function has($id) {
            return isset( $this->providers[$id] );
        }

        /**
         * @param string  $id
         * @param array   $parameters
         * @param Service $service
         * @return \SocialConnect\Provider\AbstractBaseProvider
         */
        public function factory($id, array $parameters, Service $service) {
            $consumer = new Consumer($parameters['applicationId'],
                                     $parameters['applicationSecret']);

            if (isset( $parameters['applicationPublic'] )) {
                $consumer->setPublic($parameters['applicationPublic']);
            }

            $id = strtolower($id);

            if (!isset( $this->providers[$id] )) {
                throw new LogicException('Provider with $id = ' . $id . ' doest not exist');
            }

            $providerClassName = $this->providers[$id];

            /**
             * @var $provider \SocialConnect\Provider\AbstractBaseProvider
             */
            $provider = new $providerClassName($service->getHttpClient(),
                                               $service->getSession(),
                                               $consumer,
                                               array_merge($parameters,
                                                           $service->getConfig()));

            return $provider;
        }

        /**
         * Register new provider to Provider's collection
         *
         * @param $providerName
         * @param $providerClass
         */
        public function register($providerName, $providerClass) {
            $this->providers[$providerName] = $providerClass;
        }
    }
