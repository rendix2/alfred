<?php

namespace Alfred\App\WebModule\Forms;

use Contributte\FormsBootstrap\BootstrapForm;
use Nette\Application\UI\Form;

/**
 * class ChatForm
 *
 * @package Alfred\App\WebModule\Forms
 */
class ChatForm
{

    public function create() : Form
    {
        $form = new BootstrapForm();

        $form->addText('name', 'Jméno')
            ->setRequired('Zadejte prosím jméno.');

        $form->addInteger('telegramId', 'Telegram ID')
            ->setRequired('Zadejte prosím Telegram ID.');

        $form->addCheckbox('isActive', 'Aktivní?');

        $form->addSubmit('send', 'Uložit Chat');

        return $form;
    }
}
