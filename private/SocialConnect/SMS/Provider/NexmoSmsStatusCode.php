<?php
    /**
     * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
     */

    namespace SocialConnect\SMS\Provider;

    final class NexmoSmsStatusCode {
        /**
         * The message was successfully accepted for delivery by Nexmo.
         * @var int
         */
        const STATUS_SUCCESS = 0;

        /**
         * You have exceeded the submission capacity allowed on this account. Please wait and retry
         * @var int
         */
        const STATUS_THROTTLED = 1;

        /**
         * Your request is incomplete and missing some mandatory parameters.
         * @var int
         */
        const STATUS_MISSING_PARAMS = 2;

        /**
         * The value of one or more parameters is invalid.
         * @var int
         */
        const STATUS_INVALID_PARAMS = 3;

        /**
         * The api_key / api_secret you supplied is either invalid or disabled.
         * @var int
         */
        const STATUS_INVALID_CREDENTIALS = 4;

        /**
         * @var int
         */
        const STATUS_INTERNAL_ERROR = 5;

        /**
         * @var int
         */
        const STATUS_INVALID_MESSAGE = 6;

        /**
         * @var int
         */
        const STATUS_NUMBER_BARRED = 6;

        /**
         * @var int
         */
        const STATUS_PARTNER_ACCOUNT_BARRED = 7;

        /**
         * @var int
         */
        const STATUS_PARTNER_QUOTA_EXCEEDED = 8;

        /**
         * @var int
         */
        const STATUS_ACCOUNT_NOT_ENABLED_FOR_REST = 9;

        /**
         * @var int
         */
        const STATUS_MESSAGE_TOO_LONG = 11;

        /**
         * @var int
         */
        const STATUS_COMMUNICATION_FAILED = 13;

        /**
         * @var int
         */
        const STATUS_INVALID_SIGNATURE = 14;

        /**
         * @var int
         */
        const STATUS_INVALID_SENDER_ADDRESS = 15;

        /**
         * @var int
         */
        const STATUS_INVALID_TTL = 16;

        /**
         * @var int
         */
        const STATUS_FACILITY_NOT_ALLOWED = 19;

        /**
         * @var int
         */
        const STATUS_INVALID_MESSAGE_CLASS = 20;

        /**
         * @var int
         */
        const STATUS_BAD_CALLBACK_URL = 23;

        /**
         * The phone number you set in to is not in your pre-approved destination list. To send messages to this phone number, add it using Nexmo Dashboard.
         * @var int
         */
        const STATUS_NON_WHITE_LISTED_DESTINATION = 29;

        public static function getNexmoStatusCodeDescription($statusCode) {
            $descriptions = NexmoSmsStatusCode::getNexmoStatusCodeDescriptions();
            if (isset( $descriptions[$statusCode] )) {
                return $descriptions[$statusCode];
            }

            return "";
        }

        public static function getNexmoStatusCodeDescriptions() {
            return [ 1  => _("You have exceeded the submission capacity allowed on this account. Please wait and retry."),
                     2  => _("Your request is incomplete and missing some mandatory parameters."),
                     3  => _("The value of one or more parameters is invalid."),
                     4  => _("The api_key / api_secret you supplied is either invalid or disabled."),
                     5  => _("There was an error processing your request in the Platform."),
                     6  => _("The Platform was unable to process your request. For example, due to an unrecognised prefix for the phone number."),
                     7  => _("The number you are trying to submit to is blacklisted and may not receive messages."),
                     8  => _("The api_key you supplied is for an account that has been barred from submitting messages."),
                     9  => _("Your pre-paid account does not have sufficient credit to process this message."),
                     11 => _("This account is not provisioned for REST submission, you should use SMPP instead."),
                     12 => _("The length of udh and body was greater than 140 octets for a binary type SMS request."),
                     13 => _("Message was not submitted because there was a communication failure."),
                     14 => _("Message was not submitted due to a verification failure in the submitted signature."),
                     15 => _("Due to local regulations, the SenderID you set in from in the request was not accepted. Please check the Global messaging section."),
                     16 => _("The value of ttl in your request was invalid."),
                     19 => _("Your request makes use of a facility that is not enabled on your account."),
                     20 => _("The value of message-class in your request was out of range. See https://en.wikipedia.org/wiki/Data_Coding_Scheme."),
                     23 => _("You did not include https in the URL you set in callback."),
                     29 => _("The phone number you set in to is not in your pre-approved destination list. To send messages to this phone number, add it using Dashboard."),
                     34 => _("The phone number you supplied in the to parameter of your request was either missing or invalid."), ];
        }
    }
