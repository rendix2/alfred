<?php

namespace Alfred\App\WebModule\Grids;

use Alfred\App\Model\Entity\ChatEntity;
use Alfred\App\Model\Entity\EventEntity;
use Alfred\App\Model\Entity\RequestEntity;
use Doctrine\ORM\QueryBuilder;
use Nette\ComponentModel\IContainer;
use Nettrine\ORM\EntityManagerDecorator;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

/**
 * class RequestGrid
 *
 * @package Alfred\App\WebModule\Grids
 */
class RequestGrid
{
    public function __construct(
        private EntityManagerDecorator $em
    )
    {

    }

    public function create(IContainer $container, string $name) : DataGrid
    {
        $dataSource = $this->em
            ->getRepository(RequestEntity::class)
            ->createQueryBuilder('_request')

            ->addSelect('_word')
            ->addSelect('_chat')
            ->addSelect('_event')

            ->innerJoin('_request.word', '_word')
            ->innerJoin('_request.chat', '_chat')
            ->leftJoin('_request.event', '_event');

        $grid = new DataGrid($container, $name);

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

        $grid->addColumnText('aggressiveness', 'Agresivita')
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


}
