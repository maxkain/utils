<?php

declare(strict_types=1);

namespace App\Utils\Crud;

class FieldsDefaultsSetter
{
    public function setFieldsDefaults(array &$fields, array $options = []): void
    {
        foreach ($fields as &$field) {
            $this->setFieldDefaults($field, $options);
        }

        array_walk_recursive($fields, function (&$value, $key) use ($options) {
            if ($key === 'fields') {
                foreach ($value as &$field) {
                    $this->setFieldDefaults($field, $options);
                }
            }
        });
    }

    protected function setFieldDefaults(array &$field, array $options = []): void
    {
        $field = array_replace_recursive(['required' => false], $field, $options);

        if (str_ends_with($field['type'] ?? '', 'CollectionType') && ($field['disabled'] ?? false)) {
            $field['allow_add'] = false;
            $field['allow_delete'] = false;
        }

        $field['tab'] = $field['tab'] ?? 'basic';
    }
}
