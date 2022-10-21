<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    use KAASoft\Controller\ControllerBase;
    use KAASoft\Environment\SMTPSettings;
    use PHPMailer\PHPMailer;

    class Emailer extends PHPMailer {

        /**
         * @param string $sendMethod - "mail" or "smtp"
         * @param string $subject
         * @param string $body
         */
        public function __construct($sendMethod = "mail", $subject = null, $body = null) {
            parent::__construct();

            switch ($sendMethod) {
                case "smtp":
                    $this->IsSMTP();    // Set mailer to use SMTP
                    $smtpSetting = new SMTPSettings();
                    $smtpSetting->loadSettings();
                    if ($smtpSetting == null) {
                        ControllerBase::getLogger()->warn("SMTP settings doesn't set. PHP mail will be used.");
                        $this->IsMail();
                        break;
                    }

                    $this->setSMTPSetting($smtpSetting);
                    break;
                case "mail":            // Set mailer to use php mail
                default:
                    $this->IsMail();
                    break;
            }

            $this->WordWrap = 50;       // Set word wrap to 50 characters
            $this->IsHTML(true);        // Set email format to HTML

            $this->Subject = $subject;
            $this->Body = $body;
            $this->CharSet = 'utf8';
        }

        /**
         * @param $smtpSettings SMTPSettings
         */
        public function setSMTPSetting($smtpSettings) {
            $this->Host = $smtpSettings->getServer();
            $this->Port = $smtpSettings->getPort();
            $this->SMTPAuth = true;
            $this->Username = $smtpSettings->getUserName();
            $this->Password = $smtpSettings->getPassword();
            $this->SMTPSecure = strtolower($smtpSettings->getSecurity());
        }

        /**
         * @return true|string
         */
        public function sendEmail() {
            if (!$this->Send()) {
                return $this->ErrorInfo;
            }

            return true;
        }
    }