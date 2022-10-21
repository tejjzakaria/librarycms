<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    use Exception;
    use KAASoft\Controller\Admin\Image\ImageUploadBaseAction;
    use KAASoft\Controller\Controller;
    use RecursiveDirectoryIterator;
    use RecursiveIteratorIterator;

    /**
     * Class FileHelper
     * @package KAASoft\Util
     */
    class FileHelper {

        const CUSTOM_TEMPLATE_FOLDER_NAME        = "custom";
        const NOTIFICATIONS_TEMPLATE_FOLDER_NAME = "notifications";
        const THEMES_FOLDER_NAME                 = "themes";
        const INSTALLER_FOLDER_NAME              = "installer";


        public static function getDocumentRoot() {
            return realpath($_SERVER["DOCUMENT_ROOT"]);
        }

        public static function getSiteRoot() {

            return realpath(dirname(dirname(dirname(dirname(__FILE__)))));
        }

        public static function getVCardsLocation() {
            return FileHelper::getSiteRoot() . DIRECTORY_SEPARATOR . "vCards";
        }

        public static function getPrivateFolderLocation() {
            return realpath(FileHelper::getSiteRoot()) . DIRECTORY_SEPARATOR . "private";
        }

        public static function getKaaSoftFolderLocation() {
            return realpath(FileHelper::getPrivateFolderLocation()) . DIRECTORY_SEPARATOR . "KAASoft";
        }

        public static function getConfigFolderLocation() {
            return realpath(FileHelper::getKaaSoftFolderLocation()) . DIRECTORY_SEPARATOR . "Config";
        }

        public static function getSmartyWorkingLocation() {
            return realpath(FileHelper::getPrivateFolderLocation()) . DIRECTORY_SEPARATOR . "SmartyWorking";
        }

        public static function getLocaleLocation() {
            return FileHelper::getSiteRoot() . DIRECTORY_SEPARATOR . "locale";
        }

        public static function getInstallerLocation() {
            return FileHelper::getSiteRoot() . DIRECTORY_SEPARATOR . FileHelper::INSTALLER_FOLDER_NAME;
        }

        public static function getStorageRootLocation() {
            return FileHelper::getPrivateFolderLocation() . DIRECTORY_SEPARATOR . "data";
        }

        public static function getLogsLocation() {
            return FileHelper::getSiteRoot() . DIRECTORY_SEPARATOR . "logs";
        }

        public static function getLogFileName() {
            return FileHelper::getLogsLocation() . DIRECTORY_SEPARATOR . "KAASoft.log";
        }

        public static function getElectronicBookRootLocation() {
            return FileHelper::getSiteRoot() . DIRECTORY_SEPARATOR . "eBooks";
        }


        public static function getImageRootLocation() {
            return FileHelper::getSiteRoot() . DIRECTORY_SEPARATOR . "images";
        }

        public static function getPostImagesLocation() {
            return FileHelper::getImageRootLocation() . DIRECTORY_SEPARATOR . "post";
        }

        public static function getPageImagesLocation() {
            return FileHelper::getImageRootLocation() . DIRECTORY_SEPARATOR . "page";
        }

        public static function getPortfolioImagesLocation() {
            return FileHelper::getImageRootLocation() . DIRECTORY_SEPARATOR . "portfolio";
        }

        public static function getUserPhotoLocation() {
            return FileHelper::getImageRootLocation() . DIRECTORY_SEPARATOR . "userPhotos";
        }

        public static function getCoverLocation() {
            return FileHelper::getImageRootLocation() . DIRECTORY_SEPARATOR . "covers";
        }

        public static function getBookImageLocation() {
            return FileHelper::getImageRootLocation() . DIRECTORY_SEPARATOR . "book-images";
        }

        public static function getAuthorPhotoLocation() {
            return FileHelper::getImageRootLocation() . DIRECTORY_SEPARATOR . "authors";
        }

        public static function getSiteViewOptionFilesLocation() {
            return FileHelper::getImageRootLocation() . DIRECTORY_SEPARATOR . "site-view-options";
        }

        public static function getDefaultTemplateDirectory() {
            return FileHelper::getPrivateFolderLocation() . DIRECTORY_SEPARATOR . 'Templates';
        }

        public static function getCustomTemplateDirectory($themeName) {
            return FileHelper::getThemesDirectory() . DIRECTORY_SEPARATOR . $themeName . DIRECTORY_SEPARATOR . FileHelper::CUSTOM_TEMPLATE_FOLDER_NAME;
        }

        public static function getEmailNotificationTemplateDirectory($themeName) {
            return FileHelper::getActiveThemeDirectory($themeName) . DIRECTORY_SEPARATOR . FileHelper::NOTIFICATIONS_TEMPLATE_FOLDER_NAME;
        }

        public static function getThemesDirectory() {
            return FileHelper::getSiteRoot() . DIRECTORY_SEPARATOR . FileHelper::THEMES_FOLDER_NAME;
        }

        public static function getActiveThemeDirectory($themeName) {
            return FileHelper::getThemesDirectory() . DIRECTORY_SEPARATOR . $themeName;
        }

        /**
         * @param null $baseLocation
         * @return string
         */
        public static function getImageCurrentDayLocation($baseLocation = null) {
            if ($baseLocation == null) {
                $baseLocation = FileHelper::getImageRootLocation();
            }
            $year = date('Y');
            $month = date('F');
            $day = date('d');

            return $baseLocation . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . $day;
        }

        public static function getImageCurrentMonthLocation($baseLocation = null) {
            if ($baseLocation == null) {
                $baseLocation = FileHelper::getImageRootLocation();
            }
            $year = date('Y');
            $month = date('F');

            return $baseLocation . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $month;
        }

        public static function getDocumentLocation() {
            return FileHelper::getStorageRootLocation() . DIRECTORY_SEPARATOR . "documents";
        }

        /**
         * @param string $fileNameVariable
         * @return string|null
         */
        public static function getUploadedFileName($fileNameVariable = 'file') {
            $error = $_FILES[$fileNameVariable]["error"];
            $tempFileName = $_FILES[$fileNameVariable]["tmp_name"];
            if ($error == UPLOAD_ERR_OK) {
                return $tempFileName;
            }
            else {
                return null;
            }
        }

        /**
         * @param string $destinationFolder
         * @param string $fileNameVariable
         * @param array  $validFileExtensions
         * @return array
         */
        public static function saveUploadedFile($destinationFolder, $fileNameVariable = "file", $validFileExtensions = ImageUploadBaseAction::VALID_FILE_EXTENSIONS) {
            $errorMessage = null;
            if (FileHelper::hasUploadedFile($fileNameVariable)) {
                $error = $_FILES[$fileNameVariable]["error"];
                $tempFileName = $_FILES[$fileNameVariable]["tmp_name"];
                $name = basename($_FILES[$fileNameVariable]["name"]);

                $uploadedFileExtension = strrchr($_FILES[$fileNameVariable]["name"],
                                                 ".");

                // Check that the uploaded file is actually an image
                // and move it to the right folder if is.
                if (!in_array($uploadedFileExtension,
                              $validFileExtensions)
                ) {
                    $supportedFileTypes = "";
                    foreach ($validFileExtensions as $validFileExtension) {
                        $supportedFileTypes .= "*" . $validFileExtension . ", ";
                    }
                    $supportedFileTypes = substr($supportedFileTypes,
                                                 0,
                                                 strlen($supportedFileTypes) - 2);
                    $errorMessage = sprintf("Couldn't upload file. File type is not supported. Supported file types: %s",
                                            $supportedFileTypes);

                    return [ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ];
                }

                $newFilePath = self::getUniqueFileName($destinationFolder,
                                                       $name);

                if (FileHelper::createDirectory(dirname($newFilePath))) {
                    if ($error == UPLOAD_ERR_OK) {
                        if (is_uploaded_file($tempFileName)) {
                            if (!@move_uploaded_file($tempFileName,
                                                     $newFilePath)
                            ) {
                                $errorMessage .= sprintf(_("Could not move uploaded file '%s' to '%s'."),
                                                         $tempFileName,
                                                         $newFilePath);
                            }
                        }
                        else {
                            if (!@rename($tempFileName,
                                         $newFilePath)
                            ) {
                                $errorMessage .= sprintf(_("Could not move uploaded file '%s' to '%s'."),
                                                         $tempFileName,
                                                         $newFilePath);
                            }
                        }

                    }
                    else {
                        $errorMessage .= sprintf(_("Upload error on file '%s': [%s => %s]. Please try again."),
                                                 $name,
                                                 $error,
                                                 FileHelper::codeToMessage($error));
                    }
                }
                else {
                    $errorMessage .= sprintf(_("Couldn't create folder for uploaded documents '%s'."),
                                             dirname($newFilePath));
                }

                if ($errorMessage !== null) {
                    return [ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ];
                }

                return [ "uploadedFile" => $newFilePath ];
            }

            $errorMessage .= _("There is no file to save. Please make sure that file size is less than values in PHP variables 'upload_max_filesize' and 'post_max_size'.");

            // no file to save
            return [ Controller::AJAX_PARAM_NAME_ERROR => $errorMessage ];
        }

        /**
         * @param $filePath
         * @return string - file name without extension
         */
        public static function getFileName($filePath) {
            $pathInfo = pathinfo($filePath);

            return basename($filePath,
                            '.' . $pathInfo['extension']);
        }

        /**
         * @param $filePath
         * @return mixed - file extension
         */
        public static function getFileExtension($filePath) {
            $pathInfo = pathinfo($filePath);

            return $pathInfo['extension'];
        }

        /**
         * @param $code
         * @return string
         */
        private static function codeToMessage($code) {
            switch ($code) {
                case UPLOAD_ERR_INI_SIZE:
                    $message = _("The uploaded file exceeds the upload_max_filesize directive in php.ini");
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $message = _("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form");
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $message = _("The uploaded file was only partially uploaded");
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $message = _("No file was uploaded");
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $message = _("Missing a temporary folder");
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $message = _("Failed to write file to disk");
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $message = _("File upload stopped by extension");
                    break;

                default:
                    $message = _("Unknown uploading error");
                    break;
            }

            return $message;
        }

        /**
         * @param string $fileVarName
         * @return bool
         */
        public static function hasUploadedFile($fileVarName = 'file') {
            return isset( $_FILES ) && isset( $_FILES[$fileVarName] );
        }

        /**
         * @param $directoryPath
         * @return bool|string
         */
        public static function createDirectory($directoryPath) {
            $dirName = $directoryPath;
            if (!file_exists($dirName)) {
                if (!mkdir($dirName,
                           0777,
                           true)
                ) {
                    // Couldn't create directory:  . $dirName
                    return false;
                }

                // success
                return $dirName;
            }

            // folder is already exists
            return true;
        }

        /**
         * @param $filePath
         * @return bool
         */
        public static function deleteFile($filePath) {
            if (file_exists($filePath)) {
                return unlink($filePath);
            }

            return true;
        }

        /**
         * recursive delete folder
         * @param $dirPath
         * @return bool
         */
        public static function deleteFolder($dirPath) {
            $files = array_diff(scandir($dirPath),
                                [ '.',
                                  '..' ]);
            foreach ($files as $file) {
                ( is_dir("$dirPath/$file") ) ? FileHelper::deleteFolder("$dirPath/$file") : unlink("$dirPath/$file");
            }

            return rmdir($dirPath);
        }

        /**
         * @param $destinationDirectory
         * @param $baseFileName
         * @return string
         */
        public static function getUniqueFileName($destinationDirectory, $baseFileName) {
            $uniqueSuffix = "";
            $uniqueIndex = 0;

            $fullFilePath = $destinationDirectory . DIRECTORY_SEPARATOR . $baseFileName;
            $pathParts = pathinfo($fullFilePath);


            while (file_exists($uniqueFileName = $destinationDirectory . DIRECTORY_SEPARATOR . $pathParts['filename'] . $uniqueSuffix . '.' . $pathParts['extension'])) {
                $uniqueIndex++;
                $uniqueSuffix = "(" . $uniqueIndex . ")";
            }

            return $uniqueFileName;
        }

        /**
         * @param $destinationDirectory
         * @return string
         */
        public static function getUniqueFolderName($destinationDirectory) {
            $dirname = uniqid();
            $uniqueSuffix = "";
            $uniqueIndex = 0;

            $fullFilePath = $destinationDirectory . DIRECTORY_SEPARATOR . $dirname;

            while (file_exists($uniqueDirName = $fullFilePath . $uniqueSuffix)) {
                $uniqueIndex++;
                $uniqueSuffix = $uniqueIndex;
            }

            return $uniqueDirName;
        }

        /**
         * @return string
         */
        public static function getTempFileName() {
            return tempnam(sys_get_temp_dir(),
                           "tempFile");
        }

        public static function copyFile($srcPath, $destPath) {
            return @copy($srcPath,
                         $destPath);
        }

        /**
         * @param $from
         * @param $to
         * @return string
         */
        public static function getRelativePath($from, $to) {
            $from = ( $from );
            $to = ( $to );
            // some compatibility fixes for Windows paths
            $from = is_dir($from) ? rtrim($from,
                                          '\/') . '/' : $from;
            $to = is_dir($to) ? rtrim($to,
                                      '\/') . '/' : $to;
            $from = str_replace('\\',
                                '/',
                                $from);
            $to = str_replace('\\',
                              '/',
                              $to);

            $from = explode('/',
                            $from);
            $to = explode('/',
                          $to);
            $relPath = $to;

            foreach ($from as $depth => $dir) {
                // find first non-matching dir
                if ($dir === $to[$depth]) {
                    // ignore this directory
                    array_shift($relPath);
                }
                else {
                    // get number of remaining dirs to $from
                    $remaining = count($from) - $depth;
                    if ($remaining > 1) {
                        // add traversals up to first matching dir
                        $padLength = ( count($relPath) + $remaining - 1 ) * -1;
                        $relPath = array_pad($relPath,
                                             $padLength,
                                             '..');
                        break;
                    }
                    else {
                        $relPath[0] = '/' . $relPath[0];
                    }
                }
            }

            return implode('/',
                           $relPath);
        }

        /**
         * @param $content
         * @param $filePath
         * @param $fileName
         * @return bool
         */
        public static function saveGZipFile($content, $filePath, $fileName) {
            $file = false;
            try {
                if (!$file = gzopen($filePath . $fileName,
                                    'w')
                ) {
                    return false;
                }

                gzwrite($file,
                        $content);

                $result = gzclose($file);

                if ($result === true) {
                    $file = false;
                }

                return $result;
            }
            catch (Exception $e) {
                return false;
            }
            finally {
                if ($file != false) {
                    gzclose($file);
                }
            }
        }

        /**
         * @param $string
         * @param $filePath
         * @return bool
         */
        public static function saveStringToFile($string, $filePath) {
            $handle = false;
            try {
                if (!$handle = fopen($filePath,
                                     'w')
                ) {
                    return false;
                }

                if (!fwrite($handle,
                            $string)
                ) {
                    return false;
                }
            }
            catch (Exception $e) {
                return false;
            }
            finally {
                if ($handle != false) {
                    fclose($handle);
                }
            }

            return true;
        }

        /**
         * @param array $properties
         * @param       $filePath
         * @return bool
         */
        public static function saveIniFile(array $properties, $filePath) {

            $fileContent = "";
            foreach ($properties as $key => $value) {
                $fileContent .= $key . " = " . $value . "\n";
            }

            return FileHelper::saveStringToFile($fileContent,
                                                $filePath);

        }

        /**
         * @param $fileName
         * @return string|null
         */
        public static function getFileMimeType($fileName) {
            if (file_exists($fileName)) {
                $fileInfo = null;
                try {
                    $fileInfo = finfo_open(FILEINFO_MIME_TYPE); // возвращает mime-тип
                    $mimeType = finfo_file($fileInfo,
                                           $fileName);

                    return $mimeType !== false ? $mimeType : null;
                }
                catch (Exception $e) {
                    return null;
                }
                finally {
                    if ($fileInfo !== null) {
                        finfo_close($fileInfo);
                    }
                }
            }

            return null;
        }

        public static function getRecursiveFiles($dirName) {
            if (!file_exists($dirName)) {
                return null;
            }

            $recursiveIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirName));

            $files = [];

            foreach ($recursiveIterator as $file) {
                if ($file->isDir()) {
                    continue;
                }

                $files[] = $file->getPathname();

            }

            return $files;
        }

        public static function getSitePublicResourceLocation($absoluteFileName) {
            return FileHelper::getRelativePath(FileHelper::getDocumentRoot(),
                                               $absoluteFileName);
        }

        /**
         * @param     $fileName
         * @param int $mode
         * @return bool
         */
        public static function createFile($fileName, $mode = 0777) {
            $result = touch($fileName);
            if ($result === true) {
                return chmod($fileName,
                             $mode);
            }

            return false;
        }

        public static function getSiteRelativeLocation() {
            $docRootRelativePath = FileHelper::getRelativePath($_SERVER["DOCUMENT_ROOT"],
                                                               FileHelper::getSiteRoot());
            // remove trailing slash
            $docRootRelativePath = ( strcmp(substr($docRootRelativePath,
                                                   -1),
                                            "/") === 0 ? substr($docRootRelativePath,
                                                                0,
                                                                strlen($docRootRelativePath) - 1) : $docRootRelativePath );

            return $docRootRelativePath;
        }


    }