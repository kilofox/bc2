<?php

namespace Bootphp\Auth;

/**
 * User authorization library. Handles user login and logout, as well as secure
 * password hashing.
 *
 * @package    Bootphp/Auth
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
abstract class Auth
{
    /**
     * Auth instances.
     *
     * @var object
     */
    protected static $_instance;

    /**
     * Singleton pattern.
     *
     * @return Auth
     */
    public static function instance()
    {
        if (!isset(self::$_instance)) {
            // Load the configuration for this type
            $config = \Bootphp\Core::$config->load('auth');

            $type = isset($config['driver']) ? $config['driver'] : 'ORM';

            // Set the session class name
            $class = 'Bootphp\\Auth\\Driver\\' . ucfirst($type) . 'Driver';

            // Create a new session instance
            self::$_instance = new $class($config);
        }

        return self::$_instance;
    }

    protected $_session;
    protected $_config;

    /**
     * Loads Session and configuration options.
     *
     * @param   array   $config Config Options
     * @return  void
     */
    public function __construct($config = array())
    {
        // Save the config in the object
        $this->_config = $config;

        $this->_session = \Bootphp\Session::instance($this->_config['session_type']);
    }

    abstract protected function _login($username, $password, $remember);
    abstract public function password($username);
    abstract public function checkPassword($password);
    /**
     * Gets the currently logged in user from the session.
     * Returns null if no user is currently logged in.
     *
     * @param   mixed  $default  Default value to return if the user is currently not logged in.
     * @return  mixed
     */
    public function getUser($default = null)
    {
        return $this->_session->get($this->_config['session_key'], $default);
    }

    /**
     * Attempt to log in a user by using an ORM object and plain-text password.
     *
     * @param   string  $username   Username to log in
     * @param   string  $password   Password to check against
     * @param   boolean $remember   Enable autologin
     * @return  boolean
     */
    public function login($username, $password, $remember = false)
    {
        if (empty($password))
            return false;

        return $this->_login($username, $password, $remember);
    }

    /**
     * Log out a user by removing the related session variables.
     *
     * @param   boolean $destroy    Completely destroy the session
     * @param   boolean $logoutAll  Remove all tokens for user
     * @return  boolean
     */
    public function logout($destroy = false, $logoutAll = false)
    {
        if ($destroy === true) {
            // Destroy the session completely
            $this->_session->destroy();
        } else {
            // Remove the user from the session
            $this->_session->delete($this->_config['session_key']);

            // Regenerate session_id
            $this->_session->regenerate();
        }

        // Double check
        return !$this->loggedIn();
    }

    /**
     * Check if there is an active session. Optionally allows checking for a
     * specific role.
     *
     * @param   string  $role   Role name
     * @return  mixed
     */
    public function loggedIn($role = null)
    {
        return ($this->getUser() !== null);
    }

    /**
     * Perform a hmac hash, using the configured method.
     *
     * @param   string  $str    String to hash
     * @return  string
     * @throw   \Bootphp\BootphpException
     */
    public function hash($str)
    {
        if (!$this->_config['hash_key'])
            throw new \Bootphp\BootphpException('A valid hash key must be set in your auth config.');

        return hash_hmac($this->_config['hash_method'], $str, $this->_config['hash_key']);
    }

    /**
     * Complete the login for a user by incrementing the logins and setting
     * session data: user_id, username, roles.
     *
     * @param   object  $user   User ORM object
     * @return  void
     */
    protected function completeLogin($user)
    {
        // Regenerate session_id
        $this->_session->regenerate();

        // Store username in session
        $this->_session->set($this->_config['session_key'], $user);

        return true;
    }

}
