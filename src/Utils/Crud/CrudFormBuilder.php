<?php

namespace App\Utils\Crud;

use Symfony\Component\Form\FormBuilderInterface;

class CrudFormBuilder
{
    private array $notFormOptions = ['type', 'template', 'tab', 'view_type'];

    public function build(FormBuilderInterface $formBuilder, array $fields): void
    {
        foreach ($fields as $key => $field) {
            if (is_string($key)) {
                $fieldName = $key;
                $fieldType = $field['type'] ?? null;
                $this->unsetNotFormOptions($field);
                $fieldOptions = $field;
            } else {
                $fieldName = $field[0];
                $fieldType = $field[1];
                $fieldOptions = $field[2] ?? [];
            }

            if (isset($fieldType)) {
                $formBuilder->add($fieldName, $fieldType, $fieldOptions);
            }
        }
    }

    private function unsetNotFormOptions(array &$field): void
    {
        foreach ($field as $optionName => $option) {
            if (in_array($optionName, $this->notFormOptions)) {
                unset($field[$optionName]);
            }
        }
    }
}
