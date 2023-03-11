<?php

namespace Alfred\App\WebModule\Presenters;

use Alfred\App\Model\Entity\AnswerEntity;
use Alfred\App\Model\Entity\ChatEntity;
use Alfred\App\Model\Entity\EventEntity;
use Alfred\App\Model\Entity\GifEntity;
use Alfred\App\Model\Entity\LocationEntity;
use Alfred\App\Model\Entity\PollEntity;
use Alfred\App\Model\Entity\RequestEntity;
use Alfred\App\Model\Entity\ResponseEntity;
use Alfred\App\Model\Entity\WordEntity;
use Alfred\App\WebModule\Forms\AnswerForm;
use Alfred\App\WebModule\Forms\ChatForm;
use Alfred\App\WebModule\Forms\EventForm;
use Alfred\App\WebModule\Forms\GifForm;
use Alfred\App\WebModule\Forms\LocationForm;
use Alfred\App\WebModule\Forms\PollForm;
use Alfred\App\WebModule\Forms\RequestForm;
use Alfred\App\WebModule\Forms\ResponseForm;
use Alfred\App\WebModule\Forms\WordForm;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nettrine\ORM\EntityManagerDecorator;

/**
 * class ReactionPresenter
 *
 * @package Alfred\App\WebModule\Presenters
 */
class ReactionPresenter extends Presenter
{

    public function __construct
    (
        private EntityManagerDecorator $em,
        private DoctrineFormMapper     $doctrineFormMapper,

        private AnswerForm             $answerForm,
        private ChatForm               $chatForm,
        private EventForm              $eventForm,
        private GifForm                $gifForm,
        private LocationForm           $locationForm,
        private PollForm               $pollForm,
        private RequestForm            $requestForm,
        private ResponseForm           $responseForm,
        private WordForm               $wordForm,
    ) {

    }

    public function createComponentWordForm() : Form
    {
        $form = $this->wordForm->create();

        $form->onSuccess[] = [$this, 'wordFormSuccess'];
        $this->doctrineFormMapper->load(WordEntity::class, $form);

        return $form;
    }

    public function wordFormSuccess(Form $form) : void
    {
        $word = $this->doctrineFormMapper->save(WordEntity::class, $form);

        $this->em->persist($word);
        $this->em->flush();

        $this->flashMessage('Slovo bylo vytvořeno.', 'success');
        $this->redirect('Reaction:default');
    }


    public function createComponentEventForm() : Form
    {
        $form = $this->eventForm->create();

        $form->onSuccess[] = [$this, 'eventFormSuccess'];
        $this->doctrineFormMapper->load(EventEntity::class, $form);

        return $form;
    }

    public function eventFormSuccess(Form $form) : void
    {
        $event = $this->doctrineFormMapper->save(EventEntity::class, $form);

        $this->em->persist($event);
        $this->em->flush();

        $this->flashMessage('Událost byla vytvořena.', 'success');
        $this->redirect('Reaction:default');
    }


    public function createComponentChatForm() : Form
    {
        $form = $this->chatForm->create();

        $form->onSuccess[] = [$this, 'chatFormSuccess'];
        $this->doctrineFormMapper->load(ChatEntity::class, $form);

        return $form;
    }

    public function chatFormSuccess(Form $form) : void
    {
        $chat = $this->doctrineFormMapper->save(ChatEntity::class, $form);

        $this->em->persist($chat);
        $this->em->flush();

        $this->flashMessage('Chat byl vytvořen.', 'success');
        $this->redirect('Reaction:default');
    }


    public function createComponentRequestForm() : Form
    {
        $form = $this->requestForm->create();

        $form->onSuccess[] = [$this, 'requestFormSuccess'];
        $this->doctrineFormMapper->load(RequestEntity::class, $form);

        return $form;
    }

    public function requestFormSuccess(Form $form) : void
    {
        $request = $this->doctrineFormMapper->save(RequestEntity::class, $form);

        $this->em->persist($request);
        $this->em->flush();

        $this->flashMessage('Požadavek byl vytvořen.', 'success');
        $this->redirect('Reaction:default');
    }


    public function createComponentAnswerForm() : Form
    {
        $form = $this->answerForm->create();

        $form->onSuccess[] = [$this, 'answerSuccess'];
        $this->doctrineFormMapper->load(AnswerEntity::class, $form);

        return $form;
    }

    public function answerSuccess(Form $form) : void
    {
        $answer = $this->doctrineFormMapper->save(AnswerEntity::class, $form);

        $this->em->persist($answer);
        $this->em->flush();

        $this->flashMessage('Textová odpověď byla vytvořena.', 'success');
        $this->redirect('Reaction:default');
    }


    public function createComponentResponseForm() : Form
    {
        $form = $this->responseForm->create();

        $form->onSuccess[] = [$this, 'responseSuccess'];
        $this->doctrineFormMapper->load(ResponseEntity::class, $form);

        return $form;
    }

    public function responseSuccess(Form $form) : void
    {
        $response = $this->doctrineFormMapper->save(ResponseEntity::class, $form);

        $this->em->persist($response);
        $this->em->flush();

        $this->flashMessage('Odpověď byla vytvořena.', 'success');
        $this->redirect('Reaction:default');
    }


    public function createComponentLocationForm() : Form
    {
        $form = $this->locationForm->create();

        $form->onSuccess[] = [$this, 'locationSuccess'];
        $this->doctrineFormMapper->load(LocationEntity::class, $form);

        return $form;
    }

    public function locationSuccess(Form $form) : void
    {
        $location = $this->doctrineFormMapper->save(LocationEntity::class, $form);

        $this->em->persist($location);
        $this->em->flush();

        $this->flashMessage('Poloha byla vytvořena.', 'success');
        $this->redirect('Reaction:default');
    }


    public function createComponentGifForm() : Form
    {
        $form = $this->gifForm->create();

        $form->onSuccess[] = [$this, 'gifSuccess'];
        $this->doctrineFormMapper->load(GifEntity::class, $form);

        return $form;
    }

    public function gifSuccess(Form $form) : void
    {
        $gif = $this->doctrineFormMapper->save(GifEntity::class, $form);

        $this->em->persist($gif);
        $this->em->flush();

        $this->flashMessage('GIF byl vytvořen.', 'success');
        $this->redirect('Reaction:default');
    }

    public function createComponentPollForm() : Form
    {
        $form = $this->pollForm->create();

        $form->onSuccess[] = [$this, 'pollSuccess'];
        $this->doctrineFormMapper->load(PollEntity::class, $form);

        return $form;
    }

    public function pollSuccess(Form $form) : void
    {
        $gif = $this->doctrineFormMapper->save(PollEntity::class, $form);

        $this->em->persist($gif);
        $this->em->flush();

        $this->flashMessage('Anketa byla vytvořena.', 'success');
        $this->redirect('Reaction:default');
    }

    public function actionDefault() : void
    {

    }
}
