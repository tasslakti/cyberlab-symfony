<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startTime', DateTimeType::class, [
                'label' => 'Date et heure de début',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control datetimepicker',
                    'min' => (new \DateTime())->format('Y-m-d\TH:i')
                ]
            ])
            ->add('endTime', DateTimeType::class, [
                'label' => 'Date et heure de fin',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control datetimepicker',
                    'min' => (new \DateTime())->format('Y-m-d\TH:i')
                ]
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes (optionnel)',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Notes supplémentaires pour la réservation...'
                ]
            ]);

        // Seulement pour les instructeurs/admin
        if ($options['is_instructor']) {
            $builder->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => Reservation::STATUS_PENDING,
                    'Confirmée' => Reservation::STATUS_CONFIRMED,
                    'Annulée' => Reservation::STATUS_CANCELLED,
                    'Terminée' => Reservation::STATUS_COMPLETED,
                ],
                'attr' => ['class' => 'form-select']
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'is_instructor' => false,
        ]);
    }
}