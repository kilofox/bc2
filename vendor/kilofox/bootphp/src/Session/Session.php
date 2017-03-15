<?php

namespace Bootphp\Session;

use Bootphp\BootphpException;
use Bootphp\Encrypt;
use Bootphp\Core;

/**
 * Base session class.
 *
 * @package    Bootphp
 * @category   Session
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
abstract class Session
{
    /**
     * @var     string  Default session adapter
     */
    public static $default = 'native';

    /**
     * @var     array   Session instances
     */
    public static $instances = [];

    /**
     * Creates a singleton session of the given type. Some session types
     * (native, database) also support restarting a session by passing a
     * session id as the second parameter.
     *
     *     $session = Session::instance();
     *
     * [!!] [Session::write] will automatically be called when the request ends.
     *
     * @param   string  $type   Type of session (native, cookie, etc)
     * @param   string  $id     Session identifier
     * @return  Session
     * @uses    Core::$config
     */
    public static function instance($type = null, $id = null)
    {
        if ($type === null) {
            // Use the default type
            $type = self::$default;
        }

        if (!isset(self::$instances[$type])) {
            // Load the configuration for this type
            $config = Core::$config->load('session');

            // Set the session class name
            $class = 'Bootphp\\Session\\Session' . ucfirst($type);

            // Create a new session instance
            self::$instances[$type] = $session = new $class($config, $id);

            // Write the session at shutdown
            register_shutdown_function([$session, 'write']);
        }

        return self::$instances[$type];
    }

    /**
     * @var     string  Cookie name
     */
    protected $name = 'session';

    /**
     * @var     integer Cookie lifetime
     */
    protected $lifetime = 0;

    /**
     * @var     boolean Encrypt session data?
     */
    protected $encrypted = false;

    /**
     * @var     array   Session data
     */
    protected $data = [];

    /**
     * @var     boolean Session destroyed?
     */
    protected $destroyed = false;

    /**
     * Overloads the name, lifetime, and encrypted session settings.
     *
     * [!!] Sessions can only be created using the [Session::instance] method.
     *
     * @param   array   $config Configuration
     * @param   string  $id     Session id
     * @return  void
     * @uses    Session::read
     */
    public function __construct(array $config = null, $id = null)
    {
        if (isset($config['name'])) {
            // Cookie name to store the session id in
            $this->name = (string) $config['name'];
        }

        if (isset($config['lifetime'])) {
            // Cookie lifetime
            $this->lifetime = (int) $config['lifetime'];
        }

        if (isset($config['encrypted'])) {
            if ($config['encrypted'] === true) {
                // Use the default Encrypt instance
                $config['encrypted'] = 'default';
            }

            // Enable or disable encryption of data
            $this->encrypted = $config['encrypted'];
        }

        // Load the session
        $this->read($id);
    }

    /**
     * Session object is rendered to a serialized string. If encryption is
     * enabled, the session will be encrypted. If not, the output string will
     * be encoded.
     *
     *     echo $session;
     *
     * @return  string
     * @uses    Encrypt::encode
     */
    public function __toString()
    {
        // Serialize the data array
        $data = $this->_serialize($this->data);

        if ($this->encrypted) {
            // Encrypt the data using the default key
            $data = Encrypt::instance($this->encrypted)->encode($data);
        } else {
            // Encode the data
            $data = $this->_encode($data);
        }

        return $data;
    }

    /**
     * Returns the current session array. The returned array can also be
     * assigned by reference.
     *
     *     // Get a copy of the current session data
     *     $data = $session->as_array();
     *
     *     // Assign by reference for modification
     *     $data =& $session->as_array();
     *
     * @return  array
     */
    public function &as_array()
    {
        return $this->data;
    }

    /**
     * Get the current session id, if the session supports it.
     *
     *     $id = $session->id();
     *
     * [!!] Not all session types have ids.
     *
     * @return  string
     */
    public function id()
    {
        return null;
    }

    /**
     * Get the current session cookie name.
     *
     *     $name = $session->name();
     *
     * @return  string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get a variable from the session array.
     *
     *     $foo = $session->get('foo');
     *
     * @param   string  $key        Variable name
     * @param   mixed   $default    Default value to return
     * @return  mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

    /**
     * Get and delete a variable from the session array.
     *
     *     $bar = $session->get_once('bar');
     *
     * @param   string  $key        Variable name
     * @param   mixed   $default    Default value to return
     * @return  mixed
     */
    public function getOnce($key, $default = null)
    {
        $value = $this->get($key, $default);

        unset($this->data[$key]);

        return $value;
    }

    /**
     * Set a variable in the session array.
     *
     *     $session->set('foo', 'bar');
     *
     * @param   string  $key    Variable name
     * @param   mixed   $value  Value
     * @return  $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Set a variable by reference.
     *
     *     $session->bind('foo', $foo);
     *
     * @param   string  $key    Variable name
     * @param   mixed   $value  Referenced value
     * @return  $this
     */
    public function bind($key, &$value)
    {
        $this->data[$key] = &$value;

        return $this;
    }

    /**
     * Removes a variable in the session array.
     *
     *     $session->delete('foo');
     *
     * @param   string  $key    Variable name
     * @return  $this
     */
    public function delete($key)
    {
        $args = func_get_args();

        foreach ($args as $key) {
            unset($this->data[$key]);
        }

        return $this;
    }

    /**
     * Loads existing session data.
     *
     *     $session->read();
     *
     * @param   string  $id     Session id
     * @return  void
     */
    public function read($id = null)
    {
        $data = null;

       // try {
            if (is_string($data = $this->_read($id))) {
                if ($this->encrypted) {
                    // Decrypt the data using the default key
                    $data = Encrypt::instance($this->encrypted)->decode($data);
                } else {
                    // Decode the data
                    $data = $this->_decode($data);
                }

                // Unserialize the data
                $data = $this->_unserialize($data);
            } else {
                // Ignore these, session is valid, likely no data though.
            }
       // } catch (\Exception $e) {
            // Error reading the session, usually a corrupt session.
        //    throw new BootphpException('Error reading session data.', 1);
        //}

        if (is_array($data)) {
            // Load the data locally
            $this->data = $data;
        }
    }

    /**
     * Generates a new session id and returns it.
     *
     *     $id = $session->regenerate();
     *
     * @return  string
     */
    public function regenerate()
    {
        return $this->_regenerate();
    }

    /**
     * Sets the last_active timestamp and saves the session.
     *
     *     $session->write();
     *
     * [!!] Any errors that occur during session writing will be logged, but not
     * displayed, because sessions are written after output has been sent.
     *
     * @return  boolean
     * @uses    Core::$log
     */
    public function write()
    {
        if (headers_sent() || $this->destroyed) {
            // Session cannot be written when the headers are sent or when the session has been destroyed
            return false;
        }

        // Set the last active timestamp
        $this->data['last_active'] = time();

        try {
            return $this->_write();
        } catch (\Exception $e) {
            // Log and ignore all errors when a write fails
            Core::$log->add(\Bootphp\Log::ERROR, BootphpException::text($e))->write();

            return false;
        }
    }

    /**
     * Completely destroy the current session.
     *
     *     $success = $session->destroy();
     *
     * @return  boolean
     */
    public function destroy()
    {
        if ($this->destroyed === false) {
            if ($this->destroyed = $this->_destroy()) {
                // The session has been destroyed, clear all data
                $this->data = [];
            }
        }

        return $this->destroyed;
    }

    /**
     * Restart the session.
     *
     *     $success = $session->restart();
     *
     * @return  boolean
     */
    public function restart()
    {
        if ($this->destroyed === false) {
            // Wipe out the current session.
            $this->destroy();
        }

        // Allow the new session to be saved
        $this->destroyed = false;

        return $this->_restart();
    }

    /**
     * Serializes the session data.
     *
     * @param   array  $data    Data
     * @return  string
     */
    protected function _serialize($data)
    {
        return serialize($data);
    }

    /**
     * Unserializes the session data.
     *
     * @param   string  $data   Data
     * @return  array
     */
    protected function _unserialize($data)
    {
        return unserialize($data);
    }

    /**
     * Encodes the session data using [base64_encode].
     *
     * @param   string  $data   Data
     * @return  string
     */
    protected function _encode($data)
    {
        return base64_encode($data);
    }

    /**
     * Decodes the session data using [base64_decode].
     *
     * @param   string  $data   Data
     * @return  string
     */
    protected function _decode($data)
    {
        return base64_decode($data);
    }

    /**
     * Loads the raw session data string and returns it.
     *
     * @param   string  $id     Session id
     * @return  string
     */
    abstract protected function _read($id = null);
    /**
     * Generate a new session id and return it.
     *
     * @return  string
     */
    abstract protected function _regenerate();
    /**
     * Writes the current session.
     *
     * @return  boolean
     */
    abstract protected function _write();
    /**
     * Destroys the current session.
     *
     * @return  boolean
     */
    abstract protected function _destroy();
    /**
     * Restarts the current session.
     *
     * @return  boolean
     */
    abstract protected function _restart();
}
