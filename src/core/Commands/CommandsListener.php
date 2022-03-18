<?php

namespace Project\Core\Commands;

use Phalcon\Commands\CommandsException;
use Phalcon\Events\Event;
use Phalcon\Commands\Command;
use Phalcon\Commands\DotPhalconMissingException;

/**
 * Commands Listener
 *
 * @package Phalcon\Commands
 */
class CommandsListener
{
    /**
     * Before command executing
     *
     * @param Event   $event
     * @param Command $command
     *
     * @return bool
     * @throws CommandsException
     * @throws \Exception
     */
    public function beforeCommand(Event $event, Command $command)
    {
        $parameters = $command->parseParameters([], ['h' => 'help']);

        if (count($parameters) < ($command->getRequiredParams() + 1) ||
            $command->isReceivedOption(['help', 'h', '?']) ||
            in_array($command->getOption(1), ['help', 'h', '?'])
        ) {
            $command->getHelp();

            return false;
        }

        if ($command->canBeExternal() == false) {
            $path = $command->getOption('directory');
            if ($path) {
                $path = realpath($path) . DIRECTORY_SEPARATOR;
            };

            if (!file_exists($path . 'public/index.php')) {
                throw new \Exception('请在phalcon项目内使用此命令');
            }
            $fp = fopen($path . 'public/index.php', 'r');
            $innerPhalcon = false;
            if ($fp) {
                while (($str = fgets($fp, 4096)) !== false) {
                    if (0 === strpos($str, 'use Phalcon\Di\FactoryDefault;')) {
                        $innerPhalcon = true;
                        break;
                    }
                }
                fclose($fp);
            }
            if (!$innerPhalcon) {
                throw new \Exception('请在phalcon项目内使用此命令');
            }
        }

        return true;
    }
}
