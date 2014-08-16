<?php

namespace Kotoblog;

class LanguageDetector
{
    protected $languages = array(
        'english' => array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'
        ),
        'russian' => array('а','б','в','г','д','e','ё','э','ж','з','и','й','к','л','м','н','о','п','р','с','т','у',
            'ф','х','ц','ч','ш','щ','ы','ъ','ь', 'ю','я','А','Б','В','Г','Д','Е','Ё','Э','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У',
            'Ф','Х','Ц','Ч','Ш','Щ','Ы','Ъ','Ь','Ю','Я'
        ),
        'ukrainian' => array('а','б','в','г','д','e','ё','э','ж','з','и','й','к','л','м','н','о','п','р','с','т','у',
            'ф','х','ц','ч','ш','щ','ы','ъ','ь', 'ю','я','А','Б','В','Г','Д','Е','Ё','Э','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У',
            'Ф','Х','Ц','Ч','Ш','Щ','Ы','Ъ','Ь','Ю','Я'
        ),
    );

    public function detectSimple($word)
    {
        $languagesWeight = array();
        foreach ($this->strSplitUtf8($word) as $symbol) {
            foreach ($this->languages as $languageName => $alphabet) {
                if (in_array($symbol, $alphabet)) {
                    @$languagesWeight[$languageName]++;
                }
            }
        }

        return $this->guess($languagesWeight);
    }

    public function getLanguages()
    {
        return array_keys($this->languages);
    }

    protected function guess(array $languagesWeight)
    {
        asort($languagesWeight);
        end($languagesWeight);

        return key($languagesWeight);
    }

    protected function strSplitUtf8($string) {
        $split = 1;
        $array = array();
        for ($i=0; $i < strlen($string); ){
            $value = ord($string[$i]);
            if($value > 127){
                if ($value >= 192 && $value <= 223)      $split = 2;
                elseif ($value >= 224 && $value <= 239)  $split = 3;
                elseif ($value >= 240 && $value <= 247)  $split = 4;
            } else $split = 1;
            $key = NULL;
            for ( $j = 0; $j < $split; $j++, $i++ ) $key .= $string[$i];
            array_push( $array, $key );
        }
        return $array;
    }
}
