<?php
    /**
     * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
     */

    namespace SocialConnect\SMS;

    use SocialConnect\SMS\Provider\ProviderInterface;

    class ProviderContainer {
        /**
         * @var Provider\ProviderInterface[]
         */
        protected $providers = [];

        /**
         * @var ProviderFactory
         */
        protected $providerFactory;

        /**
         * @param ProviderFactory $providerFactory
         */
        public function __construct(ProviderFactory $providerFactory) {
            $this->providerFactory = $providerFactory;
        }

        /**
         * @param string $name
         * @return ProviderInterface
         */
        public function get($name) {
            if (isset( $this->providers[$name] )) {
                return $this->providers[$name];
            }

            return $this->providers[$name] = $this->providerFactory->factory($name);
        }
    }
