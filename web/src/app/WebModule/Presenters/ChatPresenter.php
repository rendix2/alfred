<?php

namespace Alfred\App\WebModule\Presenters;

use Alfred\App\Model\Entity\ChatEntity;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nettrine\ORM\EntityManagerDecorator;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

/**
 * class ChatPresenter
 *
 * @package Alfred\App\WebModule\Presenters
 */
class ChatPresenter extends Presenter
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

    public function actionEdit(int $id) : void
    {

        $chat = $this->em->getRepository(ChatEntity::class)->find($id);

        if (!$chat) {
            $this->flashMessage('Chat nenalezen.', 'danger');
        }

        $this->doctrineFormMapper->load($chat, $this['form']);
    }

    public function handleDelete(int $id) : void
    {
        $chat = $this->em->getRepository(ChatEntity::class)->find($id);

        if (!$chat) {
            $this->flashMessage('Chat nenalezen.', 'danger');
        }

        try {
            $this->em->remove($chat);
            $this->em->flush();

            $message = sprintf('Chat %s byl smazán.', $chat->name);

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
            ->getRepository(ChatEntity::class)
            ->createQueryBuilder('_chat',);

        $grid = new DataGrid($this, $name);

        $grid->setDataSource($dataSource);

        $grid->addColumnNumber('id', 'ID')
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnNumber('telegramId', 'Telegram ID')
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('name', 'Jméno')
            ->setEditableCallback(
                function ($id, string $value) : void {
                    $chat = $this->em->getRepository(ChatEntity::class)->find($id);
                    $chat->name = $value;

                    $this->em->persist($chat);
                    $this->em->flush();
                }
            )
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('isActive', 'Aktivní?')
            ->setSortable(true)
            ->setReplacement([0 => 'Ne', 1 => 'Ano'])
            ->setFilterSelect([null => 'Vyberte', 0 => 'Ne', 1 => 'Ano']);

        $grid->addAction('edit', 'Editovat')
            ->setIcon('edit');

        $grid->addAction('delete', 'Smazat', 'delete!')
            ->setIcon('trash')
            ->setTitle('Smazat')
            ->setConfirmation(
                new StringConfirmation('Opravdu chcete smazat Chat %s?', 'name') // Second parameter is optional
            );

        return $grid;
    }

    public function createComponentForm() : Form
    {
        $form = new Form();

        $form->addText('name', 'Jméno')
            ->setRequired('Zadejte prosím jméno.');

        $form->addInteger('telegramId', 'Telegram ID')
            ->setRequired('Zadejte prosím Telegram ID.');

        $form->addCheckbox('isActive', 'Aktivní?');

        $form->addSubmit('send', 'Uložit Chat');

        $this->doctrineFormMapper->load(ChatEntity::class, $form);

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    public function formSuccess(Form $form) : void
    {
        $id = $this->getParameter('id');

        if ($id) {
            $input = $this->em->getRepository(ChatEntity::class)->find($id);
        } else {
            $input = ChatEntity::class;
        }

        $chat = $this->doctrineFormMapper->save($input, $form);

        $this->em->persist($chat);
        $this->em->flush();

        $this->flashMessage('Chat byl uložen.', 'success');
        $this->redirect('Chat:default');
    }
}
