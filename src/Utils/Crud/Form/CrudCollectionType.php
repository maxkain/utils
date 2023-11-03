<?php

namespace App\Utils\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CrudCollectionType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['button_add'] = $options['button_add'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_add' => true,
            'allow_delete' => true,
            'button_add' => ['text' => 'Добавить элемент'],
            'entry_options' => ['block_prefix' => 'crud_collection_entry'],
        ]);
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }
}
