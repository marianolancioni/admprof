<?php

namespace App\Form;

use App\Entity\Circunscripcion;
use App\Entity\Colegio;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'Usuario',
                    'required' => true,
                    'attr' => ['autofocus' => null, 'maxlength' => '180']
                ]
            )
            ->add('colegio', EntityType::class, [
                'required' => false,
                'class' => Colegio::class,
                'placeholder' => 'Seleccione un Colegio',
                'help' => 'Tenga en cuenta que No Seleccionar un Colegio hará que el usuario acceda a todos los Colegios',
                'choice_label' => function (Colegio $colegio) {
                    return $colegio->__toString();
                }
            ])
            ->add('circunscripcion', EntityType::class, [
                'required' => false,
                'class' => Circunscripcion::class,
                'placeholder' => 'Seleccione una Circunscripción',
                'choice_label' => 'circunscripcion',
                'label'    => 'Circunscripción',
                'help' => 'Tenga en cuenta que No Seleccionar una Circunscripción hará que el usuario acceda a todas las Circunscripciones'
            ])
            ->add('dni', NumberType::class, ['label' => 'DNI', 'required' => false, 'attr' => ['max' => '99999999']])
            ->add('apellido', TextType::class, ['label' => 'Apellido', 'required' => true, 'attr' => ['maxlength' => '50'] ])
            ->add('nombre', null, ['label' => 'Nombre', 'attr' => ['maxlength' => '50'] ])
            ->add('email', EmailType::class, [
                'label' => 'Correo', 
                'required' => true, 
                'help' => 'Se enviará clave del usuario a la dirección de correo que se indique aquí',
                'attr' => ['maxlength' => '50'] ])
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true, // render check-boxes
                'label'    => 'Rol',
                'help' => 'Sugerencia: Seleccionar un único rol para el usuario',
                'choices'  => [
                    'Consultor' => 'ROLE_CONSULTOR',
                    'Editor' => 'ROLE_EDITOR',
                    'Administrador' => 'ROLE_ADMIN',
                    'Super Admin' => 'ROLE_SUPER_ADMIN'
                ],
                'label_attr' => array(
                    'class' => 'checkbox-inline'
                )
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
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
    }
  
}