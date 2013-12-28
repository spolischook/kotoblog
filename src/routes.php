<?php

$app->get('/', 'Kotoblog\Controller\IndexController::indexAction')
    ->bind('homepage');
