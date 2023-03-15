<?php

namespace App\Form;

use App\Entity\Profesional;
use App\Entity\Estado;
use App\Entity\Localidad;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Service\ControlaMatriculaService;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;


class ProfesionalType extends AbstractType
{
    private $_controlaMatriculaService;

    public function __construct(ControlaMatriculaService $controlaMatriculaService)
    {
        $this->_controlaMatriculaService =  $controlaMatriculaService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
        ->add('matricula', TextType::class, ['label' => 'Matrícula', 'required' => true, 'attr' => ['autofocus' => null, 'maxlength' => '20'] ])
        ->add('numeroDocumento', NumberType::class, [
            'label' => 'DNI',
            'required' => false,
            'attr' => ['max' => '99999999'],
            'constraints' => [                
                new Regex([
                    'pattern' => '/^[0123456789]{1,8}$/',
                    'message' => "Debe ingresar un máximo de 8 dígitos"
                ]),
            ]
        ])
        ->add('apellido', TextType::class, ['label' => 'Apellido', 'required' => true, 'attr' => array('maxlength' => '80')])
        ->add('nombre', TextType::class, ['label' => 'Nombre', 'required' => false, 'attr' => array('maxlength' => '80')])
        ->add('correo', EmailType::class, ['label' => 'Correo', 'required' => false, 'attr' => array('maxlength' => '50')])
        ->add('domicilio', TextType::class, ['label' => 'Domicilio', 'required' => false, 'attr' => array('maxlength' => '120')])
        ->add('telefono1', TextType::class, [
            'label' => 'Telefono1',
            'required' => false,
            'attr' => array('maxlength' => '20'),
            'constraints' => [                
                new Regex([
                    'pattern' => '/^[0123456789-]{1,20}$/',
                    'message' => "Debe ingresar sólo números o guiones"
                ]),
            ]
        ])
        ->add('telefono2', TextType::class, [
            'label' => 'Telefono2',
             'required' => false,
             'attr' => array('maxlength' => '20'),
             'constraints' => [                
                new Regex([
                    'pattern' => '/^[0123456789-]{1,20}$/',
                    'message' => "Debe ingresar sólo números o guiones"
                ]),
            ]
        ])
        
        ->add('desde', DateType::class, [
            'widget' => 'single_text',
            'label' => 'Fecha Desde',
            'attr' => ['class' => 'js-datepicker'],
            'required' =>false,
            ])
        
            ->add('hasta', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha Hasta',
                'attr' => ['class' => 'js-datepicker'],
                'required' =>false,
                ])

            ->add('localidad', EntityType::class, [
                'required' => true,
                'class' => Localidad::class,
                'placeholder' => 'Seleccione una Localidad',
                'choice_label' => 'localidad',
                'label'    => 'Localidad'
            ])

            ->add('estadoProfesional', EntityType::class, [
                'required' => true,
                'class' => Estado::class,
                'placeholder' => 'Seleccione un estado',
                'choice_label' => 'estadoProfesional',
                'label'    => 'Estado'
            ])

            ->add('tipoDocumento')
            ->add('observaciones', TextType::class, ['label' => 'Observación', 'required' => false, 'attr' => array('maxlength' => '255')])           
            ;
        
        
            $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);
    }

        function onPostSubmit(FormEvent $event) {
             $form = $event->getForm();
             $data = $event->getData();
             $idprof = $data->getId();

             $matricula  = $form->get('matricula')->getData();

             $desde  = $form->get('desde')->getData();
             $hasta  = $form->get('hasta')->getData();

             $circuns = $event->getForm()->getConfig()->getOptions()['circu'];
             $coleg = $event->getForm()->getConfig()->getOptions()['cole'];
             $col = $coleg->getId();
             $circ = $circuns->getId();
             $validacion = $event->getForm()->getConfig()->getOptions()['validacion'];


            if($validacion=='new'){
                $validaCaracteres = $this->_controlaMatriculaService->ValidaMatricula($matricula,$col,$circ);
                $validaDuplicada = $this->_controlaMatriculaService->ValidaMatricuDuplicada($matricula,$col,$circ,'N',0);
                if ($validaCaracteres==false) {
                    $form['matricula']->addError(new FormError("Debe ingresar sólo caracteres permitidos"));
                }
                if ($validaDuplicada==false) {
                    $form['matricula']->addError(new FormError("MATRICULA EXISTENTE"));
                }
                if ($desde > $hasta && isset($hasta) && isset($desde) ){
                    $form['desde']->addError(new FormError("La fecha Desde debe ser menor que la fecha Hasta"));
                }
            }

            if($validacion=='edit'){
                $validaCaracteres = $this->_controlaMatriculaService->ValidaMatricula($matricula,$col,$circ);
                $validaDuplicada = $this->_controlaMatriculaService->ValidaMatricuDuplicada($matricula,$col,$circ,'E',$idprof);
                if ($validaCaracteres==false) {
                    $form['matricula']->addError(new FormError("Debe ingresar sólo caracteres permitidos"));
                }
                if ($validaDuplicada==false) {
                    $form['matricula']->addError(new FormError("MATRICULA EXISTENTE"));
                }
                if ($desde > $hasta && isset($hasta) && isset($desde) ){
                    $form['desde']->addError(new FormError("La fecha Desde debe ser menor que la fecha Hasta"));
                }                
            }

        }
        

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver ->setRequired('circu');
        $resolver ->setRequired('cole');
        $resolver ->setRequired('validacion');
        $resolver->setDefaults([
            'data_class' => Profesional::class,
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
        usort($view->children['localidad']->vars['choices'], function (ChoiceView $a, ChoiceView $b) {
            //cambio para que no considere las vocales acentuadas después de la Z en los ordenamientos
            //ejemplo 'córdoba' quede entre cora y coronda, no entre curupaty y desmochado. 
            $patterns = array(
                'a' => '(á|à|â|ä|Á|À|Â|Ä)',
                'e' => '(é|è|ê|ë|É|È|Ê|Ë)',
                'i' => '(í|ì|î|ï|Í|Ì|Î|Ï)',
                'o' => '(ó|ò|ô|ö|Ó|Ò|Ô|Ö)',
                'u' => '(ú|ù|û|ü|Ú|Ù|Û|Ü)'
            );          
            $name1 =  $a->label;
            $name2 =  $b->label;
            $name1 = preg_replace(array_values($patterns), array_keys($patterns), $name1);
            $name2 = preg_replace(array_values($patterns), array_keys($patterns), $name2);
            return strcasecmp($name1, $name2);
            //return strcasecmp($a->label, $b->label);
        });       
    }


}
