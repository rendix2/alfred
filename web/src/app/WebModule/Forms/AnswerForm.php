<?php

namespace Alfred\App\WebModule\Forms;

use Contributte\FormsBootstrap\BootstrapForm;
use Nette\Application\UI\Form;

/**
 * class AnswerForm
 *
 * @package Alfred\App\WebModule\Forms
 */
class AnswerForm
{

    public function create() : Form
    {
        $form = new BootstrapForm();

        $form->addTextArea('answerText', 'Text')
            ->setRequired('Zadejte prosím text.');

        //$form->addCheckbox('isActive', 'Aktivní?');

        $form->addSubmit('send', 'Uložit Odpověď');

        return $form;
    }
}
