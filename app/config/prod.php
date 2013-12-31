<?php

require __DIR__ . '/config.php';

$app['db.options'] = $dbConfig;

// Cache
$app['cache.path'] = __DIR__ . '/../cache';

// Twig cache
$app['twig.options.cache'] = $app['cache.path'] . '/twig';
