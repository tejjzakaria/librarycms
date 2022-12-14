<?php
    /**
     * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
     */

    namespace SocialConnect\SMS\Provider;

    use SocialConnect\Common\Http\Client\Client;
    use SocialConnect\Common\Http\Client\ClientInterface;
    use SocialConnect\Common\HttpClient;
    use SocialConnect\SMS\Entity\NexmoSmsResult;
    use SocialConnect\SMS\Exception\LogicException;
    use SocialConnect\SMS\Exception\ResponseErrorException;

    class Nexmo implements ProviderInterface {
        use HttpClient;

        /**
         * @var array
         */
        protected $configuration;

        /**
         * @var string
         */
        private $baseUrl = 'https://rest.nexmo.com/';

        public function __construct(array $configuration, ClientInterface $httpClient) {
            $this->configuration = $configuration;
            $this->httpClient = $httpClient;
        }

        /**
         * @param string $uri
         * @param array  $parameters
         * @param string $method
         * @return bool|mixed
         * @throws \SocialConnect\SMS\Exception\ResponseErrorException
         */
        public function request($uri, array $parameters = [], $method = Client::GET) {
            $baseParameters = array( 'api_key'    => $this->configuration['apiKey'],
                                     'api_secret' => $this->configuration['apiSecret'] );

            $response = $this->httpClient->request($this->baseUrl . $uri,
                                                   array_merge($baseParameters,
                                                               $parameters),
                                                   $method,
                                                   [],
                                                   []);

            if ($response->isSuccess()) {
                return $response->json();
            }

            throw new ResponseErrorException($response->getBody(),
                                             $response->getStatusCode());
        }

        /**
         * @return float
         * @throws \SocialConnect\SMS\Exception\ResponseErrorException
         * @throws \SocialConnect\SMS\Exception\LogicException
         */
        public function getBalance() {
            $result = $this->request('account/get-balance');
            if (!$result) {
                throw new LogicException('Wrong response on account/get-balance');
            }

            return (float)$result->value;
        }

        /**
         * @param string     $from
         * @param int|string $phone
         * @param string     $message
         * @return bool|mixed
         * @throws \SocialConnect\SMS\Exception\ResponseErrorException
         * @throws \SocialConnect\SMS\Exception\LogicException
         */
        public function send($from, $phone, $message) {
            $response = $this->request('sms/json',
                                       [ 'from' => $from,
                                         'text' => $message,
                                         'to'   => $phone,
                                         'type' => 'unicode' ],
                                       Client::GET);

            if ($response) {
                $result = current($response->messages);

                $messageId = null;
                if (isset( $result->{"message-id"} )) {
                    $messageId = $result->{"message-id"};
                }

                return new NexmoSmsResult($messageId,
                                          $result->status);
            }

            throw new LogicException('Response is null');
        }
    }
