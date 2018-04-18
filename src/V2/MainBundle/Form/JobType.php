<?php

namespace V2\MainBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use V2\MainBundle\Entity\BuildLocation;

class JobType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', HiddenType::class)
            ->add('salesOrder', TextType::class, array(
                'label'                 =>  'Sales Order #',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
                ),
            ))
            ->add('estimatedShipDate', DateType::class, array(
                'label'                 =>  'Estimated Ship Date (ESD)',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
                ),
                'widget'                =>  'single_text',
                'html5'                 =>  false,
                'attr'                  =>  array(
                    'class'             =>  'js-datepicker'
                ),
            ))
            ->add('type', TextType::class, array(
                'label'                 =>  'Type',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
                ),
            ))
            ->add('model', TextType::class, array(
                'label'                 =>  'Model',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
                ),
            ))
            ->add('quantity', IntegerType::class, array(
                'label'                 =>  'Quantity',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
                ),
            ))
            // ->add('macPurchaseOrder', TextType::class, array(
            //     'label'                 =>  'MAC Purchase Order #',
            //     'label_attr'            =>  array(
            //         'class'             =>  'col-sm-4 control-label',
            //     ),
            //     'required'              =>  false,
            // ))
            ->add('buildLocation', EntityType::class, array(
                'label'                 =>  'Build Location',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
                ),
                'class'                 =>  'V2\MainBundle\Entity\BuildLocation',
                'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('bl')
                                    ->orderBy('bl.id', 'ASC');
                },
                'choice_label'          =>  'name',
                'attr'                  =>  array(
                    'class'             =>  'col-sm-2 select2-box',
                ),
            ))
            ->add('paint1', TextType::class, array(
                'label'                 =>  'Paint Color 1',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
                ),
                'mapped'                =>  false,
            ))
            ->add('paint2', TextType::class, array(
                'label'                 =>  'Paint Color 2',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
                ),
                'mapped'                =>  false,
                'required'              =>  false,
            ))
            ->add('plannerEstimatedShipDate', DateType::class, array(
                'label'                 =>  'Planner ESD',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
                ),
                'widget'                =>  'single_text',
                'html5'                 =>  false,
                'attr'                  =>  array(
                    'class'             =>  'js-datepicker'
                ),
                'required'              =>  false,
            ))
            ->add('save', SubmitType::class, array(
                'label'                 =>  'Create',
                'attr'                  =>  array(
                    'class'                 =>  'btn btn-primary',
                ),
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'V2\MainBundle\Entity\Job'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'v2_mainbundle_job';
    }


}
