<?php

namespace App\Utils\Crud;

use App\Utils\Form\CrudType;
use App\Utils\Security\AbstractPermissionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractCrudController extends AbstractController
{
    protected CrudContext $context;

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            ManagerRegistry::class,
        ]);
    }

    public function __invoke(Request $request)
    {
        return $this->index($request);
    }

    public function index(Request $request): Response
    {
        $currentRoute = $request->attributes->get('_route');
        $routePrefix = $this->extractRoutePrefix($currentRoute);
        $action = $this->extractAction($currentRoute);

        $config = (new CrudConfig())->setActions($this->configureActions($routePrefix));
        if ($config->getAction($action) === null || !method_exists($this, $action)) {
            throw $this->createNotFoundException();
        }

        $config->setTabs($this->configureTabs());
        $listAction = $config->getAction('list');
        if ($listAction !== null) {
            $config->setFormBackUrl($this->generateUrl($config->getRoute('list')));
        }

        $this->configureCrud($config);
        $context = (new CrudContext())
            ->setRequest($request)
            ->setCrudConfig($config)
            ->setCurrentAction($action)
            ->setCurrentRoute($currentRoute);

        $this->context = $context;

        $this->handleBeforeAction();

        return $this->$action($context);
    }

    protected function handleBeforeAction(): void
    {

    }

    protected function extractRoutePrefix(string $route): string
    {
        $routeParts = explode('_', $route);
        unset($routeParts[count($routeParts) - 1]);

        return implode('_', $routeParts) . '_';
    }

    protected function extractAction(string $route): string
    {
        $routeParts = explode('_', $route);

        return $routeParts[count($routeParts) - 1];
    }

    protected function configureActionList(): array
    {
        return ['create', 'edit', 'delete', 'list', 'listPage', 'view'];
    }

    protected function configureActions(string $routePrefix): array
    {
        $actionList = $this->configureActionList();
        $actions = array_flip($actionList);

        foreach ($actions as $action => &$actionConfig) {
            $actionConfig = [];
            $actionConfig['route'] = $routePrefix . $action;
        }

        return $actions;
    }

    protected function configureCrud(CrudConfig $config): CrudConfig
    {
        return $config;
    }

    protected function configureTabs(): array
    {
        return ['basic' => ['title' => 'Основная информация', 'active' => true]];
    }

    protected function renderTemplate(string $template, array $parameters = []): Response
    {
        $context = $this->context;
        $config = $context->getCrudConfig();
        $action = $context->getCurrentAction();
        if (isset($parameters['entityId'])) {
            $parameters['title'] .= ' #' . $parameters['entityId'];
        }

        return $this->render($template, array_merge([
            'context' => $context,
            'config' => $config,
            'actionConfig' => $config->getAction($action),
            'currentAction' => $action,
            'breadcrumbs' => $config->getBreadcrumbs()
        ], $parameters, $context->getCustomTemplateVars()));
    }

    protected function create(CrudContext $context): Response
    {
        $config = $context->getCrudConfig();

        $this->denyAccessUnlessGranted(AbstractPermissionService::CREATE, $config->getEntityClass());
        $creator = $config->getCreator();
        $context->setFormOptions(['data_class' => $creator->getDtoClass()]);
        $dto = new ($creator->getDtoClass());
        $context->setDto($dto);
        $form = $this->createSaveForm($dto);
        $context->setForm($form);
        $form->handleRequest($context->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $entity = $this->createEntity();
            $context->setEntity($entity);
            $this->handleFormSave();
            $this->flush();
            if ($config->getOnCreateEvent() && $config->getDispatcher()) {
                $eventClass = $config->getOnCreateEvent();
                $config->getDispatcher()->dispatch(new $eventClass($entity));
            }

            $this->addFlash('success', 'flash.created_successfully');

            return $this->redirectAfterSubmit($context);
        }

        $this->createFormBreadcrumbs($config->getCreateTitle());

        return $this->renderTemplate($config->getFormTemplate(), [
            'form' => $form->createView(),
            'title' => $config->getCreateTitle(),
            'fields' => $config->getFormFields()
        ]);
    }

    protected function edit(CrudContext $context): Response
    {
        $config = $context->getCrudConfig();
        $entityId = $this->extractEntityId();
        $editor = $config->getUpdater();
        $entity = $editor->findEntity($entityId);

        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $context->setEntity($entity);
        $this->denyAccessUnlessGranted(AbstractPermissionService::UPDATE, $entity);
        $dto = $editor->createDtoFromEntity($entity);
        $context->setDto($dto);

        $form = $this->createSaveForm($dto);
        $context->setForm($form);
        $form->handleRequest($context->getRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $this->updateEntity();
            $this->handleFormSave();
            $this->flush();
            $this->addFlash('success', 'flash.updated_successfully');

            return $this->redirectAfterSubmit($context);
        }

        $entityLogger = $this->container->get(EntityLoggerRepositoryInterface::class);
        $this->createFormBreadcrumbs($config->getEditTitle());

        return $this->renderTemplate($config->getFormTemplate(), [
            'form' => $form->createView(),
            'entityId' => $entityId,
            'title' => $config->getEditTitle(),
            'logEntries' => $entityLogger->getLogEntries($entity),
            'fields' => $config->getFormFields(),
            'item' => $config->getViewFetcher()->getOne($entityId)
        ]);
    }

    protected function createEntity(): object
    {
        $data = $this->context->getForm()?->getData();

        return $this->context->getCrudConfig()->getCreator()->createEntityFromDto($data, false);
    }

    protected function updateEntity(): object
    {
        $entity = $this->context->getEntity();
        $data = $this->context->getForm()?->getData();

        return $this->context->getCrudConfig()->getUpdater()->updateEntityFromDto($data, $entity,false);
    }

    protected function handleFormSave(): void
    {
    }

    protected function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    protected function createFormBreadcrumbs(string $title): void
    {
        $config = $this->context->getCrudConfig();
        if ($config->getBreadcrumbs() === null) {
            $breadcrumbs = [];
            if ($config->getFormBackUrl() !== null) {
                $breadcrumbs[] = new Breadcrumb($config->getFormBackBreadcrumbTitle(), $config->getFormBackUrl());
            }
            $breadcrumbs[] = new Breadcrumb($title);
            $config->setBreadcrumbs($breadcrumbs);
        }
    }

    protected function createSaveForm(mixed $data): FormInterface
    {
        return $this->createDefaultForm($data, $this->getFormFields());
    }

    protected function createDefaultForm(mixed $data, array $fields): FormInterface
    {
        return $this->createForm(CrudType::class, $data, array_merge([
            'fields' => $fields,
            'custom_vars' => $this->context->getCustomTemplateVars()
        ], $this->context->getFormOptions()));
    }

    protected function delete(CrudContext $context): Response
    {
        $config = $context->getCrudConfig();
        $route = $config->getRoute('delete');
        $request = $context->getRequest();
        $id = $this->extractEntityId();
        if ($this->isCsrfTokenValid($route . $id, $request->request->get('_token'))) {
            $config->getRemover()->remove($id);
            $this->addFlash('success', 'Запись успешно удалена!');
        }

        return $this->redirectToRoute($config->getRoute('list'));
    }

    protected function listPage(CrudContext $context): Response
    {
        $page = $this->extractPage($context->getRequest());
        if ($page === 1) {
             return $this->redirectToRoute($context->getCrudConfig()->getRoute('list'));
        }

        return $this->list($context);
    }

    protected function list(CrudContext $context): Response
    {
        $config = $context->getCrudConfig();
        $listFetcher = $config->getListFetcher();
        $this->denyAccessUnlessGranted(AbstractPermissionService::LIST, $config->getEntityClass());
        $filter = $this->getListFilterDto($context->getRequest());

        $paginator = $this->paginate($filter, $listFetcher, $context->getRequest());
        if ($config->getBreadcrumbs() === null) {
            $config->setBreadcrumbs([new Breadcrumb($config->getListTitle())]);
        }

        return $this->renderTemplate($config->getListTemplate(), [
            'items' => $paginator->getResults(),
            'filter' => $filter,
            'fields' => $this->getListFields(),
            'title' => $config->getListTitle(),
            'paginator' => $paginator
        ]);
    }

    protected function view(CrudContext $context): Response
    {
        $config = $context->getCrudConfig();
        $this->denyAccessUnlessGranted(AbstractPermissionService::VIEW, $config->getEntityClass());
        $entityId = $this->extractEntityId();

        return $this->renderTemplate($config->getViewTemplate(), [
            'item' => $config->getViewFetcher()->getOne($entityId),
            'title' => $config->getViewTitle(),
        ]);
    }

    protected function paginate(object $filter, ListFetcherInterface $listFetcher, Request $request): Paginator
    {
        $numResults = $listFetcher->getListNumResults($filter);
        $pageSize = Paginator::PAGE_SIZE;
        $items = $listFetcher->getList($filter, $pageSize);
        $paginator = new Paginator(null, (array) $filter, null, $pageSize);
        $paginator->paginateResults($items, $numResults, $this->extractPage($request));

        return $paginator;
    }

    protected function getFormFields(): array
    {
        $config = $this->context->getCrudConfig();
        if ($config->getFormFields() === null) {
            $config->setFormFields($this->configureFormFields());
        }

        return $config->getFormFields();
    }

    protected function configureFormFields(): array
    {
        $config = $this->context->getCrudConfig();

        return $config->getFieldsConfigurator()->configureFormFields();
    }

    protected function getListFields(): array
    {
        $config = $this->context->getCrudConfig();
        if ($config->getListFields() === null) {
            $config->setListFields($config->getFieldsConfigurator()->configureListFields());
        }

        return $config->getListFields();
    }

    protected function extractPage(Request $request): int
    {
        return (int) $request->attributes->get('page', 1);
    }

    protected function extractEntityId(?Request $request = null): mixed
    {
        $request = $request ?? $this->context->getRequest();

        return $request->attributes->get('id');
    }

    protected function redirectAfterSubmit(CrudContext $context): RedirectResponse
    {
        $config = $context->getCrudConfig();
        $id = $this->extractEntityId();

        if ($config->getRedirectAfterSubmitUrl() !== null) {
            return $this->redirect($config->getRedirectAfterSubmitUrl());
        }

        if ($id !== null) {
            return $this->redirectToRoute($config->getRoute('edit'), ['id' => $id]);
        }

        return $this->redirectToRoute($config->getFormBackUrl());
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        $entityClass = $this->context->getCrudConfig()->getEntityClass();

        return $this->container->get(ManagerRegistry::class)->getManagerForClass($entityClass);
    }

    protected function getRepository(): EntityRepository
    {
        $entityClass = $this->context->getCrudConfig()->getEntityClass();

        return $this->getEntityManager()->getRepository($entityClass);
    }

    protected function getListFilterDto(Request $request): object
    {
        return new \stdClass();
    }
}
