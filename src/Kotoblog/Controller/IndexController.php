<?php

namespace Kotoblog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    public function indexAction(Request $request, Application $app)
    {
        $articles = $app['repository.article']->findAll();

        return $app['twig']->render('index.html.twig', array('articles' => $articles));
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
