<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;
use Kotoblog\Entity\Article;

$app = new Silex\Application();

require __DIR__ . '/../app/config/dev.php';
require __DIR__ . '/../src/app.php';

$tags = $app['orm.em']->getRepository('Kotoblog\Entity\Tag')->findAll();
foreach ($tags as $tag) {
    $tag->setWeight(0);
}

$app['orm.em']->flush();


$articles = $app['orm.em']->getRepository('Kotoblog\Entity\Article')->findAll();
foreach ($articles as $article) {
    foreach ($article->getTags() as $tag) {
        $tag->setWeight($tag->getWeight() +1);
    }

}

$app['orm.em']->flush();
