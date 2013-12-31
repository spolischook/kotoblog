<?php

namespace Kotoblog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    public function indexAction(Request $request, Application $app)
    {
        return $app['twig']->render('layout.html.twig');

    public function aboutMeAction(Request $request, Application $app)
    {
        return $app['twig']->render('about-me.html.twig');
    }
}
