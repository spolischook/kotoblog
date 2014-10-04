<?php

$app->get('/', 'Kotoblog\Controller\IndexController::indexAction')
    ->bind('homepage');
$app->get('/about-me', 'Kotoblog\Controller\IndexController::aboutMeAction')
    ->bind('aboutMe');
$app->get('/search', 'Kotoblog\Controller\IndexController::searchAction')
    ->bind('search');
$app->get('/articles', 'Kotoblog\Controller\ArticleController::articlesAction')
    ->bind('articles');
$app->get('/articles/{slug}', 'Kotoblog\Controller\ArticleController::articleAction')
    ->bind('showArticle');

$app->get('/sitemap', 'Kotoblog\Controller\IndexController::sitemapAction')
    ->bind('sitemap');

$app->get('/tags/{slug}', 'Kotoblog\Controller\TagController::tagAction')
    ->bind('showTag');

$app->get('/sidebar/articles/', 'Kotoblog\Controller\SidebarController::getArticlesAction')
    ->bind('sidebarArticles');
$app->get('/sidebar/tags/', 'Kotoblog\Controller\SidebarController::getTagsAction')
    ->bind('sidebarTags');

$app->get('/example/{exampleNumber}', 'Kotoblog\Controller\IndexController::exampleAction')
    ->bind('example');

$app->match('/login', 'Kotoblog\Controller\UserController::loginAction')
    ->bind('login');
$app->get('/logout', 'Kotoblog\Controller\UserController::logoutAction')
    ->bind('logout');

$app->get('/admin', 'Kotoblog\Controller\BackendController::indexAction')
    ->bind('adminIndex');
$app->get('/admin/articles', 'Kotoblog\Controller\BackendController::articlesAction')
    ->bind('adminArticles');
$app->match('/admin/articles/new', 'Kotoblog\Controller\BackendController::createArticleAction')
    ->bind('adminCreateArticle')
    ->method('GET|POST');
$app->match('/admin/articles/{slug}', 'Kotoblog\Controller\BackendController::editArticleAction')
    ->bind('adminEditArticle')
    ->method('GET|POST');
$app->match('/admin/search-indexes', 'Kotoblog\Controller\BackendController::searchIndexesAction')
    ->bind('searchIndexes')
    ->method('GET|POST');

$app->get('/admin/pagination', 'Kotoblog\Controller\BackendController::paginationAction')
    ->bind('adminPagination');

$app->get('/enable-cookies', function () use ($app) {
    return $app['twig']->render('enable-cookies.html.twig');
})
    ->bind('enableCookies');
