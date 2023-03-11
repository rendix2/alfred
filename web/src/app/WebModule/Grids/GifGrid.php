<?php

namespace Alfred\App\WebModule\Grids;

use Alfred\App\Model\Entity\GifEntity;
use Nette\ComponentModel\IContainer;
use Nettrine\ORM\EntityManagerDecorator;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

/**
 * class GifGrid
 *
 * @package Alfred\App\WebModule\Grids
 */
class GifGrid
{
    public function __construct(
        private EntityManagerDecorator $em
    )
    {

    }

    public function create(IContainer $container, string $name) : DataGrid
    {
        $dataSource = $this->em
            ->getRepository(GifEntity::class)
            ->createQueryBuilder('_gif',);

        $grid = new DataGrid($container, $name);

        $grid->setDataSource($dataSource);

        $grid->addColumnNumber('id', 'ID')
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('url', 'URL');


        $grid->addColumnText('name', 'Jméno')
            ->setEditableCallback(
                function ($id, string $value) : void {
                    $gif = $this->em->getRepository(GifEntity::class)->find($id);
                    $gif->name = $value;

                    $this->em->persist($gif);
                    $this->em->flush();
                }
            )
            ->setSortable(true)
            ->setFilterText();

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
                new StringConfirmation('Opravdu chcete smazat Gif %s?', 'name') // Second parameter is optional
            );

        return $grid;
    }

}
