<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::Class, [
                'mapped' => false, 
                'required' => true, 
                'label' => 'Contraseña Actual', 
                'attr' => ['autofocus' => null]
            ])
            ->add('newPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'required' => false,
                'first_options' => ['label' => 'Nueva Contraseña'],
                'second_options' => ['label' => 'Confirme Contraseña'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 8]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[$&#?¿)(])(?=.*\\d).{8,}$/i',
                        'message' => "Debe ingresar una combinación de letras, números y símbolos.\nSímbolos admitidos: $&#?¿)("
                    ]),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    } 
}