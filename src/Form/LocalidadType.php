<?php

namespace App\Form;

use App\Entity\Localidad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Provincia;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;


class LocalidadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('localidad', TextType::class, ['attr' => ['autofocus' => null, 'maxlength' => '80']] )
            ->add('codigoPostal', NumberType::class, ['label' => 'Código Postal'])
            ->add('subCodigoPostal', NumberType::class, ['label' => 'Sub Código Postal']) 
            /*Para el campo Estado el FALSE or 0: Habilitado y el TRUE or 1: Deshabilitado*/ 
            ->add('estado', CheckboxType::class, ['required' => false, 'label' => 'Deshabilitado', 'label_attr' => ['class' => 'mt-1'] ])
            ->add('provincia', EntityType::class ,[
                'class' => Provincia::class,
                'placeholder' => 'Seleccione una Provincia',
                'choice_label' => 'provincia',
                'label'    => 'Provincia',               
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Localidad::class,
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
        usort($view->children['provincia']->vars['choices'], function (ChoiceView $a, ChoiceView $b) {
            return strcasecmp($a->label, $b->label);
        });       
    }


}
