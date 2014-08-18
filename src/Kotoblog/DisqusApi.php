<?php

namespace Kotoblog;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Generator\UrlGenerator;

class DisqusApi
{
    const THREAD_CACHE_PREFIX = 'thread_';

    /** @var  string */
    protected $apiKey;

    /** @var \Doctrine\ORM\EntityManager */
    protected $em;

    /** @var \Symfony\Component\Routing\Generator\UrlGenerator */
    protected $urlGenerator;

    public function __construct($apiKey, EntityManager $em, UrlGenerator $urlGenerator)
    {
        $this->apiKey       = $apiKey;
        $this->em           = $em;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param string $url
     * @return integer|false
     */
    public function getThread($url)
    {
        $cacheKey = $this->getCacheKey(self::THREAD_CACHE_PREFIX, $url);

        if (false === apc_exists($cacheKey)) {
            $this->updateThreadsCache();
        }

        return apc_fetch($cacheKey);
    }

    /**
     * @return void
     */
    protected function updateThreadsCache()
    {
        $articles = $this->em->getRepository('Kotoblog\Entity\Article')->findAll();
        $threads = array();
        $parameters = ['api_key' => $this->apiKey];

        foreach ($articles as $article) {
            $url = $this->urlGenerator->generate('showArticle', ['slug' => $article->getSlug()]);
            $threads[] = 'link:'.$url;
        }

        $parameters['thread'] = $threads;

        $response = file_get_contents('https://disqus.com/api/3.0/threads/set.json?' . http_build_query($parameters));
        $response = json_decode($response);

        foreach ($response['response'] as $thread) {
            $cacheKey = $this->getCacheKey(self::THREAD_CACHE_PREFIX, $thread['link']);
            apc_add($cacheKey, $thread);
        }
    }

    protected function getCacheKey($prefix, $url)
    {
        return $prefix . md5($url);
    }
}
