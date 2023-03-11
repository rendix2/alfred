<?php

namespace Alfred\App\WebModule\Forms;

use Alfred\App\Model\Entity\WordEntity;
use Contributte\FormsBootstrap\BootstrapForm;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
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
