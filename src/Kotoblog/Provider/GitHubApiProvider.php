<?php

namespace Kotoblog\Provider;

use Github\Client;
use Silex\Application;
use Silex\ServiceProviderInterface;

class GitHubApiProvider implements ServiceProviderInterface
{
     /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $app['github.client'] = $app->share(function ($app) {
            return new Client();
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
        $app['github.client']->authenticate($app['github.username'], $app['github.password'], Client::AUTH_HTTP_PASSWORD);
    }
}
