<?php

$app->get('/', 'Kotoblog\Controller\IndexController::indexAction')
    ->bind('homepage');
$app->get('/about-me', 'Kotoblog\Controller\IndexController::aboutMeAction')
    ->bind('aboutMe');

$app->match('/login', 'Kotoblog\Controller\UserController::loginAction')
    ->bind('login');
$app->get('/logout', 'Kotoblog\Controller\UserController::logoutAction')
    ->bind('logout');
