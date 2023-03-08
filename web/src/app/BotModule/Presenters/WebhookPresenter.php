<?php

namespace Alfred\App\BotModule\Presenters;

use Nette\Application\UI\Presenter;
use Telegram\Bot\Api as TelegramApi;

/**
 * class WebhookPresenter
 *
 * @package Alfred\App\BotModule\Presenters
 */
class WebhookPresenter extends Presenter
{
    /** @param TelegramApi $telegramApi
     */
    public function __construct(
        private TelegramApi $telegramApi,
    ) {
    }

    public function actionSet() : void
    {
        $result = $this->telegramApi->setWebhook(
            [
                'url' => $this->link('//:Bot:Bot:Default'),
                'drop_pending_updates' => true
            ]
        );

        $this->sendJson(['result' => $result]);
    }

    public function actionStatus() : void
    {
        $this->sendJson(['result' => $this->telegramApi->getWebhookInfo()]);
    }

    public function actionDelete() : void
    {
        $this->sendJson(['result' => $this->telegramApi->deleteWebhook()]);
    }
}
