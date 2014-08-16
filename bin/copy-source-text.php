<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;
use Kotoblog\Entity\Article;

$app = new Silex\Application();

require __DIR__ . '/../app/config/dev.php';
require __DIR__ . '/../src/app.php';

$articles = $app['orm.em']->getRepository('Kotoblog\Entity\Article')->findAll();
/** @var \Kotoblog\Entity\Article $article */
foreach ($articles as $article) {
    $article->setTextSource($article->getText());
}

$app['orm.em']->flush();
