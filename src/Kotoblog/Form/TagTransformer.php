<?php

namespace Kotoblog\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Kotoblog\Entity\Tag;
use Kotoblog\Repository\TagRepository;

class TagTransformer implements DataTransformerInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param ArrayCollection $tags
     * @return mixed|string
     */
    public function transform($tags)
    {
        if ($tags->isEmpty()) {
            return "";
        }

        return implode(',', $tags->toArray());
    }

    public function reverseTransform($tagsString)
    {
        if (null === $tagsString) {
            return;
        }

        $tagsArray = explode (',', $tagsString);
        $tagObjects = new ArrayCollection();

        foreach ($tagsArray as $tagTitle) {
            $tag = $this->em->getRepository('Kotoblog\Entity\Tag')->findOneByTitle($tagTitle);

            if (!$tag) {
                $tag = new Tag();
                $tag->setTitle($tagTitle);

                $this->em->persist($tag);
            }

            $tagObjects->add($tag);
        }

        return $tagObjects;
    }
}
