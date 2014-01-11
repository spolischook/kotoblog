<?php

namespace Kotoblog;

use Intervention\Image\Image;

class ImageHandler extends Image
{
    public $filters = array(
        'articleMainImage' => array(
            'width' => 300,
            'height' => 200,
        ),
    );

    public function getDir($src)
    {
        return pathinfo($src, PATHINFO_DIRNAME);
    }

    public function getFileName($src)
    {
        return pathinfo($src, PATHINFO_FILENAME);
    }

    public function getExtension($src)
    {
        return pathinfo($src, PATHINFO_EXTENSION);
    }

    public function saveImage(Image $img, $src, $filterName)
    {
        $path = $this->getDir($src) . $this->getFileName($src) . '-' . $filterName . '.' . $this->getExtension($src);
        $img->save(KOTOBLOG_PUBLIC_ROOT . $path);

        return $path;
    }

    public function makeHtmlImage($path, $width, $height)
    {
        return "<img src='$path' class='img-thumbnail' width='$width' height='$height' />";
    }
}