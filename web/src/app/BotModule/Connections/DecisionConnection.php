<?php

namespace Alfred\App\BotModule\Connections;

use Dibi\Bridges\Tracy\Panel;
use Dibi\Connection;

/**
 * class DecisionConnection
 *
 * @package Alfred\App\BotModule\Connections
 */
class DecisionConnection
{
    private Connection $connection;

    public function __construct(array $config, string $name)
    {
        $this->connection = new Connection($config, $name);

        $panel = new Panel(true);
        $panel->register($this->connection);
    }

    public function getConnection() : Connection
    {
        return $this->connection;
    }
}
