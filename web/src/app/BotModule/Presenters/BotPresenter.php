<?php

namespace Alfred\App\BotModule\Presenters;

use Alfred\App\BotModule\ResponseStrategies\KrakenStrategy;
use Nette\Application\UI\Presenter;
use Nette\Utils\Json;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

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

    public function actionDefault() : void
    {
        $update = $this->telegram->getWebhookUpdate();
        $message = $update->getMessage();

        $foundCommand = false;

        foreach ($message->entities as $entity) {
            if ($entity->offset === 0 && $entity->type === 'bot_command') {
                $foundCommand = true;
                break;
            }
        }

        if ($foundCommand) {
            $this->getHttpResponse()->setCode(200);
            $this->terminate();
        }

        $response = $this->yolandaStrategy->run($message);

        if ($response) {
            if ($response->answer) {
                $this->telegram->sendMessage(
                    [
                        'chat_id' => $message->chat->id,
                        'text' => $response->answer->answerText,
                        'parse_mode' => 'HTML',
                        'reply_to_message_id' => $message->messageId,
                    ]
                );
            }

            if ($response->location) {
                $this->telegram->sendLocation(
                    [
                        'chat_id' => $message->chat->id,
                        'latitude' => $response->location->latitude,
                        'longitude' => $response->location->longitude,
                        'reply_to_message_id' => $message->messageId,
                    ]
                );
            }

            if ($response->gif) {
                $this->telegram->sendAnimation(
                    [
                        'chat_id' => $message->chat->id,
                        'animation' => InputFile::create($response->gif->url),
                        'reply_to_message_id' => $message->messageId,
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
                        'reply_to_message_id' => $message->messageId,
                    ]
                );
            }
        }

        $this->getHttpResponse()->setCode(200);
        $this->terminate();
    }
}
