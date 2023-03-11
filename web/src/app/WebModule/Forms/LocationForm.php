<?php

namespace Alfred\App\WebModule\Forms;

use Contributte\FormsBootstrap\BootstrapForm;
use Nette\Application\UI\Form;

/**
 * class LocationForm
 *
 * @package Alfred\App\WebModule\Forms
 */
class LocationForm
{

    public function create() : Form
    {
        $form = new BootstrapForm();

        $form->addText('latitude', 'Latitude')
            ->setRequired(true);

        $form->addText('longitude', 'Longitude')
            ->setRequired(true);

        $form->addText('name', 'Jméno')
            ->setRequired(true);

        $form->addTextArea('description', 'Popis')
            ->setNullable();

        $form->addSubmit('send', 'Uložit Polohu');

        return $form;
    }
}
