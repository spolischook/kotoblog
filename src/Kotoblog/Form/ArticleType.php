<?php

namespace Kotoblog\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleType extends AbstractType
{
    private $tagTransformer;

    private $articleTextGistTransformer;

    public function __construct(TagTransformer $tagTransformer, ArticleTextGistTransformer $articleTextGistTransformer)
    {
        $this->tagTransformer = $tagTransformer;
        $this->articleTextGistTransformer = $articleTextGistTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'constraints' => new Assert\NotBlank(),
                'attr' => array('class' => 'form-control'),
            ))
            ->add(
                $builder->create('text', 'textarea', array(
                'attr' => array(
                    'class' => 'form-control', 'style' => 'height: 350px',
                )))
                ->addModelTransformer($this->articleTextGistTransformer)
            )
            ->add(
                $builder->create('tags', 'text', array(
                    'attr' => array('class' => 'form-control'),
                    'required'  => false,
                ))
                ->addModelTransformer($this->tagTransformer)
            )
            ->add('publish', 'choice', array(
                'choices'   => array(1 => 'Publish', 0 => 'Draft'),
                'attr' => array('class' => 'form-control'),
            ))
            ->add('file', 'file', array(
                'data' => null,
                'required'  => false,
            ))
            ->add('save', 'submit', array('attr' => array('class' => 'btn btn-default')));
    }

    public function getName()
    {
        return 'article';
    }
}