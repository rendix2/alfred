<?php

namespace Alfred\App\WebModule\Forms;

use Alfred\App\Model\Entity\AnswerEntity;
use Alfred\App\Model\Entity\GifEntity;
use Alfred\App\Model\Entity\LocationEntity;
use Alfred\App\Model\Entity\PollEntity;
use Contributte\FormsBootstrap\BootstrapForm;
use FreezyBee\DoctrineFormMapper\IComponentMapper;
use Nette\Application\UI\Form;

/**
 * class ResponseForm
 *
 * @package Alfred\App\WebModule\Forms
 */
class ResponseForm
{

    public function create() : Form
    {
        $form = new BootstrapForm();

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

        return $form;
    }
}
