<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
     */

    namespace SocialConnect\SMS\Provider;

    use KAASoft\Util\ValidationHelper;
    use SocialConnect\Common\Http\Client\Client;
    use SocialConnect\Common\Http\Client\ClientInterface;
    use SocialConnect\Common\HttpClient;
    use SocialConnect\SMS\Entity\TextLocalResult;
    use SocialConnect\SMS\Exception\InvalidConfigParameter;

    class TextLocal implements ProviderInterface {
        use HttpClient;

        /**
         * @var array
         */
        protected $configuration;


        /**
         * @var string
         */
        private $baseUrl = 'https://api.txtlocal.com/';

        public function __construct(array $configuration, ClientInterface $httpClient) {
            $this->configuration = $configuration;
            $this->httpClient = $httpClient;

            if (empty( $this->configuration['apiKey'] )) {
                throw new InvalidConfigParameter('apiKey cannot be empty!');
            }
            if (empty( $this->configuration['sender'] )) {
                throw new InvalidConfigParameter('sender cannot be empty!');
            }
        }

        /**
         * @param string $uri
         * @param array  $parameters
         * @return bool|string
         */
        public function request($uri, array $parameters = []) {
            $baseParameters = [ 'apiKey' => $this->configuration['apiKey'],
                                "format" => "json" ];

            $response = $this->httpClient->request($this->baseUrl . $uri,
                                                   array_merge($baseParameters,
                                                               $parameters),
                                                   Client::POST,
                                                   [],
                                                   []);
            if ($response->isSuccess()) {
                return json_decode($response->getBody(),
                                   true);

            }

            return false;
        }

        /**
         * @return float
         */
        public function getBalance() {
            $responseArray = $this->request('balance');

            if (!ValidationHelper::isEmpty($responseArray["status"]) and $responseArray["status"] !== false and strcmp(ValidationHelper::getString($responseArray["status"]),
                                                                                                                       "success") == 0
            ) {
                if (!ValidationHelper::isArrayEmpty($responseArray["balance"])) {
                    $balances = ValidationHelper::getArray($responseArray["balance"]);

                    if (!ValidationHelper::isEmpty($balances["sms"])) {
                        return ValidationHelper::getFloat($balances["sms"]);
                    }
                }

            }

            return 0;
        }

        /**
         * @param string     $from
         * @param int|string $phones
         * @param string     $message
         * @return bool|TextLocalResult
         */
        public function send($from, $phones, $message) {
            $responseArray = $this->request('send',
                                            [ 'numbers' => ( is_array($phones) ? implode(',',
                                                                                         $phones) : $phones ),
                                              'message' => $message,
                                              'sender'  => $from ]);

            if (!ValidationHelper::isEmpty($responseArray["status"]) and $responseArray["status"] !== false and strcmp(ValidationHelper::getString($responseArray["status"]),
                                                                                                                       "success") == 0
            ) {
                return new TextLocalResult($responseArray);
            }
            elseif (!ValidationHelper::isArrayEmpty($responseArray["errors"])) {
                $smsResult = new TextLocalResult($responseArray);
                $smsResult->setIsSuccess(false);
                $errors = $responseArray["errors"];
                if (isset( $errors[0]["code"] )) {
                    $smsResult->setErrorCode(ValidationHelper::getInt($errors[0]["code"]));
                }
                if (!ValidationHelper::isEmpty($errors[0]["message"])) {
                    $smsResult->setErrorMessage(ValidationHelper::getString($errors[0]["message"]));
                }

                return $smsResult;
            }

            return false;
        }
    }
