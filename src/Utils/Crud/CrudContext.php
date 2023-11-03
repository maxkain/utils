<?php

namespace App\Utils\Crud;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CrudContext
{
    private CrudConfig $crudConfig;
    private Request $request;
    private string $currentAction;
    private string $currentRoute;
    private array $customTemplateVars = [];
    private array $formOptions = [];
    private ?FormInterface $form = null;
    private mixed $entity = null;
    private mixed $dto = null;

    public function getCrudConfig(): CrudConfig
    {
        return $this->crudConfig;
    }

    public function setCrudConfig(CrudConfig $crudConfig): static
    {
        $this->crudConfig = $crudConfig;
        return $this;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): static
    {
        $this->request = $request;
        return $this;
    }

    public function getCurrentAction(): string
    {
        return $this->currentAction;
    }

    public function setCurrentAction(string $currentAction): static
    {
        $this->currentAction = $currentAction;
        return $this;
    }

    public function getCurrentRoute(): string
    {
        return $this->currentRoute;
    }

    public function setCurrentRoute(string $currentRoute): static
    {
        $this->currentRoute = $currentRoute;
        return $this;
    }

    public function getCustomTemplateVars(): array
    {
        return $this->customTemplateVars;
    }

    public function setCustomTemplateVars(array $customTemplateVars): static
    {
        $this->customTemplateVars = $customTemplateVars;
        return $this;
    }

    public function getCustomTemplateVar(string $key): mixed
    {
        return $this->customTemplateVars[$key] ?? null;
    }

    public function setCustomTemplateVar(string $key, mixed $value): static
    {
        $this->customTemplateVars[$key] = $value;
        return $this;
    }

    public function getFormOptions(): array
    {
        return $this->formOptions;
    }

    public function setFormOptions(array $formOptions): static
    {
        $this->formOptions = $formOptions;
        return $this;
    }

    public function getForm(): ?FormInterface
    {
        return $this->form;
    }

    public function setForm(?FormInterface $form): static
    {
        $this->form = $form;
        return $this;
    }

    public function getEntity(): mixed
    {
        return $this->entity;
    }

    public function setEntity(mixed $entity): static
    {
        $this->entity = $entity;
        return $this;
    }

    public function getDto(): mixed
    {
        return $this->dto;
    }

    public function setDto(mixed $dto): static
    {
        $this->dto = $dto;
        return $this;
    }
}
