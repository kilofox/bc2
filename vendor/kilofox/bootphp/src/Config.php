<?php

namespace Bootphp;

/**
 * Wrapper for configuration arrays. Multiple configuration readers can be
 * attached to allow loading configuration from files, database, etc.
 *
 * Configuration directives cascade across config sources in the same way that
 * files cascade across the filesystem.
 *
 * Directives from sources high in the sources list will override ones from those
 * below them.
 *
 * @package    Bootphp
 * @category   Configuration
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class Config
{
    /**
     * Load a configuration file.
     *
     * @param   string  $group      Configuration group name
     * @param   string  $directory  Specified directory
     * @return  mixed
     * @throws  BootphpException
     */
    public function load($group, $directory = null)
    {
        if (empty($group) || !is_string($group)) {
            throw new BootphpException('Config group must be a non-empty string');
        }

        $config = [];

        $file = ($directory === null ? APP_PATH . '/Config/' : (string) $directory) . $group . '.php';
        if (is_file($file)) {
            $config = require $file;
        }

        return $config;
    }

}
