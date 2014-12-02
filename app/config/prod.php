<?php

require __DIR__ . '/config.php';

$app['db.options'] = $config['db'];
$app['pagination.per_page'] = $config['pagination']['per_page'] ? $config['pagination']['per_page'] : 10;

$app['search.available_languages'] = array(
    'english' => array('iso' => 'en', 'unix' => 'en_EN'),
    'russian' => array('iso' => 'ru', 'unix' => 'ru_RU'),
    'ukrainian' => array('iso' => 'uk', 'unix' => 'uk_UA'),
    'german' => array('iso' => 'de', 'unix' => 'de_DE'),
);
// Detector languages
$omitLanguages = array("albanian", "arabic", "azeri", "bengali", "bulgarian", "cebuano", "croatian", "czech", "danish", "dutch", "english", "estonian", "farsi", "finnish", "french", "german", "hausa", "hawaiian", "hindi", "hungarian", "icelandic", "indonesian", "italian", "kazakh", "kyrgyz", "latin", "latvian", "lithuanian", "macedonian", "mongolian", "nepali", "norwegian", "pashto", "pidgin", "polish", "portuguese", "romanian", "russian", "serbian", "slovak", "slovene", "somali", "spanish", "swahili", "swedish", "tagalog", "turkish", "ukrainian", "urdu", "uzbek", "vietnamese", "welsh");

foreach ($config['search']['languages'] as $language) {
    if (!array_key_exists($language, $app['search.available_languages'])) {
        throw new \Exception(sprintf('Language "%s" not supported for search', $language));
    }

    unset($omitLanguages[array_search($language, $omitLanguages)]);
}

$app['language_detector.omitLanguages'] = $omitLanguages;
$app['search.weight'] = $config['search']['weight'];
$app['search.languages'] = $config['search']['languages'];
$app['search.minWordLength'] = $config['search']['minWordLength'];

// Cache
$app['cache.path'] = __DIR__ . '/../cache';

// Twig cache
$app['twig.options.cache'] = $app['cache.path'] . '/twig';

foreach ($config['disqus'] as $property => $value) {
    $app['disqus.' . $property] = $value;
}

$app['cache.options'] = array("default" => array(
    "driver" => function() {
        $cache = new \Doctrine\Common\Cache\MemcacheCache();
        $memcache = new \Memcache;
        $memcache->pconnect('unix:///home/silkck/.system/memcache/socket', 0);

        $cache->setMemcache($memcache);

        return $cache;
    }
));

$app['kotoblog.host'] = 'http://kotoblog.pp.ua';
$app['github.username'] = $config['github']['username'];
$app['github.password'] = $config['github']['password'];
