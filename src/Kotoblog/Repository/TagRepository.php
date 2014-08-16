<?php

namespace Kotoblog\Repository;

use Doctrine\DBAL\Connection;
use Kotoblog\Entity\Tag;

class TagRepository extends AbstractRepository
{
    /**
     * {@inheritdoc }
     * @var $tag Tag
     */
    public function save($tag)
    {
        $tagData = array(
            'title'  => $tag->getTitle(),
            'weight' => $tag->getWeight(),
        );

        if ($tag->getId()) {
            $this->db->update('tags', $tagData, array('slug' => $tag->getSlug()));
        } else {
            $newSlug = $this->processSlug($tag, $tag->getTitle());
            $tagData['slug'] = $newSlug;

            $this->db->insert('tags', $tagData);
            $tag->setSlug($newSlug);
        }

        return $this->findOneBySlug($tag->getSlug());
    }

    public function findOneByTitleOrCreateNewIfNotExist($title)
    {
        if (!$tag = $this->findOneByTitle($title)) {
            $tag = new Tag();
            $tag->setTitle($title);

            $tag = $this->save($tag);
        }

        return $tag;
    }

    public function build($tagData)
    {
        if (false === $tagData) {
            return false;
        }

        $tag = new Tag();

        $tag
            ->setId($tagData['id'])
            ->setTitle($tagData['title'])
            ->setSlug($tagData['slug'])
            ->setWeight($tagData['weight'])
        ;

        return $tag;
    }

    /**
     * Returns the total number of entities.
     *
     * @return int The total number of entities.
     */
    public function getCount()
    {
        // TODO: Implement getCount() method.
    }

    /**
     * Deletes the entity.
     *
     * @param integer $id
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Finds an object by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     *
     * @return object The object.
     */
    public function find($id)
    {
        // TODO: Implement find() method.
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
            $orderBy = array('weight' => 'ASC');
        }

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('t.*')
            ->from('tags', 't')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('t.' . key($orderBy), current($orderBy));

        if (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                $queryBuilder->andWhere('t.'.$field.'='.$value);
            }
        }

        $statement = $queryBuilder->execute();
        $tagsData = $statement->fetchAll();

        $tags = array();
        foreach ($tagsData as $tagData) {
            $articleId = $tagData['id'];
            $tags[$articleId] = $this->build($tagData);
        }

        return $tags;
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
            ->select('t.*')
            ->from('tags', 't')
        ;

        if (!empty($criteria)) {
            $parameterNumber = 0;
            foreach ($criteria as $field => $value) {
                $queryBuilder
                    ->andWhere('t.'.$field.' = ?')
                    ->setParameter($parameterNumber, $value)
                ;
                $parameterNumber++;
            }
        }

        $statement = $queryBuilder->execute();
        $tagsData = $statement->fetch();

        return $this->build($tagsData);
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
}
