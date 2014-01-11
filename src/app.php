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
                'admin' => array('ROLE_ADMIN', 'Nr4mS9Hl0a41Wb6Iu8ONZf2C97Txq7U8BUyJMIrhHfHXZU05+6qgrzzhuAvcuagrIqXhMdulsSj3Ba5GYTd91Q=='),
            ),
        ),
    ),
    'security.role_hierarchy' => array(
        'ROLE_ADMIN' => array('ROLE_USER'),
    ),
));
$app['security.access_rules'] = array(
    array('^/admin', 'ROLE_ADMIN'),
);

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

$app['data_transformer.tag'] = $app->share(function ($app) {
    return new \Kotoblog\Form\TagTransformer($app['repository.tag']);
});

$app['form_type.article'] = $app->share(function ($app) {
    return new Kotoblog\Form\ArticleType($app['data_transformer.tag']);
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
    $twig->addFunction('image', new \Twig_SimpleFunction('image', 'Kotoblog\TwigExtension::getImage'));
    $twig->addFunction('tagCloud', new \Twig_SimpleFunction('tagCloud', 'Kotoblog\TwigExtension::getTagCloud'));

    return $twig;
}));
