<?php

namespace Bootphp;
use Bootphp\Response;
use Bootphp\Exception\ExceptionHandler;
/**
 * 处理与显示视图模板的视图模板类
 *
 * @package	Bootphp
 * @author		Tinsh
 */
class View extends Response
{
	// 静态配置
	protected static $_config = array(
		'default_format' => 'html',
		'default_extension' => 'php',
		'path' => NULL,
		'path_layouts' => NULL,
		'auto_layout' => false
	);
	// 模板特定的东西
	protected $_file;
	protected $_fileFormat;
	protected $_vars = [];
	protected $_path;
	protected $_layout;
	protected $_layoutPath;
	protected $_templateContent;
	protected $_exists;
	/**
	 * 构造方法
	 *
	 * @param	$module	string	配置参数
	 */
	public function __construct()
	{
		// 自动布局
		if ( self::$_config['auto_layout'] )
		{
			$this->layout(self::$_config['auto_layout']);
		}
	}
	/**
	 * 配置主模板目录等
	 */
	public static function config($cfg = NULL)
	{
		// 获取
		if ( $cfg === NULL )
		{
			return self::$_config;
		}
		// 设置
		self::$_config = array_merge(self::$_config, $cfg);
	}
	/**
	 * 获取或设置布局
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
	 * 获取单个模板变量
	 *
	 * 抑制变量未找到时的 notice 错误，使模板语法变量更简单
	 *
	 * @param	string	键
	 * @return	mixed 值（如果找到了键）
	 * @return	NULL （如果键没找到）
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
	 * 分配模板变量
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
	 * 获取所有模板变量
	 *
	 * @return	array
	 */
	public function vars()
	{
		return $this->_vars;
	}
	/**
	 * 获取或设置查找模板的路径
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
	 * 获取设置的模板名
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
	 * 返回带有格式和扩展的完整模板文件名
	 *
	 * @param OPTIONAL $template string 要返回的完整文件格式的模板名
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
	 * 转义 HTML 实体
	 * 用于防止 XSS 攻击
	 */
	public function h($str)
	{
		return htmlentities($str, ENT_NOQUOTES);
	}
	/**
	 * 加载并返回局部视图对象
	 *
	 * @param	string	$template 使用的模板文件
	 * @param array $vars 传递给局部的变量
	 * @return	Bootphp\View\Template
	 */
	public function partial($template, array $vars = [])
	{
		$tpl = new static($template, $vars);
		return $tpl->layout(false);
	}
	/**
	 * 验证模板是否存在，并抛出异常（可选）
	 *
	 * @param	boolean $throwException 抛出异常
	 * @throws	Bootphp\View\Exception\TemplateMissing
	 * @return	boolean
	 */
	public function exists()
	{
		// 避免多次 file_exists 检查
		if ( $this->_exists )
		{
			return true;
		}
		$vpath = $this->path();
		$template = $this->fileName();
		$vfile = $vpath . $template;
		// 确保设置了路径
		if ( !$vpath )
		{
			throw new ExceptionHandler('未设置模板基路径！用 $view->path() 来给模板文件设置基路径！');
		}
		// 确保模板文件存在
		if ( !file_exists($vfile) )
		{
			throw new ExceptionHandler('模板文件 \'' . $template . '\' 不存在。路径：' . $vpath);
		}
		$this->_exists = true;
		return true;
	}
	/**
	 * 清除上一次渲染并缓存的内容
	 *
	 * @return	self
	 */
	public function clearCachedContent()
	{
		$this->_templateContent = NULL;
		return $this;
	}
	/**
	 * 读取模板文件的内容字符串并返回之
	 *
	 * @return	string
	 */
	public function render()
	{
		if ( !$this->_templateContent )
		{
			$this->exists();
			$vfile = $this->path() . $this->fileName();
			// 使用闭包隔离起来
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
					// 删除输出缓冲
					ob_end_clean();
					// 重新抛出异常
					throw $e;
				}
				return ob_get_clean();
			};
			$templateContent = $render($vfile);
			$templateContent = trim($templateContent);
			// 在布局中包装模板内容
			if ( $this->layout() )
			{
				// 确保布局没有递归渲染
				self::$_config['auto_layout'] = false;
				// 为布局实例化模板
				$layout = new self();
				$layout->file($this->layout());
				// 如果指定了布局路径，就设置一下
				$layout->path($this->_layoutPath !== NULL ? $this->_layoutPath : self::$_config['path_layouts']);
				// 将所有本类变量传递给布局
				$layout->set($this->_vars);
				// 设置主内容块
				$layout->set(['yield' => $templateContent]);
				// 取得内容
				$templateContent = $layout->render();
			}
			$this->_templateContent = $templateContent;
		}
		return $this->_templateContent;
	}
}