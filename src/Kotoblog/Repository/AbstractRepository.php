<?php

namespace Kotoblog\Repository;

use Kotoblog\Entity\SlugAbleInterface;
use Kotoblog\Repository\ObjectRepository;
use Doctrine\DBAL\Connection;

abstract class AbstractRepository implements ObjectRepository
{
    private $transliterator = array('Gedmo\Sluggable\Util\Urlizer', 'transliterate');
    private $urlizer = array('Gedmo\Sluggable\Util\Urlizer', 'urlize');
    const MAX_SIMILAR_SLUGS = 10;

    /** @var Connection */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        return $this->findBy(array());
    }

    protected function processSlug(SlugAbleInterface $object, $text)
    {
        $slug = call_user_func_array(
            $this->transliterator,
            array($text, '-', $object)
        );

        $slug = call_user_func($this->urlizer, $slug, '-');

        if (function_exists('mb_strtolower')) {
            $slug = mb_strtolower($slug);
        } else {
            $slug = strtolower($slug);
        }

        $slug = $this->makeUniqueSlug($slug);

        $object->setSlug($slug);
    }

    private function makeUniqueSlug($slug, $index = 0)
    {
        if ($index > self::MAX_SIMILAR_SLUGS) {
            throw new \Exception(sprintf('Max similar slug cat\'t be more then %s', self::MAX_SIMILAR_SLUGS));
        }

        $newSlug = $index ? $slug . '-' . $index: $slug;
        $object = $this->findOneBySlug($newSlug);

        if (false !== $object) {
            $newSlug = $this->makeUniqueSlug($slug, ++$index);
        }

        return $newSlug;
    }

    public function __call($method, $arguments)
    {
        switch (true) {
            case (0 === strpos($method, 'findBy')):
                $by = substr($method, 6);
                $method = 'findBy';
                break;

            case (0 === strpos($method, 'findOneBy')):
                $by = substr($method, 9);
                $method = 'findOneBy';
                break;

            default:
                throw new \BadMethodCallException(
                    "Undefined method '$method'. The method name must start with ".
                    "either findBy or findOneBy!"
                );
        }

        if (empty($arguments)) {
            throw new \Exception('You must provide an arguments');
        }

        $fieldName = lcfirst(\Doctrine\Common\Util\Inflector::classify($by));

        switch (count($arguments)) {
            case 1:
                return $this->$method(array($fieldName => $arguments[0]));

            case 2:
                return $this->$method(array($fieldName => $arguments[0]), $arguments[1]);

            case 3:
                return $this->$method(array($fieldName => $arguments[0]), $arguments[1], $arguments[2]);

            case 4:
                return $this->$method(array($fieldName => $arguments[0]), $arguments[1], $arguments[2], $arguments[3]);

            default:
                // Do nothing
        }
    }

    abstract protected function build($data);
}
