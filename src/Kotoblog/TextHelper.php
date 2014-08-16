<?php

namespace Kotoblog;

use TextLanguageDetect\TextLanguageDetect;

class TextHelper
{
    protected $app;

    /** @var $languageDetector TextLanguageDetect */
    protected $languageDetector;

    public function __construct($app)
    {
        $this->app = $app;
        $this->languageDetector = $app['language_detector'];
    }

    public function normalize($word)
    {
        $language = $this->languageDetector->detectSimple($word);

        if ($language) {
            $normalizedWord = $this->app['morphology_provider.'.$language]->lemmatize($word);

            return $normalizedWord[0];
        }

        return false;
    }

    public function splitString($string)
    {
        $string = mb_strtoupper (str_ireplace("ё", "е", strip_tags($string)), "UTF-8");
        preg_match_all ('/([a-zа-яё]+)/ui', $string, $wordsArray);

        return $wordsArray[0];
    }
}