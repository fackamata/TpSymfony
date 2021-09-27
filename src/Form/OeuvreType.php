<?php

namespace App\Form;

use App\Entity\Oeuvre;
use Symfony\Component\DomCrawler\Field\TextareaFormField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OeuvreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('description', TextareaType::class)
            ->add('image', FileType::class,[ 
                'required' => false,
                'mapped' => false,
                'label' => 'photo',
            ])
            ->add('dateCreation', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('prix')
            ->add('evenements')
            ->add('type')
            ->add('artiste')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Oeuvre::class,
        ]);
    }
}
