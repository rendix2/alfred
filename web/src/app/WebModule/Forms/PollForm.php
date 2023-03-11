<?php

namespace Alfred\App\WebModule\Forms;

use Contributte\FormsBootstrap\BootstrapForm;
use Nette\Application\UI\Form;

/**
 * class PollForm
 *
 * @package Alfred\App\WebModule\Forms
 */
class PollForm
{
    public function create() : Form
    {
        $form = new BootstrapForm();

        $form->addText('question', 'Otázka')
            ->setRequired(true)
            ->setMaxLength(512);

        $form->addRadioList('type', 'Typ', ['regular' => 'Běžná', 'quiz' => 'Kvíz'])
            ->setDefaultValue('regular')
            ->setRequired('Type je povinný');

        $form->addCheckbox('allowsMultipleAnswers', 'Více možných odpověďí?');

        $form->addSubmit('save', 'Uložit Anketu');

        return $form;
    }
}
