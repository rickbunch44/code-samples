<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextType::class)
            ->add('groupId', TextType::class)
            ->add('mediaUrl', TextType::class, [
                'required' => false,
                'empty_data' => null,
            ])
            ->add('mediaType', TextType::class, [
                'required' => false,
                'empty_data' => null,
            ])
            ->add('privacy', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Post::class,
        ));
    }

    public function getBlockPrefix()
    {
        return 'ping_group_post';
    }
}