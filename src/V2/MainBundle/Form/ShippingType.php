<?php

namespace V2\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ShippingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shipDate', DateType::class, array(
                'label'                 =>  'Ship Date',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
                'widget'                => 'single_text',
            ))
            ->add('isComplete', ChoiceType::class, array(
                'expanded'              => true,
                'multiple'              => false,
                'choices'                  =>  array(
                    'No. (Partial only)'    => 0,
                    'Yes. (Complete)'       => 1),
                'label'                 =>  'Complete?',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
                'attr'                  =>  array(
                    'class'             =>  'col-sm-3 radio radio-success',
                ),
            ))
            ->add('secondShipDate', DateType::class, array(
                'label'                 =>  'Second Ship Date',
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
            'data_class' => 'V2\MainBundle\Entity\Shipping'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'v2_mainbundle_shipping';
    }


}
