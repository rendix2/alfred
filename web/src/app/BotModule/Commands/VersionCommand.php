<?php

namespace Alfred\App\BotModule\Commands;

use Telegram\Bot\Commands\Command;

/**
 * class VersionCommand
 *
 * @package Alfred\App\BotModule\Commands
 */
class VersionCommand extends Command
{
    protected $name = 'version';                      // Your command's name
    protected $description = 'Command to get Alfred Version'; // Your command description
    protected $usage = '/version';                    // Usage of your command
    protected $version = '1.0.1';                  // Version of your command

    public function handle()
    {
        $data = [                                  // Set up the new message data
            'text'    => "Jsem připraven vám sloužit holoto líná. Má verze je: ". VERSION . '.', // Set message to send
        ];

        return $this->replyWithMessage($data);
    }
}