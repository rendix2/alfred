<?php

namespace Alfred\App\WebModule\Components\Word;

use Alfred\App\Model\Entity\ChatEntity;
use Alfred\App\Model\Entity\EventEntity;
use Alfred\App\Model\Entity\RequestEntity;
use Alfred\App\Model\Entity\WordEntity;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use Nette\Application\UI\Control;
use Nettrine\ORM\EntityManagerDecorator;

/**
 * class ChatCards
 *
 * @package Alfred\App\WebModule\Components\Word
 */
class ChatCard extends Control
{

    public function __construct
    (
        private EntityManagerDecorator $em,
        private DoctrineFormMapper     $doctrineFormMapper,
        private WordEntity             $word,
    ) {

    }

    public function render() : void
    {
        $sep = DIRECTORY_SEPARATOR;

        $chats = $this->em->getRepository(ChatEntity::class)->findAll();
        $events = $this->em->getRepository(EventEntity::class)->findAll();
        $requests = $this->em->getRepository(RequestEntity::class)->findBy(['word' => $this->word]);

        $result = [];

        foreach ($chats as $chat) {
            foreach ($events as $event) {
                $result[$chat->id][$event->id] = false;

                foreach ($requests as $request) {
                    if (isset($result[$request->chat->id][$request?->event?->id])) {
                        $result[$request->chat->id][$request?->event?->id] = true;
                    }
                }
            }
        }

        $this->template->word = $this->word;
        $this->template->chats = $chats;
        $this->template->events = $events;
        $this->template->isActive = $result;

        $this->template->setFile(__DIR__ . $sep . 'ChatCard.latte');
        $this->template->render();
    }

    public function handleAdd(int $wordId, int $chatId, int $eventId) : void
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Word:edit', $wordId);
        }

        /**
         * @var WordEntity $word
         */
        $word = $this->em->getRepository(WordEntity::class)->find($wordId);

        if (!$word) {
            $this->flashMessage('Slovo nenalezeno.', 'danger');
        }

        /**
         * @var ChatEntity $chat
         */
        $chat = $this->em->getRepository(ChatEntity::class)->find($chatId);

        if (!$chat) {
            $this->flashMessage('Chat nenalezen.', 'danger');
        }

        $event = $this->em->getRepository(EventEntity::class)->find($eventId);

        if (!$event) {
            $this->flashMessage('Událost nenalezena.', 'danger');
        }

        $request = new RequestEntity();
        $request->event = $event;
        $request->chat = $chat;
        $request->word = $word;
        $request->isActive = true;
        $request->isExplicit = false;
        $request->priority = 5;
        $request->aggressiveness = 0;

        $this->em->persist($request);
        $this->em->flush();

        $this->flashMessage('Požadavek přidán.', 'success');

        $this->redrawControl('requests');
        $this->redrawControl('flashes');
    }

    public function handleDelete(int $wordId, int $chatId, int $eventId) : void
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Word:edit', $wordId);
        }

        /**
         * @var WordEntity $word
         */
        $word = $this->em->getRepository(WordEntity::class)->find($wordId);

        if (!$word) {
            $this->flashMessage('Slovo nenalezeno.', 'danger');
        }

        /**
         * @var ChatEntity $chat
         */
        $chat = $this->em->getRepository(ChatEntity::class)->find($chatId);

        if (!$chat) {
            $this->flashMessage('Chat nenalezen.', 'danger');
        }

        $event = $this->em->getRepository(EventEntity::class)->find($eventId);

        if (!$event) {
            $this->flashMessage('Událost nenalezena.', 'danger');
        }

        $request = $this->em->getRepository(RequestEntity::class)->findOneBy(
            [
                'word' => $word,
                'chat' => $chat,
                'event' => $event
            ]
        );

        if ($request) {
            $this->flashMessage('Požadavek nenalezen.','danger');
        }

        $this->em->remove($request);
        $this->em->flush();

        $this->flashMessage('Chat odstraněn.', 'success');

        $this->redrawControl('requests');
        $this->redrawControl('flashes');
    }
}
