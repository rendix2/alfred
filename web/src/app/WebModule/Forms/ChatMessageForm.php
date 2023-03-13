<?php

namespace Alfred\App\WebModule\Forms;

use Alfred\App\Model\Entity\ChatEntity;
use Contributte\FormsBootstrap\BootstrapForm;
use Nette\Application\UI\Form;
use Nettrine\ORM\EntityManagerDecorator;
use Telegram\Bot\Api as Telegram;

/**
 * class ChatMEssa
 *
 * @package Alfred\App\WebModule\Forms
 */
class ChatMessageForm
{

    public function __construct(
        private Telegram $telegram,
        private EntityManagerDecorator $em,
    ) {

    }

    private function getChats() : array
    {
        $chats = $this->em
            ->getRepository(ChatEntity::class)
            ->findAll();

        $assocChats = [];

        foreach ($chats as $chat) {
            $assocChats[$chat->id] = $chat->name;
        }

        return $assocChats;
    }

    public function create() : Form
    {
        $form = new BootstrapForm();

        $form->addSelect('chat_id', 'Chat', $this->getChats())
            ->setRequired('Prosím vyberte Chat')
            ->setPrompt('Vyberte Chat');

        $form->addTextArea('text', 'Zpráva')
            ->setRequired('Prosím zadejte zprávu, kterou chcete poslat');

        $form->addSubmit('send', 'Poslat zprávu');

        return $form;
    }

    public function success(Form $form) : void
    {

    }
}
