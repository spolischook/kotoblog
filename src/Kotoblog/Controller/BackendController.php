<?php

namespace Kotoblog\Controller;

use Kotoblog\Entity\Article;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use TextLanguageDetect\TextLanguageDetect;
use TextLanguageDetect\LanguageDetect\TextLanguageDetectException;
use Kotoblog\Form\ArticleType;

class BackendController
{
    public function indexAction(Request $request, Application $app)
    {
        return $app['twig']->render('Backend/index.html.twig');
    }

    public function articlesAction(Request $request, Application $app)
    {
        $perPage = $request->query->get('count', $app['pagination.per_page']);
        $page    = $request->query->get('page', 1);

        $articles = $app['orm.em']->getRepository('Kotoblog\Entity\Article')->findBy([], ['createdAt' => 'DESC'], $perPage, ($page-1)*$perPage);
        $entryCount = count($app['orm.em']->getRepository('Kotoblog\Entity\Article')->findAll());

        return $app['twig']->render('Backend/Article/articles.html.twig', array(
            'articles'   => $articles,
            'entryCount' => $entryCount,
        ));
    }

    public function editArticleAction(Request $request, Application $app, $slug)
    {
        $article = $app['orm.em']->getRepository('Kotoblog\Entity\Article')->findOneBySlug($slug);

        if (!$article) {
            $app->abort(404, sprintf('The requested article with slug "%s" was not found.', $slug));
        }

        return $this->processArticle($request, $app, $article);
    }

    public function createArticleAction(Request $request, Application $app)
    {
        return $this->processArticle($request, $app, new Article());
    }

    protected function processArticle(Request $request, Application $app, Article $article)
    {
        $form = $app['form.factory']->create($app['form_type.article'], $article);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                if (!$article->getId()) {
                    $app['orm.em']->persist($article);
                }
                $app['orm.em']->flush();

                $message = 'The article "' . $article->getTitle() . '" has been saved.';
                $app['session']->getFlashBag()->add('success', $message);

                return $app->redirect($app['url_generator']->generate('adminEditArticle', array('slug' => $article->getSlug())));
            }
        }

        $data = array(
            'form' => $form->createView(),
            'title' => $article->getTitle(),
        );


        return $app['twig']->render('Backend/Article/form.html.twig', $data);
    }

    public function paginationAction(Request $request, Application $app)
    {
        $entryCount = $request->query->get('entry-count');

        if (!$entryCount) {
            throw new MissingMandatoryParametersException('You must specify "entry-count" parameter to render pagination');
        }

        $perPage = $request->query->get('count', $app['pagination.per_page']);
        $page    = $request->query->get('page', 1);
        $requestUrl = $request->query->get('requestUrl');

        return $app['twig']->render('Backend/pagination.html.twig', array(
            'entryCount' => $entryCount,
            'perPage'    => $perPage,
            'page'       => $page,
            'requestUrl' => $requestUrl,
        ));
    }

    public function searchIndexesAction(Request $request, Application $app)
    {
        if ($request->isMethod('POST')) {
            $articles = $app['orm.em']->getRepository('Kotoblog\Entity\Article')->findAll();

            foreach($articles as $article) {
                $app['repository.articleSearchindex']->updateIndex($article);
            }
        }

        return $app['twig']->render('Backend/SearchIndexes/index.html.twig', array(
            'supportedLanguages' => $app['language_detector']->getLanguages()
        ));
    }
}
