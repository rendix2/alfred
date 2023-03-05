<?php

namespace Alfred\App\BotModule\Presenters;

use Alfred\App\BotModule\ResponseStrategies\KrakenStrategy;
use Nette\Application\UI\Presenter;
use Telegram\Bot\Api;

/**
 * class BotPresenter
 *
 * @package Alfred\App\BotModule\Presenters
 */
class BotPresenter extends Presenter
{
    public function __construct(
        private Api $telegram,
        private KrakenStrategy $yolandaStrategy,
    ) {
    }

    public function actionDefault()
    {
        $update = $this->telegram->getWebhookUpdate();
        $message = $update->getMessage();

        $response = $this->yolandaStrategy->run(/*$message*/);

        dump($response);

        exit;

        if ($response) {
            if ($response->answer) {
                $this->telegram->sendMessage(
                    [
                        'chat_id' => $message->chat->id,
                        'text' => $response->answer->answerText,
                        'parse_mode' => 'HTML',
                        'reply_to_message_id' => $message->id,
                    ]
                );
            }

            if ($response->location) {
                $this->telegram->sendLocation(
                    [
                        'chat_id' => $message->chat->id,
                        'latitude' => $response->location->latitude,
                        'longitude' => $response->location->longitude,
                        'reply_to_message_id' => $message->id,
                    ]
                );
            }

            if ($response->gif) {
                $this->telegram->sendAnimation(
                    [
                        'chat_id' => $message->chat->id,
                        'animation' => $response->gif->url,
                        'reply_to_message_id' => $message->id,
                    ]
                );
            }

            if ($response->poll) {

                $options = [];

                foreach ($response->poll->options as $option) {
                    $options[] = $option->optionText;
                }

                $this->telegram->sendPoll(
                    [
                        'chat_id' => $message->chat->id,
                        'question' => $response->poll->question,
                        'options' => $options,
                        'is_anonymous' => true,
                        'type' => $response->poll->type,
                        'allows_multiple_answers' => $response->poll->allowsMultipleAnswers,
                        'reply_to_message_id' => $message->id,
                    ]
                );
            }
        }

        $this->terminate();
    }
}
