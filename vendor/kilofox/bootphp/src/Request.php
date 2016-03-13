<?php

namespace Bootphp;
/**
 * 请求类
 *
 * 包装请求变量，并提供有用的辅助方法。
 *
 * @package Bootphp
 * @author Tinsh
 */
class Request
{
	// 请求 URL
	protected $_method;
	protected $_url;
	protected $_format = NULL;
	// 请求参数
	protected $_headers = [];
	protected $_params = [];
	protected $_postParams = [];
	protected $_queryParams = [];
	protected $_accept;
	// 其它
	protected $_raw;
	// MIME 类型
	protected $_mimeTypes = array(
		'txt' => 'text/plain',
		'html' => 'text/html',
		'xhtml' => 'application/xhtml+xml',
		'xml' => 'application/xml',
		'css' => 'text/css',
		'js' => 'application/javascript',
		'json' => 'application/json',
		'csv' => 'text/csv',
		// 图像
		'png' => 'image/png',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'gif' => 'image/gif',
		'bmp' => 'image/bmp',
		'ico' => 'image/vnd.microsoft.icon',
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		// 归档
		'zip' => 'application/zip',
		'rar' => 'application/x-rar-compressed',
		// Adobe
		'pdf' => 'application/pdf'
	);
	/**
	 * 设置请求对象
	 *
	 * @param	string	HTTP 方式
	 * @param	string	请求 URI
	 * @param	array	请求变量
	 * @param	array	HTTP 头
	 */
	public function __construct($method = NULL, $url = NULL, array $params = [], array $headers = [], $rawBody = NULL)
	{
		$this->_postParams = $_POST;
		$this->_queryParams = $_GET;
		// 设置方式
		if ( $method !== NULL )
		{
			$this->_method = $method;
		}
		// 设置 URL
		if ( $url !== NULL )
		{
			$this->url($url);
		}
		// 设置参数（如果给定）
		if ( !empty($params) )
		{
			if ( $method === 'POST' )
			{
				$this->_postParams = $params;
			}
			elseif ( $method === 'GET' )
			{
				$this->_queryParams = $params;
			}
			else
			{
				$this->_params = $params;
			}
		}
		// 设置头
		$this->_headers = $headers;
		if ( empty($this->_headers) )
		{
			$this->_headers = $_SERVER;
		}
		// 设置原始请求主体
		if ( $rawBody !== NULL )
		{
			$this->_raw = $rawBody;
		}
		// 解析 Accept 头，看一下请求的是什么格式
		$accept = $this->accept();
		if ( !empty($accept) )
		{
			// 用第一个 accept 类型作为默认格式
			$firstType = array_shift($accept);
			$formatAny = in_array($firstType, array(NULL, '*/*', '', '*'), true);
			$this->format($formatAny ? NULL : $firstType);
		}
		// 正确处理 PATCH、 PUT、和 DELETE 请求，POST 请求不带参数且 post 主体
		if ( $this->isPatch() || $this->isPut() || $this->isDelete() || ($this->isPost() && empty($_POST) && empty($this->_params)) )
		{
			// 获得并解析原始请求主体
			$raw = $this->raw();
			// 处理 JSON 请求
			if ( strpos($this->header('Content-Type'), 'json') !== false )
			{
				// 清理不良 JSON，以便我们能够解析它
				$raw = stripslashes(str_replace(array('\r\n', '\n', '\r'), '', $raw));
				$json = json_decode($raw, true);
				if ( $json )
				{
					$params = $json;
				}
			}
			else
			{
				// 处理其它请求（可以是查询参数）
				parse_str($raw, $params);
				// 检查并确保原始主体被正确解码。如果不是，整个原始字符串将会成为结果数组的一个键，而不是敏感的东西，比如……，不知道，也许是布尔值 false。
				// 此外，parse_str 会把空格和点转换为下划线，我们要留意这一点。
				$raw_transformed = str_replace(array(' ', '.'), '_', $raw);
				if ( isset($params[$raw_transformed]) )
				{
					$params = [];
					$json = json_decode($raw, true);
					if ( $json !== false )
					{
						$params = $json;
					}
				}
			}
			// 如果解码成功，设置参数
			if ( $params )
			{
				// 真正的 POST 和 GET 参数优先于原始主体
				if ( $method === 'POST' )
				{
					$this->_postParams = array_merge($params, $this->_postParams);
				}
				elseif ( $method === 'GET' )
				{
					$this->_queryParams = array_merge($params, $this->_queryParams);
				}
				else
				{
					$this->setParams($params);
				}
			}
		}
	}
	/**
	 * 返回请求的 URL 路径
	 *
	 * 用于 HTTP(S) 请求和使用 -u 标志的 CLI 请求的 URL 调度
	 *
	 * @return	string	请求的 URL 路径分段
	 */
	public function url($url = NULL)
	{
		if ( $this->_url === NULL )
		{
			if ( $url !== NULL )
			{
				// 用给定值设置 url
				$requestUrl = $url;
			}
			else
			{
				// 自动检测 url
				if ( $this->isCli() )
				{
					// CLI 请求
					$cliArgs = getopt("u:");
					$requestUrl = isset($cliArgs['u']) ? $cliArgs['u'] : '/';
					$qs = parse_url($requestUrl, PHP_URL_QUERY);
					$cliRequestParams = [];
					parse_str($qs, $cliRequestParams);
					// 将解析的查询参数设回请求对象
					$this->setParams($cliRequestParams);
				}
				else
				{
					// HTTP 请求
					$requestUrl = $this->query('u') ? $this->query('u') : '/';
				}
			}
			// 设置 requestUrl 并移除查询串（如果有的话），以便路由能够按预期解析它
			if ( $qsPos = strpos($requestUrl, '?') )
			{
				$fullUrl = $requestUrl;
				$requestUrl = substr($requestUrl, 0, $qsPos);
				parse_str(substr($fullUrl, $qsPos + 1), $urlRequestParams);
				// 将解析的查询参数设回请求对象
				$this->setParams($urlRequestParams);
			}
			$this->_url = $requestUrl;
		}
		return $this->_url;
	}
	/**
	 * 访问值包含在作为公共成员的超全局变量中
	 * 优先顺序：1. GET, 2. POST, 3. COOKIE, 4. SERVER, 5. ENV
	 *
	 * @see http://msdn.microsoft.com/en-us/library/system.web.httprequest.item.aspx
	 * @param	string	$key
	 * @return	mixed
	 */
	public function get($key, $default = NULL)
	{
		switch( true )
		{
			case isset($this->_params[$key]):
				$value = $this->_params[$key];
				break;
			case isset($this->_queryParams[$key]):
				$value = $this->_queryParams[$key];
				break;
			case isset($this->_postParams[$key]):
				$value = $this->_postParams[$key];
				break;
			case isset($_COOKIE[$key]):
				$value = $_COOKIE[$key];
				break;
			case isset($_SERVER[$key]):
				$value = $_SERVER[$key];
				break;
			case isset($_ENV[$key]):
				$value = $_ENV[$key];
				break;
			default:
				$value = $default;
		}
		// 键未找到，使用默认
		if ( $value === $default )
		{
			// 检查点分隔符（方便访问嵌套数组的值）
			if ( strpos($key, '.') !== false )
			{
				// 取得所有点分隔的键分组
				$keyParts = explode('.', $key);
				// 移除第一个键，因为我们要开始使用它
				$keyFirst = array_shift($keyParts);
				$value = $this->get($keyFirst);
				// 遍历其余键分组，看看值能否在结果数组中找到
				foreach( $keyParts as $keyPart )
				{
					if ( is_array($value) )
					{
						if ( isset($value[$keyPart]) )
						{
							$value = $value[$keyPart];
						}
						else
						{
							$value = $default;
						}
					}
				}
			}
		}
		return $value;
	}
	/**
	 * 自动伴侣方法
	 */
	public function __get($key)
	{
		return $this->get($key);
	}
	/**
	 * 	覆盖请求参数的值
	 *
	 * 	@param	string	$key
	 * 	@param	string	$value
	 */
	public function set($key, $value)
	{
		$this->_params[$key] = $value;
	}
	/**
	 * 自动伴侣方法
	 */
	public function __set($key, $value)
	{
		$this->set($key, $value);
	}
	/**
	 * 检查一下，看是否设置了某个属性
	 *
	 * @param	string	$key
	 * @return	boolean
	 */
	public function __isset($key)
	{
		switch( true )
		{
			case isset($this->_params[$key]):
				return true;
			case isset($this->_queryParams[$key]):
				return true;
			case isset($this->_postParams[$key]):
				return true;
			case isset($_COOKIE[$key]):
				return true;
			case isset($_SERVER[$key]):
				return true;
			case isset($_ENV[$key]):
				return true;
			default:
				return false;
		}
	}
	/**
	 * 取得接受格式的数组，或检查请求是否接受指定的格式
	 */
	public function accept($format = NULL)
	{
		if ( $this->_accept === NULL )
		{
			$this->parseAcceptHeader();
		}
		// 检查请求是否接受特定的格式
		if ( $format !== NULL )
		{
			if ( isset($this->_mimeTypes[$format]) )
			{
				return in_array($this->_mimeTypes[$format], $this->_accept);
			}
			return false;
		}
		return $this->_accept;
	}
	/**
	 * 获取请求参数
	 *
	 * @return	array	返回所有 GET 和 POST 的数组，并设置参数
	 */
	public function params(array $params = [])
	{
		// 设置
		if ( count($params) > 0 )
		{
			foreach( $params as $pKey => $pValue )
			{
				$this->set($pKey, $pValue);
			}
		}
		else
		{
			// 取得
			$params = array_merge($this->_queryParams, $this->_postParams, $this->_params);
		}
		return $params;
	}
	/**
	 * 设置额外的请求参数
	 */
	public function setParams($params)
	{
		if ( $params && is_array($params) )
		{
			foreach( $params as $pKey => $pValue )
			{
				$this->set($pKey, $pValue);
			}
		}
	}
	/**
	 * 获取 $params 变量的成员
	 *
	 * 如果没有传递 $key，则返回整个 $params 数组。
	 *
	 * @param	string	$key
	 * @param	mixed	$default 未找到键时使用的默认值
	 * @return	mixed	如果键不存在，则返回 NULL
	 */
	public function param($key = NULL, $default = NULL)
	{
		if ( $key === NULL )
		{
			return $this->_params;
		}
		return isset($this->_params[$key]) ? $this->_params[$key] : $default;
	}
	/**
	 * 获取超全局变量 $_GET 的成员
	 *
	 * 如果没有传递 $key，则返回整个 $_GET 数组。
	 *
	 * @param	string	$key
	 * @param	mixed	$default 键没找到时使用的默认值
	 * @return	mixed	如果键不存在，就返回 NULL
	 */
	public function query($key = NULL, $default = NULL)
	{
		if ( $key === NULL )
		{
			return array_diff_key($this->_queryParams, $this->param() + array('u' => 1));
		}
		return isset($this->_queryParams[$key]) ? $this->_queryParams[$key] : $default;
	}
	/**
	 * 获取超全局变量 $_POST 的成员
	 *
	 * 如果没有传递 $key，则返回整个 $_POST 数组。
	 *
	 * @param	string	$key
	 * @param	mixed	$default 键未找到时使用的默认值
	 * @return	mixed	如果键不存在，就返回 NULL
	 */
	public function post($key = NULL, $default = NULL)
	{
		if ( $key === NULL )
		{
			return $this->_postParams;
		}
		return isset($this->_postParams[$key]) ? $this->_postParams[$key] : $default;
	}
	/**
	 * 获取超全局变量 $_COOKIE 的成员
	 *
	 * 如果没有传递 $key，则返回整个 $_COOKIE 数组。
	 *
	 * @param	string	$key
	 * @param	mixed	$default 未找到键时使用的默认值
	 * @return	mixed	如果键不存在，就返回 NULL
	 */
	public function cookie($key = NULL, $default = NULL)
	{
		if ( $key === NULL )
		{
			return $_COOKIE;
		}
		return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
	}
	/**
	 * 获取超全局变量 $_SERVER 的成员
	 *
	 * 如果没有传递 $key，则返回整个 $_SERVER 数组。
	 *
	 * @param	string	$key
	 * @param	mixed	$default 未找到键时使用的默认值
	 * @return	mixed	如果键不存在，就返回 NULL
	 */
	public function server($key = NULL, $default = NULL)
	{
		if ( $key === NULL )
		{
			return $_SERVER;
		}
		return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
	}
	/**
	 * 格式的取得与设置
	 *
	 * 如果没有传递 $format，则返回当前格式
	 *
	 * @param	string	$key
	 * @return	string	格式
	 */
	public function format($format = NULL)
	{
		if ( $format !== NULL )
		{
			// 如果使用完整的 MIME 类型，我们只需要扩展名
			if ( strpos($format, '/') !== false && in_array($format, $this->_mimeTypes) )
			{
				$format = array_search($format, $this->_mimeTypes);
			}
			$this->_format = $format;
		}
		// 检查扩展名并将其指定为所要求的格式（覆盖 'Accept' 头）
		$dotPos = strpos($this->url(), '.');
		if ( $dotPos !== false )
		{
			$ext = substr($this->url(), $dotPos + 1);
			$this->_format = $ext;
		}
		return $this->_format;
	}
	/**
	 * 返回给定头的值。
	 * 传递普通的HTTP规定的头名称。例如，'Accept' 用以取得 Accept 头，'Accept-Encoding' 用以取得 Accept-Encoding 头。
	 *
	 * @param	string	$header HTTP头名称
	 * @return	string|false	HTTP头值，没找到时为 false
	 */
	public function header($header)
	{
		// 先尝试由 $_SERVER 的键获取
		$temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
		if ( !empty($this->_headers[$temp]) )
		{
			return $this->_headers[$temp];
		}
		// 现在尝试直接由 header 获取
		if ( !empty($this->_headers[$header]) )
		{
			return $this->_headers[$header];
		}
		// 在 Apache 上，这似乎是取得授权头的惟一途径
		if ( function_exists('apache_request_headers') )
		{
			$headers = apache_request_headers();
			if ( !empty($headers[$header]) )
			{
				return $headers[$header];
			}
		}
		return false;
	}
	/**
	 * 请求方式。总是返回 HTTP_METHOD 的大写。
	 *
	 * @return	string	HTTP请求方式的大写
	 */
	public function method()
	{
		if ( $this->_method === NULL )
		{
			$sm = strtoupper($this->server('REQUEST_METHOD', 'GET'));
			if ( $sm == 'POST' && $this->get('_method') )
			{
				return strtoupper($this->get('_method'));
			}
			$this->_method = $sm;
		}
		return $this->_method;
	}
	/**
	 * 请求 URI
	 *
	 * @return	string	来自超全局变量 $_SERVER 的请求 URI
	 */
	public function uri()
	{
		return $this->server('REQUEST_URI');
	}
	/**
	 * 请求方案（http、https 或 cli）
	 *
	 * @return	string	'http'、'https' 或 'cli'
	 */
	public function scheme()
	{
		return $this->isCli() ? 'cli' : ($this->isSecure() ? 'https' : 'http' );
	}
	/**
	 * 请求子域
	 *
	 * @return	string 请求子域
	 */
	public function subdomain()
	{
		$parts = explode('.', $this->host());
		$count = count($parts);
		return $count > 2 ? $parts[0] : false;
	}
	/**
	 * 请求的 HTTP_HOST
	 *
	 * @return	string	来自超全局变量 $_SERVER 的请求 HOST
	 */
	public function host()
	{
		return $this->header('Host');
	}
	/**
	 * 请求端口
	 *
	 * @return	integer	请求端口
	 */
	public function port()
	{
		return $this->server('SERVER_PORT');
	}
	/**
	 * 取得用户的正确 IP 地址
	 * 获取 IP 的后方防火墙或 ISP 代理
	 *
	 * @return	string	IP地址
	 */
	public function ip()
	{
		$ip = false;
		if ( !empty($_SERVER['HTTP_CLIENT_IP']) )
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		if ( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) )
		{
			// 将 IP 放入稍后将要用到的数组中。
			$ips = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ( $ip )
			{
				array_unshift($ips, $ip);
				$ip = false;
			}
			for( $i = 0; $i < count($ips); $i++ )
			{
				if ( !eregi("^(10|172\.16|192\.168)\.", $ips[$i]) )
				{
					$ip = $ips[$i];
					break;
				}
			}
		}
		return $ip ? $ip : $_SERVER['REMOTE_ADDR'];
	}
	/**
	 * 取得原始的、未解析的请求主体（对编码了的 PUT 和 POST 请求主体有用——如 JSON）
	 * PHP 不能多次获取 php://input 的内容
	 */
	public function raw()
	{
		if ( $this->_raw === NULL )
		{
			$this->_raw = file_get_contents('php://input');
		}
		return $this->_raw;
	}
	/**
	 * 	确定输入的请求为 POST
	 *
	 * @return	boolean
	 */
	public function isPost()
	{
		return $this->method() == 'POST';
	}
	/**
	 * 确定输入的请求为 GET
	 *
	 * @return	boolean
	 */
	public function isGet()
	{
		return $this->method() == 'GET';
	}
	/**
	 * 确定输入的请求为 PUT
	 *
	 * @return	boolean
	 */
	public function isPut()
	{
		return $this->method() == 'PUT';
	}
	/**
	 * 确定输入的请求为 DELETE
	 *
	 * @return	boolean
	 */
	public function isDelete()
	{
		return $this->method() == 'DELETE';
	}
	/**
	 * 确定输入的请求为 PATCH
	 *
	 * @return	boolean
	 */
	public function isPatch()
	{
		return $this->method() == 'PATCH';
	}
	/**
	 * 确定输入的请求为 HEAD
	 *
	 * @return	boolean
	 */
	public function isHead()
	{
		return $this->method() == 'HEAD';
	}
	/**
	 * 确定输入的请求为 OPTIONS
	 *
	 * @return	boolean
	 */
	public function isOptions()
	{
		return $this->method() == 'OPTIONS';
	}
	/**
	 * 确定输入的请求为安全HTTPS
	 *
	 * @return	boolean
	 */
	public function isSecure()
	{
		return !isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on' ? false : true;
	}
	/**
	 * 该请求是 Javascript XMLHttpRequest 吗？
	 *
	 * @return	boolean
	 */
	public function isAjax()
	{
		return $this->header('X_REQUESTED_WITH') == 'XMLHttpRequest';
	}
	/**
	 * 该请求是来自移动设备吗？
	 *
	 * 适用于 iPhone, Android, Windows Mobile, Windows Phone 7, Symbian, 或其它手机浏览器
	 *
	 * @return	boolean
	 */
	public function isMobile()
	{
		$op = strtolower($_SERVER['HTTP_X_OPERAMINI_PHONE']);
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		$ac = strtolower($_SERVER['HTTP_ACCEPT']);
		return strpos($ac, 'application/vnd.wap.xhtml+xml') !== false || strpos($ac, 'text/vnd.wap.wml') !== false || $op != '' || strpos($ua, 'iphone') !== false || strpos($ua, 'android') !== false || strpos($ua, 'iemobile') !== false || strpos($ua, 'kindle') !== false || strpos($ua, 'sony') !== false || strpos($ua, 'symbian') !== false || strpos($ua, 'nokia') !== false || strpos($ua, 'samsung') !== false || strpos($ua, 'mobile') !== false || strpos($ua, 'windows ce') !== false || strpos($ua, 'epoc') !== false || strpos($ua, 'opera mini') !== false || strpos($ua, 'nitro') !== false || strpos($ua, 'j2me') !== false || strpos($ua, 'midp-') !== false || strpos($ua, 'cldc-') !== false || strpos($ua, 'netfront') !== false || strpos($ua, 'mot') !== false || strpos($ua, 'up.browser') !== false || strpos($ua, 'up.link') !== false || strpos($ua, 'audiovox') !== false || strpos($ua, 'blackberry') !== false || strpos($ua, 'ericsson,') !== false || strpos($ua, 'panasonic') !== false || strpos($ua, 'philips') !== false || strpos($ua, 'sanyo') !== false || strpos($ua, 'sharp') !== false || strpos($ua, 'sie-') !== false || strpos($ua, 'portalmmm') !== false || strpos($ua, 'blazer') !== false || strpos($ua, 'avantgo') !== false || strpos($ua, 'danger') !== false || strpos($ua, 'palm') !== false || strpos($ua, 'series60') !== false || strpos($ua, 'palmsource') !== false || strpos($ua, 'pocketpc') !== false || strpos($ua, 'smartphone') !== false || strpos($ua, 'rover') !== false || strpos($ua, 'ipaq') !== false || strpos($ua, 'au-mic,') !== false || strpos($ua, 'alcatel') !== false || strpos($ua, 'ericy') !== false || strpos($ua, 'up.link') !== false || strpos($ua, 'vodafone/') !== false || strpos($ua, 'wap1.') !== false || strpos($ua, 'wap2.') !== false;
	}
	/**
	 * 该请求是来自机器人或蜘蛛吗？
	 *
	 * @return	boolean
	 */
	public function isBot()
	{
		$ua = strtolower($this->server('HTTP_USER_AGENT'));
		$botList = array(
			'googlebot',
			'msnbot',
			'yahoo',
			'slurp',
			'bot',
			'spider',
			'nutch',
			'crawler',
			'facebook',
			'bing',
			'siteanalyzer',
			'dnsqueries',
			'httpclient',
			'indy library',
			'netcraftsurveyagent',
		);
		foreach( $botList as $bot )
		{
			if ( strpos($ua, $bot) !== false )
			{
				return true;
			}
		}
		return false;
	}
	/**
	 * 请求是来自 CLI （命令行接口）吗？
	 *
	 * @return	boolean
	 */
	public function isCli()
	{
		return !isset($_SERVER['HTTP_HOST']);
	}
	/**
	 * 这是一个 Flash 请求吗？
	 *
	 * @return	bool
	 */
	public function isFlash()
	{
		return $this->header('USER_AGENT') == 'Shockwave Flash';
	}
	/**
	 * 解析 Accept HTTP 头
	 */
	protected function parseAcceptHeader()
	{
		$hdr = $this->header('Accept');
		$accept = [];
		foreach( preg_split('/\s*,\s*/', $hdr) as $i => $term )
		{
			$o = new \stdClass;
			$o->pos = $i;
			if ( preg_match(",^(\S+)\s*;\s*(?:q|level)=([0-9\.]+),i", $term, $M) )
			{
				$o->type = $M[1];
				$o->q = (double)$M[2];
			}
			else
			{
				$o->type = $term;
				$o->q = 1;
			}
			$accept[] = $o;
		}
		usort($accept, function ($a, $b){
			// q 因数大者胜出
			$diff = $b->q - $a->q;
			if ( $diff > 0 )
			{
				$diff = 1;
			}
			elseif ( $diff < 0 )
			{
				$diff = -1;
			}
			else
			{
				// 同分决胜，第一项胜出
				$diff = $a->pos - $b->pos;
			}
			return $diff;
		});
		$this->_accept = [];
		foreach( $accept as $a )
		{
			if ( empty($a->type) )
			{
				continue;
			}
			$this->_accept[$a->type] = $a->type;
		}
		return $this->_accept;
	}
}