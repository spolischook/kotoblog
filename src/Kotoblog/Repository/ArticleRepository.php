<?php

namespace Kotoblog\Repository;

use Doctrine\DBAL\Connection;
use Kotoblog\Entity\Article;

class ArticleRepository implements RepositoryInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function save($article)
    {

    }

    public function delete($article)
    {

    }

    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(id) FROM articles');
    }

    public function find($slug)
    {
        $articleData = $this->db->fetchAssoc('SELECT * FROM articles WHERE slug = ?', array($slug));

        return $articleData ? $this->build($articleData) : false;
    }

    public function findAll($limit = 10, $offset = 0, $orderBy = array())
    {
        if (!$orderBy) {
            $orderBy = array('created_at' => 'ASC');
        }

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('b.*')
            ->from('articles', 'b')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('b.' . key($orderBy), current($orderBy));
        $statement = $queryBuilder->execute();
        $articlesData = $statement->fetchAll();

        $articles = array();
        foreach ($articlesData as $articleData) {
            $articleId = $articleData['id'];
            $articles[$articleId] = $this->build($articleData);
        }
        return $articles;
    }

    protected function handleFileUpload($article)
    {

    }

    protected function build($articleData)
    {
        $article = new Article();
        $createdAt = new \DateTime($articleData['created_at']);

        $article
            ->setTitle($articleData['title'])
            ->setText($articleData['text'])
            ->setSlug($articleData['slug'])
            ->setPublish($articleData['publish'])
            ->setCreatedAt($createdAt)
        ;

        return $article;
    }
}