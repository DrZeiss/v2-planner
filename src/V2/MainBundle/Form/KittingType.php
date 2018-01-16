<?php

namespace V2\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use V2\MainBundle\Form\KittingShortType;

class KittingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('completionDate', DateType::class, array(
                'label'                 =>  'Completion Date',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
                'widget'                => 'single_text',
            ))
            ->add('initials', TextType::class, array(
                'label'                 =>  'Initials',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
            ))
            ->add('filledCompletely', ChoiceType::class, array(
                'expanded'              => true,
                'multiple'              => false,
                'choices'                  =>  array(
                    'No'        => 0,
                    'Yes'       => 1),
                'label'                 =>  '100% Filled?',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
            ))
            ->add('location', TextType::class, array(
                'label'                 =>  'Location',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
            ))
            ->add('epoxyNeeded', CheckboxType::class, array(
                'label'                 =>  'Epoxy Needed?',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
                'attr'            =>  array(
                    'class'             =>  'form-control',
                    'style'             =>  'width:20%',
                ),
                'required'              =>  false,
            ))
            ->add('epoxyDone', ChoiceType::class, array(
                'expanded'              => true,
                'multiple'              => false,
                'choices'                  =>  array(
                    'No'        => 0,
                    'Yes'       => 1),
                'label'                 =>  'Epoxy Done?',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
                'required'              =>  false,
            ))
            ->add('tubeNeeded', CheckboxType::class, array(
                'label'                 =>  'Tube Needed?',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
                'attr'            =>  array(
                    'class'             =>  'form-control',
                    'style'             =>  'width:20%',
                ),
                'required'              =>  false,
            ))
            ->add('tubeDone', ChoiceType::class, array(
                'expanded'              => true,
                'multiple'              => false,
                'choices'                  =>  array(
                    'No'        => 0,
                    'Yes'       => 1),
                'label'                 =>  'Tube Done?',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
                'required'              =>  false,
            ))
            ->add('kittingShort1', KittingShortType::class, array(
                'label'                 =>  'Kitting Short #1',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-12 control-label',
                    'style'             =>  'font-weight:bold',
                ),
                'required'              =>  false,
            ))
            // ->add('update', SubmitType::class, array(
            //     'label'                 =>  'Update',
            //     'attr'                  =>  array(
            //         'class'                 =>  'btn btn-primary',
            //     ),
            // ))
            ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'V2\MainBundle\Entity\Kitting'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'v2_mainbundle_kitting';
    }


}
