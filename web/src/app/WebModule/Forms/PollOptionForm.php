<?php

namespace Alfred\App\WebModule\Forms;

use Alfred\App\Model\Entity\PollEntity;
use Contributte\FormsBootstrap\BootstrapForm;
use FreezyBee\DoctrineFormMapper\IComponentMapper;
use Nette\Application\UI\Form;

/**
 * class PollOptionForm
 *
 * @package Alfred\App\WebModule\Forms
 */
class PollOptionForm
{
    public function create() : Form
    {
        $form = new BootstrapForm();

        $form->addSelect('poll', 'Anketa')
            ->setPrompt('Vyberte Anketu')
            ->setRequired('Vyberte Anketu')
            ->setOption(
                IComponentMapper::ITEMS_TITLE,
                function (PollEntity $pollEntity) : string {
                    return $pollEntity->question;
                }
            )
            ->setOption(IComponentMapper::ITEMS_ORDER, ['question' => 'ASC']);

        $form->addTextArea('optionText', 'Text možnosti')
            ->setRequired(true);

        $form->addSubmit('send', 'Uložit Možnost Ankety');

        return $form;
    }
}
