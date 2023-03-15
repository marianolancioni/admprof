<?php

namespace App\Form;

use App\Entity\Provincia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProvinciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('provincia', TextType::class,['attr' => ['autofocus' => null, 'maxlength' => '30'] ])
            /*Para el campo Estado el FALSE or 0: Habilitado y el TRUE or 1: Deshabilitado*/ 
            ->add('estado', CheckboxType::class, ['required' => false, 'label' => 'Deshabilitado', 'label_attr' => ['class' => 'mt-1'] ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Provincia::class,
        ]);
    }
}
