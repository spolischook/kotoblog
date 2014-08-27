<?php

namespace Kotoblog;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\ORM\EntityManager;
use Kotoblog\Entity\Article;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CacheUpdater implements CacheInterface
{
    /** @var  CacheProvider */
    protected $cache;

    public function __construct(CacheProvider $cache)
    {
        $this->cache        = $cache;
    }

    public function updateArticleCache(Article $article)
    {
        $this->cache->setNamespace(self::ARTICLE_NAMESPACE);
        $cacheKey = 'kotoblog_article_' . $article->getSlug();

        $this->cache->delete($cacheKey);
        $this->cache->save($cacheKey, $article);
    }

    public function removeTagCache()
    {
        $this->cache->setNamespace(self::TAG_CACHE_NAMESPACE);
        $this->cache->deleteAll();
    }
}
