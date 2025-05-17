<?php

declare(strict_types=1);

namespace App\Form;

use App\Value\Newsletter;
use Huluti\AltchaBundle\Type\AltchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewsletterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                    new Length(['min' => 6, 'max' => 255]),
                ],

            ])
            ->add('entityId', HiddenType::class)
            ->add('security', AltchaType::class, [
                'label' => false,
                'hide_logo' => true,
                'hide_footer' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Newsletter::class,
        ]);
    }
}
