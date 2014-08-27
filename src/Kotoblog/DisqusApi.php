<?php

namespace Kotoblog;

use Guzzle\Http\Client;
use Doctrine\Common\Cache\CacheProvider;

class DisqusApi
{
    /** @var  string */
    protected $apiKey;

    /** @var  CacheProvider */
    protected $cache;

    public function __construct($apiKey, CacheProvider $cache)
    {
        $this->apiKey       = $apiKey;
        $this->cache        = $cache;
    }

    /**
     * @param string $url
     * @return integer|false
     */
    public function getThread($url)
    {
        $this->cache->setNamespace(CacheInterface::THREAD_CACHE_NAMESPACE);
        $cacheKey = $this->getCacheKey($url);

//        if (false === $this->cache->contains($cacheKey)) {
//            $this->updateThreadCache($url);
//        }

        return $this->cache->fetch($cacheKey);
    }

    public function updateThreadCache($url)
    {
        $this->cache->setNamespace(CacheInterface::THREAD_CACHE_NAMESPACE);
        $client = new Client();

        try {
            $request = $client->get(sprintf('https://disqus.com/api/3.0/threads/set.json?api_key=%s&thread=link:%s', $this->apiKey, $url));
            $response = $request->send();
        } catch (\Exception $e) {
            return false;
        }

        $arrayResponse = $response->json();
        $cacheKey = $this->getCacheKey($url);

        if (!array_key_exists(0, $arrayResponse['response'])) {
            $this->cache->save($cacheKey, 0);
            return;

        }

        $this->cache->save($cacheKey, $arrayResponse['response'][0]);
    }

    protected function getCacheKey($url)
    {
        return $url;
    }
}
