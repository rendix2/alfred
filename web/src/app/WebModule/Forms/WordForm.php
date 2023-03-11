<?php

namespace Alfred\App\WebModule\Forms;

use Contributte\FormsBootstrap\BootstrapForm;
use Nette\Application\UI\Form;

/**
 * class WordForm
 *
 * @package Alfred\App\WebModule\Forms
 */
class WordForm
{

    public function create() : Form
    {
        $form = new BootstrapForm();

        $form->addText('wordText', 'Slovo')
            ->setRequired('Zadejte prosím slovo.');

        $form->addTextArea('description', 'Popis')
            ->setNullable(true)
            ->setRequired(false);

        $form->addSubmit('send', 'Uložit Slovo');

        return $form;
    }

}
