<?php

namespace Alfred\App\BotModule\Commands;

use Commands\AICommand;
use Nette\Utils\Json;
use Orhanerday\OpenAi\OpenAi;
use Telegram\Bot\Commands\Command;

/**
 * class OpenAiCommand
 *
 * @package Alfred\App\BotModule\Commands
 */
class OpenAiCommand extends Command
{
    protected $name = 'ai';
    protected $description = 'Command to communicate with open AI';
    protected $usage = '/ai';
    protected $version = '1.0.1';

    public function __construct(
        private OpenAi $ai,
    )
    {
    }

    public static function getDisabledWords() : array
    {
        return [
            'feminis',
        ];
    }

    public function handle()
    {
        $arguments = $this->getArguments()[0];
        $lowerArguments = mb_strtolower($arguments);

        foreach (static::getDisabledWords() as $word) {
            if (preg_match('#' . $word . '#', $lowerArguments)) {
                $responseData = [
                    'text' => 'Jejda. Něco se pokazilo. Náš tým odborníků se na to dříve nebo později podívá. Slibujeme, protože slibem nezarmoutíš. Přejeme hezký den.'
                ];

                return $this->replyWithMessage($responseData);
            }
        }

        $aiResult = $this->ai->completion(
            [
                'model' => 'text-davinci-003',
                'prompt' => $arguments,
                'temperature' => 0.5,
                'max_tokens' => 150,
                'top_p' => 0.3,
                'frequency_penalty' => 0.5,
                'presence_penalty' => 0,
            ]
        );

        $aiResultDecoded = Json::decode($aiResult);
        $responseText = $aiResultDecoded->choices[0]->text;

        $responseData = [
            'text' => $responseText
        ];

        return $this->replyWithMessage($responseData);
    }
}
