<?php

namespace Kotoblog\Controller;

use Silex\Application;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function exampleAction(Request $request, Application $app, $exampleNumber)
    {
        $filesystem = new Filesystem();
//        var_dump(sprintf('%s/Example/example-%s.html.twig', $app['twig.path'][0], $exampleNumber)); exit;

        if (false === $filesystem->exists(sprintf('%s/Example/example-%s.html.twig', $app['twig.path'][0], $exampleNumber))) {
            throw new NotFoundHttpException(sprintf('No example with number "%s" founded', $exampleNumber));
        }

        return $app['twig']->render(sprintf('Example/example-%s.html.twig', $exampleNumber));
    }
}
