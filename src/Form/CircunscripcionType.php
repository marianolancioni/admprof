<?php

namespace App\Form;

use App\Entity\Circunscripcion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CircunscripcionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('circunscripcion', TextType::class, ['attr' => ['autofocus' => null, 'maxlength' => '30'] ])
            /*Para el campo Estado el FALSE or 0: Habilitado y el TRUE or 1: Deshabilitado*/ 
            ->add('estado', CheckboxType::class, ['required' => false, 'label' => 'Deshabilitado', 'label_attr' => ['class' => 'mt-1'] ])
            /*Para el campo Visible el FALSE or 0: No Visible y el TRUE or 1: Visible*/ 
            ->add('visible', CheckboxType::class, ['required' => false, 'label' => 'Visible',  'label_attr' => ['class' => 'mt-1'] ]);
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Circunscripcion::class,
        ]);
    }
}
