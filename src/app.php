<?php

$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());

$app['loginCheckRoute'] = '/admin/login_check';
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'main' => array(
            'pattern' => '^/admin',
            'form' => array(
                'login_path' => '/login',
                'check_path' => '/admin/login_check',
                'username_parameter' => 'form[username]',
                'password_parameter' => 'form[password]',
            ),
            'logout' => array('logout_path' => '/admin/logout'),
            'users' => array(
                'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
            ),
        ),
    ),
    'security.role_hierarchy' => array(
        'ROLE_ADMIN' => array('ROLE_USER'),
    ),
));
$app['security.access_rules'] = array(
    array('^/admin', 'ROLE_ADMIN'),
    array('^/', 'ROLE_USER'),
);

$app->register(new Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider, array(
    "orm.proxies_dir" => __DIR__ . "/app/proxies",
    "orm.em.options" => array(
        "mappings" => array(
//            array(
//                "type" => "annotation",
//                "namespace" => "Kotoblog\Entity",
//                "path" => __DIR__."/Kotoblog/Entity",
//            ),
            array(
                "type" => "yml",
                "namespace" => "Kotoblog\Entity",
                "path" => __DIR__ . "/../bin/Mapping",
            ),
        ),
    ),
));

//$app['article.subscriber'] = $app->share(function ($app) {
//    return new \Kotoblog\Event\ArticleSubscriber($app['orm.em']);
//});
$app['db.event_manager']->addEventSubscriber(new \Kotoblog\Event\ArticleSubscriber());

$app->register(new \Kotoblog\Provider\GitHubApiProvider(), [
    'github.username' => 'spolischook',
    'github.password' => 'dctktyyfz1985',
]);

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.options' => array(
        'cache' => isset($app['twig.options.cache']) ? $app['twig.options.cache'] : false,
        'strict_variables' => true,
        'globals' => array('loginCheckRoute' => 'wXMgt4EB3pG9Jki35t6bpGLJMrkQces6ETTE9fkQ4JM'),
    ),
    'twig.path' => array(__DIR__ . '/../app/views')
));

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addFunction('allTags', new \Twig_SimpleFunction('allTags', function() use ($app) {
        $tags = $app['repository.tag']->findAll();

        $tagsString = '';
        foreach ($tags as $tag) {
            $tagsString .= '"' . $tag->getTitle() . '",';
        }

        return rtrim($tagsString, ',');
    }));

    return $twig;
}));

$app['repository.tag'] = $app->share(function ($app) {
    return new Kotoblog\Repository\TagRepository($app['db']);
});

$app['repository.article'] = $app->share(function ($app) {
    return new Kotoblog\Repository\ArticleRepository($app['db'], $app['repository.tag']);
});

$app['repository.articleSearchindex'] = $app->share(function ($app) {
    return new Kotoblog\Repository\ArticleSearchindexRepository(
        $app['db'],
        $app['repository.article'],
        $app['text_helper'],
        $app['search.weight'],
        $app['search.minWordLength']
    );
});

$app['data_transformer.tag'] = $app->share(function ($app) {
    return new \Kotoblog\Form\TagTransformer($app['repository.tag']);
});

$app['data_transformer.article_text'] = $app->share(function ($app) {
    return new \Kotoblog\Form\ArticleTextGistTransformer($app['github.client'], $app['url_generator']);
});

$app['form_type.article'] = $app->share(function ($app) {
    return new Kotoblog\Form\ArticleType($app['data_transformer.tag'], $app['data_transformer.article_text']);
});

$app['twig_extension.kotoblog'] = $app->share(function ($app) {
    return new Kotoblog\TwigExtensionKotoblog($app['orm.em']);
});

$app['text_helper'] = $app->share(function ($app) {
    return new Kotoblog\TextHelper($app);
});

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $request = $app['request'];
    $cookies = $request->cookies;
    $screenWidth = null;

    if ($cookies->has('screenWidth'))
    {
        $screenWidth = $cookies->get('screenWidth');
    }

    $twig->addGlobal('screenWidth', $screenWidth);
    $twig->addExtension($app['twig_extension.kotoblog']);

    return $twig;
}));

$app['language_detector'] = $app->share(function ($app) {
//    $languageDetector = new TextLanguageDetect\TextLanguageDetect();
//    $languageDetector->omitLanguages($app['language_detector.omitLanguages']);
    $languageDetector = new Kotoblog\LanguageDetector();

    return $languageDetector;
});

// Created morphology instances
foreach ($app['search.languages'] as $language) {
    $languageConfig = $app['search.available_languages'][$language];
    $app['morphology_provider.'.$language] = $app->share(function ($app) use ($languageConfig) {
        require_once( '/var/www/kotoblog/vendor/phpMorphy/phpMorphy/src/common.php');
        $dir = '/var/www/kotoblog/vendor/phpMorphy/phpMorphy/dicts';
        $lang = $languageConfig['unix'];

        try {
            return new phpMorphy($dir, $lang, array('storage' => PHPMORPHY_STORAGE_FILE));
        } catch(phpMorphy_Exception $e) {
            throw new \Exception('Error occured while creating phpMorphy instance: ' . $e->getMessage());
        }
    });
}
