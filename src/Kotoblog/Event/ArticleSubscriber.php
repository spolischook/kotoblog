<?php

namespace Kotoblog\Event;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Github\Client;
use Kotoblog\Entity\Article;
use Symfony\Component\DomCrawler\Crawler;

class ArticleSubscriber implements EventSubscriber
{
    /** @var  Client */
    protected $githubClient;

    public function __construct(Client $client)
    {
        $this->githubClient = $client;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(Events::prePersist, Events::preUpdate, Events::postLoad);
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof Article) {
            $this->updateGists($event->getEntityManager(), $entity);
        }
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof Article) {
            $this->updateGists($event->getEntityManager(), $entity);
        }
    }

    public function postLoad(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof Article) {
            $this->updateGists($event->getEntityManager(), $entity);
        }
    }

    protected function updateGists(EntityManager $em, Article $article)
    {
        $crawler = new Crawler();
        $crawler->addContent($article->getText());

        $crawler->filter('code')->each(function (Crawler $node, $i) {
            var_dump($node->attr('class'));
            var_dump($node->text());
        });

        exit;
    }
}
