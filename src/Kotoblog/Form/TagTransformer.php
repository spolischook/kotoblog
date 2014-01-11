<?php

namespace Kotoblog\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Kotoblog\Entity\Tag;
use Kotoblog\Repository\TagRepository;

class TagTransformer implements DataTransformerInterface
{
    /** @var TagRepository */
    private $tagRepository;

    public function __construct($tagRepository)
    {
        $this->tagRepository = $tagRepository;
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
        $tagObjects = array();

        foreach ($tagsArray as $tagTitle) {
            $tagObjects[] = $this->tagRepository->findOneByTitleOrCreateNewIfNotExist(trim($tagTitle));
        }

        return new ArrayCollection($tagObjects);
    }
}
