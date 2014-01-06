<?php

namespace Kotoblog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Kotoblog\Form\ArticleType;

class BackendController
{
    protected $perPageDefault = 10;

    public function indexAction(Request $request, Application $app)
    {
        return $app['twig']->render('Backend/index.html.twig');
    }

    public function articlesAction(Request $request, Application $app)
    {
        $perPage = $request->query->get('count') ? $request->query->get('count') : $this->perPageDefault;
        $page    = $request->query->get('page') ? $request->query->get('page') : 1;

        $articles   = $app['repository.article']->findAll($perPage, ($page-1)*$perPage, array('created_at' => 'DESC'));
        $entryCount = $app['repository.article']->getCount();

        return $app['twig']->render('Backend/Article/articles.html.twig', array(
            'articles'   => $articles,
            'entryCount' => $entryCount,
        ));
    }

    public function editArticleAction(Request $request, Application $app, $slug)
    {
        $article = $app['repository.article']->find($slug);

        if (!$article) {
            $app->abort(404, sprintf('The requested article with slug "%s" was not found.', $slug));
        }

        $form = $app['form.factory']->create(new ArticleType(), $article);

        $data = array(
            'form' => $form->createView(),
            'title' => 'Edit article ' . $article->getTitle(),
        );

        return $app['twig']->render('Backend/Article/form.html.twig', $data);
    }

    public function paginationAction(Request $request, Application $app)
    {
        $entryCount = $request->query->get('entry-count');

        if (!$entryCount) {
            throw new MissingMandatoryParametersException('You must specify "entry-count" parameter to render pagination');
        }

        $perPage = $request->query->get('count') ? $request->query->get('count') : $this->perPageDefault;
        $page    = $request->query->get('page') ? $request->query->get('page') : 1;
        $requestUrl = $request->query->get('requestUrl');

        return $app['twig']->render('Backend/pagination.html.twig', array(
            'entryCount' => $entryCount,
            'perPage'    => $perPage,
            'page'       => $page,
            'requestUrl' => $requestUrl,
        ));
    }
}
