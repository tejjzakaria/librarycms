<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util\CSV;

    use KAASoft\Util\CustomTemplate;
    use KAASoft\Util\FileHelper;

    /**
     * Class CustomTemplateReader
     * @package KAASoft\Util\CSV
     */
    class CustomTemplateReader extends CSVReader {

        const CONFIG_FILE_NAME = "custom.csv";

        /**
         * CustomTemplateReader constructor.
         * @param string $activeThemeName
         */
        public function __construct($activeThemeName) {
            parent::__construct(FileHelper::getCustomTemplateDirectory($activeThemeName) . DIRECTORY_SEPARATOR . CustomTemplateReader::CONFIG_FILE_NAME,
                                true,
                                ",");
        }

        /**
         * @param null $groupName
         * @return array
         */
        public function readConfigFile($groupName = null) {
            $customTemplates = [];

            do {
                $row = $this->readLine();

                if ($row === false) {
                    break; // EOF
                }
                if ($row === null) {
                    break;// skip empty line
                }

                if (( $groupName != null and !empty( $groupName ) && strcmp($groupName,
                                                                            $row["Group"]) === 0 ) || ( $groupName === null )
                ) {
                    $customTemplate = new CustomTemplate($row["Group"],
                                                         $row["FileName"],
                                                         $row["DisplayName"]);
                    $customTemplates [] = $customTemplate;
                }
            } while (1);

            return $customTemplates;
        }
    }

    /*
        $parser = new CustomTemplateReader();
        $parser->readConfigFile(CustomTemplate::PAGE_GROUP);*/