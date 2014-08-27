<?php

namespace Kotoblog;

use Doctrine\ORM\EntityManager;
use Kotoblog\ImageHandler;
use Kotoblog\Entity\Tag;
use Silex\Application;
use Doctrine\Common\Cache\CacheProvider;
use Symfony\Component\Stopwatch\Stopwatch;

class TwigExtensionKotoblog extends \Twig_Extension
{
    const TAG_CLOUD_CACHE_KEY = 'html_cloud';
    const TAG_CLOUD_CACHE_NAMESPACE = 'tag_cloud_';

    /** @var \Doctrine\ORM\EntityManager  */
    protected $em;

    private $environment;

    /** @var DisqusApi */
    private $disqusApi;

    /** @var  CacheProvider */
    protected $cache;

    public function __construct(EntityManager $em, DisqusApi $disqusApi, CacheProvider $cache)
    {
        $this->em = $em;
        $this->disqusApi = $disqusApi;
        $this->cache = $cache;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('tagCloud', array($this, 'getTagCloud'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('image', array($this, 'getImage'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('commentsCount', array($this, 'getCommentsCount'), array('is_safe' => array('html'))),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('withoutMore', array($this, 'withoutMore'), array('is_safe' => array('html')))
        );
    }

    public function getCommentsCount($url)
    {
        $thread = $this->disqusApi->getThread($url);

        return is_array($thread) && array_key_exists('posts', $thread) ? $thread['posts'] : 0;
    }

    public function withoutMore($text)
    {
        if ( preg_match('/<!--more(.*?)?-->/', $text, $matches) ) {
            list($main, $extended) = explode($matches[0], $text, 2);
        } else {
            $main = $text;
            $extended = '';
        }

        // Strip leading and trailing whitespace
        $main = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $main);
        $extended = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $extended);

        return $main;
    }

    public function getImage($src, $filterName = 'raw')
    {
        $imageHandler = new ImageHandler();

        if (file_exists(KOTOBLOG_PUBLIC_ROOT . $src)) {
            return $this->renderHtmlImage(
                $src,
                $imageHandler->filters[$filterName]['width'],
                $imageHandler->filters[$filterName]['height']
            );
        }

        $img = $imageHandler::make(KOTOBLOG_PUBLIC_ROOT . $src)->resize(
            $imageHandler->filters[$filterName]['width'],
            $imageHandler->filters[$filterName]['height']
        );
        $path = $imageHandler->saveImage($img, $src, $filterName);

        return $this->renderHtmlImage(
            $path,
            $imageHandler->filters[$filterName]['width'],
            $imageHandler->filters[$filterName]['height']
        );
    }

    public function getTagCloud(array $parameters = array())
    {
        $this->cache->setNamespace(self::TAG_CLOUD_CACHE_NAMESPACE);

        if ($this->cache->contains(self::TAG_CLOUD_CACHE_KEY)) {
            return $this->cache->fetch(self::TAG_CLOUD_CACHE_KEY);
        }

        $defaultParameters = array(
            'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
            'orderBy' => 'title', 'order' => 'ASC',
        );
        $tagCloudConfig = array_merge($defaultParameters, $parameters);

        $tags = $this->em->getRepository('Kotoblog\Entity\Tag')->findBy(
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

        $this->cache->save(self::TAG_CLOUD_CACHE_KEY, $this->environment->render('Twig/tagsCloud.html.twig', array(
            'tags' => $tags,
            'unit' => $tagCloudConfig['unit'],
        )));

        return $this->cache->fetch(self::TAG_CLOUD_CACHE_KEY);
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

    protected function renderHtmlImage($src, $width, $height)
    {
        return $this->environment->render('Twig/image.html.twig', array(
            'path' => $src,
            'width' => $width,
            'height' => $height,
        ));
    }
}
