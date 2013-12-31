<?php

$app->get('/', 'Kotoblog\Controller\IndexController::indexAction')
    ->bind('homepage');
$app->get('/about-me', 'Kotoblog\Controller\IndexController::aboutMeAction')
    ->bind('aboutMe');

