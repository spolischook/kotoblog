<?php

namespace Kotoblog;

use Kotoblog\ImageHandler;

class TwigExtension
{
    public function getImage($src, $filterName = 'raw')
    {
        $imageHandler = new ImageHandler();
        $img = $imageHandler::make(KOTOBLOG_PUBLIC_ROOT . $src)->resize(
            $imageHandler->filters[$filterName]['width'],
            $imageHandler->filters[$filterName]['height']
        );
        $path = $imageHandler->saveImage($img, $src, $filterName);

        return $imageHandler->makeHtmlImage($path, $imageHandler->filters[$filterName]['width'], $imageHandler->filters[$filterName]['height']);
    }

    public function getTagCloud($tags)
    {

    }
}