<?php

namespace Kotoblog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    public function indexAction(Request $request, Application $app)
    {
        $perPage  = $request->query->get('count', $app['pagination.per_page']);
        $articles = $app['orm.em']->getRepository('Kotoblog\Entity\Article')->findBy([], ['createdAt' => 'DESC'], $perPage);

        return $app['twig']->render('index.html.twig', ['articles' => $articles]);
    }

    public function articlesAction(Request $request, Application $app)
    {
        $perPage = $request->query->get('count', $app['pagination.per_page']);
        $page    = $request->query->get('page', 1);

        $articles = $app['orm.em']->getRepository('Kotoblog\Entity\Article')->findBy(array(), array('createdAt' => 'DESC'), $perPage, ($page-1)*$perPage);

        if (!count($articles)) {
            return new Response("No articles founded", 404);
        }

        return $app['twig']->render('articles.html.twig', array('articles' => $articles));
    }

    public function articleAction(Request $request, Application $app, $slug)
    {
        $article = $app['orm.em']->getRepository('Kotoblog\Entity\Article')->findOneBySlug($slug);

        return $app['twig']->render('article.html.twig', array('article' => $article));
    }

    public function tagAction(Request $request, Application $app, $slug)
    {
        $tag = $app['orm.em']->getRepository('Kotoblog\Entity\Tag')->findOneBySlug($slug);

        return $app['twig']->render('tag.html.twig', array('tag' => $tag));
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
