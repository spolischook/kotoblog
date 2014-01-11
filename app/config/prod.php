<?php

require __DIR__ . '/config.php';

$app['db.options'] = $config['db'];
$app['pagination.per_page'] = $config['pagination.per_page'] ? $config['pagination.per_page'] : 10;

// Cache
$app['cache.path'] = __DIR__ . '/../cache';

// Twig cache
$app['twig.options.cache'] = $app['cache.path'] . '/twig';
