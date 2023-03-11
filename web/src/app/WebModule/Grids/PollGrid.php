<?php

namespace Alfred\App\WebModule\Grids;

use Alfred\App\Model\Entity\PollEntity;
use Nette\ComponentModel\IContainer;
use Nettrine\ORM\EntityManagerDecorator;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

/**
 * class PollGrid
 *
 * @package Alfred\App\WebModule\Grids
 */
class PollGrid
{

    public function __construct(
        private EntityManagerDecorator $em
    )
    {

    }

    public function create(IContainer $container, string $name) : DataGrid
    {
        $dataSource = $this->em
            ->getRepository(PollEntity::class)
            ->createQueryBuilder('_poll',);

        $grid = new DataGrid($container, $name);

        $grid->setDataSource($dataSource);

        $grid->addColumnNumber('id', 'ID')
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('question', 'Otázka')
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('type', 'Typ')
            ->setSortable(true)
            ->setReplacement(['regular' => 'Běžná', 'quiz' => 'Kvíz'])
            ->setFilterSelect(['regular' => 'Běžná', 'quiz' => 'Kvíz']);

        $grid->addColumnText('allowsMultipleAnswers', 'Více možných odpověďí?')
            ->setSortable(true)
            ->setReplacement([0 => 'Ne', 1 => 'Ano'])
            ->setFilterSelect([0 => 'Ne', 1 => 'Ano']);

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
                new StringConfirmation('Opravdu chcete smazat Anketu %s?', 'question') // Second parameter is optional
            );

        return $grid;
    }
}
