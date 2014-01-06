<?php

namespace Kotoblog\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleType extends AbstractType
{
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
            ->add('publish', 'choice', array(
                'choices'   => array(1 => 'Publish', 0 => 'Draft'),
                'required'  => false,
                'attr' => array('class' => 'form-control'),
            ))
            ->add('createdAt', 'datetime', array(
                'attr' => array('type' => 'date'),
            ))
            ->add('save', 'submit', array('attr' => array('class' => 'btn btn-default')));
    }

    public function getName()
    {
        return 'article';
    }
}