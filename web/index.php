<?php

define('KOTOBLOG_PUBLIC_ROOT', __DIR__);

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

require __DIR__ . '/../app/config/prod.php';
require __DIR__ . '/../src/app.php';
require __DIR__ . '/../src/routes.php';

$app->error(function (\Exception $e, $code) use ($app) {
    switch ($code) {
        case 404:
            $message = '404 - The requested page could not be found.';
            break;
        default:
            $message = 'We are sorry, but something went terribly wrong.';
    }

    return $app['twig']->render('error.html.twig', array('message' => $message));
});

$request = \Kotoblog\Request::createFromGlobals();
$app->run($request);
