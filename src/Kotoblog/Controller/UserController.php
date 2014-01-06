<?php

namespace Kotoblog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class UserController
{
    public function loginAction(Request $request, Application $app)
    {
        if ($app['security']->getToken()) {
            return $app->redirect($app['url_generator']->generate('homepage'));
        }

        $form = $app['form.factory']->createBuilder('form')
            ->add('username', 'text', array('label' => 'Username', 'attr' => array('class' => 'form-control')))
            ->add('password', 'password', array('label' => 'Password', 'attr' => array('class' => 'form-control')))
            ->add('login', 'submit', array('attr' => array('class' => 'btn btn-lg btn-primary btn-block')))
            ->getForm();

        $data = array(
            'last_username' => $app['session']->get('_security.last_username'),
            'error' => $app['security.last_error']($request),
            'form' => $form->createView(),
        );

        return $app['twig']->render('login.html.twig', $data);
    }

    public function logoutAction(Request $request, Application $app)
    {
        $app['session']->clear();
        return $app->redirect($app['url_generator']->generate('homepage'));
    }
}
