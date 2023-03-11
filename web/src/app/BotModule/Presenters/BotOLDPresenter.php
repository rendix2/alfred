<?php

namespace Alfred\App\BotModule\Presenters;

use Alfred\App\Model\Entity\ResponseEntity;
use Alfred\App\Model\Entity\WordEntity;
use Alfred\App\Model\Tables;
use DateTime;
use dibi;
use Dibi\Connection;
use Dibi\Exception;
use GuzzleHttp\Client as GuzzleClient;
use Nette\Application\UI\Presenter;
use Nette\Utils\Json;
use Nettrine\ORM\EntityManagerDecorator;
use Telegram\Bot\Api as TelegramApi;
use Tracy\Debugger;

/**
 * class BotPresenter
 *
 * @package Alfred\App\BotModule\Presenters
 */
class BotOLDPresenter extends Presenter
{
    private Connection $connection;

    private EntityManagerDecorator $em;

    /**
     * @var TelegramApi $telegramApi
     */
    private TelegramApi $telegramApi;

    private GuzzleClient $client;

    /**
     * @param Connection             $connection
     * @param EntityManagerDecorator $em
     * @param GuzzleClient           $client
     * @param TelegramApi            $telegramApi
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Nette\Utils\JsonException
     */
    public function __construct(
        Connection             $connection,
        EntityManagerDecorator $em,
        GuzzleClient           $client,
        TelegramApi            $telegramApi
    )
    {
        $this->connection = $connection;
        $this->em = $em;
        $this->telegramApi = $telegramApi;

        $members = $client->request('GET', 'https://back.komunitnicentrum.eu/api.member/get-all');

        $members = Json::decode($members->getBody()->getContents());
    }

    public function actionTest() : void
    {
        $testWord = 'vejplata';

        $now = new DateTime('-2hours');

        $alfred = $this->connection
            ->select('*')
            ->from(Tables::ALFRED_TABLE)
            ->where('%dt >= [startedAt]', $now)
            ->where('(%dt <= [finishedAt] OR [finishedAt] IS NULL)', $now)
            ->where('[isActive] = %i', 1)
            ->fetch();

        if ($alfred) {
            $alfredMood = $alfred->mood;
        } else {
            $alfredMood = random_int(1, 5);

            $alfredData = [
                'mood' => $alfredMood,
                'startedAt' => new DateTime(),
                'finishedAt' => new DateTime('+1 day')
            ];

            $id = $this->connection
                ->insert(Tables::ALFRED_TABLE, $alfredData)
                ->execute(\dibi::IDENTIFIER);

            $alfred = $this->connection
                ->select('*')
                ->from(Tables::ALFRED_TABLE)
                ->where('[id] = %i', $id)
                ->fetch();
        }

        $wordIds = $this->connection
            ->select('wordId')
            ->from(Tables::WORD_VARIANT)
            ->where('[variantText] REGEXP %s', $testWord)
            ->fetchPairs(null, 'wordId');

        if (count($wordIds)) {
            $ids = $this->connection
                ->select('id')
                ->from(Tables::WORD_TABLE)
                ->where('[id] IN %in', $wordIds)
                ->where('[isActive] = %i', 1)
                ->fetchPairs(null, 'id');
        } else {
            $ids = $this->connection
                ->select('id')
                ->from(Tables::WORD_TABLE)
                ->where('[wordText] REGEXP %s', $testWord)
                ->where('[isActive] = %i', 1)
                ->fetchPairs(null, 'id');
        }

        $words = $this->em
            ->getRepository(WordEntity::class)
            ->findBy(
                ['id' => $ids],
                ['priority' => 'DESC']
            );

        $responses = $this->em
            ->getRepository(ResponseEntity::class)
            ->findBy(
                ['word' => $words],
            /*['priority' => 'desc']*/
            );


        foreach ($responses as $response) {
            bdump($response->answer->text);
        }

        $historyData = [
            'messageId' => 45,
            'wordId' => $words[0]->id,
            'responseId' => $responses[0]->id
        ];

        $historyId = $this->connection
            ->insert(Tables::RESPONSE_HISTORY_TABLE, $historyData)
            ->execute(dibi::IDENTIFIER);
    }

    /**
     * @return void
     *
     */
    public function actionChat() : void
    {
        try {
            $update = $this->telegramApi->getWebhookUpdate();
            $message = $update->getMessage();

            $chat = $message->getChat()->getId();
            $text = $message->getText();

            if (str_contains($text, 'HELLO')) {
                $this->telegramApi->sendMessage(
                    [
                        'chat_id' => $chat,
                        'text' => 'Reaguji na citlivé slovo.',
                        'reply_to_message_id' => $message->getMessageId()
                    ]
                );
            }

            $this->getHttpResponse()->setCode(200);
            $this->terminate();
        } catch (Exception $e) {
            Debugger::log($e->getMessage());
        }
    }
}
