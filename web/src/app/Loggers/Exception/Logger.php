<?php

namespace Alfred\App\Loggers\Exception;

use Alfred\App\Model\Connections\DecisionConnection;

/**
 * class Logger
 *
 * @package Alfred\App\BotModule\Loggers\Exception
 */
abstract class Logger
{
    public function __construct(
        private DecisionConnection $decisionConnection,
    ) {
        $this->decisionConnection->getConnection()->query('CREATE TABLE IF NOT EXISTS logs (log JSON NOT NULL, date DATETIME NOT NULL);');
    }
}
