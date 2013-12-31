<?php

namespace Kotoblog\Repository;

use Doctrine\DBAL\Connection;
use Kotoblog\Entity\Blogpost;

class BlogpostRepository implements RepositoryInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function save($blogpost)
    {

    }

    public function delete($blogpost)
    {

    }

    public function getCount() {

    }

    public function find($id)
    {

    }

    public function findAll($limit = 10, $offset = 0, $orderBy = array())
    {
        if (!$orderBy) {
            $orderBy = array('created_at' => 'ASC');
        }

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('b.*')
            ->from('blogposts', 'b')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('b.' . key($orderBy), current($orderBy));
        $statement = $queryBuilder->execute();
        $blogpostsData = $statement->fetchAll();

        $blogposts = array();
        foreach ($blogpostsData as $blogpostData) {
            $blogpostId = $blogpostData['id'];
            $blogposts[$blogpostId] = $this->build($blogpostData);
        }
        return $blogposts;
    }

    protected function handleFileUpload($blogpost)
    {

    }

    protected function build($blogpostData)
    {
        $blogpost = new Blogpost();
        $createdAt = new \DateTime($blogpostData['created_at']);

        $blogpost
            ->setTitle($blogpostData['title'])
            ->setText($blogpostData['text'])
            ->setSlug($blogpostData['slug'])
            ->setPublish($blogpostData['publish'])
            ->setCreatedAt($createdAt)
        ;

        return $blogpost;
    }
}