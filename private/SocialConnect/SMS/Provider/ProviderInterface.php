<?php
    /**
     * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
     */

    namespace SocialConnect\SMS\Provider;

    use SocialConnect\Common\Http\Client\ClientInterface;

    interface ProviderInterface {
        /**
         * @param array           $configuration Configuration for provider
         * @param ClientInterface $httpClient    Http client for requesting data
         */
        public function __construct(array $configuration, ClientInterface $httpClient);

        /**
         * @return float
         */
        public function getBalance();

        /**
         * @param string         $from
         * @param string|integer $phone
         * @param string         $message
         * @return bool|\SocialConnect\SMS\Entity\SmsResult
         */
        public function send($from, $phone, $message);
    }
