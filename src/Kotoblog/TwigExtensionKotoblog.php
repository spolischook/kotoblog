<?php

namespace Kotoblog;

use Kotoblog\ImageHandler;
use Kotoblog\Repository\TagRepository;
use Kotoblog\Entity\Tag;
use Silex\Application;

class TwigExtensionKotoblog extends \Twig_Extension
{
    protected $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('tagCloud', array($this, 'getTagCloud')),
            new \Twig_SimpleFunction('image', array($this, 'getImage')),
        );
    }

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

    public function getTagCloud(array $parameters = array())
    {
        $defaultParameters = array(
            'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
            'orderBy' => 'title', 'order' => 'ASC',
        );
        $tagCloudConfig = array_merge($defaultParameters, $parameters);

        $tags = $this->tagRepository->findBy(
            array(),
            array($tagCloudConfig['orderBy'] => $tagCloudConfig['order']),
            $tagCloudConfig['number']
        );


        $tagCount = count($tags);

        if (0 == $tagCount) {
            return '';
        } elseif (1 == $tagCount) {
            $ratio = 0;
            $tagWeight = $tagCloudConfig['largest'];
        } else {
            $ratio = ($tagCloudConfig['largest'] - $tagCloudConfig['smallest']) / ($tagCount -1);
            $tagWeight = $tagCloudConfig['smallest'];
        }

        $tagsHierarchy = $tags;
        usort($tagsHierarchy, function ($a, $b) {
            if ($a->getWeight() == $b->getWeight()) {
                return 0;
            }
            return ($a->getWeight() < $b->getWeight()) ? -1 : 1;
        });

        /** @var $tag Tag */
        foreach ($tagsHierarchy as $tag) {
            $tag->setWeight($tagWeight);
            $tagWeight = $tagWeight + $ratio;
        }

        $tagCloud = '';

        /** @var $tag Tag */
        foreach ($tags as $tag) {
            $tagCloud .= sprintf('<a class="tag" href="#" style="font-size: %s%s;">%s</a> ', $tag->getWeight(), $tagCloudConfig['unit'], $tag->getTitle());
        }

        return $tagCloud;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'kotoblog';
    }
}
