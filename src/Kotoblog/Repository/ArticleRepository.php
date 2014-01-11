<?php

namespace Kotoblog\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\Common\Collections\ArrayCollection;
use Kotoblog\Entity\Article;
use Kotoblog\Entity\Tag;
use Kotoblog\Repository\TagRepository;

class ArticleRepository extends AbstractRepository
{
    /** @var \Doctrine\DBAL\Connection */
    protected $db;

    /** @var TagRepository */
    protected $tagRepository;

    public function __construct(Connection $db, TagRepository $tagRepository)
    {
        $this->db = $db;
        $this->tagRepository = $tagRepository;
    }

    /**
     * @param Article $article
     */
    public function save($article)
    {
        $articleData = array(
            'title' => $article->getTitle(),
            'text' => $article->getText(),
            'publish' => $article->getPublish(),
            'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
        );

        $file = $this->handleFileUpload($article);
        if ($file) {
            $articleData['image'] = $article->getImage();
        }

        if ($article->getId()) {
            $slug = $this->updateSlugIfItNeeded($article);
            $articleData['slug'] = $slug;

            $this->db->update('articles', $articleData, array('slug' => $article->getSlug()));
        } else {
            $this->processSlug($article, $article->getTitle());
            $articleData['slug'] = $article->getSlug();

            if (!$articleData['created_at']) {
                $now = new \DateTime();
                $articleData['created_at'] = $now->format('Y-m-d H:i:s');
            }

            $this->db->insert('articles', $articleData);
        }

        $id = $this->db->lastInsertId() ? $this->db->lastInsertId() : $article->getId();

        $this->handleTags($id, $article->getTags());
//        var_dump($article->getTags()); exit;

        return $this->findOneBySlug($article->getSlug());
    }

    public function delete($article)
    {

    }

    public function getCount() {
        return $this->db->fetchColumn('SELECT COUNT(id) FROM articles');
    }

    public function find($id)
    {
        $articleData = $this->db->fetchAssoc('SELECT * FROM articles WHERE id = ?', array($id));

        return $articleData ? $this->build($articleData) : false;
    }

    /**
     * Finds objects by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw
     * an UnexpectedValueException if certain values of the sorting or limiting details are
     * not supported.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array The objects.
     *
     * @throws \UnexpectedValueException
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        if (!$orderBy) {
            $orderBy = array('created_at' => 'DESC');
        }

        if (!isset($criteria['publish'])) {
            $criteria['publish'] = 1;
        }

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('a.*')
            ->from('articles', 'a')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('a.' . key($orderBy), current($orderBy));

        if (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                $queryBuilder->andWhere('a.'.$field.'='.$value);
            }
        }

        $statement = $queryBuilder->execute();
        $articlesData = $statement->fetchAll();

        $articles = array();
        foreach ($articlesData as $articleData) {
            $articleId = $articleData['id'];
            $articles[$articleId] = $this->build($articleData);
        }

        return $articles;
    }

    /**
     * Finds a single object by a set of criteria.
     *
     * @param array $criteria The criteria.
     *
     * @return object The object.
     */
    public function findOneBy(array $criteria)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('a.*')
            ->from('articles', 'a')
        ;

        if (!empty($criteria)) {
            $parameterNumber = 0;
            foreach ($criteria as $field => $value) {
                $queryBuilder
                    ->andWhere('a.'.$field.' = ?')
                    ->setParameter($parameterNumber, $value)
                ;
                $parameterNumber++;
            }
        }

        $statement = $queryBuilder->execute();
        $articlesData = $statement->fetch();

        return $this->build($articlesData);
    }

    /**
     * Returns the class name of the object managed by the repository.
     *
     * @return string
     */
    public function getClassName()
    {
        // TODO: Implement getClassName() method.
    }

    /**
     * Handles the upload of an artist image.
     *
     * @param \Kotoblog\Entity\Article $article
     *
     * @param boolean TRUE if a new artist image was uploaded, FALSE otherwise.
     */
    protected function handleFileUpload($article) {
        // If a temporary file is present, move it to the correct directory
        // and set the filename on the artist.
        $file = $article->getFile();
        if ($file) {
            $date = new \DateTime();
            $dir = $date->format('Y/m/');
            $newFilename = $article->getSlug() .'.' . $file->guessExtension();
            $file->move(KOTOBLOG_PUBLIC_ROOT . '/uploads/' . $dir, $newFilename);
            $article->setFile(null);
            $article->setImage('/uploads/' . $dir . $newFilename);
            return TRUE;
        }

        return FALSE;
    }

    protected function build($articleData)
    {
        if (false === $articleData) {
            return false;
        }

        $article = new Article();
        $createdAt = new \DateTime($articleData['created_at']);

        $article
            ->setId($articleData['id'])
            ->setTitle($articleData['title'])
            ->setText($articleData['text'])
            ->setSlug($articleData['slug'])
            ->setImage($articleData['image'])
            ->setWeight($articleData['weight'])
            ->setPublish($articleData['publish'])
            ->setCreatedAt($createdAt)
            ->setTags($this->getArticleTags($articleData['id']))
        ;

        return $article;
    }

    protected function updateSlugIfItNeeded(Article $article)
    {
        $originalArticle = $this->findOneBySlug($article->getSlug());

        if ($originalArticle->getTitle() != $article->getTitle()) {
            $this->processSlug($article, $article->getTitle());
        }

        return $article->getSlug();
    }

    protected function getArticleTags($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('t.*')
            ->from('tag_article', 'ta')
            ->where('ta.article_id = :article_id')
            ->innerJoin('ta', 'tags', 't', 'ta.tag_id = t.id')
            ->setParameter(':article_id', $id)
        ;

        $statement = $queryBuilder->execute();
        $tagsData = $statement->fetchAll();

        $tags = new ArrayCollection();
        foreach ($tagsData as $tagData) {
            $tags->add($this->tagRepository->build($tagData));
        }

        return $tags;
    }

    protected function removeArticleTags($id, $articleTags)
    {
        if (0 == count($articleTags)) {
            return;
        }

        $ids = array();
        /** @var Tag $articleTag */
        foreach ($articleTags as $articleTag) {
            $ids[] = $articleTag->getId();
            $articleTag->setWeight($articleTag->getWeight() -1);
            $this->tagRepository->save($articleTag);
        }

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->delete('tag_article')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->in('tag_id', $ids),
                    $queryBuilder->expr()->eq('article_id', $id)
                )
            )
        ;

        $queryBuilder->execute();
    }

    protected function addArticleTags($articleId, $articleTags)
    {
        if (0 == count($articleTags)) {
            return;
        }

        /** @var Tag $articleTag */
        foreach ($articleTags as $articleTag) {
            $this->db->insert('tag_article', array(
                'article_id' => $articleId,
                'tag_id' => $articleTag->getId(),
            ));

            $articleTag->setWeight($articleTag->getWeight() +1);
            $this->tagRepository->save($articleTag);
        }
    }

    protected function handleTags($id, $tags)
    {
        $articleTags = $this->getArticleTags($id);


        /** $tag Tag */
        foreach ($tags as $tag) {
            /** $articleTag Tag */
            foreach ($articleTags as $articleTag) {
                if ($tag->getId() == $articleTag->getId()) {
                    $tags->removeElement($tag);
                    $articleTags->removeElement($articleTag);
                }
            }
        }

        $this->removeArticleTags($id, $articleTags);
        $this->addArticleTags($id, $tags);
    }
}