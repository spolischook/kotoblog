<?php

namespace Kotoblog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    public function indexAction(Request $request, Application $app)
    {
        $blogposts = $app['repository.blogpost']->findAll();

        return $app['twig']->render('index.html.twig', array('blogposts' => $blogposts));
    }

    public function aboutMeAction(Request $request, Application $app)
    {
        return $app['twig']->render('about-me.html.twig');
    }
}
