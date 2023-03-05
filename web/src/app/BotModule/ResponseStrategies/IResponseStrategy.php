<?php

namespace Alfred\App\BotModule\ResponseStrategies;

use Alfred\App\Model\Entity\ResponseEntity;

/**
 * class IResponseStrategy
 *
 * @package Alfred\App\BotModule\ResponseStrategies
 */
interface IResponseStrategy
{
    public function run() : ?ResponseEntity;
}
