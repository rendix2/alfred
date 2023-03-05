<?php

namespace Alfred\App\WebModule\Presenters;

use Alfred\App\Model\Entity\AnswerEntity;
use Alfred\App\Model\Entity\GifEntity;
use Alfred\App\Model\Entity\LocationEntity;
use Alfred\App\Model\Entity\PollEntity;
use Alfred\App\Model\Entity\ResponseEntity;
use Doctrine\DBAL\Exception as DbalException;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use FreezyBee\DoctrineFormMapper\IComponentMapper;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nettrine\ORM\EntityManagerDecorator;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

/**
 * class ResponsePresenter
 *
 * @package Alfred\App\WebModule\Presenters
 */
class ResponsePresenter extends Presenter
{

    public function __construct(
        private EntityManagerDecorator $em,
        private DoctrineFormMapper     $doctrineFormMapper,
    ) {

    }

    public function actionDefault() : void
    {
    }

    public function actionAdd()
    {

    }

    public function actionEdit(int $id) : void
    {
        if ($id) {
            $response = $this->em->getRepository(ResponseEntity::class)->find($id);

            if (!$response) {
                $this->flashMessage('Požadavek nenalezen.', 'danger');
            }

            $this->doctrineFormMapper->load($response, $this['form']);

        }
    }

    public function handleDelete(int $id) : void
    {
        $response = $this->em->getRepository(ResponseEntity::class)->find($id);

        if (!$response) {
            $this->flashMessage('Odpověď nenalezena.', 'danger');
            $this->redrawControl('flashes');
        }

        try {
            $this->em->remove($response);
            $this->em->flush();

            $message = sprintf('Odpověď %s byla smazána.', $response->name);

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
            ->getRepository(ResponseEntity::class)
            ->createQueryBuilder('_response');

        $grid = new DataGrid($this, $name);

        $grid->setTemplateFile(__DIR__ . $sep . '..' . $sep . 'templates/Response/grid.latte');

        $grid->setDataSource($dataSource);

        $grid->addColumnNumber('id', 'ID')
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('answer', 'Odpověď')
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('gif', 'Gif')
            ->setSortable(true);
        //->setFilterText();

        $grid->addColumnText('location', 'Poloha')
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('poll', 'Anketa')
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('isActive', 'Aktivní?')
            ->setSortable(true)
            ->setReplacement([0 => 'Ne', 1 => 'Ano'])
            ->setFilterSelect([null => 'Vyberte', 0 => 'Ne', 1 => 'Ano']);

        $grid->addColumnText('isExplicit', 'Explicitní?')
            ->setSortable(true)
            ->setReplacement([0 => 'Ne', 1 => 'Ano'])
            ->setFilterSelect([null => 'Vyberte', 0 => 'Ne', 1 => 'Ano']);

        $grid->addColumnNumber('priority', 'Priorita')
            ->setSortable(true)
            ->setReplacement([1 => 'Nízká', 5 => 'Střední', 10 => 'Vysoká'])
            ->setFilterSelect([null => 'Vyberte', 1 => 'Nízká', 5 => 'Střední', 10 => 'Vysoká']);

        $grid->addColumnNumber('aggressiveness', 'Agresivita')
            ->setSortable(true)
            ->setReplacement([1 => 'Nízká', 5 => 'Střední', 10 => 'Vysoká'])
            ->setFilterSelect([null => 'Vyberte', 1 => 'Nízká', 5 => 'Střední', 10 => 'Vysoká']);

        $grid->addAction('edit', 'Editovat')
            ->setIcon('edit');

        $grid->addAction('delete', 'Smazat', 'delete!')
            ->setIcon('trash')
            ->setTitle('Smazat')
            ->setConfirmation(
                new StringConfirmation('Opravdu chcete smazat Odpověď %s?') // Second parameter is optional
            );

        return $grid;
    }

    public function createComponentForm() : Form
    {
        $form = new Form();

        $form->addSelect('answer', 'Odpověď')
            ->setPrompt('Vyberte Odpověď')
            ->setOption(
                IComponentMapper::ITEMS_TITLE,
                function (AnswerEntity $answerEntity) : string {
                    return $answerEntity->answerText;
                }
            )
            ->setOption(IComponentMapper::ITEMS_ORDER, ['answerText' => 'ASC']);

        $form->addSelect('gif', 'GIF')
            ->setPrompt('Vyberte GIF')
            ->setOption(
                IComponentMapper::ITEMS_TITLE,
                function (GifEntity $gifEntity) : string {
                    return $gifEntity->url;
                }
            );

        $form->addSelect('location', 'Poloha')
            ->setPrompt('Vyberte Polohu')
            ->setOption(
                IComponentMapper::ITEMS_TITLE,
                function (LocationEntity $locationEntity) : string {
                    return $locationEntity->name;
                }
            )
            ->setOption(IComponentMapper::ITEMS_ORDER, ['name' => 'ASC']);

        $form->addSelect('poll', 'Anketa')
            ->setPrompt('Vyberte Ankteru')
            ->setOption(
                IComponentMapper::ITEMS_TITLE,
                function (PollEntity $pollEntity) : string {
                    return $pollEntity->question;
                }
            )
            ->setOption(IComponentMapper::ITEMS_ORDER, ['question' => 'ASC']);

        $form->addCheckbox('isActive', 'Aktivní?');
        $form->addCheckbox('isExplicit', 'Explicitní?');

        $form->addRadioList('priority', 'Priorita', [1 => 'Nízká', 5 => 'Střední', 10 => 'Vysoká'])
            ->setRequired('Vyberte prosím prioritu.');

        $form->addRadioList('aggressiveness', 'Agresivita', [1 => 'Nízká', 5 => 'Střední', 10 => 'Vysoká'])
            ->setRequired('Vyberte prosím agresivitu.');

        $form->addSubmit('send', 'Uložit Odpověď');

        $this->doctrineFormMapper->load(ResponseEntity::class, $form);

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    public function formSuccess(Form $form) : void
    {
        $id = $this->getParameter('id');

        if ($id) {
            $input = $this->em->getRepository(ResponseEntity::class)->find($id);
        } else {
            $input = ResponseEntity::class;
        }

        $response = $this->doctrineFormMapper->save($input, $form);

        $this->em->persist($response);
        $this->em->flush();

        $this->flashMessage('Odpověď byla uložena.', 'success');
        $this->redirect('Response:default');
    }

}
