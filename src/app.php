<?php

$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
//$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app['url_generator'] = $app->share(function ($app) {
    $app->flush();

    return new Kotoblog\KotoblogUrlGenerator($app['routes'], $app['request_context']);
});
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
    'security.access_rules' => array(
        array('^/admin', 'ROLE_ADMIN'),
    ),
));

$app['disqus_api'] = $app->share(function($app) {
    return new \Kotoblog\DisqusApi($app['disqus.api_key'], $app['cache']);
});

$app['cache_updater'] = $app->share(function ($app) {
    return new \Kotoblog\CacheUpdater($app['cache']);
});

$app->register(new Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider, array(
    "orm.proxies_dir" => __DIR__ . "/../app/proxies",
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

$app->register(new \CHH\Silex\CacheServiceProvider);
$app['session.storage.handler'] = new \Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeSessionHandler();

$app['article.subscriber'] = $app->share(function ($app) {
    return new \Kotoblog\Event\ArticleSubscriber($app['cache_updater']);
});
$app['db.event_manager']->addEventSubscriber($app['article.subscriber']);
$app['db.event_manager']->addEventSubscriber(new \Gedmo\Sluggable\SluggableListener());

//UrlReplacer
$app->before(function (\Kotoblog\Request $request) use ($app) {
    $urlMap      = \Kotoblog\UrlReplacer::$urlMap;
    $urlRedirect = \Kotoblog\UrlReplacer::$urlRedirect;
    $path        = $request->getPathInfo();

    if (array_key_exists($path, $urlRedirect)) {
        return new \Symfony\Component\HttpFoundation\RedirectResponse($urlRedirect[$path], 301);
    }

    if (array_key_exists($path, $urlMap)) {
        return new \Symfony\Component\HttpFoundation\RedirectResponse($urlMap[$path], 301);
    }

    $urlReverseMap = array_flip($urlMap);
    if (array_key_exists($path, $urlReverseMap)) {
        $request->setPathInfo($urlReverseMap[$path]);
    }
}, \Silex\Application::EARLY_EVENT);


$app->register(new \Kotoblog\Provider\GitHubApiProvider(), [
    'github.username' => $app['github.username'],
    'github.password' => $app['github.password'],
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
        $tags = $app['orm.em']->getRepository('Kotoblog\Entity\Tag')->findAll();

        $tagsString = '';
        foreach ($tags as $tag) {
            $tagsString .= '"' . $tag->getTitle() . '",';
        }

        return rtrim($tagsString, ',');
    }));

    return $twig;
}));

$app['repository.articleSearchindex'] = $app->share(function ($app) {
    return new Kotoblog\Repository\ArticleSearchindexRepository(
        $app['orm.em'],
        $app['cache'],
        $app['text_helper'],
        $app['search.weight'],
        $app['search.minWordLength']
    );
});

$app['data_transformer.tag'] = $app->share(function ($app) {
    return new \Kotoblog\Form\TagTransformer($app['orm.em']);
});

$app['data_transformer.article_text'] = $app->share(function ($app) {
    return new \Kotoblog\Form\ArticleTextGistTransformer($app['github.client'], $app['url_generator']);
});

$app['form_type.article'] = $app->share(function ($app) {
    return new Kotoblog\Form\ArticleType($app['data_transformer.tag'], $app['data_transformer.article_text']);
});

$app['twig_extension.kotoblog'] = $app->share(function ($app) {
    return new Kotoblog\TwigExtensionKotoblog($app['orm.em'], $app['disqus_api'], $app['cache']);
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
        require_once( __DIR__ . '/../vendor/phpMorphy/phpMorphy/src/common.php');
        $dir = __DIR__ . '/../vendor/phpMorphy/phpMorphy/dicts';
        $lang = $languageConfig['unix'];

        try {
            return new phpMorphy($dir, $lang, array('storage' => PHPMORPHY_STORAGE_FILE));
        } catch(phpMorphy_Exception $e) {
            throw new \Exception('Error occured while creating phpMorphy instance: ' . $e->getMessage());
        }
    });
}
