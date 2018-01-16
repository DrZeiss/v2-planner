<?php

namespace V2\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class SchedulingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('priority', IntegerType::class, array(
                'label'                 =>  'Priority',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
            ))
            ->add('subReady', ChoiceType::class, array(
                'expanded'              => true,
                'multiple'              => false,
                'choices'                  =>  array(
                    'No'        => 0,
                    'Yes'       => 1),
                'label'                 =>  'Sub Ready?',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
            ))
            ->add('completionDate', DateType::class, array(
                'label'                 =>  'Completion Date',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
                'widget'                => 'single_text',
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
            'data_class' => 'V2\MainBundle\Entity\Scheduling'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'v2_mainbundle_scheduling';
    }


}
