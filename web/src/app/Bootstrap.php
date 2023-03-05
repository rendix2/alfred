<?php

declare(strict_types=1);

namespace Alfred\App;

use Exception;
use Nette\Bootstrap\Configurator;

/**
 * class Bootstrap
 *
 * @package Alfred\App
 */
class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;
		$appDir = dirname(__DIR__);

		//$configurator->setDebugMode('secret@23.75.345.200'); // enable for your remote IP

        if (isset($_SERVER['HTTP_HOST'])) {
            $configurator->setDebugMode($_SERVER['HTTP_HOST'] === 'alfred.test:9080');
        } else {
            $configurator->setDebugMode(false);
        }

		$configurator->enableTracy($appDir . '/log');

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory($appDir . '/temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

        $commonNeonPath = $appDir . '/config/common.neon';
        $localNeonPath = $appDir . '/config/local.neon';
        $prodNeonPath = $appDir  . '/config/prod.neon';

        if (file_exists($commonNeonPath)) {
            $configurator->addConfig($commonNeonPath);
        } else {
            throw new Exception('Common.neon is missing.');
        }

        if (file_exists($localNeonPath)) {
            $configurator->addConfig($localNeonPath);
        } elseif (file_exists($prodNeonPath)) {
            $configurator->addConfig($prodNeonPath);
        } else {
            $message = sprintf('Local.neon or prod.nenon is missing.');
            throw new Exception($message);
        }

		return $configurator;
	}
}
