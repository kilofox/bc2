<?php

namespace Bootphp\Session;

use Bootphp\Cookie;

/**
 * Native PHP session class.
 *
 * @package    Bootphp
 * @category   Session
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class SessionNative extends Session
{
    /**
     * @return  string
     */
    public function id()
    {
        return session_id();
    }

    /**
     * @param   string  $id     Session id
     * @return  null
     */
    protected function _read($id = null)
    {
        /**
         * session_set_cookie_params will override php ini settings.
         * If Cookie::$domain is null or empty and is passed, PHP will override
         * ini and sent cookies with the host name of the server which generated
         * the cookie.
         *
         * see issue #3604
         *
         * see http://www.php.net/manual/en/function.session-set-cookie-params.php
         * see http://www.php.net/manual/en/session.configuration.php#ini.session.cookie-domain
         *
         * set to Cookie::$domain if available, otherwise default to ini setting
         */
        $sessionCookieDomain = empty(Cookie::$domain) ? ini_get('session.cookie_domain') : Cookie::$domain;

        // Sync up the session cookie with Cookie parameters
        session_set_cookie_params($this->lifetime, Cookie::$path, $sessionCookieDomain, Cookie::$secure, Cookie::$httpOnly);

        // Do not allow PHP to send Cache-Control headers
        session_cache_limiter(false);

        // Set the session cookie name
        session_name($this->name);

        if ($id) {
            // Set the session id
            session_id($id);
        }

        // Start the session
        session_start();

        // Use the $_SESSION global for storing data
        $this->data = &$_SESSION;

        return null;
    }

    /**
     * @return  string
     */
    protected function _regenerate()
    {
        // Regenerate the session id
        session_regenerate_id();

        return session_id();
    }

    /**
     * @return  bool
     */
    protected function _write()
    {
        // Write and close the session
        session_write_close();

        return true;
    }

    /**
     * @return  bool
     */
    protected function _restart()
    {
        // Fire up a new session
        $status = session_start();

        // Use the $_SESSION global for storing data
        $this->data = &$_SESSION;

        return $status;
    }

    /**
     * @return  bool
     */
    protected function _destroy()
    {
        // Destroy the current session
        session_destroy();

        // Did destruction work?
        $status = !session_id();

        if ($status) {
            // Make sure the session cannot be restarted
            Cookie::delete($this->name);
        }

        return $status;
    }

}
