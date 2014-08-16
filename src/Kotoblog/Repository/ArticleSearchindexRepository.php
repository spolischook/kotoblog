<?php

namespace Kotoblog\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\Common\Collections\ArrayCollection;
use Kotoblog\TextHelper;
use Kotoblog\Entity\ArticleSearchindex;
use Kotoblog\Entity\Article;
use Kotoblog\Repository\ArticleRepository;

class ArticleSearchindexRepository extends AbstractRepository implements SearchableInterface
{
    /** @var $db Connection */
    protected $db;

    /** @var $articleRepository ArticleRepository */
    protected $articleRepository;

    /** @var $textHelper TextHelper */
    protected $textHelper;

    protected $searchWeight;
    protected $searchMinWordLength;

    public function __construct($db, $articleRepository, $textHelper, $searchWeight, $searchMinWordLength)
    {
        $this->db = $db;
        $this->articleRepository = $articleRepository;
        $this->textHelper = $textHelper;
        $this->searchWeight = $searchWeight;
        $this->searchMinWordLength = $searchMinWordLength;
    }

    /** @var $article Article */
    public function createIndex($article)
    {
        $words = new ArrayCollection();

        $tags = '';
        foreach ($article->getTags() as $tag) {
            $tags .= " " . $tag->getTitle();
        }

        $wordsTitle = $this->textHelper->splitString($article->getTitle());
        $wordsText = $this->textHelper->splitString($article->getText());
        $wordsTags = $this->textHelper->splitString($tags);

        foreach ($wordsTitle as $key => $word)
        {
            $this->processWord($word, $article, $words, $this->searchWeight['title']);
        }

        foreach ($wordsText as $key => $word)
        {
            $this->processWord($word, $article, $words, $this->searchWeight['text']);
        }

        foreach ($wordsTags as $key => $word)
        {
            $this->processWord($word, $article, $words, $this->searchWeight['tag']);
        }

        foreach ($words as $word) {
            $this->save($word);
        }
    }

    public function updateIndex($article)
    {
        $this->delete($article);
        $this->createIndex($article);
    }

    public function search($string)
    {
        $words = $this->textHelper->splitString($string);
        $preResults = array();

        foreach ($words as $word) {
            $word = $this->textHelper->normalize($word);

            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder
                ->select('s.*')
                ->from('articles_searchindex', 's')
                ->where('s.word = :word')
                ->setParameter(':word', $word)
            ;

            $statement = $queryBuilder->execute();
            $articlesSearchIndexArray = $statement->fetchAll();

            foreach ($articlesSearchIndexArray as $articlesSearchIndex) {
                @$preResults[$articlesSearchIndex['article_id']] += $articlesSearchIndex['weight'];
            }
        }

        //ToDo: Make priority for articles that have all searching words

        asort($preResults);
        $preResults = array_reverse($preResults, true);
        $searchResults = array();


        foreach ($preResults as $articleId => $weight) {
            $searchResults[] = $this->articleRepository->find($articleId);
        }

        return $searchResults;
    }

    protected function build($data)
    {
        // TODO: Implement build() method.
    }

    /**
     * Saves the entity to the database.
     *
     * @param ArticleSearchindex $articleSearchindex
     */
    public function save($articleSearchIndex)
    {
        $articleSearchIndexData = $articleSearchIndex->toArray();

        if ($articleSearchIndex->isNew) {
            $this->db->insert('articles_searchindex', $articleSearchIndexData);
        } else {
            $this->db->update('articles_searchindex', $articleSearchIndexData, array(
                'word' => $articleSearchIndex->getWord(),
                'article_id' => $articleSearchIndex->getArticleId(),
            ));
        }
    }

    /**
     * Deletes the entity.
     *
     * @param integer $id
     */
    public function delete($article)
    {
        $this->db->delete('articles_searchindex', array('article_id' => $article->getId()));
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
        // TODO: Implement findBy() method.
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
        // TODO: Implement findOneBy() method.
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

    protected function processWord($word, $searchableObject, ArrayCollection $words, $weight)
    {
        if (mb_strlen($word, "UTF-8") > $this->searchMinWordLength)
        {
            $word = $this->textHelper->normalize($word);

            if (false != $word) {
                $articleSearchindex = new ArticleSearchindex();

                $articleSearchindex
                    ->setArticleId($searchableObject->getId())
                    ->setWord($word)
                ;

                if ($words->containsKey($word)) {
                    $articleSearchindex->setWeight($words->get($word)->getWeight() + $weight);
                } else {
                    $articleSearchindex->setWeight($weight);
                }

                $words->set($word, $articleSearchindex);
            }
        }
    }
}