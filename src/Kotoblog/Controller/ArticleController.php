<?php

namespace Kotoblog\Controller;

use Kotoblog\CacheInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleController
{
    public function articlesAction(Request $request, Application $app)
    {
        $app['cache']->setNamespace(CacheInterface::ARTICLE_NAMESPACE);

        $perPage = $request->query->get('count', $app['pagination.per_page']);
        $page    = $request->query->get('page', 1);
        $cacheKey = 'kotoblog_articles_page_' . $perPage . '_per_page_' . $page;

        if (false == $app['cache']->contains($cacheKey)) {
            $articles = $app['orm.em']->getRepository('Kotoblog\Entity\Article')->findBy([], ['createdAt' => 'DESC'], $perPage, ($page-1)*$perPage);

            if (!count($articles)) {
                throw new NotFoundHttpException("No articles founded");
            }

            $app['cache']->save($cacheKey, $articles);
        }

        $articles = $app['cache']->fetch($cacheKey);

        return $app['twig']->render('articles.html.twig', array('articles' => $articles));
    }

    public function articleAction(Request $request, Application $app, $slug)
    {
        $app['cache']->setNamespace(CacheInterface::ARTICLE_NAMESPACE);
        $cacheKey = 'kotoblog_article_' . $slug;

        if (false === $app['cache']->contains($cacheKey)) {
            $article = $app['orm.em']->getRepository('Kotoblog\Entity\Article')->findOneBySlug($slug);
            if (!$article) {
                throw new NotFoundHttpException("No article founded");
            }
            $app['cache']->save($cacheKey, $article);
        }

        $article = $app['cache']->fetch($cacheKey);

        return $app['twig']->render('article.html.twig', array('article' => $article));
    }
}
