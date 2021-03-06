<?php

namespace V2\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use V2\MainBundle\Entity\BuildLocation;

class EditJobType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label'                 =>  'Name',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
            ))
            ->add('salesOrder', TextType::class, array(
                'label'                 =>  'Sales Order #',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
            ))
            ->add('estimatedShipDate', DateType::class, array(
                'label'                 =>  'Estimated Ship Date (ESD)',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
                'widget'                => 'single_text',
            ))
            ->add('type', TextType::class, array(
                'label'                 =>  'Type',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
            ))
            ->add('model', TextType::class, array(
                'label'                 =>  'Model',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
            ))
            ->add('quantity', IntegerType::class, array(
                'label'                 =>  'Quantity',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
            ))
            ->add('manufacturingOrder', TextType::class, array(
                'label'                 =>  'Manufacturing Order #',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
                'required'              => false,
            ))
            ->add('macPurchaseOrder', TextType::class, array(
                'label'                 =>  'MAC Purchase Order #',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
                'required'              => false,
            ))
            ->add('buildLocation', EntityType::class, array(
                'label'                 =>  'Build Location',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
                'class'                 =>  'V2\MainBundle\Entity\BuildLocation',
                'choice_label'          =>  'name',
                'attr'                  =>  array(
                    'class'             =>  'col-sm-2 select2-box',
                ),
            ))
            ->add('plannerEstimatedShipDate', DateType::class, array(
                'label'                 =>  'Planner ESD',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
                'widget'                => 'single_text',
                'required'              => false,
            ))
            ->add('update', SubmitType::class, array(
                'label'                 =>  'Update',
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
