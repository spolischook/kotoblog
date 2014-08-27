<?php

namespace Kotoblog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    public function indexAction(Request $request, Application $app)
    {
        return $app['twig']->render('index.html.twig');
    }

    public function aboutMeAction(Request $request, Application $app)
    {
        return $app['twig']->render('about-me.html.twig');
    }

    public function searchAction(Request $request, Application $app)
    {
        $query = $request->query->get('query', false);
        $results = array();

        if (false !== $query) {
            if (mb_strlen($query, "UTF-8") > $app['search.minWordLength'])
            {
                $results = $app['repository.articleSearchindex']->search($query);
            }
        }

        return $app['twig']->render('searchResults.html.twig', array('results' => $results));
    }
}
