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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class KittingShortType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('partNumber', TextType::class, array(
                'label'                 =>  'Part Number',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
                ),
            ))
            // ->add('shortClass', CheckboxType::class, array(
            //     'label'                 =>  'Part Painted?',
            //     'label_attr'            =>  array(
            //         'class'             =>  'col-sm-3 control-label',
            //     ),
            //     'attr'            =>  array(
            //         'class'             =>  'form-control',
            //         'style'             =>  'width:20%',
            //     ),
            //     'required'              =>  false,
            // ))
            ->add('dateNeeded', DateType::class, array(
                'label'                 =>  'Date Needed',
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
            ->add('vendor', TextType::class, array(
                'label'                 =>  'Vendor',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
                ),
                'required'              =>  false,
            ))
            ->add('vendorPoNumber', TextType::class, array(
                'label'                 =>  'Vendor PO#',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
                ),
                'required'              =>  false,
            ))
            ->add('modWo', TextType::class, array(
                'label'                 =>  'Mod WO#',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
                ),
                'required'              =>  false,
            ))
            ->add('estimatedDeliveryDate', DateType::class, array(
                'label'                 =>  'Estimated Delivery Date (ESD)',
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
            // ->add('modDoneDate', DateType::class, array(
            //     'label'                 =>  'Mod Done Date',
            //     'label_attr'            =>  array(
            //         'class'             =>  'col-sm-3 control-label',
            //     ),
            //     'widget'                => 'single_text',
            //     'required'              =>  false,
            // ))
            // ->add('receivedDate', DateType::class, array(
            //     'label'                 =>  'Received Date',
            //     'label_attr'            =>  array(
            //         'class'             =>  'col-sm-3 control-label',
            //     ),
            //     'widget'                => 'single_text',
            //     'required'              =>  false,
            // ))
            ->add('quantity', IntegerType::class, array(
                'label'                 =>  'Quantity',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
                ),
            ))            
            ->add('notes', TextareaType::class, array(
                'label'                 =>  'Notes',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-4 control-label',
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
            'data_class' => 'V2\MainBundle\Entity\KittingShort'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'v2_mainbundle_kittingshort';
    }


}
