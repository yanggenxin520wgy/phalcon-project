<?php


namespace Project\Core\Version;


class Version extends \Phalcon\Devtools\Version
{
    protected static function _getVersion()
    {
        return [1, 0, 0, 4, 0];
    }
}