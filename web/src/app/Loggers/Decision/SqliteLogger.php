<?php

namespace Alfred\App\Loggers\Decision;

use Alfred\App\Model\Connections\DecisionConnection;
use DateTime;
use Nette\Utils\Json;

/**
 * class SqliteLogger
 *
 * @package Alfred\App\BotModule\Decision\Loggers
 */
class SqliteLogger extends Logger
{
    public function __construct(
        private DecisionConnection $decisionConnection,
    ) {
        $this->decisionConnection->getConnection()->query('CREATE TABLE IF NOT EXISTS logs (log JSON NOT NULL, date DATETIME NOT NULL);');
    }

    public function addToLog() : void
    {
        $json = Json::encode($this->createLog());

        $this->decisionConnection->getConnection()->insert('logs', ['log' => $json, 'date' => new DateTime()])->execute();
    }
}
