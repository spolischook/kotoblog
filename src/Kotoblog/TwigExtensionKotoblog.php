<?php

namespace Kotoblog;

use Kotoblog\ImageHandler;
use Kotoblog\Repository\TagRepository;
use Kotoblog\Entity\Tag;
use Silex\Application;

class TwigExtensionKotoblog extends \Twig_Extension
{
    protected $tagRepository;

    private $environment;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('tagCloud', array($this, 'getTagCloud'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('image', array($this, 'getImage'), array('is_safe' => array('html'))),
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

        return $this->environment->render('Twig/image.html.twig', array(
            'path' => $path,
            'width' => $imageHandler->filters[$filterName]['width'],
            'height' => $imageHandler->filters[$filterName]['height'],
        ));
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

        return $this->environment->render('Twig/tagsCloud.html.twig', array(
            'tags' => $tags,
            'unit' => $tagCloudConfig['unit'],
        ));
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

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }
}
