<?php
namespace form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('trade_number', TextType::class, [
                'label' => 'Номер торгов',
                'attr' => [
                    'placeholder' => 'Введите номер торгов (например, 31710-ОТПП)'
                ]
            ])
            ->add('lot_number', IntegerType::class, [
                'label' => 'Номер лота',
                'attr' => [
                    'placeholder' => 'Введите номер лота (1-9999)'
                ]
            ])
            ->add('submit_btn', SubmitType::class, [
                'label' => 'Найти',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Установите здесь действие, которое будет выполнено при отправке формы
             'action' => '/index.php',
            // Установите здесь метод, используемый при отправке формы
             'method' => 'POST',
        ]);
    }
}
