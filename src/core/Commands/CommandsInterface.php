<?php


namespace Project\Core\Commands;

/**
 * @package Phalcon\Commands
 */
interface CommandsInterface extends \Phalcon\Commands\CommandsInterface
{
    public function getDesc(): string;
}
