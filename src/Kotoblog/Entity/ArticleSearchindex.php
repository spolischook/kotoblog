<?php

namespace Kotoblog\Entity;

/**
 * ArticleSearchindex
 */
class ArticleSearchindex
{
    /**
     * @var string
     */
    private $word;

    /**
     * @var integer
     */
    private $article_id;

    /**
     * @var integer
     */
    private $weight;

    public $isNew = true;


    /**
     * Set word
     *
     * @param string $word
     *
     * @return ArticleSearchindex
     */
    public function setWord($word)
    {
        $this->word = $word;

        return $this;
    }

    /**
     * Get word
     *
     * @return string 
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * Set article_id
     *
     * @param integer $articleId
     *
     * @return ArticleSearchindex
     */
    public function setArticleId($articleId)
    {
        $this->article_id = $articleId;

        return $this;
    }

    /**
     * Get article_id
     *
     * @return integer 
     */
    public function getArticleId()
    {
        return $this->article_id;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     *
     * @return ArticleSearchindex
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    public function toArray()
    {
        return $articleSearchindexData = array(
            'article_id' => $this->getArticleId(),
            'word' => $this->getWord(),
            'weight' => $this->getWeight(),
        );
    }

    public static function fromArray(array $array)
    {
        $articleSearchindex = new ArticleSearchindex();

        $articleSearchindex
            ->setArticleId($array['article_id'])
            ->setWord($array['word'])
            ->setWeight($array['weight'])
        ;
        $articleSearchindex->isNew = false;

        return $articleSearchindex;
    }
}
