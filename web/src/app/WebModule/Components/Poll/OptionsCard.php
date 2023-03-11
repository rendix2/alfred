<?php

namespace Alfred\App\WebModule\Components\Poll;

use Alfred\App\Model\Entity\PollEntity;
use Alfred\App\Model\Entity\PollOptionEntity;
use Doctrine\DBAL\Exception;
use Nette\Application\UI\Control;
use Nettrine\ORM\EntityManagerDecorator;

/**
 * class OptionsCard
 *
 * @package Alfred\App\WebModule\Components\Poll
 */
class OptionsCard extends Control
{

    public function __construct
    (
        private EntityManagerDecorator $em,
        private PollEntity             $pollEntity,
    ) {

    }

    public function render()
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->template->poll = $this->pollEntity;

        $this->template->setFile(__DIR__ . $sep . 'OptionsCard.latte');
        $this->template->render();
    }

    public function handleDelete(int $pollId, int $pollOptionId) : void
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Poll:edit', $pollId);
        }

        /**
         * @var PollEntity $poll
         */
        $poll = $this->em->getRepository(PollEntity::class)->find($pollId);

        if (!$poll) {
            $this->flashMessage('Anketa nenalezena.', 'danger');
        }

        /**
         * @var PollOptionEntity $pollOptionEntity
         */
        $pollOptionEntity = $this->em->getRepository(PollOptionEntity::class)->find($pollOptionId);

        if (!$pollOptionEntity) {
            $this->flashMessage('Možnost Ankety nenalezena.', 'danger');
        }

        try {
            $this->em->remove($pollOptionEntity);
            $this->em->flush();

            $this->flashMessage('Možnost Ankety byla smazána.', 'success');
        } catch (Exception $e) {
            $this->flashMessage('Možnost Ankety se nepodařilo smazat.', 'danger');
        }

        $this->redrawControl('options');
        $this->redrawControl('flashes');
        $this->presenter->redrawControl('flashes');
    }
}
