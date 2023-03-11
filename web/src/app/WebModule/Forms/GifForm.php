<?php

namespace Alfred\App\WebModule\Forms;

use Contributte\FormsBootstrap\BootstrapForm;
use Nette\Application\UI\Form;

/**
 * class GidForm
 *
 * @package Alfred\App\WebModule\Forms
 */
class GifForm
{
    public function create() : Form
    {
        $form = new BootstrapForm();

        $form->addText('url', 'URL')
            ->setRequired(true);

        $form->addText('name', 'Jméno')
            ->setRequired(true);

        $form->addSubmit('send', 'Uložit GIF');

        return $form;
    }
}
