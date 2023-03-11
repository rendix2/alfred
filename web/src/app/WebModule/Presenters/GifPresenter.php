<?php

namespace Alfred\App\WebModule\Presenters;

use Alfred\App\Model\Entity\GifEntity;
use Alfred\App\WebModule\Forms\GifForm;
use Alfred\App\WebModule\Grids\GifGrid;
use Doctrine\DBAL\Exception as DbalExcetion;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nettrine\ORM\EntityManagerDecorator;
use Ublaboo\DataGrid\DataGrid;

/**
 * class GifPresenter
 *
 * @package Alfred\App\WebModule\Presenters
 */
class GifPresenter extends Presenter
{
    public function __construct(
        private EntityManagerDecorator $em,
        private DoctrineFormMapper     $doctrineFormMapper,
        private GifForm           $gifForm,
        private GifGrid $gifGrid
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
        $gif = $this->em->getRepository(GifEntity::class)->find($id);

        if (!$gif) {
            $this->flashMessage('GIF nenalezen.', 'danger');
        }

        $this->doctrineFormMapper->load($gif, $this['form']);
    }

    public function handleDelete(int $id) : void
    {
        $gif = $this->em->getRepository(GifEntity::class)->find($id);

        if (!$gif) {
            $this->flashMessage('GIF nenalezen.', 'danger');
        }

        try {
            $this->em->remove($gif);
            $this->em->flush();

            $message = sprintf('GIF %s byl smazán.', $gif->name);

            $this->flashMessage($message);

            if ($this->isAjax()) {
                $this->redrawControl('flashes');
                $this['grid']->reload();
            } else {
                $this->redirect('this');
            }
        } catch (DbalExcetion $e) {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redrawControl('flashes');
        }
    }


    public function createComponentGrid(string $name) : DataGrid
    {
        return $this->gifGrid->create($this, $name);
    }

    public function createComponentForm() : Form
    {
        $form = $this->gifForm->create();

        $this->doctrineFormMapper->load(GifEntity::class, $form);

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    public function formSuccess(Form $form) : void
    {
        $id = $this->getParameter('id');

        if ($id) {
            $input = $this->em->getRepository(GifEntity::class)->find($id);
        } else {
            $input = GifEntity::class;
        }

        $gif = $this->doctrineFormMapper->save($input, $form);

        $this->em->persist($gif);
        $this->em->flush();

        $this->flashMessage('Gif byla uložen.', 'success');
        $this->redirect('Gif:default');
    }
}