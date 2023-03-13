<?php

namespace Alfred\App\Loggers\Exception;

use Alfred\App\Model\Connections\ExceptionConnection;
use DateTime;
use Nette\Utils\Json;

/**
 * class SqliteLogger
 *
 * @package Alfred\App\BotModule\Loggers\Exception
 */
class SqliteLogger extends Logger
{
    public function __construct(
        private ExceptionConnection $exceptionConnection
    ) {
        $this->exceptionConnection->getConnection()->query('CREATE TABLE IF NOT EXISTS logs (
            trace JSON NOT NULL, 
            message TEXT, 
            date DATETIME NOT NULL,
            code INT NOT NULL,
            line INT NOT NULL,
            file TEXT NOT NULL,
            type TEXT NOT NULL
        );');
    }

    public function addToLog(\Exception $exception) : void
    {
        $data = [
            'trace' => Json::encode($exception->getTrace()),
            'message' => $exception->getMessage(),
            'date' => new DateTime(),
            'code' => $exception->getCode(),
            'line' => $exception->getLine(),
            'file' => $exception->getFile(),
            'type' => $exception::class,
        ];

        $this->exceptionConnection->getConnection()->insert('logs', $data)->execute();
    }
}
