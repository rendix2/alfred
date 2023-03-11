<?php

namespace Alfred\App\WebModule\Presenters;

use Alfred\App\Model\Entity\RequestEntity;
use Alfred\App\WebModule\Components\Request\ResponseCard;
use Alfred\App\WebModule\Forms\RequestForm;
use Alfred\App\WebModule\Grids\RequestGrid;
use Doctrine\DBAL\Exception as DbalException;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nettrine\ORM\EntityManagerDecorator;
use Ublaboo\DataGrid\DataGrid;

/**
 * class RequestPresenter
 *
 * @package Alfred\App\WebModule\Presenters
 */
class RequestPresenter extends Presenter
{
    private RequestEntity $requestEntity;

    public function __construct(
        private EntityManagerDecorator $em,
        private DoctrineFormMapper     $doctrineFormMapper,
        private RequestForm            $requestForm,
        private RequestGrid            $requestGrid,
    ) {

    }

    public function actionDefault() : void
    {
    }

    public function actionEdit(?int $id) : void
    {
        if ($id) {
            $request = $this->em->getRepository(RequestEntity::class)->find($id);

            if (!$request) {
                $this->flashMessage('Požadavek nenalezen.', 'danger');
            }

            $this->doctrineFormMapper->load($request, $this['form']);

            $this->requestEntity = $request;
        }
    }

    public function handleDelete(int $id) : void
    {
        $request = $this->em->getRepository(RequestEntity::class)->find($id);

        if (!$request) {
            $this->flashMessage('Požadavek nenalezen.', 'danger');
        }

        try {
            $this->em->remove($request);
            $this->em->flush();

            $message = sprintf('Požadavek %s byla smazán.', $request->name);

            $this->flashMessage($message);

            if ($this->isAjax()) {
                $this->redrawControl('flashes');
                $this['grid']->reload();
            } else {
                $this->redirect('this');
            }
        } catch (DbalException $e) {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redrawControl('flashes');
        }
    }

    public function createComponentGrid(string $name) : DataGrid
    {
        $sep  = DIRECTORY_SEPARATOR;
        $grid = $this->requestGrid->create($this, $name);

        $grid->setTemplateFile(__DIR__ . $sep . '..' . $sep . 'templates/Request/grid.latte');

        return $grid;
    }

    public function createComponentResponseCard() : ResponseCard
    {
        return new ResponseCard($this->em, $this->requestEntity);
    }

    public function createComponentForm() : Form
    {
        $form = $this->requestForm->create();

        $this->doctrineFormMapper->load(RequestEntity::class, $form);

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    public function formSuccess(Form $form) : void
    {
        $id = $this->getParameter('id');

        if ($id) {
            $input = $this->em->getRepository(RequestEntity::class)->find($id);
        } else {
            $input = RequestEntity::class;
        }

        $request = $this->doctrineFormMapper->save($input, $form);

        $this->em->persist($request);
        $this->em->flush();

        $this->flashMessage('Požadevek byl uložen.', 'success');
        $this->redirect('Request:default');
    }
}
