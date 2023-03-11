<?php

namespace Alfred\App\WebModule\Forms;

use Alfred\App\Model\Entity\ChatEntity;
use Alfred\App\Model\Entity\EventEntity;
use Alfred\App\Model\Entity\WordEntity;
use Contributte\FormsBootstrap\BootstrapForm;
use FreezyBee\DoctrineFormMapper\IComponentMapper;
use Nette\Application\UI\Form;

/**
 * class RequestForm
 *
 * @package Alfred\App\WebModule\Forms
 */
class RequestForm
{

    public function create() : Form
    {
        $form = new BootstrapForm();

        $form->addSelect('word', 'Slovo')
            ->setPrompt('Vyberte Slovo')
            ->setRequired('Slovo je povinné.')
            ->setOption(
                IComponentMapper::ITEMS_TITLE,
                function (WordEntity $wordEntity) : string {
                    return $wordEntity->wordText;
                }
            )
            ->setOption(IComponentMapper::ITEMS_ORDER, ['wordText' => 'ASC']);

        $form->addSelect('event', 'Událost')
            ->setPrompt('Vyberte Událost')
            ->setOption(
                IComponentMapper::ITEMS_TITLE,
                function (EventEntity $eventEntity) : string {
                    return $eventEntity->name;
                }
            )
            ->setOption(IComponentMapper::ITEMS_ORDER, ['name' => 'ASC']);

        $form->addSelect('chat', 'Chat')
            ->setPrompt('Vyberte Chat')
            ->setRequired('Chat je povinný.')
            ->setOption(
                IComponentMapper::ITEMS_TITLE,
                function (ChatEntity $chatEntity) : string {
                    return $chatEntity->name;
                }
            )
            ->setOption(IComponentMapper::ITEMS_ORDER, ['name' => 'ASC']);

        $form->addRadioList('aggressiveness', 'Agresivita', [1 => 'Nízká', 5 => 'Střední', 10 => 'Vysoká'])
            ->setRequired('Vyberte prosím agresivitu.');

        $form->addRadioList('priority', 'Priorita', [1 => 'Nízká', 5 => 'Střední', 10 => 'Vysoká'])
            ->setRequired('Vyberte prosím prioritu.');

        $form->addCheckbox('isActive', 'Aktivní?');
        $form->addCheckbox('isExplicit', 'Explicitní?');

        $form->addSubmit('send', 'Uložit Požadavek');

        return $form;
    }
}
