<?php

namespace Bootphp\Auth\Driver;

use Bootphp\ORM\ORM;
use Bootphp\Cookie;

/**
 * ORM Auth driver.
 *
 * @package    Bootphp/Auth
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class ORMDriver extends \Bootphp\Auth\Auth
{
    /**
     * Checks if a session is active.
     *
     * @param   mixed   $role   Role name string, role ORM object, or array with role names
     * @return  boolean
     */
    public function loggedIn($role = null)
    {
        // Get the user from the session
        $user = $this->getUser();

        if (!$user)
            return false;

        if ($user instanceof UserModel and $user->loaded()) {
            // If we don't have a roll no further checking is needed
            if (!$role)
                return true;

            if (is_array($role)) {
                // Get all the roles
                $roles = ORM::factory('Role')->where('name', 'IN', $role)->findAll()->asArray(null, 'id');

                // Make sure all the roles are valid ones
                if (count($roles) !== count($role))
                    return false;
            } else {
                if (!is_object($role)) {
                    // Load the role
                    $roles = ORM::factory('Role')->where('name', '=', $role)->find();

                    if (!$roles->loaded())
                        return false;
                }
                else {
                    $roles = $role;
                }
            }

            return $user->has('roles', $roles);
        }
    }

    /**
     * Logs a user in.
     *
     * @param   string  $username
     * @param   string  $password
     * @param   boolean $remember   Enable autologin
     * @return  boolean
     */
    protected function _login($user, $password, $remember)
    {
        if (!is_object($user)) {
            $username = $user;

            // Load the user
            $user = ORM::factory('User', 'Bootphp\\Auth\\Driver\\ORM\\Model\\');
            $user->where($user->uniqueKey($username), '=', $username)->find();
        }

        if (is_string($password)) {
            // Create a hashed password
            $password = $this->hash($password);
        }

        // If the passwords match, perform a login
        if ($user->password === $password) {
            if ($remember === true) {
                // Token data
                $data = [
                    'user_id' => $user->pk(),
                    'expires' => time() + $this->_config['lifetime'],
                    'user_agent' => sha1(Request::$userAgent),
                ];

                // Create a new autologin token
                $token = ORM::factory('User_Token')->values($data)->create();

                // Set the autologin cookie
                Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
            }

            // Finish the login
            $this->completeLogin($user);

            return true;
        }

        // Login failed
        return false;
    }

    /**
     * Forces a user to be logged in, without specifying a password.
     *
     * @param   mixed   $user                   Username string, or user ORM object
     * @param   boolean $markSessionAsForced    Mark the session as forced
     * @return  boolean
     */
    public function forceLogin($user, $markSessionAsForced = false)
    {
        if (!is_object($user)) {
            $username = $user;

            // Load the user
            $user = ORM::factory('User');
            $user->where($user->uniqueKey($username), '=', $username)->find();
        }

        if ($markSessionAsForced === true) {
            // Mark the session as forced, to prevent users from changing account information
            $this->session->set('auth_forced', true);
        }

        // Run the standard completion
        $this->completeLogin($user);
    }

    /**
     * Logs a user in, based on the authautologin cookie.
     *
     * @return  mixed
     */
    public function autoLogin()
    {
        if ($token = Cookie::get('authautologin')) {
            // Load the token and user
            $token = ORM::factory('User_Token', ['token' => $token]);

            if ($token->loaded() && $token->user->loaded()) {
                if ($token->user_agent === sha1(Request::$userAgent)) {
                    // Save the token to create a new unique token
                    $token->save();

                    // Set the new token
                    Cookie::set('authautologin', $token->token, $token->expires - time());

                    // Complete the login with the found data
                    $this->completeLogin($token->user);

                    // Automatic login was successful
                    return $token->user;
                }

                // Token is invalid
                $token->delete();
            }
        }

        return false;
    }

    /**
     * Gets the currently logged in user from the session (with auto_login check).
     * Returns $default if no user is currently logged in.
     *
     * @param   mixed   $default    To return in case user isn't logged in
     * @return  mixed
     */
    public function getUser($default = null)
    {
        $user = parent::getUser($default);

        if ($user === $default) {
            // check for "remembered" login
            if (($user = $this->autoLogin()) === false)
                return $default;
        }

        return $user;
    }

    /**
     * Log a user out and remove any autologin cookies.
     *
     * @param   boolean $destroy    Completely destroy the session
     * @param   boolean $logoutAll  Remove all tokens for user
     * @return  boolean
     */
    public function logout($destroy = false, $logoutAll = false)
    {
        // Set by force_login()
        $this->session->delete('auth_forced');

        if ($token = Cookie::get('authautologin')) {
            // Delete the autologin cookie to prevent re-login
            Cookie::delete('authautologin');

            // Clear the autologin token from the database
            $token = ORM::factory('User_Token', ['token' => $token]);

            if ($token->loaded() && $logoutAll) {
                // Delete all user tokens. This isn't the most elegant solution but does the job
                $tokens = ORM::factory('User_Token')->where('user_id', '=', $token->user_id)->findAll();

                foreach ($tokens as $_token) {
                    $_token->delete();
                }
            } elseif ($token->loaded()) {
                $token->delete();
            }
        }

        return parent::logout($destroy);
    }

    /**
     * Get the stored password for a username.
     *
     * @param   mixed   $user   Username string, or user ORM object
     * @return  string
     */
    public function password($user)
    {
        if (!is_object($user)) {
            $username = $user;

            // Load the user
            $user = ORM::factory('User');
            $user->where($user->uniqueKey($username), '=', $username)->find();
        }

        return $user->password;
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
        $user->completeLogin();

        return parent::completeLogin($user);
    }

    /**
     * Compare password with original (hashed). Works for current (logged in) user.
     *
     * @param   string  $password
     * @return  boolean
     */
    public function checkPassword($password)
    {
        $user = $this->getUser();

        if (!$user)
            return false;

        return $this->hash($password) === $user->password;
    }

}
