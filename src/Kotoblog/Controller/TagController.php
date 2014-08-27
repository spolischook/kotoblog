<?php

namespace Kotoblog\Controller;

use Kotoblog\CacheInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TagController
{
    public function tagAction(Request $request, Application $app, $slug)
    {
        $app['cache']->setNamespace(CacheInterface::TAG_CACHE_NAMESPACE);

        if (false === $app['cache']->contains($slug)) {
            $tag = $app['orm.em']->getRepository('Kotoblog\Entity\Tag')->findOneBySlug($slug);

            if (!count($tag)) {
                return new Response("No tag founded", 404);
            }

            $app['cache']->save($slug, $tag);
        }

        $tag = $app['cache']->fetch($slug);

        return $app['twig']->render('tag.html.twig', array('tag' => $tag));
    }
}
