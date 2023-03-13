<?php

namespace Alfred\App\WebModule\Presenters;

use Alfred\App\Loggers\Exception\SqliteLogger as ExceptionLogger;
use Alfred\App\Model\Entity\ChatEntity;
use Alfred\App\WebModule\Forms\ChatForm;
use Alfred\App\WebModule\Forms\ChatMessageForm;
use Doctrine\DBAL\Exception as DbalException;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nettrine\ORM\EntityManagerDecorator;
use Telegram\Bot\Api as Telegram;
use Telegram\Bot\Exceptions\TelegramSDKException;
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
        private ChatForm               $chatForm,
        private ChatMessageForm        $chatMessageForm,

        private Telegram               $telegram,

        private ExceptionLogger        $exceptionLogger,
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

        $this['messageForm-chat_id']->setDisabled(true)
            ->setValue($id);
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
        } catch (DbalException $e) {
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

    public function createComponentMessageForm() : Form
    {
        $form = $this->chatMessageForm->create();

        $form->onSuccess[] = [$this, 'messageFormSuccess'];

        return $form;
    }

    public function messageFormSuccess(Form $form) : void
    {
        $values = $form->getValues();

        try {
            $this->telegram->sendMessage(
                [
                    'chat_id' => $this->getParameter('id'),
                    'message' => $values->text,
                    'parse_mode' => 'HTML',
                ]
            );

            $this->flashMessage('Zpráva byla odeslána.', 'success');
        } catch (TelegramSDKException $e) {
            $this->flashMessage('Zprávu se nepodařilo odeslat.', 'danger');
            $this->exceptionLogger->addToLog($e);
        }
    }

    public function createComponentForm() : Form
    {
        $form = $this->chatForm->create();

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
