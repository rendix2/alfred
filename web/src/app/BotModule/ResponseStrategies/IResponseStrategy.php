<?php

namespace Alfred\App\BotModule\ResponseStrategies;

use Alfred\App\Model\Entity\ResponseEntity;
use Telegram\Bot\Objects\Message;

/**
 * class IResponseStrategy
 *
 * @package Alfred\App\BotModule\ResponseStrategies
 */
interface IResponseStrategy
{
    public function run(Message $message) : ?ResponseEntity;
}
