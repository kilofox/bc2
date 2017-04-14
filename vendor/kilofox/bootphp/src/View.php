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
     * Template filename.
     *
     * @var     string
     */
    protected $template;

    /**
     * The name of the subfolder containing templates for this View.
     *
     * @var     string
     */
    protected $templatePath;

    /**
     * The name of the layout file to render the template inside of.
     *
     * @var     string
     */
    protected $layout;

    /**
     * The name of the layouts subfolder containing layouts for this View.
     *
     * @var     string
     */
    protected $layoutPath;

    /**
     * File extension.
     *
     * @var     string
     */
    protected $extension = 'html';

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
    public function __get($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        } else {
            throw new BootphpException('View variable is not set: ' . $key . '.');
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
     * Get/set path for template files.
     *
     * @param   string  $path   Path for template files
     * @return  string|View
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
     * Get/set path for layout files.
     *
     * @param   string  $path   Path for layout files
     * @return  string|View
     */
    public function layoutPath($path = null)
    {
        if ($path === null) {
            return $this->layoutPath;
        }

        $this->layoutPath = $path;

        return $this;
    }

    /**
     * Get/set the name of the template file to render.
     *
     * @param   string  $name   Template file name to set
     * @param   string  $ext    Template file extension to set
     * @return  string|View
     */
    public function template($name = null, $ext = null)
    {
        if ($name === null) {
            return $this->template;
        }

        $this->template = $name;
        $ext and $this->extension = $ext;

        return $this;
    }

    /**
     * Get/set the name of the layout file to render the template inside of.
     *
     * @param   string  $name   Layout file name to set
     * @return  string|View
     */
    public function layout($name = null)
    {
        if ($name === null) {
            return $this->layout;
        }

        $this->layout = $name;

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
    public function render()
    {
        if (empty($this->template)) {
            throw new BootphpException('You must set the file to use within your view before rendering.');
        }

        if (!is_file($file = $this->templatePath . $this->template . '.' . $this->extension)) {
            throw new BootphpException('The requested view `' . $this->template . '` could not be found.');
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

        $templateContent = $render($file, $this->data);

        if ($this->layout) {
            // New template for layout
            $layout = new self($this->layout, $this->data);

            // Set layout path if specified
            if ($this->layoutPath) {
                $layout->templatePath($this->layoutPath);
            }

            // Set main yield content block
            $layout->set('yield', $templateContent);

            // Get content
            $templateContent = $layout->render();
        }

        return $templateContent;
    }

}
