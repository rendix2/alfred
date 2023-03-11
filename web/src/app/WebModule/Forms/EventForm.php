<?php

namespace Alfred\App\WebModule\Forms;

use Contributte\FormsBootstrap\BootstrapForm;
use Nette\Application\UI\Form;

/**
 * class EventForm
 *
 * @package Alfred\App\WebModule\Forms
 */
class EventForm
{

    public function create() : Form
    {
        $form = new BootstrapForm();

        $form->addText('name', 'Jméno')
            ->setRequired('Zadejte prosím jméno.');

        $form->addTextArea('description', 'Popis')
            ->setRequired(false)
            ->setNullable(true);

        $form->addCheckbox('isActive', 'Aktivní?');

        $form->addSubmit('send', 'Uložit Událost');

        return $form;
    }

}
