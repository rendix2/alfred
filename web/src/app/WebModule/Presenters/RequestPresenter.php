<?php

namespace Alfred\App\WebModule\Presenters;

use Alfred\App\Model\Entity\ChatEntity;
use Alfred\App\Model\Entity\EventEntity;
use Alfred\App\Model\Entity\RequestEntity;
use Alfred\App\Model\Entity\WordEntity;
use Alfred\App\WebModule\Components\Request\ResponseCard;
use Doctrine\DBAL\Exception as DbalException;
use Doctrine\ORM\QueryBuilder;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use FreezyBee\DoctrineFormMapper\IComponentMapper;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nettrine\ORM\EntityManagerDecorator;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

/**
 * class RequestPresenter
 *
 * @package Alfred\App\WebModule\Presenters
 */
class RequestPresenter extends Presenter
{
    private RequestEntity $requestEntity;

    public function __construct(
        private EntityManagerDecorator $em,
        private DoctrineFormMapper     $doctrineFormMapper,
    ) {

    }

    public function actionDefault() : void
    {
    }

    public function actionEdit(?int $id) : void
    {
        if ($id) {
            $request = $this->em->getRepository(RequestEntity::class)->find($id);

            if (!$request) {
                $this->flashMessage('Požadavek nenalezen.', 'danger');
            }

            $this->doctrineFormMapper->load($request, $this['form']);

            $this->requestEntity = $request;
        }
    }

    public function handleDelete(int $id) : void
    {
        $request = $this->em->getRepository(RequestEntity::class)->find($id);

        if (!$request) {
            $this->flashMessage('Požadavek nenalezen.', 'danger');
        }

        try {
            $this->em->remove($request);
            $this->em->flush();

            $message = sprintf('Požadavek %s byla smazán.', $request->name);

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
        $sep = DIRECTORY_SEPARATOR;

        $dataSource = $this->em
            ->getRepository(RequestEntity::class)
            ->createQueryBuilder('_request')

            ->addSelect('_word')
            ->addSelect('_chat')
            ->addSelect('_event')

            ->innerJoin('_request.word', '_word')
            ->innerJoin('_request.chat', '_chat')
            ->leftJoin('_request.event', '_event');

        $grid = new DataGrid($this, $name);
        $grid->setTemplateFile(__DIR__ . $sep . '..' . $sep . 'templates/Request/grid.latte');
        $grid->setDataSource($dataSource);
        $grid->setItemsPerPageList([10, 20, 50, 75]);
        $grid->setDefaultPerPage(50);
        $grid->setDefaultSort(['word' => 'ASC']);

        $grid->addColumnNumber('id', 'ID')
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('word', 'Slovo')
            ->setSortable(true)
            ->setSortableCallback(
                function (QueryBuilder $queryBuilder, array $sort) {
                    if (isset($sort['word'])) {
                        $queryBuilder->orderBy('_word.wordText', $sort['word']);
                    }
                }
            )
            ->setFilterText()
            ->setCondition(
                function (QueryBuilder $queryBuilder, string $value)
                {
                    $queryBuilder->where('REGEXP(_word.wordText, :regexp) = true')
                        ->setParameter('regexp', $value);
                }
            );

        $tempChats = $this->em
            ->getRepository(ChatEntity::class)
            ->createQueryBuilder('_chat')
            ->getQuery()
            ->getResult();

        $chats = [null => 'Vyberte'];

        foreach ($tempChats as $chat) {
            $chats[$chat->id] = $chat->name;
        }

        $grid->addColumnText('chat', 'Chat')
            ->setRenderer(
                function (RequestEntity $requestEntity) {
                    return $requestEntity?->chat?->name;
                }
            )
            ->setSortable(true)
            ->setSortableCallback(
                function (QueryBuilder $queryBuilder, array $sort) {
                    if (isset($sort['chat'])) {
                        $queryBuilder->orderBy('_chat.name', $sort['chat']);
                    }
                }
            )
            ->setFilterSelect($chats);

        $tempEvents = $this->em
            ->getRepository(EventEntity::class)
            ->createQueryBuilder('_event')
            ->getQuery()
            ->getResult();

        $events = [null => 'Vyberte'];

        foreach ($tempEvents as $event) {
            $events[$event->id] = $event->name;
        }

        $grid->addColumnText('event', 'Událost')
            ->setSortable(true)
            ->setSortableCallback(
                function (QueryBuilder $queryBuilder, array $sort) {
                    if (isset($sort['event'])) {
                        $queryBuilder->orderBy('_event.name', $sort['event']);
                    }
                }
            )
            ->setFilterSelect($events);

        $grid->addColumnText('priority', 'Priorita')
            ->setReplacement([1 => 'Nízká', 5 => 'Střední', 10 => 'Vysoká'])
            ->setSortable(true)
            ->setFilterSelect([null => 'Vyberte', 1 => 'Nízká', 5 => 'Střední', 10 => 'Vysoká']);

        $grid->addColumnText('isActive', 'Aktivní?')
            ->setSortable(true)
            ->setReplacement([0 => 'Ne', 1 => 'Ano'])
            ->setFilterSelect([null => 'Vyberte', 0 => 'Ne', 1 => 'Ano']);

        $grid->addColumnText('isExplicit', 'Explicitní?')
            ->setSortable(true)
            ->setReplacement([0 => 'Ne', 1 => 'Ano'])
            ->setFilterSelect([null => 'Vyberte', 0 => 'Ne', 1 => 'Ano']);

        $grid->addAction('edit', 'Editovat')
            ->setIcon('edit');

        $grid->addAction('delete', 'Smazat', 'delete!')
            ->setIcon('trash')
            ->setTitle('Smazat')
            ->setConfirmation(
                new StringConfirmation('Opravdu chcete smazat Požadavek %d?', 'id') // Second parameter is optional
            );

        return $grid;
    }

    public function createComponentForm() : Form
    {
        $form = new Form();

        $form->addSelect('word', 'Slovo')
            ->setPrompt('Vyberte Slovo')
            ->setRequired('Slovo je povinné.')
            ->setOption(
                IComponentMapper::ITEMS_TITLE,
                function (WordEntity $wordEntity) : string {
                    return $wordEntity->wordText;
                }
            );

        $form->addSelect('chat', 'Chat')
            ->setPrompt('Vyberte Chat')
            ->setRequired('Chat je povinný.')
            ->setOption(
                IComponentMapper::ITEMS_TITLE,
                function (ChatEntity $chatEntity) : string {
                    return $chatEntity->name;
                }
            );

        $form->addSelect('event', 'Událost')
            ->setPrompt('Vyberte Událost')
            ->setOption(
                IComponentMapper::ITEMS_TITLE,
                function (EventEntity $eventEntity) : string {
                    return $eventEntity->name;
                }
            );

        $form->addRadioList('priority', 'Priorita', [1 => 'Nízká', 5 => 'Střední', 10 => 'Vysoká']);
        $form->addCheckbox('isActive', 'Aktivní?');
        $form->addCheckbox('isExplicit', 'Explicitní?');

        $form->addSubmit('send', 'Uložit Požadavek');

        $this->doctrineFormMapper->load(RequestEntity::class, $form);

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    public function formSuccess(Form $form) : void
    {
        $id = $this->getParameter('id');

        if ($id) {
            $input = $this->em->getRepository(RequestEntity::class)->find($id);
        } else {
            $input = RequestEntity::class;
        }

        $request = $this->doctrineFormMapper->save($input, $form);

        $this->em->persist($request);
        $this->em->flush();

        $this->flashMessage('Požadevek byl uložen.', 'success');
        $this->redirect('Request:default');
    }

    public function createComponentResponseCard() : ResponseCard
    {
        return new ResponseCard($this->em, $this->doctrineFormMapper, $this->requestEntity);
    }
}
