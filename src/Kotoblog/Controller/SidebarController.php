<?php

namespace Kotoblog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Kotoblog\Entity\Article;

class SidebarController
{
    public function getArticlesAction(Request $request, Application $app)
    {
        $count = $request->query->get('count', 10);
        $type = $request->query->get('type', Article::getPreferredSorting());

        switch ($type) {
            case Article::LATEST:
                $articles = $app['orm.em']->getRepository('Kotoblog\Entity\Article')->findBy(array(), array('createdAt' => 'DESC'), $count);
                break;
            case Article::POPULAR:
                $articles = $app['orm.em']->getRepository('Kotoblog\Entity\Article')->findBy(array(), array('weight' => 'DESC'), $count);
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
