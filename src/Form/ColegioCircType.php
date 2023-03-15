<?php

namespace App\Form;

use App\Entity\ColegioCirc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Circunscripcion;
use App\Entity\Colegio;
use App\Entity\Provincia;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;

class ColegioCircType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('caracteresPermitidos', TextType::class, ['required' => false, 'attr' => ['maxlength' => '30'] ])
            ->add('domicilio', TextType::class, ['attr' => ['maxlength' => '120'] ])
            ->add('telefono1', TextType::class, ['required' => false, 'label' => 'Teléfono', 'attr' => ['maxlength' => '20']])
            ->add('telefono2', TextType::class, ['required' => false, 'label' => 'Teléfono Alternativo', 'attr' => ['maxlength' => '20']])
            ->add('correo', EmailType::class, ['required' => false, 'attr' => ['maxlength' => '50']])
            ->add('estado', CheckboxType::class, ['required' => false, 'label' => 'Deshabilitado', 'label_attr' => ['class' => 'mt-1'] ])
            ->add('visible', CheckboxType::class,  ['required' => false, 'label_attr' => ['class' => 'mt-1'] ])
            ->add('localidad', TextType::class, ['attr' => ['maxlength' => '80'] ])
            ->add('provincia', EntityType::class ,[
                'class' => Provincia::class,
                'placeholder' => 'Seleccione una Provincia',
                'choice_label' => 'provincia',
                'label'    => 'Provincia',               
            ])
            ->add('colegio', EntityType::class ,[
                'class' => Colegio::class,
                'placeholder' => 'Seleccione un Colegio',
                'choice_label' => 'colegio',
                'label'    => 'Colegio', 
                'attr' => ['autofocus' => null],              
            ])
            ->add('circunscripcion', EntityType::class ,[
                'class' => Circunscripcion::class,
                'placeholder' => 'Seleccione una Circunscripción',
                'choice_label' => 'circunscripcion',
                'label'    => 'Circunscripción',               
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ColegioCirc::class,
        ]);
    }

    /**
     * Se agrega función para ordenar el combo de localidad
     *
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     * @return void
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        usort($view->children['circunscripcion']->vars['choices'], function (ChoiceView $a, ChoiceView $b) {
            return strcasecmp($a->label, $b->label);
        });
        usort($view->children['colegio']->vars['choices'], function (ChoiceView $a, ChoiceView $b) {
            return strcasecmp($a->label, $b->label);
        });
        usort($view->children['provincia']->vars['choices'], function (ChoiceView $a, ChoiceView $b) {
            return strcasecmp($a->label, $b->label);
        });       
    }

}
