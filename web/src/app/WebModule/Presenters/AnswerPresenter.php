<?php

namespace Alfred\App\WebModule\Presenters;

use Alfred\App\Model\Entity\AnswerEntity;
use Alfred\App\WebModule\Forms\AnswerForm;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nettrine\ORM\EntityManagerDecorator;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

/**
 * class AnswerPresenter
 *
 * @package Alfred\App\WebModule\Presenters
 */
class AnswerPresenter extends Presenter
{

    public function __construct(
        private EntityManagerDecorator $em,
        private DoctrineFormMapper     $doctrineFormMapper,
        private AnswerForm             $answerForm,
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
        $answer = $this->em->getRepository(AnswerEntity::class)->find($id);

        if (!$answer) {
            $this->flashMessage('Odpověď nenalezena.', 'danger');
        }

        $this->doctrineFormMapper->load($answer, $this['form']);
    }

    public function handleDelete(int $id) : void
    {
        $answer = $this->em->getRepository(AnswerEntity::class)->find($id);

        if (!$answer) {
            $this->flashMessage('Odpověď nenalezena.', 'danger');
        }

        try {
            $this->em->remove($answer);
            $this->em->flush();

            $message = sprintf('Odpověď %s byla smazána.', $answer->answerText);

            $this->flashMessage($message);

            if ($this->isAjax()) {
                $this->redrawControl('flashes');
                $this['grid']->reload();
            } else {
                $this->redirect('this');
            }
        } catch (\Doctrine\DBAL\Exception $e) {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redrawControl('flashes');
        }
    }

    public function createComponentGrid(string $name) : DataGrid
    {
        $dataSource = $this->em
            ->getRepository(AnswerEntity::class)
            ->createQueryBuilder('_answer');

        $grid = new DataGrid($this, $name);

        $grid->setDataSource($dataSource);

        $grid->addColumnNumber('id', 'ID')
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('answerText', 'Text')
            ->setSortable(true)
            ->setFilterText();

        /*        $grid->addColumnText('isActive', 'Aktivní?')
                    ->setSortable(true)
                    ->setReplacement([0 => 'Ne', 1 => 'Ano'])
                    ->setFilterSelect([null => 'Vyberte', 0 => 'Ne', 1 => 'Ano']);*/

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
                new StringConfirmation('Opravdu chcete smazat Odpověď %s?', 'answerText') // Second parameter is optional
            );

        return $grid;
    }

    public function createComponentForm() : Form
    {
        $form = $this->answerForm->create();

        $this->doctrineFormMapper->load(AnswerEntity::class, $form);

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    public function formSuccess(Form $form) : void
    {
        $id = $this->getParameter('id');

        if ($id) {
            $input = $this->em->getRepository(AnswerEntity::class)->find($id);
        } else {
            $input = AnswerEntity::class;
        }

        $answer = $this->doctrineFormMapper->save($input, $form);

        $this->em->persist($answer);
        $this->em->flush();

        $this->flashMessage('Odpověď byla uložena.', 'success');
        $this->redirect('Answer:default');
    }
}
