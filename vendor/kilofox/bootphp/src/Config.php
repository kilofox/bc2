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
     * Array of config for default
     */
    protected $config = [];

    /**
     * Load a configuration file.
     *
     * @param   string  $file  Configuration file name
     * @return  $this
     * @throws  BootphpException
     */
    public function load($file)
    {
        if (empty($file) || !is_string($file)) {
            throw new BootphpException('Config group must be a non-empty string');
        }

        if (is_file($file)) {
            $this->config = require $file;
        }

        return $this;
    }

    /**
     * Get a variable from the configuration or return the default value.
     *
     *     $value = $config->get($key);
     *
     * @param   string  $key        Array key
     * @param   mixed   $default    Default value
     * @return  mixed
     */
    public function get($key, $default = null)
    {
        return isset($this->config[$key]) ? $this->config[$key] : $default;
    }

}
