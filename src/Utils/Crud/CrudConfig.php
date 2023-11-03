<?php

namespace App\Utils\Crud;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CrudConfig
{
    private string $entityClass;
    private CreatorInterface $creator;
    private UpdaterInterface $updater;
    private ListFetcherInterface $listFetcher;
    private ViewFetcherInterface $viewFetcher;
    private RemoverInterface $remover;
    private FieldsConfiguratorInterface $fieldsConfigurator;
    private ?array $formFields = null;
    private ?array $versionFields = null;
    private ?array $listFields = null;
    private array $tabs;
    private ?EventDispatcherInterface $dispatcher = null;
    private ?string $onCreateEvent = null;
    private array $stylesheets = [];
    private string $formTemplate = 'default/crud/form.html.twig';
    private string $listTemplate = 'default/crud/list.html.twig';
    private string $formTheme = 'default/crud/form_theme.html.twig';
    private string $viewTemplate = 'default/crud/view.html.twig';
    private array $actions;
    private string $createTitle = 'Создать элемент';
    private string $editTitle = 'Редактировать элемент';
    private string $listTitle = 'Список элементов';
    private string $viewTitle = 'Просмотр элемента';
    private string $versionTitle = 'Журнал изменений элемента';
    private ?string $formBackUrl = null;
    private ?string $redirectAfterSubmitUrl = null;
    private string $formBackTitle = 'Назад к списку';
    private string $formBackBreadcrumbTitle = 'Список элементов';

    /**
     * @var array<Breadcrumb>|null
     */
    private ?array $breadcrumbs = null;

    /**
     * @var array<Button>
     */
    private array $formButtons = [];

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function setEntityClass(string $entityClass): static
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    public function getCreator(): CreatorInterface
    {
        return $this->creator;
    }

    public function setCreator(CreatorInterface $creator): static
    {
        $this->creator = $creator;
        return $this;
    }

    public function getUpdater(): UpdaterInterface
    {
        return $this->updater;
    }

    public function setUpdater(UpdaterInterface $editor): static
    {
        $this->updater = $editor;
        return $this;
    }

    public function getListFetcher(): ListFetcherInterface
    {
        return $this->listFetcher;
    }

    public function setListFetcher(ListFetcherInterface $listFetcher): static
    {
        $this->listFetcher = $listFetcher;
        return $this;
    }

    public function getRemover(): RemoverInterface
    {
        return $this->remover;
    }

    public function setRemover(RemoverInterface $remover): static
    {
        $this->remover = $remover;
        return $this;
    }

    public function getFieldsConfigurator(): FieldsConfiguratorInterface
    {
        return $this->fieldsConfigurator;
    }

    public function setFieldsConfigurator(FieldsConfiguratorInterface $fieldsConfigurator): static
    {
        $this->fieldsConfigurator = $fieldsConfigurator;
        return $this;
    }

    public function getFormFields(): ?array
    {
        return $this->formFields;
    }

    public function setFormFields(?array $formFields): static
    {
        $this->formFields = $formFields;
        return $this;
    }

    public function getVersionFields(): ?array
    {
        return $this->versionFields;
    }

    public function setVersionFields(?array $versionFields): static
    {
        $this->versionFields = $versionFields;
        return $this;
    }

    public function getListFields(): ?array
    {
        return $this->listFields;
    }

    public function setListFields(?array $listFields): static
    {
        $this->listFields = $listFields;
        return $this;
    }

    public function getTabs(): array
    {
        return $this->tabs;
    }

    public function setTabs(array $tabs): static
    {
        $this->tabs = $tabs;
        return $this;
    }

    public function getStylesheets(): array
    {
        return $this->stylesheets;
    }

    public function setStylesheets(array $stylesheets): static
    {
        $this->stylesheets = $stylesheets;
        return $this;
    }

    public function getFormTemplate(): string
    {
        return $this->formTemplate;
    }

    public function setFormTemplate(string $formTemplate): static
    {
        $this->formTemplate = $formTemplate;
        return $this;
    }

    public function getListTemplate(): string
    {
        return $this->listTemplate;
    }

    public function setListTemplate(string $listTemplate): static
    {
        $this->listTemplate = $listTemplate;
        return $this;
    }

    public function getFormTheme(): string
    {
        return $this->formTheme;
    }

    public function setFormTheme(string $formTheme): static
    {
        $this->formTheme = $formTheme;
        return $this;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function setActions(array $actions): static
    {
        $this->actions = $actions;
        return $this;
    }

    public function getAction(string $action): ?array
    {
        return $this->actions[$action] ?? null;
    }

    public function getRoute(string $action): ?string
    {
        return $this->actions[$action]['route'] ?? null;
    }

    public function getCreateTitle(): string
    {
        return $this->createTitle;
    }

    public function setCreateTitle(string $createTitle): static
    {
        $this->createTitle = $createTitle;
        return $this;
    }

    public function getEditTitle(): string
    {
        return $this->editTitle;
    }

    public function setEditTitle(string $editTitle): static
    {
        $this->editTitle = $editTitle;
        return $this;
    }

    public function getListTitle(): string
    {
        return $this->listTitle;
    }

    public function setListTitle(string $listTitle): static
    {
        $this->listTitle = $listTitle;
        return $this;
    }

    public function getVersionTitle(): string
    {
        return $this->versionTitle;
    }

    public function setVersionTitle(string $versionTitle): static
    {
        $this->versionTitle = $versionTitle;
        return $this;
    }

    public function getViewTitle(): string
    {
        return $this->viewTitle;
    }

    public function setViewTitle(string $viewTitle): static
    {
        $this->viewTitle = $viewTitle;

        return $this;
    }

    public function getViewTemplate(): string
    {
        return $this->viewTemplate;
    }

    public function setViewTemplate(string $viewTemplate): static
    {
        $this->viewTemplate = $viewTemplate;

        return $this;
    }

    public function getViewFetcher(): ViewFetcherInterface
    {
        return $this->viewFetcher;
    }

    public function setViewFetcher(ViewFetcherInterface $viewFetcher): static
    {
        $this->viewFetcher = $viewFetcher;

        return $this;
    }

    public function getFormBackUrl(): ?string
    {
        return $this->formBackUrl;
    }

    public function setFormBackUrl(?string $formBackUrl): static
    {
        $this->formBackUrl = $formBackUrl;
        return $this;
    }

    public function getRedirectAfterSubmitUrl(): ?string
    {
        return $this->redirectAfterSubmitUrl;
    }

    public function setRedirectAfterSubmitUrl(?string $redirectAfterSubmitUrl): static
    {
        $this->redirectAfterSubmitUrl = $redirectAfterSubmitUrl;
        return $this;
    }

    public function getFormBackTitle(): string
    {
        return $this->formBackTitle;
    }

    public function setFormBackTitle(string $formBackTitle): static
    {
        $this->formBackTitle = $formBackTitle;
        return $this;
    }

    public function getFormBackBreadcrumbTitle(): string
    {
        return $this->formBackBreadcrumbTitle;
    }

    public function setFormBackBreadcrumbTitle(string $formBackBreadcrumbTitle): static
    {
        $this->formBackBreadcrumbTitle = $formBackBreadcrumbTitle;
        return $this;
    }

    public function getBreadcrumbs(): ?array
    {
        return $this->breadcrumbs;
    }

    public function setBreadcrumbs(?array $breadcrumbs): static
    {
        $this->breadcrumbs = $breadcrumbs;
        return $this;
    }

    public function getFormButtons(): array
    {
        return $this->formButtons;
    }

    public function setFormButtons(array $formButtons): static
    {
        $this->formButtons = $formButtons;
        return $this;
    }

    public function addFormButton(Button $button): static
    {
        $this->formButtons[] = $button;
        return $this;
    }

    public function setOnCreateEvent(?string $onCreateEvent): static
    {
        $this->onCreateEvent = $onCreateEvent;

        return $this;
    }

    public function getOnCreateEvent(): ?string
    {
        return $this->onCreateEvent;
    }

    public function getDispatcher(): ?EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    public function setDispatcher(?EventDispatcherInterface $dispatcher): static
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }
}
