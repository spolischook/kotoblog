<?php

namespace Kotoblog;

use Doctrine\ORM\EntityManager;
use Guzzle\Http\Client;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
            $this->updateThreadCache($url);
        }

        return apc_fetch($cacheKey);
    }

    /**
     * @return void
     */
    protected function updateThreadsCache()
    {
        $articles = $this->em->getRepository('Kotoblog\Entity\Article')->findAll();

        foreach ($articles as $article) {
            $url = $this->urlGenerator->generate('showArticle', ['slug' => $article->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
            $this->updateThreadCache($url);
        }


    }

    protected function updateThreadCache($url)
    {
        $client = new Client();

        try {
            $request = $client->get(sprintf('https://disqus.com/api/3.0/threads/set.json?api_key=%s&thread=link:%s', $this->apiKey, $url));
            $response = $request->send();
        } catch (\Exception $e) {
            return false;
        }

        $arrayResponse = $response->json();
        $cacheKey = $this->getCacheKey(self::THREAD_CACHE_PREFIX, $url);

        if (!array_key_exists(0, $arrayResponse['response'])) {
            apc_add($cacheKey, 0);
            return;

        }

        apc_add($cacheKey, $arrayResponse['response'][0]);
    }

    protected function getCacheKey($prefix, $url)
    {
        return $prefix . md5($url);
    }
}
