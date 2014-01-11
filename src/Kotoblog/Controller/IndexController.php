<?php

namespace Kotoblog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    protected $perPageDefault = 10;

    public function indexAction(Request $request, Application $app)
    {
        $perPage  = $request->query->get('count', $this->perPageDefault);
        $articles = $app['repository.article']->findBy(array(), array('created_at' => 'DESC'), $perPage);

        return $app['twig']->render('index.html.twig', array('articles' => $articles));
    }

    public function articlesAction(Request $request, Application $app)
    {
        $perPage = $request->query->get('count', $this->perPageDefault);
        $page    = $request->query->get('page', 1);

        $articles = $app['repository.article']->findBy(array(), array('created_at' => 'DESC'), $perPage, ($page-1)*$perPage);

        if (!count($articles)) {
            return new Response("No articles founded", 404);
        }

        return $app['twig']->render('articles.html.twig', array('articles' => $articles));
    }

    public function articleAction(Request $request, Application $app, $slug)
    {
        $article = $app['repository.article']->findOneBySlug($slug);

        return $app['twig']->render('article.html.twig', array('article' => $article));
    }

    public function aboutMeAction(Request $request, Application $app)
    {
        return $app['twig']->render('about-me.html.twig');
    }
}
