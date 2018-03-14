<?php

namespace V2\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use V2\UserBundle\Entity\User;

class EditUserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roleChoices = array(
            'BOM Builder'       => 'ROLE_BOM_BUILDER',
            'Kitter'            => 'ROLE_KITTER',
            'Receiver'          => 'ROLE_RECEIVER',
            'Manufacturer'      => 'ROLE_MANUFACTURER',
            'Supply Chain'      => 'ROLE_SUPPLY_CHAIN',
            'MAC Production'    => 'ROLE_MAC_PRODUCTION',
            'V2 Production'     => 'ROLE_V2_PRODUCTION',
            'Scheduler'         => 'ROLE_SCHEDULER',
            'Shipper'           => 'ROLE_SHIPPER',
            'Painter'           => 'ROLE_PAINTER',
            'Administrator'     => 'ROLE_ADMIN',
        );

        $builder
            ->add('fullname', TextType::class, array(
                'label'                 =>  'Name',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
            ))
            ->add('username', TextType::class, array(
                'label'                 =>  'Username',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
                'attr' => array(
                    'readonly'          =>  true,
                ),
            ))
            ->add('email', EmailType::class, array(
                'label'                 =>  'Email',
                'label_attr'            =>  array(
                    'class'             =>  'col-sm-3 control-label',
                ),
            ))
            ->add('update', SubmitType::class, array(
                'label'                 =>  'Update',
                'attr'                  =>  array(
                    'class'                 =>  'btn btn-primary',
                ),
            ));

        // Allow password change for admins and the user themselves
        if ($options['isAdmin'] || $options['isMe']) {
            $builder->add('plainPassword', RepeatedType::class, array(
                'type'  => PasswordType::class,
                'options' => array(
                    'translation_domain'=>  'FOSUserBundle',
                ),
                'first_options'         =>  array(
                    'label'             =>  'Password', 
                    'label_attr'        =>  array(
                        'class'             =>  'col-sm-3 control-label',
                    ),
                ),
                'second_options'        =>  array(
                    'label'             =>  'Password Confirmation', 
                    'label_attr'        =>  array(
                        'class'             =>  'col-sm-3 control-label',
                    ),
                ),
                'invalid_message'       =>  'Password mismatch!',
                'required'              =>  false,
            ));
            if ($options['isAdmin']) {
                $builder->add('roles', ChoiceType::class, array(
                    'choices'               =>  $roleChoices,
                    'label'                 =>  'Role(s)',
                    'label_attr'            =>  array(
                        'class'             =>  'col-sm-3 control-label',
                    ),
                    'multiple'              =>  true,                
                ));
            }
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'V2\UserBundle\Entity\User',
            'isMe' => false,
            'isAdmin' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'v2_userbundle_user';
    }


}
