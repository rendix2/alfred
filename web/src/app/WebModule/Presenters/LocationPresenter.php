<?php

namespace Alfred\App\WebModule\Presenters;

use Alfred\App\Model\Entity\LocationEntity;
use Alfred\App\WebModule\Forms\LocationForm;
use Doctrine\DBAL\Exception as DbalException;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nettrine\ORM\EntityManagerDecorator;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

/**
 * class LocationPresenter
 *
 * @package Alfred\App\WebModule\Presenters
 */
class LocationPresenter extends Presenter
{

    public function __construct(
        private EntityManagerDecorator $em,
        private DoctrineFormMapper     $doctrineFormMapper,
        private LocationForm           $locationForm,
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
        $location = $this->em->getRepository(LocationEntity::class)->find($id);

        if (!$location) {
            $this->flashMessage('Poloha nenalezena.', 'danger');
        }

        $this->doctrineFormMapper->load($location, $this['form']);
    }

    public function handleDelete(int $id) : void
    {
        $location = $this->em->getRepository(LocationEntity::class)->find($id);

        if (!$location) {
            $this->flashMessage('Poloha nenalezena.', 'danger');
        }

        try {
            $this->em->remove($location);
            $this->em->flush();

            $message = sprintf('Poloha %s byla smazána.', $location->name);

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
        $dataSource = $this->em
            ->getRepository(LocationEntity::class)
            ->createQueryBuilder('_location',);

        $grid = new DataGrid($this, $name);

        $grid->setDataSource($dataSource);

        $grid->addColumnNumber('id', 'ID')
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnNumber('latitude', 'Latitude')
            ->setSortable(true)
            ->setFormat(7, ',')
            ->setFilterText();

        $grid->addColumnNumber('longitude', 'Longitude')
            ->setSortable(true)
            ->setFormat(7, ',')
            ->setFilterText();

        $grid->addColumnText('name', 'Jméno')
            ->setEditableCallback(
                function ($id, string $value) : void {
                    $location = $this->em->getRepository(LocationEntity::class)->find($id);
                    $location->name = $value;

                    $this->em->persist($location);
                    $this->em->flush();
                }
            )
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('description', 'Popis')
            ->setEditableCallback(
                function ($id, string $value) : void {
                    $location = $this->em->getRepository(LocationEntity::class)->find($id);
                    $location->description = $value;

                    $this->em->persist($location);
                    $this->em->flush();
                }
            )
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnDateTime('createdAt', 'Vytvořeno')
            ->setSortable(true)
            ->setFilterDate();

        $grid->addColumnDateTime('updatedAt', 'Aktualizováno')
            ->setSortable(true)
            ->setFilterDate();

        $grid->addAction('edit', 'Editovat')
            ->setIcon('edit');

        $grid->addAction('delete', 'Smazat', 'delete!')
            ->setIcon('trash')
            ->setTitle('Smazat')
            ->setConfirmation(
                new StringConfirmation('Opravdu chcete smazat Poloha %s?', 'name') // Second parameter is optional
            );

        return $grid;
    }

    public function createComponentForm() : Form
    {
        $form = $this->locationForm->create();

        $this->doctrineFormMapper->load(LocationEntity::class, $form);

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    public function formSuccess(Form $form) : void
    {
        $id = $this->getParameter('id');

        if ($id) {
            $input = $this->em->getRepository(LocationEntity::class)->find($id);
        } else {
            $input = LocationEntity::class;
        }

        $location = $this->doctrineFormMapper->save($input, $form);

        $this->em->persist($location);
        $this->em->flush();

        $this->flashMessage('Poloha byla uložena.', 'success');
        $this->redirect('Location:default');
    }

}
