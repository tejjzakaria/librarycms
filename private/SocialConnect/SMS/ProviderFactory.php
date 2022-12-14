<?php
    /**
     * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
     */

    namespace SocialConnect\SMS;

    use KAASoft\Environment\SmsSettings;
    use RuntimeException;
    use SocialConnect\Common\Http\Client\ClientInterface;
    use SocialConnect\Common\HttpClient;
    use SocialConnect\SMS\Provider\ProviderInterface;

    class ProviderFactory {
        use HttpClient;

        /**
         * @var array
         */
        protected $configuration;

        /**
         * @var array
         */
        protected $map = [ SmsSettings::NEXMO_NAME      => "Nexmo",
                           SmsSettings::TWILIO_NAME     => "Twilio",
                           SmsSettings::TEXT_LOCAL_NAME => "TextLocal" ];

        /**
         * @param array                $configuration
         * @param ClientInterface|null $httpClient
         */
        public function __construct(array $configuration, ClientInterface $httpClient = null) {
            $this->configuration = $configuration;
            $this->httpClient = $httpClient;
        }

        /**
         * @param string $name
         * @return array
         */
        public function getProviderConfig($name) {
            if (isset( $this->configuration["provider"][$name] )) {
                return $this->configuration["provider"][$name];
            }

            throw new RuntimeException("No config for provider " . $name);
        }

        /**
         * @param string $name
         * @return ProviderInterface
         */
        public function factory($name) {
            $className = "SocialConnect\\SMS\\Provider\\" . $this->map[$name];

            return new $className($this->getProviderConfig($name),
                                  $this->httpClient);
        }
    }
