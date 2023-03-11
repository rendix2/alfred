<?php

namespace Alfred\App\WebModule\Presenters;

use Alfred\App\Model\Entity\CategoryEntity;
use Doctrine\DBAL\Exception as DbalException;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use FreezyBee\DoctrineFormMapper\IComponentMapper;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nettrine\ORM\EntityManagerDecorator;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

/**
 * class Category
 *
 * @package Alfred\App\WebModule\Presenters
 */
class CategoryPresenter extends Presenter
{
    public function __construct(
        private EntityManagerDecorator $em,
        private DoctrineFormMapper     $doctrineFormMapper,
    ) {

    }

    public function actionDefault() : void
    {
    }

    public function actionAdd() : void
    {

    }

    public function actionEdit(?int $id) : void
    {
        $category = $this->em->getRepository(CategoryEntity::class)->find($id);

        if (!$category) {
            $this->flashMessage('Kategorie nenalezena.', 'danger');
        }

        $this->doctrineFormMapper->load($category, $this['form']);
    }

    public function handleDelete(int $id) : void
    {
        $category = $this->em->getRepository(CategoryEntity::class)->find($id);

        if (!$category) {
            $this->flashMessage('Kategorie nenalezena.', 'danger');
        }

        try {
            $this->em->remove($category);
            $this->em->flush();

            $message = sprintf('Kategorie %s byla smazána.', $category->name);

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
            ->getRepository(CategoryEntity::class)
            ->createQueryBuilder('_category');

        $grid = new DataGrid($this, $name);

        $grid->setDataSource($dataSource);

        $grid->addColumnNumber('id', 'ID')
            ->setSortable(true)
            ->setFilterText();

        $parentRows = $this->em
            ->getRepository(CategoryEntity::class)
            ->createQueryBuilder('_parent')
            ->getQuery()
            ->getResult();

        $parentRowsFinal = [null => 'Vyberte'];

        foreach ($parentRows as $parentRow) {
            $parentRowsFinal[$parentRow->id] = $parentRow->name;
        }

        $grid->addColumnText('parent', 'Rodič')
            ->setSortable(true)
            ->setRenderer(
                function (CategoryEntity $categoryEntity) {
                    return $categoryEntity?->parent?->name;
                }
            )
            ->setFilterSelect($parentRowsFinal);


        $grid->addColumnText('name', 'Jméno')
            ->setEditableCallback(
                function ($id, string $value) : void {
                    $category = $this->em->getRepository(CategoryEntity::class)->find($id);
                    $category->name = $value;

                    $this->em->persist($category);
                    $this->em->flush();
                }
            )
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('isActive', 'Aktivní?')
            ->setSortable(true)
            ->setReplacement([0 => 'Ne', 1 => 'Ano'])
            ->setFilterSelect([null => 'Vyberte', 0 => 'Ne', 1 => 'Ano']);

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
                new StringConfirmation('Opravdu chcete smazat Kategorii %s?', 'name') // Second parameter is optional
            );

        return $grid;
    }

    public function createComponentForm() : Form
    {
        $form = new Form();

        $form->addText('name', 'Jméno')
            ->setRequired('Zadejte prosím jméno.');

        $form->addSelect('parent', 'Rodič')
            ->setPrompt('Vyberte Rodiče')
            ->setOption(IComponentMapper::ITEMS_TITLE, function (CategoryEntity $categoryEntity) {
                return $categoryEntity->name;
            });

        $form->addCheckbox('isActive', 'Aktivní?');

        $form->addSubmit('send', 'Uložit Kategorii');

        $this->doctrineFormMapper->load(CategoryEntity::class, $form);

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    public function formSuccess(Form $form) : void
    {
        $id = $this->getParameter('id');

        if ($id) {
            $input = $this->em->getRepository(CategoryEntity::class)->find($id);
        } else {
            $input = CategoryEntity::class;
        }

        $category = $this->doctrineFormMapper->save($input, $form);

        $this->em->persist($category);
        $this->em->flush();

        $this->flashMessage('Kategorie byla uložena.', 'success');
        $this->redirect('Category:default');
    }
}
