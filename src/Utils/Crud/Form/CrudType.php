<?php

namespace App\Utils\Form;

use App\Utils\Crud\CrudFormBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CrudType extends AbstractType
{
    public function __construct(
        private CrudFormBuilder $formBuilder
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder->build($builder, $options['fields']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, $this->resolveShowIfFields(...));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['custom_vars'] = $options['custom_vars'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'fields' => [],
            'custom_vars' => []
        ]);
    }

    public function resolveShowIfFields(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();
        foreach ($form->all() as $child) {
            $ifFields = $child->getConfig()->getOption('attr')['data-show-if'] ?? null;
            if ($ifFields !== null) {
                $show = true;
                foreach ($ifFields as $ifFieldCode => $ifFieldValue) {
                    $fieldName = explode('_', $ifFieldCode)[1];
                    if ($data[$fieldName] !== $ifFieldValue) {
                        $show = false;
                        break;
                    }
                }

                if (!$show) {
                    unset($data[$child->getName()]);
                    $form->remove($child->getName());
                }
            }
        }

        $event->setData($data);
    }
}
