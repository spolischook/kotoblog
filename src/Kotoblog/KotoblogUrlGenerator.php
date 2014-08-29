<?php

namespace Kotoblog;

use Symfony\Component\Routing\Generator\UrlGenerator;

class KotoblogUrlGenerator extends UrlGenerator
{
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $url = parent::generate($name, $parameters, $referenceType);

        $urlMap = UrlReplacer::$urlMap;

        return (array_key_exists($url, $urlMap)) ? $urlMap[$url] : $url;
    }
}
