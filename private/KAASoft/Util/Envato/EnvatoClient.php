<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Created by KAA Soft.
     * Date: 2018-02-03
     */


    namespace KAASoft\Util\Envato;


    use DateTime;
    use KAASoft\Util\Helper;
    use KAASoft\Util\HTTP\HttpClient;
    use KAASoft\Util\ValidationHelper;

    /**
     * Class EnvatoClient
     * @package KAASoft\Util\Envato
     */
    class EnvatoClient extends HttpClient {
        const KAASOFT          = "KAASoft";
        const AUTHOR_SALES_URL = "https://api.envato.com/v3/market/author/sales?page=%d";
        const AUTHOR_SALE_URL  = "https://api.envato.com/v3/market/author/sale?code=%s";

        private $token = "76WJFTDY4BHdTqE6mJx0XWSv62SykiOE";

        /**
         * EnvatoClient constructor.
         */
        public function __construct() {
            $this->init();
        }

        protected function init() {
        }

        /**
         * @param $purchaseCode
         * @return bool|EnvatoLicense|null
         */
        public function verifyPurchaseCode($purchaseCode) {
            $url = sprintf(EnvatoClient::AUTHOR_SALE_URL,
                           $purchaseCode);

            $this->addHeader("Authorization",
                             "Bearer " . $this->token);
            $this->addHeader("User-Agent",
                             "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:41.0) Gecko/20100101 Firefox/41.0");
            $this->addHeader("timeout",
                             "20");

            if ($this->fetch($url)) {
                $jsonResponse = $this->getResults();
                $envatoResponse = json_decode($jsonResponse);


                if (isset( $envatoResponse->item->name )) {
                    // Purchase is VERIFIED
                    $envatoLicense = new EnvatoLicense();
                    $envatoLicense->setLicenseType(ValidationHelper::getString($envatoResponse->license));
                    $envatoLicense->setBuyer(ValidationHelper::getString($envatoResponse->buyer));
                    $envatoLicense->setItemId(ValidationHelper::getString($envatoResponse->item->id));
                    $envatoLicense->setItemName(ValidationHelper::getString($envatoResponse->item->name));
                    $envatoLicense->setLastValidation(ValidationHelper::getString(Helper::getDateTimeString()));
                    $envatoLicense->setPurchaseCode($purchaseCode);

                    $supportedUntilDateTime = new DateTime(ValidationHelper::getString($envatoResponse->supported_until));
                    $envatoLicense->setHash(Helper::encryptPassword(EnvatoClient::KAASOFT . $envatoLicense->getItemId() . $envatoLicense->getBuyer()));
                    $supportedUntil = $supportedUntilDateTime->format('Y-m-d H:i:s');
                    $envatoLicense->setSupportUntil($supportedUntil);

                    return $envatoLicense;
                }
                else {
                    // Purchase verification is FAILED
                    return null;
                }
            }
            else {
                // Purchase verification is FAILED due to network issue
                return false;
            }
        }
    }
