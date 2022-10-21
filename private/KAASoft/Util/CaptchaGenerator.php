<?php
    /**
     * Copyright (c) 2015 - 2017 by KAA Soft. All rights reserved.
     */

    namespace KAASoft\Util;

    /**
     * Class CaptchaGenerator
     * @package KAASoft\Util
     */
    class CaptchaGenerator {

        public static $LETTERS = [ 'a',
                                   'b',
                                   'c',
                                   'd',
                                   'e',
                                   'f',
                                   'g',
                                   'h',
                                   'j',
                                   'k',
                                   'm',
                                   'n',
                                   'p',
                                   'q',
                                   'r',
                                   's',
                                   't',
                                   'u',
                                   'v',
                                   'w',
                                   'x',
                                   'y',
                                   'z',
                                   '2',
                                   '3',
                                   '4',
                                   '5',
                                   '6',
                                   '7',
                                   '8',
                                   '9' ];
        public static $FIGURES = [ '50',
                                   '70',
                                   '90',
                                   '110',
                                   '130',
                                   '150',
                                   '170',
                                   '190',
                                   '210' ];

        private $imageType;                                     //Возможные форматы: GIF, JPEG, PNG
        private $imageWidth;                                    //Ширина изображения
        private $imageHeight;                                   //Высота изображения
        private $numOfCaptchaSymbols;                           //Количество символов, которые нужно набрать
        private $pathToFonts;                                   //Путь к шрифтам

        /**
         * @param string $imageType
         * @param int    $imageWidth
         * @param int    $imageHeight
         * @param int    $numOfCaptchaSymbols
         * @param string $pathToFonts
         */
        function __construct($imageType = 'PNG', $imageWidth = 140, $imageHeight = 90, $numOfCaptchaSymbols = 4, $pathToFonts = "/fonts") {
            if ($imageType == 'GIF' || $imageType == 'JPEG' || $imageType == 'PNG') {
                $this->imageType = $imageType;
            }
            if (is_numeric($imageWidth) && $imageWidth > 0 && $imageWidth < 500) {
                $this->imageWidth = $imageWidth;
            }
            if (is_numeric($imageHeight) && $imageHeight > 0 && $imageHeight < 500) {
                $this->imageHeight = $imageHeight;
            }
            if (is_numeric($numOfCaptchaSymbols) && $numOfCaptchaSymbols > 2 && $numOfCaptchaSymbols < 10) {
                $this->numOfCaptchaSymbols = $numOfCaptchaSymbols;
            }
            $this->pathToFonts = $pathToFonts;
        }

        /**
         * @param $fileName
         * @return Captcha
         */
        public function getCaptcha($fileName = null) {

            $fontSize = intval($this->imageHeight / ( ( $this->imageHeight / $this->imageWidth ) * 5 ));
            $numChars = intval(( $this->imageWidth * $this->imageHeight ) / 150);

            $CODE = [];

            //Создаем полотно
            $src = imagecreatetruecolor($this->imageWidth,
                                        $this->imageHeight);

            //Заливаем фон
            $fon = imagecolorallocate($src,
                                      255,
                                      255,
                                      255);
            imagefill($src,
                      0,
                      0,
                      $fon);

            //Загрузка шрифтов
            $FONTS = [];
            if (is_dir($this->pathToFonts) && file_exists($this->pathToFonts)) {
                $dir = opendir($this->pathToFonts);
                while ($fontName = readdir($dir)) {
                    if ($fontName != "." && $fontName != "..") {
                        if (strtolower(strrchr($fontName,
                                               '.')) == '.ttf'
                        ) {
                            $FONTS[] = $this->pathToFonts . $fontName;
                        }
                    }
                }
                closedir($dir);
            }

            //Если есть шрифты
            if (sizeof($FONTS) > 0) {
                //Заливаем полотно буковками
                for ($i = 0; $i < $numChars; $i++) {
                    $h = 1;
                    $color = imagecolorallocatealpha($src,
                                                     rand(0,
                                                          255),
                                                     rand(0,
                                                          255),
                                                     rand(0,
                                                          255),
                                                     100);
                    $font = $FONTS[rand(0,
                                        sizeof($FONTS) - 1)];
                    $letter = CaptchaGenerator::$LETTERS[rand(0,
                                                              sizeof(CaptchaGenerator::$LETTERS) - 1)];
                    $size = rand($fontSize - 2,
                                 $fontSize + 2);
                    $angle = rand(0,
                                  60);
                    if ($h == rand(1,
                                   2)
                    ) {
                        $angle = rand(360,
                                      300);
                    }
                    //Пишем
                    imagettftext($src,
                                 $size,
                                 $angle,
                                 rand($this->imageWidth * 0.1,
                                      $this->imageWidth - $this->imageWidth * 0.1),
                                 rand($this->imageHeight * 0.2,
                                      $this->imageHeight),
                                 $color,
                                 $font,
                                 $letter);
                }

                //Заливаем основными буковками
                for ($i = 0; $i < $this->numOfCaptchaSymbols; $i++) {
                    //Ориентир
                    $h = 1;
                    //Рисуем
                    $color = imagecolorallocatealpha($src,
                                                     CaptchaGenerator::$FIGURES[rand(0,
                                                                                     sizeof(CaptchaGenerator::$FIGURES) - 1)],
                                                     CaptchaGenerator::$FIGURES[rand(0,
                                                                                     sizeof(CaptchaGenerator::$FIGURES) - 1)],
                                                     CaptchaGenerator::$FIGURES[rand(0,
                                                                                     sizeof(CaptchaGenerator::$FIGURES) - 1)],
                                                     rand(10,
                                                          30));
                    $font = $FONTS[rand(0,
                                        sizeof($FONTS) - 1)];
                    $letter = CaptchaGenerator::$LETTERS[rand(0,
                                                              sizeof(CaptchaGenerator::$LETTERS) - 1)];
                    $size = rand($fontSize * 2.1 - 1,
                                 $fontSize * 2.1 + 1);
                    $x = ( empty( $x ) ) ? $this->imageWidth * 0.08 : $x + ( $this->imageWidth * 0.8 ) / $this->numOfCaptchaSymbols + rand(0,
                                                                                                                                           $this->imageWidth * 0.01);
                    $y = ( $h == rand(1,
                                      2) ) ? ( ( $this->imageHeight * 1.15 * 3 ) / 4 ) + rand(0,
                                                                                              $this->imageHeight * 0.02) : ( ( $this->imageHeight * 1.15 * 3 ) / 4 ) - rand(0,
                                                                                                                                                                            $this->imageHeight * 0.02);
                    $angle = rand(5,
                                  20);
                    //Запоминаем
                    $CODE[] = $letter;
                    if ($h == rand(0,
                                   10)
                    ) {
                        $letter = strtoupper($letter);
                    }
                    if ($h == rand(1,
                                   2)
                    ) {
                        $angle = rand(355,
                                      340);
                    }
                    //Пишем
                    imagettftext($src,
                                 $size,
                                 $angle,
                                 $x,
                                 $y,
                                 $color,
                                 $font,
                                 $letter);
                }

                //Если нет шрифтов
            }
            else {
                //Заливаем точками
                for ($x = 0; $x < $this->imageWidth; $x++) {
                    for ($i = 0; $i < ( $this->imageHeight * $this->imageWidth ) / 1000; $i++) {
                        $color = imagecolorallocatealpha($src,
                                                         CaptchaGenerator::$FIGURES[rand(0,
                                                                                         sizeof(CaptchaGenerator::$FIGURES) - 1)],
                                                         CaptchaGenerator::$FIGURES[rand(0,
                                                                                         sizeof(CaptchaGenerator::$FIGURES) - 1)],
                                                         CaptchaGenerator::$FIGURES[rand(0,
                                                                                         sizeof(CaptchaGenerator::$FIGURES) - 1)],
                                                         rand(10,
                                                              30));
                        imagesetpixel($src,
                                      rand(0,
                                           $this->imageWidth),
                                      rand(0,
                                           $this->imageHeight),
                                      $color);
                    }
                }
                unset( $x, $y );
                //Заливаем основными буковками
                for ($i = 0; $i < $this->numOfCaptchaSymbols; $i++) {
                    //Ориентир
                    $h = 1;
                    //Рисуем
                    $color = imagecolorallocatealpha($src,
                                                     CaptchaGenerator:: $FIGURES[rand(0,
                                                                                      sizeof(CaptchaGenerator::$FIGURES) - 1)],
                                                     CaptchaGenerator:: $FIGURES[rand(0,
                                                                                      sizeof(CaptchaGenerator::$FIGURES) - 1)],
                                                     CaptchaGenerator::$FIGURES[rand(0,
                                                                                     sizeof(CaptchaGenerator::$FIGURES) - 1)],
                                                     rand(10,
                                                          30));
                    $letter = CaptchaGenerator::$LETTERS[rand(0,
                                                              sizeof(CaptchaGenerator::$LETTERS) - 1)];
                    $x = ( empty( $x ) ) ? $this->imageWidth * 0.08 : $x + ( $this->imageWidth * 0.8 ) / $this->numOfCaptchaSymbols + rand(0,
                                                                                                                                           $this->imageWidth * 0.01);
                    $y = ( $h == rand(1,
                                      2) ) ? ( ( $this->imageHeight * 1 ) / 4 ) + rand(0,
                                                                                       $this->imageHeight * 0.1) : ( ( $this->imageHeight * 1 ) / 4 ) - rand(0,
                                                                                                                                                             $this->imageHeight * 0.1);
                    //Запоминаем
                    $CODE[] = $letter;
                    if ($h == rand(0,
                                   10)
                    ) {
                        $letter = strtoupper($letter);
                    }
                    //Пишем
                    imagestring($src,
                                5,
                                $x,
                                $y,
                                $letter,
                                $color);
                }
            }

            $captcha = new Captcha();
            //Получаем код
            $captcha->setCode(implode("",
                                      $CODE));

            if ($fileName != null) {
                //Печать
                if ($this->imageType == 'PNG') {
                    imagepng($src,
                             $fileName);
                }
                elseif ($this->imageType == 'JPEG') {
                    imagejpeg($src,
                              $fileName);
                }
                else {
                    imagegif($src,
                             $fileName);
                }
            }
            imagedestroy($src);

            $captcha->setImageFile($fileName);

            return $captcha;
        }
    }

    /*
    // Sample of usage
    $gen = new CaptchaGenerator();
    $gen->getCaptcha("L:\\captcha.png");
    */