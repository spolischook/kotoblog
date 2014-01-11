<?php

$app->get('/', 'Kotoblog\Controller\IndexController::indexAction')
    ->bind('homepage');
$app->get('/about-me', 'Kotoblog\Controller\IndexController::aboutMeAction')
    ->bind('aboutMe');
$app->get('/articles/{slug}', 'Kotoblog\Controller\IndexController::articleAction')
    ->bind('showArticle');

$app->get('/sidebar/articles/', 'Kotoblog\Controller\SidebarController::getArticlesAction')
    ->bind('sidebarArticles');
$app->get('/sidebar/tags/', 'Kotoblog\Controller\SidebarController::getTagsAction')
    ->bind('sidebarTags');

$app->match('/login', 'Kotoblog\Controller\UserController::loginAction')
    ->bind('login');
$app->get('/logout', 'Kotoblog\Controller\UserController::logoutAction')
    ->bind('logout');

$app->get('/admin', 'Kotoblog\Controller\BackendController::indexAction')
    ->bind('adminIndex');
$app->get('/admin/articles', 'Kotoblog\Controller\BackendController::articlesAction')
    ->bind('adminArticles');
$app->get('/admin/articles/{slug}', 'Kotoblog\Controller\BackendController::editArticleAction')
    ->bind('adminArticle');

$app->get('/admin/pagination', 'Kotoblog\Controller\BackendController::paginationAction')
    ->bind('adminPagination');
