<?php

namespace Alfred\App\WebModule\Presenters;

use Alfred\App\Model\Entity\EventEntity;
use Alfred\App\WebModule\Forms\EventForm;
use Doctrine\DBAL\Exception as DbalExcetion;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nettrine\ORM\EntityManagerDecorator;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

/**
 * class EventPresenter
 *
 * @package Alfred\App\WebModule\Presenters
 */
class EventPresenter extends Presenter
{

    public function __construct(
        private EntityManagerDecorator $em,
        private DoctrineFormMapper     $doctrineFormMapper,
        private EventForm              $eventForm,
    ) {

    }

    public function actionDefault() : void
    {
    }

    public function actionAdd() : void
    {
    }

    public function actionEdit(int $id) : void
    {
        $event = $this->em->getRepository(EventEntity::class)->find($id);

        if (!$event) {
            $this->flashMessage('Událost nenalezena.', 'danger');
        }

        $this->doctrineFormMapper->load($event, $this['form']);
    }

    public function handleDelete(int $id) : void
    {
        $event = $this->em->getRepository(EventEntity::class)->find($id);

        if (!$event) {
            $this->flashMessage('Událost nenalezena.', 'danger');
        }

        try {
            $this->em->remove($event);
            $this->em->flush();

            $message = sprintf('Událost %s byla smazána.', $event->name);

            $this->flashMessage($message);

            if ($this->isAjax()) {
                $this->redrawControl('flashes');
                $this['grid']->reload();
            } else {
                $this->redirect('this');
            }
        } catch (DbalExcetion $e) {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redrawControl('flashes');
        }
    }

    public function createComponentGrid(string $name) : DataGrid
    {
        $dataSource = $this->em
            ->getRepository(EventEntity::class)
            ->createQueryBuilder('_event',);

        $grid = new DataGrid($this, $name);

        $grid->setDataSource($dataSource);

        $grid->addColumnNumber('id', 'ID')
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('name', 'Jméno')
            ->setEditableCallback(
                function ($id, string $value) : void {
                    $event = $this->em->getRepository(EventEntity::class)->find($id);
                    $event->name = $value;

                    $this->em->persist($event);
                    $this->em->flush();
                }
            )
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('description', 'Popis')
            ->setEditableCallback(
                function ($id, string $value) : void {
                    $event = $this->em->getRepository(EventEntity::class)->find($id);
                    $event->description = $value;

                    $this->em->persist($event);
                    $this->em->flush();
                }
            )
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('isActive', 'Aktivní?')
            ->setSortable(true)
            ->setReplacement([0 => 'Ne', 1 => 'Ano'])
            ->setFilterSelect([null => 'Vyberte', 0 => 'Ne', 1 => 'Ano']);

        $grid->addColumnDateTime('activeFrom', 'Začátek')
            ->setSortable(true)
            ->setFilterDate();

        $grid->addColumnDateTime('activeTo', 'Konec')
            ->setSortable(true)
            ->setFilterDate();

        $grid->addAction('edit', 'Editovat')
            ->setIcon('edit');

        $grid->addAction('delete', 'Smazat', 'delete!')
            ->setIcon('trash')
            ->setTitle('Smazat')
            ->setConfirmation(
                new StringConfirmation('Opravdu chcete smazat Událost %s?', 'name') // Second parameter is optional
            );

        return $grid;
    }

    public function createComponentForm() : Form
    {
        $form = $this->eventForm->create();

        $this->doctrineFormMapper->load(EventEntity::class, $form);

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    public function formSuccess(Form $form) : void
    {
        $id = $this->getParameter('id');

        if ($id) {
            $input = $this->em->getRepository(EventEntity::class)->find($id);
        } else {
            $input = EventEntity::class;
        }

        $event = $this->doctrineFormMapper->save($input, $form);

        $this->em->persist($event);
        $this->em->flush();

        $this->flashMessage('Událost byla uložena.', 'success');
        $this->redirect('Event:default');
    }
}
