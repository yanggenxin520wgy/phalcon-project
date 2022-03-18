<?php

namespace Project\Core\Commands\Builtin;

use Phalcon\Script\Color;
use Project\Core\Commands\CommandsInterface;

/**
 * Class Enumerate
 * @package Project\Core\Commands\Builtin
 */
class Enumerate extends \Phalcon\Commands\Builtin\Enumerate implements CommandsInterface
{
    const ALIAS_COLUMN_LEN = 40;

    /**
     * {@inheritdoc}
     *
     * @param array $parameters
     * @return mixed
     */
    public function run(array $parameters)
    {
        print Color::colorize('Available commands:', Color::FG_BROWN) . PHP_EOL;
        foreach ($this->getScript()->getCommands() as $commands) {
            $providedCommands = $commands->getCommands();
            $commandLen = strlen($providedCommands[0]);

            print '  ' . Color::colorize($providedCommands[0], Color::FG_GREEN);
            unset($providedCommands[0]);
            if (count($providedCommands)) {
                $spacer = str_repeat(' ', self::COMMAND_COLUMN_LEN - $commandLen);
                $alias = ' (alias of: ' . Color::colorize(join(', ', $providedCommands)) . ')';
                print $spacer . $alias;
                $alias_spacer = str_repeat(' ', self::ALIAS_COLUMN_LEN - strlen($alias) + 1);
                print $alias_spacer . Color::colorize('-' . $commands->getDesc(), Color::FG_PURPLE);
            }
            print PHP_EOL;
        }
        print PHP_EOL;
    }

    public function getDesc(): string
    {
        return '列举所有支持的命令';
    }
}
