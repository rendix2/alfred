<?php

namespace Alfred\App\BotModule\ResponseStrategies;

use Alfred\App\AlfredException;
use Alfred\App\BotModule\Loggers\Decision\Logger;
use Alfred\App\Model\Entity\CategoryEntity;
use Alfred\App\Model\Entity\ChatEntity;
use Alfred\App\Model\Entity\EventEntity;
use Alfred\App\Model\Entity\RequestEntity;
use Alfred\App\Model\Entity\ResponseEntity;
use Alfred\App\Model\Entity\ResponseHistoryEntity;
use Alfred\App\Model\Entity\WordEntity;
use DateInterval;
use DateTime;
use Nettrine\ORM\EntityManagerDecorator;
use Telegram\Bot\Objects\Message;
use Tracy\Debugger;

/**
 * class KrakenStrategy
 *
 * @package Alfred\App\BotModule\ResponseStrategies
 */
class KrakenStrategy implements IResponseStrategy
{

    public function __construct(
        private EntityManagerDecorator $em,
        private Logger                 $logger,
    ) {
    }

    public function run(Message $message) : ?ResponseEntity
    {
        $chatId = $message->getChat()->getId();
        $messageText = $message->getText();
        $user = $message->getFrom();

        $chat = $this->em
            ->getRepository(ChatEntity::class)
            ->findOneBy(
                [
                    'telegramId' => $chatId
                ]
            );

        if (!$chat) {
            $errorMessage = sprintf('Chat [%s] not found.', $chatId);

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
            ->findBy(
                [

                ]
            );

        /**
         * @var WordEntity[] $matchedWords
         */
        $matchedWords = [];

        foreach ($allWords as $allWord) {
            $pattern = "~" . str_replace(" ", "\s+", $allWord->wordText) . "~msiu";

            if (preg_match($pattern, $messageText)) {
                $matchedWords[] = $allWord;
            }
        }

        /**
         * @var CategoryEntity[] $matchedCategories
         */
        $matchedCategories = [];

        foreach ($matchedWords as $matchedWord) {
            foreach ($matchedWord->categories as $category) {
                $matchedCategories[$category->id] = $category;
            }
        }

        /**
         * @var WordEntity[] $wordsByCategories
         */
        $wordsByCategories = [];

        foreach ($matchedCategories as $matchedCategory) {
            foreach ($matchedCategory->words as $categoryWord) {
                $wordsByCategories[$categoryWord->id] = $categoryWord;
            }
        }

        $possibleRequests = $this->em
            ->getRepository(RequestEntity::class)
            ->createQueryBuilder('_request')

            ->where('_request.chat = :chat')
            ->setParameter('chat', $chat)

            ->andWhere('_request.event IN (:events)')
            ->setParameter('events', $activeEvents)

            ->andWhere('_request.word IN (:words)')
            ->setParameter('words', $matchedWords)

            ->andWhere('_request.isActive = :active')
            ->setParameter('active', true)

            ->addOrderBy('_request.priority', 'ASC')

            ->getQuery()
            ->getResult();

        $allowedDatetime = new DateTime();
        $allowedDatetime->sub(new DateInterval('P10M'));

        $historyRequests = $this->em
            ->getRepository(ResponseHistoryEntity::class)
            ->createQueryBuilder('_responseHistory')

            ->where('_responseHistory.request NOT IN (:requests)')
            ->setParameter('requests', $possibleRequests)

            ->andWhere('_responseHistory.createdAt <= :date')
            ->setParameter('date', $allowedDatetime)

            ->getQuery()
            ->getResult();

        $bannedRequests = [];

        foreach ($historyRequests as $historyRequest) {
            $bannedRequests[] = $historyRequest->request;
        }

        $requestsQuery = $this->em
            ->getRepository(RequestEntity::class)
            ->createQueryBuilder('_request')

            ->where('_request.chat = :chat')
            ->setParameter('chat', $chat)

            ->andWhere('_request.event IN (:events)')
            ->setParameter('events', $activeEvents)

            ->andWhere('_request.word IN (:words)')
            ->setParameter('words', $matchedWords)

            ->andWhere('_request.isActive = :active')
            ->setParameter('active', true);

        if (count($bannedRequests)) {
            $requestsQuery = $requestsQuery->andWhere('_request.id NOT IN (:bannedRequests)')
                ->setParameter('bannedRequests', $bannedRequests);
        }

        $requests = $requestsQuery->addOrderBy('_request.priority', 'ASC')
            ->getQuery()
            ->getResult();

        $requestsWithResponse = [];

        $logRequest = [];

        foreach ($requests as $request) {
            if (count($request->responses)) {
                $requestsWithResponse[$request->id] = $request;
                $logRequest[$request->id] = $request->id;
            }
        }

        Debugger::log($logRequest);

        $assocRequests = [];
        $groupByPriorityRequests = [];

        foreach ($requestsWithResponse as $request) {
            $assocRequests[$request->id] = $request;
            $groupByPriorityRequests[$request->priority][$request->id] = $request;
        }

        $biggestRequestGroupKey = null;
        $biggestRequestGroup = 0;

        foreach ($groupByPriorityRequests as $currentKey => $groupByPriorityRequest) {
            $currentCount = count($groupByPriorityRequest);

            if ($currentCount >= $biggestRequestGroup) {
                $biggestRequestGroupKey = $currentKey;
                $biggestRequestGroup = $currentCount;
            }
        }

        if ($biggestRequestGroupKey === null) {
            if (count($assocRequests)) {
                $selectedRequestId = array_rand($assocRequests);
                $possibleRequestIds = array_keys($assocRequests);
            } else {
                Debugger::log('NO RESPONSE');
                return null;
            }
        } else {
            $possibleRequestIds = array_keys(
                $groupByPriorityRequests[$biggestRequestGroupKey]
            );
            $selectedRequestId = array_rand($groupByPriorityRequests[$biggestRequestGroupKey]);
        }

        $selectedRequest = $assocRequests[$selectedRequestId];

        $possibleResponses = [];
        $groupByPriorityResponses = [];

        /**
         * @var ResponseEntity[] $possibleResponses
         * @var ResponseEntity   $response
         */
        foreach ($selectedRequest->responses as $response) {
            if ($response->isActive) {
                $possibleResponses[$response->id] = $response;
                $groupByPriorityResponses[$response->priority][$response->id] = $response;
            }
        }

        $biggestResponseGroupKey = null;
        $biggestResponseGroup = 0;

        foreach ($groupByPriorityResponses as $currentKey => $groupByPriorityResponse) {
            $currentCount = count($groupByPriorityResponse);

            if ($currentCount >= $biggestResponseGroup) {
                $biggestResponseGroupKey = $currentKey;
                $biggestResponseGroup = $currentCount;
            }
        }

        $possibleResponsesIds = array_keys($groupByPriorityResponses[$biggestResponseGroupKey]);
        $selectedResponseId = array_rand($groupByPriorityResponses[$biggestResponseGroupKey]);

        $selectedResponse = $possibleResponses[$selectedResponseId];

        $responseHistory = new ResponseHistoryEntity();
        $responseHistory->request = $selectedRequest;
        $responseHistory->response = $selectedResponse;

        $this->em->persist($responseHistory);
        $this->em->flush();

        $this->logger->setChat($chat);
        $this->logger->setUser($user);
        $this->logger->setDateTime(new DateTime());
        $this->logger->setEvent($selectedRequest->event);

        $this->logger->setPossibleResponses($possibleResponsesIds);
        $this->logger->setPossibleRequests($possibleRequestIds);

        $this->logger->setSelectedRequest($selectedRequestId);
        $this->logger->setSelectedResponse($selectedResponseId);

        $this->logger->setResponseStrategy($this);

        $this->logger->addToLog();

        return $selectedResponse;
    }
}
