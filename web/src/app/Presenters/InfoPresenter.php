<?php

namespace Alfred\App\Presenters;

use Nette\Application\UI\Presenter;

/**
 * class InfoPresenter
 *
 * @package Alfred\App\Presenters
 */
class InfoPresenter extends Presenter
{

    public function actionDefault() : void
    {
        phpinfo();
        exit();
    }
}
