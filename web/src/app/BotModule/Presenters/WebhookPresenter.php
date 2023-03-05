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
    private TelegramApi $telegramApi;

    /**
     * @param TelegramApi $telegramApi
     */
    public function __construct(TelegramApi $telegramApi)
    {
        $this->telegramApi = $telegramApi;
    }

    public function actionSet() : void
    {
        $result = $this->telegramApi->setWebhook(
            [
                'url' => 'https://back.komunitnicentrum.eu/bot.bot/chat',
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
