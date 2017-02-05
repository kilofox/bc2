<?php

namespace Bootphp;

use Bootphp\BootphpException;

/**
 * Acts as an object wrapper for HTML pages with embedded PHP, called "views".
 * Variables can be assigned with the view object and referenced locally within
 * the view.
 *
 * @package    Bootphp
 * @category   Base
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class View
{
    /**
     * Static config setup for usage.
     *
     * @var array
     */
    protected static $config = [
        'defaultFormat' => 'html',
        'defaultExtension' => 'php',
        'path' => null,
        'layoutPath' => null
    ];

    /**
     * Config setup for main templates directory, etc.
     */
    public static function config($cfg = null)
    {
        // Getter
        if ($cfg === null) {
            return self::$config;
        }

        // Setter
        self::$config = array_merge(self::$config, (array) $cfg);
    }

    /**
     * Captures the output that is generated when a view is included.
     * The view data will be extracted to make local variables. This method
     * is static to prevent object scope resolution.
     *
     *     $output = View::capture($file, $data);
     *
     * @param   string  $viewFilename   Filename
     * @param   array   $viewData       Variables
     * @return  string
     * @throws  Exception
     */
    protected static function capture($viewFilename, array $viewData)
    {
        // Import the view variables to local namespace
        extract($viewData, EXTR_SKIP);

        // Capture the view output
        ob_start();

        try {
            // Load the view within the current scope
            include $viewFilename;
        } catch (\Exception $e) {
            // Delete the output buffer
            ob_end_clean();

            // Re-throw the exception
            throw $e;
        }

        // Get the captured output and close the buffer
        return ob_get_clean();
    }

    /**
     * Layout filename.
     *
     * @var string
     */
    protected $layout;

    /**
     * Template filename.
     *
     * @var string
     */
    protected $template;

    /**
     * Array of local variables.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Sets the initial view filename and local data. Views should almost
     * always only be created using [View::factory].
     *
     *     $view = new View($file);
     *
     * @param   string  $file   View filename
     * @param   array   $data   Array of values
     * @uses    View::template
     */
    public function __construct($file = null, array $data = null)
    {
        if ($file !== null) {
            $this->template($file, false);
        }

        if ($data !== null) {
            // Add the values to the current data
            $this->data = $data + $this->data;
        }
    }

    /**
     * Magic method, searches for the given variable and returns its value.
     * Local variables will be returned before global variables.
     *
     *     $value = $view->foo;
     *
     * [!!] If the variable has not yet been set, an exception will be thrown.
     *
     * @param   string  $key    Variable name
     * @return  mixed
     * @throws  BootphpException
     */
    public function & __get($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        } else {
            throw new BootphpException('View variable is not set: ' . $key);
        }
    }

    /**
     * Magic method, calls [View::set] with the same parameters.
     *
     *     $view->foo = 'something';
     *
     * @param   string  $key    variable name
     * @param   mixed   $value  value
     * @return  void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Magic method, determines if a variable is set.
     *
     *     isset($view->foo);
     *
     * [!!] `null` variables are not considered to be set by [isset](http://php.net/isset).
     *
     * @param   string  $key    Variable name
     * @return  boolean
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Magic method, unsets a given variable.
     *
     *     unset($view->foo);
     *
     * @param   string  $key    Variable name
     * @return  void
     */
    public function __unset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Magic method, returns the output of [View::render].
     *
     * @return  string
     * @uses    View::render
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            /**
             * Display the exception message.
             *
             * We use this method here because it's impossible to throw an
             * exception from __toString().
             */
            $errorResponse = BootphpException::handler($e);

            return $errorResponse->body();
        }
    }

    /**
     * Layout template getter/setter.
     *
     * @param   $layout
     * @return  $this
     */
    public function layout($layout = null)
    {
        if ($layout === null) {
            return $this->layout;
        }

        $this->layout = $layout;

        return $this;
    }

    /**
     * Sets the template filename.
     *
     *     $view->template($file);
     *
     * @param   string  $file   Template filename
     * @return  $this
     */
    public function template($file = null)
    {
        if ($file === null) {
            return $this->template;
        }

        $this->template = $file;

        return $this;
    }

    /**
     * Sets the template path.
     *
     * @param   string  $path   Template path
     * @return  $this
     * @throws  BootphpException
     */
    public function templatePath($path = null)
    {
        if ($path === null) {
            return $this->templatePath;
        }

        $this->templatePath = $path;

        return $this;
    }

    /**
     * Assigns a variable by name. Assigned values will be available as a
     * variable within the view file:
     *
     *     // This value can be accessed as $foo within the view
     *     $view->set('foo', 'my value');
     *
     * You can also use an array or Traversable object to set several values at once:
     *
     *     // Create the values $food and $beverage in the view
     *     $view->set(array('food' => 'bread', 'beverage' => 'water'));
     *
     * [!!] Note: When setting with using Traversable object we're not attaching the whole object to the view,
     * i.e. the object's standard properties will not be available in the view context.
     *
     * @param   string|array|Traversable    $key    Variable name or an array of variables
     * @param   mixed                       $value  Value
     * @return  $this
     */
    public function set($key, $value = null)
    {
        if (is_array($key) || $key instanceof Traversable) {
            foreach ($key as $name => $value) {
                $this->data[$name] = $value;
            }
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Renders the view object to a string. Global and local data are merged and
     * extracted to create local variables within the view file.
     *
     *     $output = $view->render();
     *
     * [!!] Global variables with the same key name as local variables will be
     * overwritten by the local variable.
     *
     * @param   string  $file   View filename
     * @return  string
     * @throws  BootphpException
     * @uses    View::capture
     */
    public function render($file = null)
    {
        if ($file !== null) {
            $this->template($file);
        }

        if (empty($this->template)) {
            throw new BootphpException('You must set the file to use within your view before rendering');
        }

        // Combine local and global data and capture the output
        return self::capture($this->template, $this->data);
    }

}
