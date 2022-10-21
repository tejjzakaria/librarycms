<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
     */

    namespace SocialConnect\SMS\Provider;

    use RuntimeException;
    use SocialConnect\Common\Http\Client\Client;
    use SocialConnect\Common\Http\Client\ClientInterface;
    use SocialConnect\Common\HttpClient;
    use SocialConnect\SMS\Entity\SmsResult;
    use SocialConnect\SMS\Exception\InvalidConfigParameter;
    use SocialConnect\SMS\Exception\ResponseErrorException;

    class Twilio implements ProviderInterface {
        use HttpClient;

        /**
         * @var array
         */
        protected $configuration;

        /**
         * @var string
         */
        private $baseUrl = "https://api.twilio.com/2010-04-01/";

        public function __construct(array $configuration, ClientInterface $httpClient) {
            $this->configuration = $configuration;
            $this->httpClient = $httpClient;

            if (empty( $this->configuration["accountSID"] )) {
                throw new InvalidConfigParameter("accountSID cannot be empty!");
            }

            if (empty( $this->configuration["authToken"] )) {
                throw new InvalidConfigParameter("authToken cannot be empty!");
            }
        }

        /**
         * @param        $uri
         * @param array  $parameters
         * @param string $method
         * @return mixed
         * @throws ResponseErrorException
         */
        public function request($uri, array $parameters = [], $method = Client::GET) {
            $response = $this->httpClient->request($this->baseUrl . $uri,
                                                   $parameters,
                                                   $method,
                                                   [ "Authorization" => "Basic " . base64_encode($this->configuration["accountSID"] . ":" . $this->configuration["authToken"]) ]);

            if ($response->isSuccess()) {
                return json_decode($response->getBody());
            }

            $result = $response->json();
            if ($result && $result->code) {
                throw new ResponseErrorException($result->message,
                                                 $result->code);
            }

            throw new ResponseErrorException("Unknown exception");
        }

        /**
         * @return float
         */
        public function getBalance() {
            throw new RuntimeException("Twilio doesn't support get balance");
        }

        /**
         * @param string     $from
         * @param int|string $phone
         * @param string     $message
         * @return SmsResult|bool
         */
        public function send($from, $phone, $message) {
            $response = $this->request(sprintf(_("Accounts/%s/Messages.json"),
                                               $this->configuration["accountSID"]),
                                       [ "From" => $from,
                                         "Body" => $message,
                                         "To"   => $phone ],
                                       Client::POST);

            if ($response) {
                return new SmsResult($response->sid);
            }

            return false;
        }
    }
