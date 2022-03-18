<?php


namespace Project\Core\Version;


class Version extends \Phalcon\Devtools\Version
{
    protected static function _getVersion()
    {
        return [0, 0, 1, 4, 0];
    }
}