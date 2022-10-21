<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Controller\Admin\Util;


    use Exception;
    use KAASoft\Controller\Admin\Book\BookDatabaseHelper;
    use KAASoft\Controller\Admin\User\UserDatabaseHelper;
    use KAASoft\Controller\AdminActionBase;
    use KAASoft\Controller\Controller;
    use KAASoft\Database\Entity\General\Book;
    use KAASoft\Database\Entity\General\User;
    use KAASoft\Environment\EmailSettings;
    use KAASoft\Environment\SmsSettings;
    use KAASoft\Util\DisplaySwitch;
    use KAASoft\Util\Helper;
    use KAASoft\Util\ValidationHelper;
    use SocialConnect\Common\Http\Client\Curl;
    use SocialConnect\SMS\Entity\NexmoSmsResult;
    use SocialConnect\SMS\Entity\TextLocalResult;
    use SocialConnect\SMS\Provider\NexmoSmsStatusCode;
    use SocialConnect\SMS\ProviderFactory;

    /**
     * Class SmsSendAction
     * @package KAASoft\Controller\Admin\Util
     */
    class SmsSendAction extends AdminActionBase {
        /**
         * SmsSendAction constructor.
         * @param null $activeRoute
         */
        public function __construct($activeRoute) {
            parent::__construct($activeRoute);
        }

        public static function getSmsError($errorCode, $errorMessage) {
            return sprintf(_("Couldn't send SMS. Error code: '%d'. Error description: '%s'"),
                           $errorCode,
                           $errorMessage);
        }

        /**
         * @param array $args
         * @return DisplaySwitch
         * @throws \Exception
         */
        protected function action($args) {
            if (Helper::isAjaxRequest()) {
                $userId = $args["userId"];
                $smsSettings = new SmsSettings();
                $smsSettings->loadSettings();

                if (Helper::isPostRequest()) {
                    $userHelper = new UserDatabaseHelper($this);
                    $user = $userHelper->getUser($userId);

                    if ($user === null) {
                        $message = sprintf("There is no requested user(%d) in database.",
                                           $userId);
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $message ]);

                        return new DisplaySwitch();
                    }

                    if (ValidationHelper::isEmpty($user->getPhone())) {
                        $message = sprintf("%s %s doesn't have phone in profile.",
                                           $user->getFirstName(),
                                           $user->getLastName());
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => $message ]);

                        return new DisplaySwitch();
                    }

                    $phone = $user->getPhone();

                    $smsContent = ValidationHelper::getString($_POST["content"]);
                    $bookId = ValidationHelper::getNullableInt($_POST["bookId"]);
                    $sender = ValidationHelper::getString($_POST["sender"]);

                    $shortCodes = $this->getStaticShortCodes();
                    $shortCodes = array_merge($shortCodes,
                                              $this->getCustomStaticCodes($user,
                                                                          $bookId));

                    $smsContent = $this->replaceShortCodes($smsContent,
                                                           $shortCodes);

                    // $smsContent = "Bla-bla-bla";

                    $activeProviderName = $smsSettings->getActiveProvider();
                    if ($activeProviderName !== null) {
                        $providerConfig = $smsSettings->getConfig();
                        $service = new ProviderFactory($providerConfig,
                                                       new Curl());

                        $provider = $service->factory($activeProviderName);
                        $result = null;
                        try {
                            $result = $provider->send($sender,
                                                      $phone,
                                                      $smsContent);
                        }
                        catch (Exception $e) {
                            $result = null;
                            Helper::printAsJSON([ Controller::AJAX_PARAM_NAME_ERROR => SmsSendAction::getSmsError($e->getCode(),
                                                                                                                  $e->getMessage()) ]);
                            exit( 0 );
                        }

                        if ($result !== null and $result !== false and $result->isSuccess()) {
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_SUCCESS => _("SMS is sent successfully.") ]);
                        }
                        else {
                            $errorCode = -1;
                            $errorDescription = "";
                            if ($result instanceof NexmoSmsResult) {
                                $errorCode = $result->getStatus();
                                $errorDescription = NexmoSmsStatusCode::getNexmoStatusCodeDescription($errorCode);
                            }
                            elseif ($result instanceof TextLocalResult) {
                                $errorCode = $result->getErrorCode();
                                $errorDescription = $result->getErrorMessage();
                            }
                            $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => SmsSendAction::getSmsError($errorCode,
                                                                                                                            $errorDescription) ]);
                        }
                    }
                    else {
                        $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("Please select active SMS provider.") ]);
                    }

                }
                else {
                    $this->putArrayToAjaxResponse([ "smsTemplate" => $smsSettings->getDefaultMessage(),
                                                    "sender"      => $smsSettings->getSender() ]);
                }
            }
            else {
                $this->putArrayToAjaxResponse([ Controller::AJAX_PARAM_NAME_ERROR => _("AJAX request is required only.") ]);
            }

            return new DisplaySwitch();
        }

        /**
         * @param      $user User
         * @param null $bookId
         * @return array
         */
        private function getCustomStaticCodes($user, $bookId = null) {
            $result = [];

            $bookHelper = new BookDatabaseHelper($this);

            if ($user !== null and $user instanceof User) {
                $result[EmailSettings::SHORT_CODE_USER_FIRST_NAME] = $user->getFirstName();
                $result[EmailSettings::SHORT_CODE_USER_LAST_NAME] = $user->getLastName();

                $books = $bookHelper->getUserIssuedBooks($user->getId());
                $bookListString = "";

                foreach ($books as $book) {
                    if ($book instanceof Book) {
                        $bookListString .= "'" . $book->getTitle() . "', ";
                    }
                }

                if (strlen($bookListString) > 1) {
                    $bookListString = substr($bookListString,
                                             0,
                                             strlen($bookListString) - 2);
                }

                $result[EmailSettings::SHORT_CODE_BOOKS] = $bookListString;
            }

            if ($bookId !== null) {
                $book = $bookHelper->getSimpleBook($bookId);

                $result[EmailSettings::SHORT_CODE_BOOK] = "'" . $book->getTitle() . "'";
            }

            return $result;
        }
    }