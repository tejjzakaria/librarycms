<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Environment;

    use Exception;
    use KAASoft\Util\FileHelper;

    abstract class AbstractSettings implements Settings {
        public function loadSettings() {
            try {
                $fileName = $this->getConfigFileName();
                if (file_exists($fileName)) {

                    $settingsArray = json_decode(file_get_contents($fileName),
                                                 true);
                    $this->copySettings($settingsArray);

                    return;
                }
            }
            catch (Exception $e) {
                $this->setDefaultSettings();

                return;
            }
            $this->setDefaultSettings();
        }

        public function saveSettings() {
            $fileName = $this->getConfigFileName();
            if (file_exists($fileName) and !@rename($fileName,
                                                    $fileName . ".bak")
            ) {
                throw new Exception(sprintf(_("Couldn't create backup of file \"%s\"."),
                                            basename($fileName)));
            }
            if (!FileHelper::saveStringToFile(json_encode($this,
                                                          JSON_PRETTY_PRINT),
                                              $fileName)
            ) {
                throw new  Exception(sprintf(_("Couldn't create config file \"%s\"."),
                                             basename($fileName)));
            }
        }

        /**
         * copy data from assoc array to object fields
         * @param $settings mixed
         */
        public abstract function copySettings($settings);

        /**
         * copy data from another Settings object
         * @param $settings mixed
         */
        public abstract function cloneSettings($settings);
    }