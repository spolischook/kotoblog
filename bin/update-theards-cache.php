<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;
use Kotoblog\Entity\Article;

$app = new Silex\Application();

require __DIR__ . '/../app/config/dev.php';
require __DIR__ . '/../src/app.php';
require __DIR__ . '/../src/routes.php';

$app->flush();

$articles = $app['orm.em']->getRepository('Kotoblog\Entity\Article')->findAll();
/** @var Article $article */
foreach ($articles as $article) {
    $url = $app['url_generator']->generate('showArticle', ['slug' => $article->getSlug()], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH);
    $url = $app['kotoblog.host'] . $url;

    echo "Update thread for: $url \n";
    $app['disqus_api']->updateThreadCache($url);
}

echo "Cache thread is updated";

