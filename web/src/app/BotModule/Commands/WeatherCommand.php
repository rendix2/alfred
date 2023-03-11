<?php

namespace Alfred\App\BotModule\Commands;

use Telegram\Bot\Commands\Command;

/**
 * class WeatherCommand
 *
 * @package Alfred\App\BotModule\Commands
 */
class WeatherCommand extends Command
{
    protected $name = 'weather';                      // Your command's name
    protected $description = 'Command to find out weather'; // Your command description
    protected $usage = '/version';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command


    public function handle() : void
    {
        // TODO: Implement handle() method.
    }
}