<?php

namespace Alfred\App\BotModule\Loggers\Decision;

use Alfred\App\BotModule\ResponseStrategies\IResponseStrategy;
use Alfred\App\Model\Entity\ChatEntity;
use Alfred\App\Model\Entity\EventEntity;
use DateTime;
use Telegram\Bot\Objects\User;

/**
 * class Logger
 *
 * @package Alfred\App\BotModule\Decision\Loggers
 */
abstract class Logger
{

    private ChatEntity $chat;

    private EventEntity $event;

    private User $user;

    private array $possibleRequests;

    private int $selectedRequest;

    private array $possibleResponses;

    private int $selectedResponse;

    private DateTime $dateTime;

    private IResponseStrategy $responseStrategy;

    /**
     * @return IResponseStrategy
     */
    public function getResponseStrategy() : IResponseStrategy
    {
        return $this->responseStrategy;
    }

    /**
     * @param IResponseStrategy $responseStrategy
     */
    public function setResponseStrategy(IResponseStrategy $responseStrategy) : void
    {
        $this->responseStrategy = $responseStrategy;
    }

    /**
     * @return ChatEntity
     */
    public function getChat() : ChatEntity
    {
        return $this->chat;
    }

    /**
     * @param ChatEntity $chat
     */
    public function setChat(ChatEntity $chat) : void
    {
        $this->chat = $chat;
    }

    /**
     * @return EventEntity
     */
    public function getEvent() : EventEntity
    {
        return $this->event;
    }

    /**
     * @param EventEntity $event
     *
     * @return Logger
     */
    public function setEvent(EventEntity $event) : Logger
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return int
     */
    public function getUser() : int
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user) : void
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function getPossibleRequests() : array
    {
        return $this->possibleRequests;
    }

    /**
     * @param array $possibleRequests
     */
    public function setPossibleRequests(array $possibleRequests) : void
    {
        $this->possibleRequests = $possibleRequests;
    }

    /**
     * @return int
     */
    public function getSelectedRequest() : int
    {
        return $this->selectedRequest;
    }

    /**
     * @param int $selectedRequest
     */
    public function setSelectedRequest(int $selectedRequest) : void
    {
        $this->selectedRequest = $selectedRequest;
    }

    /**
     * @return array
     */
    public function getPossibleResponses() : array
    {
        return $this->possibleResponses;
    }

    /**
     * @param array $possibleResponses
     */
    public function setPossibleResponses(array $possibleResponses) : void
    {
        $this->possibleResponses = $possibleResponses;
    }

    /**
     * @return int
     */
    public function getSelectedResponse() : int
    {
        return $this->selectedResponse;
    }

    /**
     * @param int $selectedResponse
     */
    public function setSelectedResponse(int $selectedResponse) : void
    {
        $this->selectedResponse = $selectedResponse;
    }

    /**
     * @return DateTime
     */
    public function getDateTime() : DateTime
    {
        return $this->dateTime;
    }

    /**
     * @param DateTime $dateTime
     */
    public function setDateTime(DateTime $dateTime) : void
    {
        $this->dateTime = $dateTime;
    }

    abstract public function addToLog();

    public function createLog() : array
    {
        return [
            'chat' => [
                'id' => $this->chat->id,
                'name' => $this->chat->name,
                'telegramId' => $this->chat->telegramId,
            ],
            'event' => [
                'id' => $this->event->id,
                'name' => $this->event->name,
                'description' => $this->event->description,
            ],
            'user' => [
                'id' => $this->user->id,
            ],

            'possibleRequests' => $this->possibleRequests,
            'selectedRequest' => $this->selectedRequest,

            'possibleResponses' => $this->possibleResponses,
            'selectedResponse' => $this->selectedResponse,

            'responseStrategy' => $this->responseStrategy::class,
        ];
    }
}
