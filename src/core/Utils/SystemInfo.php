<?php


namespace Project\Core\Utils;

use Phalcon\Devtools\Version;
use Phalcon\Version as PhVersion;

class SystemInfo extends \Phalcon\Utils\SystemInfo
{
    public function getDirectories()
    {
        return [
            'DevTools Path' => $this->registry->offsetGet('directories')->ptoolsPath,
            'Templates Path' => $this->registry->offsetGet('directories')->templatesPath,
            'Application Path' => $this->registry->offsetGet('directories')->basePath,
            'Controllers Path' => $this->registry->offsetGet('directories')->controllersDir,
            'Models Path' => $this->registry->offsetGet('directories')->modelsDir,
            'Migrations Path' => $this->registry->offsetGet('directories')->migrationsDir,
            'WebTools Views' => $this->registry->offsetGet('directories')->webToolsViews,
            'WebTools Resources' => $this->registry->offsetGet('directories')->resourcesDir,
            'WebTools Elements' => $this->registry->offsetGet('directories')->elementsDir,
        ];
    }

    public function getVersions()
    {
        return [
            'Phalcon DevTools Version' => Version::get(),
            'Phalcon Version' => PhVersion::get(),
            'AdminLTE Version' => ADMIN_LTE_VERSION,
            'Project Version' => \Project\Core\Version\Version::get(),
        ];
    }
}