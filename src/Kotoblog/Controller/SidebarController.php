<?php

namespace Kotoblog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class SidebarController
{
    public function getArticlesAction(Request $request, Application $app)
    {
        $count = $request->query->get('count', 10);
        $type = $request->query->get('type', 'latest');

        switch ($type) {
            case 'latest':
                $articles = $app['repository.article']->findBy(array(), array('created_at' => 'DESC'), $count);
                break;
            case 'popular':
                $articles = $app['repository.article']->findBy(array(), array('weight' => 'DESC'), $count);
                break;
        }

        return $app['twig']->render('Sidebar/articles.html.twig', array('articles' => $articles));
    }

    public function getTagsAction(Request $request, Application $app)
    {
        $tags = $app['repository.tag']->findBy(array(), array('weight' => 'DESC'));

        return $app['twig']->render('Sidebar/tags.html.twig', array('tags' => $tags));
    }
}
