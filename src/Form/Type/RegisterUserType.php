<?php

/*
 * This file is part of the appname project.
 *
 * (c) Romain Gautier <mail@romain.sh>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Type;

use App\Form\Data\RegisterUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'label' => 'Username',
                'help' => 'The username is unque and is not showed to other users',
                'attr' => [
                    'maxlength' => 50,
                ],
                'label_attr' => [
                    'class' => '',
                ],
            ])
            ->add('display_name', TextType::class, [
                'required' => true,
                'label' => 'Display name',
                'help' => 'The display name is showed to other users',
                'attr' => [
                    'maxlength' => 50,
                ],
                'label_attr' => [
                    'class' => '',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RegisterUser::class,
        ]);
    }
}
