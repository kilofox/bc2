<?php

namespace Bootphp;

use Bootphp\BootphpException;

/**
 * Acts as an object wrapper for HTML pages with embedded PHP, called "views".
 * Variables can be assigned with the view object.
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
     * Layout filename.
     *
     * @var     string
     */
    protected $layout;

    /**
     * Template filename.
     *
     * @var     string
     */
    protected $file;

    /**
     * Template file format.
     *
     * @var     string
     */
    protected $fileFormat = 'php';

    /**
     * Template path.
     *
     * @var     string
     */
    protected $path;

    /**
     * Array of local variables.
     *
     * @var     array
     */
    protected $data = [];

    /**
     * Sets the initial view filename and local data.
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
            $this->template($file);
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
     * @param   string  $key    Variable name
     * @param   mixed   $value  Value
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
     * Gets/Sets path to look in for templates.
     *
     * @param   string  $path   Template path
     * @return  $this
     */
    public function path($path = null)
    {
        if ($path === null) {
            return $this->path;
        }

        $this->path = $path;

        return $this;
    }

    /**
     * Gets/Sets the template filename.
     *
     *     $view->template($file);
     *
     * @param   string  $file   Template filename
     * @return  $this
     */
    public function template($file = null, $format = null)
    {
        if ($file === null) {
            return $this->file;
        }

        $this->file = $file;
        $format and $this->fileFormat = $format;

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
     * [!!] Note: When setting with using Traversable object we're not attaching
     * the whole object to the view, i.e. the object's standard properties will
     * not be available in the view context.
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
     * @return  string
     * @throws  BootphpException
     */
    public function render($file = null)
    {
        if (empty($this->file)) {
            throw new BootphpException('You must set the file to use within your view before rendering');
        }

        $render = function($viewFilename, $viewData) {
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
        };

        $templateContent = $render($this->path . $this->file . '.' . $this->fileFormat, $this->data);

        if ($this->layout) {
            // Ensure layout doesn't get rendered recursively
            self::$_config['auto_layout'] = false;

            // New template for layout
            $layout = new self($this->layout);

            // Pass all locally set variables to layout
            $layout->set($this->_vars);

            // Set main yield content block
            $layout->set('yield', $templateContent);

            // Get content
            $templateContent = $layout->content($parsePHP);
        }

        return $templateContent;
    }

}
