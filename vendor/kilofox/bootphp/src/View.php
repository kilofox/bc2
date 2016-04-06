<?php

namespace Bootphp;
use Bootphp\Response;
use Bootphp\Exception\ExceptionHandler;
/**
 * View template class that will display and handle view templates.
 *
 * @package	Bootphp
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class View extends Response
{
	// Static config setup for usage
	protected static $_config = array(
		'default_format' => 'html',
		'default_extension' => 'php',
		'path' => NULL,
		'path_layouts' => NULL,
		'auto_layout' => false
	);
	// Template specific stuff
	protected $_file;
	protected $_fileFormat;
	protected $_vars = [];
	protected $_path;
	protected $_layout;
	protected $_layoutPath;
	protected $_templateContent;
	protected $_exists;
	/**
	 * Constructor method.
	 */
	public function __construct()
	{
		// Auto layout
		if ( self::$_config['auto_layout'] )
		{
			$this->layout(self::$_config['auto_layout']);
		}
	}
	/**
	 * Config setup for main templates directory, etc.
	 */
	public static function config($cfg = NULL)
	{
		// Getter
		if ( $cfg === NULL )
		{
			return self::$_config;
		}

		// Setter
		self::$_config = array_merge(self::$_config, $cfg);
	}
	/**
	 * Layout template getter/setter
	 */
	public function layout($layout = NULL)
	{
		if ( $layout === NULL )
		{
			return $this->_layout;
		}

		$this->_layout = $layout;
		return $this;
	}
	/**
	 * Gets a view variable.
	 *
	 * Surpress notice errors for variables not found to
	 * help make template syntax for variables simpler.
	 *
	 * @param	string  key
	 * @return	mixed	value if the key is found
	 * @return	null	if key is not found
	 */
	public function get($var, $default = NULL)
	{
		if ( isset($this->_vars[$var]) )
		{
			return $this->_vars[$var];
		}
		return $default;
	}
	/**
	 * Assign template variables.
	 *
	 * 	@param array 模板变量数组
	 */
	public function set($vars)
	{
		if ( is_array($vars) )
		{
			foreach( $vars as $k => $v )
			{
				if ( !empty($k) )
				{
					$this->_vars[$k] = $v;
				}
			}
		}
		return $this;
	}
	/**
	 * Get template variables.
	 *
	 * @return	array
	 */
	public function vars()
	{
		return $this->_vars;
	}
	/**
	 * Get/Set path to look in for templates.
	 */
	public function path($path = NULL)
	{
		if ( $path === NULL )
		{
			return $this->_path;
		}
		else
		{
			$this->_path = $path;
			$this->_exists = false;
			return $this;
		}
	}
	/**
	 * 获取或设置查找模板的路径
	 */
	public function layoutPath($path = NULL)
	{
		if ( $path === NULL )
		{
			return $this->_layoutPath;
		}
		else
		{
			$this->_layoutPath = $path;
			$this->_exists = false;
			return $this;
		}
	}
	/**
	 * Get template name that was set
	 *
	 * @return	string
	 */
	public function file($view = NULL, $format = NULL)
	{
		if ( $view === NULL )
		{
			return $this->_file;
		}
		else
		{
			$this->_file = $view;
			$this->_fileFormat = ($format) ? $format : self::$_config['default_format'];
			$this->_exists = false;
			return $this;
		}
	}
	/**
	 * Returns full template filename with format and extension.
	 *
	 * @param OPTIONAL $template string Name of the template to return full file format
	 * @return	string
	 */
	public function fileName($template = NULL)
	{
		if ( $template === NULL )
		{
			$template = $this->file();
		}
		return $template . '.' . $this->format() . '.' . self::$_config['default_extension'];
	}
	/**
	 * 获取或设置使用的格式
	 * 模板会使用到：<template>.<format>.<extension>
	 * 例如：index.html.php
	 *
	 * @param $format string html 或 xml
	 */
	public function format($format = NULL)
	{
		if ( $format === NULL )
		{
			return $this->_fileFormat;
		}
		else
		{
			$this->_fileFormat = $format;
			return $this;
		}
	}
	/**
	 * Escapes HTML entities.
	 * Use to prevent XSS attacks.
	 */
	public function h($str)
	{
		return htmlentities($str, ENT_NOQUOTES);
	}
	/**
	 * Verify template exists and optionally throw an exception if not.
	 *
	 * @param	boolean $throwException Throw an exception
	 * @throws	Bootphp\View\Exception\TemplateMissing
	 * @return	boolean
	 */
	public function exists()
	{
		// Avoid multiple file_exists checks
		if ( $this->_exists )
		{
			return true;
		}

		$vpath = $this->path();
		$template = $this->fileName();
		$vfile = $vpath . $template;

		// Ensure path has been set
		if ( !$vpath )
		{
			throw new ExceptionHandler('Base template path is not set! Use \'$view->path()\' to set root path to template files!');
		}

		// Ensure template file exists
		if ( !file_exists($vfile) )
		{
			throw new ExceptionHandler('The template file \'' . $template . '\' does not exist. Path: ' . $vpath);
		}

		$this->_exists = true;

		return true;
	}
	/**
	 * Read template file into content string and return it.
	 *
	 * @return	string
	 */
	public function render()
	{
		if ( !$this->_templateContent )
		{
			$this->exists();

			$vfile = $this->path() . $this->fileName();

			// Use closure to get isolated scope
			$view = $this;
			$vars = $this->vars();
			$render = function($templateFile) use($view, $vars){
				extract($vars);
				ob_start();

				try
				{
					require $templateFile;
				}
				catch( \Exception $e )
				{
					// Delete the output buffer
					ob_end_clean();

					// Re-throw the exception
					throw $e;
				}

				// Get the captured output and close the buffer
				return ob_get_clean();
			};
			$templateContent = $render($vfile);
			$templateContent = trim($templateContent);

			// Wrap template content in layout
			if ( $this->_layout )
			{
				// Ensure layout doesn't get rendered recursively
				self::$_config['auto_layout'] = false;

				// New template for layout
				$layout = new self();
				$layout->file($this->layout());

				// Set layout path if specified
				$layout->path($this->_layoutPath !== NULL ? $this->_layoutPath : self::$_config['path_layouts']);

				// Pass all locally set variables to layout
				$layout->set($this->_vars);

				// Set main yield content block
				$layout->set(['yield' => $templateContent]);

				// Get content
				$templateContent = $layout->render();
			}

			$this->_templateContent = $templateContent;
		}

		return $this->_templateContent;
	}
}