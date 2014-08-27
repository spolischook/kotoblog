<?php

namespace Kotoblog\Controller;

use Kotoblog\CacheInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Kotoblog\Entity\Article;

class SidebarController
{
    const LATEST_ARTICLES_CACHE_KEY = 'latest_articles';
    const POPULAR_ARTICLES_CACHE_KEY = 'popular_articles';

    public function getArticlesAction(Request $request, Application $app)
    {
        $app['cache']->setNamespace(CacheInterface::ARTICLE_NAMESPACE);

        $count = $request->query->get('count', 10);
        $type = $request->query->get('type', Article::getPreferredSorting());

        switch ($type) {
            case Article::LATEST:
                return $this->getSortedArticles('createdAt', $count, self::LATEST_ARTICLES_CACHE_KEY, $app);
            case Article::POPULAR:
                return $this->getSortedArticles('weight', $count, self::POPULAR_ARTICLES_CACHE_KEY, $app, 86400);
        }
    }

    protected function getSortedArticles($field, $count, $cacheKey, Application $app, $ttl = 0)
    {
        if ($app['cache']->contains($cacheKey)) {
            return $app['cache']->fetch($cacheKey);
        }

        $articles = $app['orm.em']->getRepository('Kotoblog\Entity\Article')->findBy([], [$field => 'DESC'], $count);
        $app['cache']->save($cacheKey, $app['twig']->render('Sidebar/articles.html.twig', ['articles' => $articles]), $ttl);

        return $app['cache']->fetch($cacheKey);
    }
}
