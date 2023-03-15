<?php

namespace App\Form;

use App\Entity\Estado;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EstadoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('estadoProfesional', TextType::class, ['label' => 'Estado Profesional', 'required' => true, 'attr' => ['autofocus' => null, 'maxlength' => '30']])
            ->add('estado', NumberType::class, ['label' => 'Código', 'required' => true, 'attr' => array('class' => 'smallEntry')])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Estado::class,
        ]);
    }
}
