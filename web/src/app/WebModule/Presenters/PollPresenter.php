<?php

namespace Alfred\App\WebModule\Presenters;

use Alfred\App\Model\Entity\PollEntity;
use Alfred\App\Model\Entity\PollOptionEntity;
use Alfred\App\WebModule\Components\Poll\OptionsCard;
use Alfred\App\WebModule\Forms\PollForm;
use Alfred\App\WebModule\Forms\PollOptionForm;
use Alfred\App\WebModule\Grids\PollGrid;
use Doctrine\DBAL\Exception as DbalException;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nettrine\ORM\EntityManagerDecorator;
use Ublaboo\DataGrid\DataGrid;

/**
 * class PollPresenter
 *
 * @package Alfred\App\WebModule\Presenters
 */
class PollPresenter extends Presenter
{
    private PollEntity $pollEntity;

    public function __construct(
        private EntityManagerDecorator $em,
        private DoctrineFormMapper     $doctrineFormMapper,
        private PollForm               $pollForm,
        private PollOptionForm         $pollOptionForm,
        private PollGrid               $pollGrid,
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
        $poll = $this->em->getRepository(PollEntity::class)->find($id);

        if (!$poll) {
            $this->flashMessage('Anketa nenalezena.', 'danger');
        }

        $this['optionForm-poll']->setDisabled(true)->setValue($id);

        $this->pollEntity = $poll;

        $this->doctrineFormMapper->load($poll, $this['form']);
    }

    public function renderEdit(int $id) : void
    {
        $this->template->poll = $this->pollEntity;
    }

    public function handleDelete(int $id) : void
    {
        $poll = $this->em->getRepository(PollEntity::class)->find($id);

        if (!$poll) {
            $this->flashMessage('Anketa nenalezena.', 'danger');
        }

        try {
            $this->em->remove($poll);
            $this->em->flush();

            $message = sprintf('Anketa %s byla smazána.', $poll->question);

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
        return $this->pollGrid->create($this, $name);
    }

    public function createComponentOptionsCard() : OptionsCard
    {
        return new OptionsCard($this->em, $this->pollEntity);
    }

    public function createComponentForm() : Form
    {
        $form = $this->pollForm->create();

        $this->doctrineFormMapper->load(PollEntity::class, $form);

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    public function formSuccess(Form $form) : void
    {
        $id = $this->getParameter('id');

        if ($id) {
            $input = $this->em->getRepository(PollEntity::class)->find($id);
        } else {
            $input = PollEntity::class;
        }

        $location = $this->doctrineFormMapper->save($input, $form);

        $this->em->persist($location);
        $this->em->flush();

        $this->flashMessage('Anketa byla uložena.', 'success');
        $this->redirect('Poll:default');
    }

    public function createComponentOptionForm() : Form
    {
        $form = $this->pollOptionForm->create();

        $this->doctrineFormMapper->load(PollOptionEntity::class, $form);

        $form->onSuccess[] = [$this, 'optionFormSuccess'];

        return $form;
    }

    public function optionFormSuccess(Form $form) : void
    {
        $id = $this->getParameter('id');

        $poll = $this->em->getRepository(PollEntity::class)->find($id);

        if (!$poll) {
            $this->flashMessage('Anketa nenalezena.', 'danger');
        }

        $pollOption = $this->doctrineFormMapper->save(PollOptionEntity::class, $form);

        $this->em->persist($pollOption);
        $this->em->flush();

        $this->flashMessage('Možnost Ankety byla uložena.', 'success');
        $this->redirect('Poll:edit', $id);
    }
}
