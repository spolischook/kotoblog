<?php

namespace Kotoblog\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleType extends AbstractType
{
    private $tagTransformer;

    public function __construct($tagTransformer)
    {
        $this->tagTransformer = $tagTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'constraints' => new Assert\NotBlank(),
                'attr' => array('class' => 'form-control'),
            ))
            ->add('text', 'textarea', array(
                'attr' => array(
                    'rows' => '14',
                    'class' => 'form-control wysiwyg',
                )
            ))
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