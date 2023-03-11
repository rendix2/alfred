<?php

namespace Alfred\App\BotModule\ResponseStrategies;

use Alfred\App\AlfredException;
use Alfred\App\BotModule\Loggers\Decision\Logger;
use Alfred\App\Model\Entity\ChatEntity;
use Alfred\App\Model\Entity\EventEntity;
use Alfred\App\Model\Entity\RequestEntity;
use Alfred\App\Model\Entity\ResponseEntity;
use Alfred\App\Model\Entity\ResponseHistoryEntity;
use Alfred\App\Model\Entity\WordEntity;
use DateTime;
use Nettrine\ORM\EntityManagerDecorator;
use Telegram\Bot\Objects\Message;

/**
 * class YloandaStrategy
 *
 * @package Alfred\App\BotModule\ResponseStrategies
 */
class YolandaStrategy implements IResponseStrategy
{
    public function __construct(
        private EntityManagerDecorator $em,
        private Logger                 $logger,
    ) {
    }

    public function run(Message $message) : ?ResponseEntity
    {
        $chatId = $message->getChat()->getId();
        $text = $message->getText();
        /*
                $chatId = -1001525276996; // okultní jelita
                $message = 'Dobré ráno pracanti';*/
        $explodedWords = explode(' ', $text);

        $chat = $this->em
            ->getRepository(ChatEntity::class)
            ->findOneBy(['telegramId' => $chatId]);

        if (!$chat) {
            $errorMessage = sprintf('Chat [%d] not found.', $chatId);

            throw new AlfredException($errorMessage);
        }

        if (!$chat->isActive) {
            $errorMessage = sprintf('Chat [%d] [%s] is not active.', $chatId, $chat->name);

            throw new AlfredException($errorMessage);
        }

        $activeEvents = $this->em
            ->getRepository(EventEntity::class)
            ->findBy(
                [
                    'isActive' => 1
                ]
            );

        $allWords = $this->em
            ->getRepository(WordEntity::class)
            ->findAll();

        $matchedWords = [];

        foreach ($allWords as $allWord) {
            if (preg_match("~" . str_replace(" ", "\s+", $allWord->wordText) . "~msiu", $message)) {
                $matchedWords[] = $allWord;
            }
        }

        $requests = $this->em
            ->getRepository(RequestEntity::class)
            ->findBy(
                [
                    'chat' => $chat,
                    'event' => $activeEvents,
                    'word' => $matchedWords,
                    'isActive' => true,
                ]
            );

        $possibleRequestIds = [];
        $assocRequests = [];

        foreach ($requests as $request) {
            $possibleRequestIds[$request->id] = $request->id;
            $assocRequests[$request->id] = $request;
        }

        $selectedRequestId = array_rand($possibleRequestIds);
        $selectedRequest = $assocRequests[$selectedRequestId];

        $possibleResponsesIds = [];
        $possibleResponses = [];

        foreach ($selectedRequest->responses as $response) {
            $possibleResponsesIds[$response->id] = $response->id;
            $possibleResponses[$response->id] = $response;
        }

        $selectedResponseId = array_rand($possibleResponses);
        $selectedResponse = $possibleResponses[$selectedResponseId];

        $responseHistory = new ResponseHistoryEntity();
        $responseHistory->request = $assocRequests[$selectedRequestId];
        $responseHistory->response = $selectedResponse;

        $this->em->persist($responseHistory);
        $this->em->flush();

        $this->logger->setChat($chat);
        $this->logger->setUser(1);
        $this->logger->setDateTime(new DateTime());
        $this->logger->setEvent($selectedRequest->event);

        $this->logger->setPossibleResponses(array_values($possibleResponsesIds));
        $this->logger->setPossibleRequests(array_values($possibleRequestIds));

        $this->logger->setSelectedRequest($selectedRequestId);
        $this->logger->setSelectedResponse($selectedResponseId);

        $this->logger->setResponseStrategy($this);

        $this->logger->addToLog();

        return $selectedResponse;
    }
}
