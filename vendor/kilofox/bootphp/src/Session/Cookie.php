<?php

namespace Bootphp\Session;

/**
 * Cookie-based session class.
 *
 * @package    Bootphp
 * @category   Session
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class SessionCookie extends \Bootphp\Session
{
    /**
     * @param   string  $id  session id
     * @return  string
     */
    protected function _read($id = null)
    {
        return Cookie::get($this->_name, null);
    }

    /**
     * @return  null
     */
    protected function _regenerate()
    {
        // Cookie sessions have no id
        return null;
    }

    /**
     * @return  bool
     */
    protected function _write()
    {
        return Cookie::set($this->_name, $this->__toString(), $this->_lifetime);
    }

    /**
     * @return  bool
     */
    protected function _restart()
    {
        return true;
    }

    /**
     * @return  bool
     */
    protected function _destroy()
    {
        return Cookie::delete($this->_name);
    }

}
