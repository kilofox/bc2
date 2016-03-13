<?php

namespace Bootphp;
/**
 * 响应类
 *
 * 包括响应主体、状态和头等。
 *
 * @package Bootphp
 * @author Tinsh
 */
class Response
{
	protected $_status = 200;
	protected $_content;
	protected $_cacheTime;
	protected $_encoding = 'UTF-8';
	protected $_contentType = 'text/html';
	protected $_protocol = 'HTTP/1.1';
	protected $_headers = [];
	/**
	 * 构造方法
	 */
	public function __construct($content = NULL, $status = 200)
	{
		// 组成响应对象
		$class = __CLASS__;
		if ( $content instanceof $class )
		{
			$this->_content = $content->content();
			$this->_status = $content->status();
		}
		else
		{
			$this->_content = $content;
			$this->_status = $status;
		}
		$this->_protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'http';
	}
	/**
	 * 设置或取得 HTTP 头
	 *
	 * @param	string	$type HTTP 头类型
	 * @param	string	$content 头内容与值
	 * @param boolean $replace 是否替换已存在的头
	 * @return	mixed
	 */
	public function header($type, $content = NULL, $replace = true)
	{
		if ( $content === NULL )
		{
			if ( isset($this->_headers[$type]) )
			{
				return $this->_headers[$type];
			}
			return false;
		}
		// 规范头部，确保大小写正确
		for( $tmp = explode('-', $type), $i = 0; $i < count($tmp); $i++ )
		{
			$tmp[$i] = ucfirst($tmp[$i]);
		}
		$type = implode('-', $tmp);
		if ( $type == 'Content-Type' )
		{
			if ( preg_match('/^(.*);\w*charset\w*=\w*(.*)/', $content, $matches) )
			{
				$this->_contentType = $matches[1];
				$this->_encoding = $matches[2];
			}
			else
			{
				$this->_contentType = $content;
			}
			return $this;
		}
		if ( $replace )
		{
			$this->_headers[$type] = $content;
		}
		else
		{
			$this->appendHeader($type, $content);
		}
		return $this;
	}
	/**
	 * 向头列表追加具有相同名称的头。
	 *
	 * @param	string	$type HTTP 头类型
	 * @param	string	$content 头内容与值
	 * @return	\Bootphp\Response
	 */
	protected function appendHeader($type, $content)
	{
		// 如果该头还没有设置，将它作为数组放在开始处。
		if ( !isset($this->_headers[$type]) )
		{
			$this->_headers[$type] = array($content);
			return $this;
		}
		// 如果该头不是内容与值的数组，将它转换为数组。
		if ( !is_array($this->_headers[$type]) )
		{
			$this->_headers[$type] = array($this->_headers[$type]);
		}
		$this->_headers[$type][] = $content;
		return $this;
	}
	/**
	 * 取得所有 HTTP 头的数组
	 *
	 * @return	array
	 */
	public function headers()
	{
		return $this->_headers;
	}
	/**
	 * 设置要返回的 HTTP 状态
	 *
	 * @param int $status HTTP 状态码
	 */
	public function status($status = NULL)
	{
		if ( $status === NULL )
		{
			return $this->_status;
		}
		$this->_status = $status;
		return $this;
	}
	/**
	 * 设置 HTTP 缓存时间
	 *
	 * @param mixed $time 布尔型 false, 整数时间, 或 strtotime() 的字符串
	 */
	public function cache($time = NULL)
	{
		if ( $time === NULL )
		{
			return $this->_cacheTime;
		}
		if ( $time instanceof \DateTime )
		{
			$time = $time->getTimestamp();
		}
		elseif ( is_string($time) )
		{
			$time = strtotime($time);
		}
		elseif ( is_int($time) )
		{
			// 给定的时间不是时间戳，假设将秒添加到当前时间
			if ( strlen($time) < 10 )
			{
				$time = time() + $time;
			}
		}
		if ( $time === false )
		{
			// 明确没有缓存
			$this->header('Cache-Control', 'no-cache, no-store');
		}
		else
		{
			// Max-age 是从现在开始的秒数
			$this->header('Cache-Control', 'public, max-age=' . ($time - time()));
			$this->header('Expires', gmdate("D, d M Y H:i:s", $time));
		}
		$this->_cacheTime = $time;
		return $this;
	}
	/**
	 * 设置 HTTP 要使用的编码
	 *
	 * @param	string	$encoding 要使用的字符编码
	 */
	public function encoding($encoding = NULL)
	{
		if ( $encoding === NULL )
		{
			return $this->_encoding;
		}
		$this->_encoding = $encoding;
		return $this;
	}
	/**
	 * 设置 HTTP 响应主体
	 *
	 * @param	string	$content 内容
	 */
	public function content($content = NULL)
	{
		if ( $content === NULL )
		{
			return $this->_content;
		}
		$this->_content = $content;
	}
	public function appendContent($content)
	{
		$this->_content .= $content;
	}
	/**
	 * 设置 HTTP 内容类型
	 *
	 * @param	string	$contentType 响应的内容类型
	 */
	public function contentType($contentType = NULL)
	{
		if ( $contentType == NULL )
		{
			return $this->_contentType;
		}
		$this->_contentType = $contentType;
		return $this;
	}
	/**
	 * 清除先前设置的 HTTP 头
	 */
	public function clearHeaders()
	{
		$this->_headers = [];
		return $this;
	}
	/**
	 * 清除先前设置的 HTTP 重定向
	 */
	public function clearRedirects()
	{
		if ( isset($this->_headers['Location']) )
		{
			unset($this->_headers['Location']);
		}
		return $this;
	}
	/**
	 * 看看是否有重定向设置
	 *
	 * @return	boolean
	 */
	public function hasRedirects()
	{
		return isset($this->_headers['Location']);
	}
	/**
	 * 重定向
	 *
	 * @param	string	$location URL
	 * @param int $status 重定向的 HTTP 状态码（3xx）
	 */
	public function redirect($location, $status = 302)
	{
		$this->status($status);
		$this->header('Location', $location);
		return $this;
	}
	/**
	 * 发送 HTTP 状态头
	 */
	protected function sendStatus()
	{
		// 发送 HTTP 头
		header($this->_protocol . ' ' . $this->_status . ' ' . $this->statusText($this->_status));
	}
	/**
	 * 根据状态码取得 HTTP 头响应文本
	 */
	public function statusText($statusCode)
	{
		$responses = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			226 => 'IM Used',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Reserved',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			426 => 'Upgrade Required',
			428 => 'Precondition Required',
			429 => 'Too Many Requests',
			431 => 'Request Header Fields Too Large',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			510 => 'Not Extended',
			511 => 'Network Authentication Required'
		);
		$statusText = false;
		if ( isset($responses[$statusCode]) )
		{
			$statusText = $responses[$statusCode];
		}
		return $statusText;
	}
	/**
	 * 发送设置的所有 HTTP 头
	 */
	public function sendHeaders()
	{
		if ( isset($this->_contentType) )
		{
			header('Content-Type: ' . $this->_contentType . "; charset=" . $this->_encoding);
		}
		// 发送所有头
		foreach( $this->_headers as $key => $value )
		{
			if ( $value === NULL )
			{
				continue;
			}
			if ( is_array($value) )
			{
				foreach( $value as $content )
				{
					header($key . ': ' . $content, false);
				}
				continue;
			}
			header($key . ": " . $value);
		}
	}
	/**
	 * 发送 HTTP 主体内容
	 */
	public function sendBody()
	{
		echo $this->_content;
	}
	/**
	 * 发送 HTTP 响应（头和主体）
	 */
	public function send()
	{
		echo $this; // 执行下面的 __toString 方法
	}
	/**
	 * 发送 HTTP 响应（字符串）
	 */
	public function __toString()
	{
		// 取得要返回的主体内容
		try
		{
			$content = (string)$this->content();
		}
		catch( \Exception $e )
		{
			$content = (string)$e;
			$this->status(500);
		}
		// 写并关闭会话
		if ( session_id() )
		{
			session_write_close();
		}
		// 发送头（如果还没有发送）
		if ( !headers_sent() )
		{
			$this->sendStatus();
			$this->sendHeaders();
		}
		return $content;
	}
}