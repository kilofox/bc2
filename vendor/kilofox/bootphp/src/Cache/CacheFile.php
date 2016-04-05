<?php

namespace Bootphp\Cache;
use Bootphp\Cache\Cache;
use Bootphp\Exception\ExceptionHandler;
/**
 * [BootPHP 缓存](api/Bootphp/Cache) 文件驱动。为 Bootphp 缓存库提供基于文件的驱动。这是最慢的缓存方式之一。
 *
 * ### 配置举例
 *
 * 下面是 _文件_ 服务器配置的例子。
 *
 *     return array(
 *         'file' => array(						// file 分组
 *             'driver' => 'file',				// 使用文件驱动
 *             'cache_dir' => APPPATH.'cache',	// 缓存位置
 *         )
 *     );
 *
 * 在只需要一个缓存分组的情况下，如果分组命名为 `default`，那么在实例化缓存实例时，就不需要传递分组名。
 *
 * #### 一般缓存分组配置
 *
 * 名称	     | 必需	  | 描述
 * --------- | ------ | -----------------------------------
 * driver    | __是__ | (_string_) 使用的驱动类型
 * cache_dir | __否__ | (_string_) 该缓存实例要使用的缓存路径
 *
 * @package	BootPHP/缓存
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class CacheFile extends Cache
{
	/**
	 * @var string 缓存目录
	 */
	protected $_cacheDir;
	/**
	 * 创建一个基于字符串的加密的文件名。用于为每一个缓存文件创建短小的惟一id。
	 *
	 * 	   // 创建缓存文件名
	 *     $filename = CacheFile::filename($this->_sanitizeId($id));
	 *
	 * @param	string	$string 要加密的文件名字符串
	 * @return	string
	 */
	protected static function filename($string)
	{
		return sha1($string) . '.cache';
	}
	/**
	 * 构造文件缓存驱动。这个方法不能被外部调用。该驱动必须用 Cache::instance() 方法来实例化。
	 *
	 * @param array $config 配置
	 * @throws ExceptionHandler
	 */
	protected function __construct(array $config)
	{
		// 设置父类
		parent::__construct($config);
		try
		{
			$directory = isset($this->_config['cache_dir']) ? $this->_config['cache_dir'] : 'xxx';
			$this->_cacheDir = new \SplFileInfo($directory);
		}
		// PHP >= 5.3 异常处理
		catch( UnexpectedValueException $e )
		{
			$this->_cacheDir = $this->_makeDirectory($directory, 0777, true);
		}
		// 如果定义的目录是一个文件，那就从这儿滚出去
		if ( $this->_cacheDir->isFile() )
		{
			throw new ExceptionHandler('不能用一个已经存在的文件 ' . $this->_cacheDir->getRealPath() . ' 作为缓存目录');
		}
		// 检查目录的读状态
		if ( !$this->_cacheDir->isReadable() )
		{
			throw new ExceptionHandler('缓存目录 ' . $this->_cacheDir->getRealPath() . ' 不可读');
		}
		// 检查目录的写状态
		if ( !$this->_cacheDir->isWritable() )
		{
			throw new ExceptionHandler('缓存目录 ' . $this->_cacheDir->getRealPath() . ' 不可写');
		}
	}
	/**
	 * 根据 id 获取缓存值条目。
	 *
	 * 	   // 从 file 分组获取缓存条目
	 *     $data = Cache::instance('file')->get('foo');
	 *
	 * @param	string	$id 缓存条目的id
	 * @return	mixed
	 * @throws ExceptionHandler
	 */
	public function get($id)
	{
		$filename = self::filename($this->_sanitizeId($id));
		$directory = $this->_resolveDirectory($filename);
		try
		{
			// 打开文件
			$file = new \SplFileInfo($directory . $filename);
			// 如果文件不存在
			if ( !$file->isFile() )
			{
				return NULL;
			}
			// 测试是否过期
			if ( $this->_isExpired($file) )
			{
				// 删除文件
				$this->_deleteFile($file, false, true);
				return NULL;
			}
			// 打开文件以读取数据
			$fileObj = $file->openFile();
			// 先运行 fgets()。缓存数据开始于第二行，因为第一行是到期时间时间戳。
			$fileObj->fgets();
			$cache = '';
			while( $fileObj->eof() === false )
			{
				$cache .= $fileObj->fgets();
			}
			return unserialize($cache);
		}
		catch( \ErrorException $e )
		{
			// 处理反序列化失败导致的 ErrorException
			if ( $e->getCode() === E_NOTICE )
			{
				throw new ExceptionHandler(__METHOD__ . ' 反序列化缓存对象失败：' . $e->getMessage());
			}
			// 否则抛出异常
			throw $e;
		}
	}
	/**
	 * 用 id 和到期时间给缓存设值。
	 *
	 * 	   $data = 'bar';
	 *
	 * 	   // 将 'bar' 置给 file 分组中的 'foo'，使用默认期限
	 * 	   Cache::instance('file')->set('foo', $data);
	 *
	 * 	   // 将 'bar' 置给 file 分组中的 'foo'，30秒过期
	 * 	   Cache::instance('file')->set('foo', $data, 30);
	 *
	 * @param	string	$id 缓存条目的id
	 * @param	string	$data 置给缓存的数据
	 * @param	integer	$lifetime 以“秒”为单位的到期时间
	 * @return	boolean
	 */
	public function set($id, $data, $lifetime = NULL)
	{
		$filename = CacheFile::filename($this->_sanitizeId($id));
		$directory = $this->_resolveDirectory($filename);
		// 如果到期时间为 NULL
		if ( $lifetime === NULL )
		{
			// 设置默认期限
			$lifetime = isset($this->_config['default_expire']) ? $this->_config['default_expire'] : Cache::DEFAULT_EXPIRE;
		}
		// 打开目录
		$dir = new \SplFileInfo($directory);
		// 如果目录路径不是一个目录
		if ( !$dir->isDir() )
		{
			// 创建目录
			if ( !mkdir($directory, 0777, true) )
			{
				throw new ExceptionHandler(__METHOD__ . ' 无法创建目录：:directory', array(':directory' => $directory));
			}
			// chmod 用于解决潜在的 umask 问题
			chmod($directory, 0777);
		}
		// 打开文件进行检查
		$resouce = new \SplFileInfo($directory . $filename);
		$file = $resouce->openFile('w');
		try
		{
			$data = $lifetime . "\n" . serialize($data);
			$file->fwrite($data, strlen($data));
			return (bool)$file->fflush();
		}
		catch( \ErrorException $e )
		{
			// 如果序列化出现错误异常
			if ( $e->getCode() === E_NOTICE )
			{
				// 抛出缓存错误
				throw new ExceptionHandler(__METHOD__ . ' 序列化缓存数据失败：' . $e->getMessage());
			}
			// 否则抛出错误异常
			throw $e;
		}
	}
	/**
	 * 根据 id 删除缓存条目。
	 *
	 * 	   // 从 file 分组中删除 'foo' 条目
	 * 	   Cache::instance('file')->delete('foo');
	 *
	 * @param	string	$id 要删除的条目id
	 * @return	boolean
	 */
	public function delete($id)
	{
		$filename = self::filename($this->_sanitizeId($id));
		$directory = $this->_resolveDirectory($filename);
		return $this->_deleteFile(new \SplFileInfo($directory . $filename), false, true);
	}
	/**
	 * 删除全部缓存条目。
	 *
	 * 在使用共享内存的缓存系统时，请谨慎使用这个方法，它会清除系统内所有客户端的每个条目。
	 *
	 *    	// 删除 file 分组中的全部缓存条目
	 *    	Cache::instance('file')->deleteAll();
	 *
	 * @return	boolean
	 */
	public function deleteAll()
	{
		return $this->_deleteFile($this->_cacheDir, true);
	}
	/**
	 * 垃圾回收方法，用于清理过期的缓存条目。
	 *
	 * @return	void
	 */
	public function garbageCollect()
	{
		$this->_deleteFile($this->_cacheDir, true, false, true);
		return;
	}
	/**
	 * 递归删除文件，并在出现任何错误时返回 false
	 *
	 *     // 删除文件或文件夹，同时保留父目录，并忽略所有错误
	 * 	   $this->_deleteFile($folder, true, true);
	 *
	 * @param SplFileInfo $file 文件
	 * @param boolean $retainParentDirectory 保留父目录
	 * @param boolean $ignoreErrors 忽略错误，以防止异常打断执行
	 * @param boolean $onlyExpired 只是过期的文件
	 * @return	boolean
	 * @throws ExceptionHandler
	 */
	protected function _deleteFile(\SplFileInfo $file, $retainParentDirectory = false, $ignoreErrors = false, $onlyExpired = false)
	{
		try
		{
			// 如果是文件
			if ( $file->isFile() )
			{
				try
				{
					// 处理忽略的文件
					if ( in_array($file->getFilename(), $this->config('ignore_on_delete')) )
					{
						$delete = false;
					}
					// 如果 $onlyExpired 未设置
					elseif ( $onlyExpired === false )
					{
						// 我们要删除这个文件
						$delete = true;
					}
					else
					{
						// 评估文件期限，用以标记为删除
						$delete = $this->_isExpired($file);
					}
					// 如果设置了删除标志，则删除文件
					if ( $delete )
						return unlink($file->getRealPath());
					else
						return false;
				}
				catch( \ErrorException $e )
				{
					// 捕捉文件删除警告
					if ( $e->getCode() === E_WARNING )
					{
						throw new ExceptionHandler(__METHOD__ . ' 删除文件失败：' . $file->getRealPath());
					}
				}
			}
			// 如果是目录
			elseif ( $file->isDir() )
			{
				$files = new DirectoryIterator($file->getPathname());
				while( $files->valid() )
				{
					// 提取文件名称
					$name = $files->getFilename();
					// 如果名称不是“点”
					if ( $name != '.' && $name != '..' )
					{
						// 创建新的文件源
						$fp = new \SplFileInfo($files->getRealPath());
						// 删除该文件
						$this->_deleteFile($fp);
					}
					// 移动文件指针
					$files->next();
				}
				// 如果设置为保留父目录，返回
				if ( $retainParentDirectory )
				{
					return true;
				}
				try
				{
					// 移除文件迭代（修复 Windows PHP 打开 DirectoryIterator 的权限问题）
					unset($files);
					// 尝试移除父目录
					return rmdir($file->getRealPath());
				}
				catch( \ErrorException $e )
				{
					// 捕捉目录删除警告
					if ( $e->getCode() === E_WARNING )
					{
						throw new ExceptionHandler(__METHOD__ . ' 删除目录失败：' . $file->getRealPath());
					}
					throw $e;
				}
			}
			else
			{
				// 文件已经被删除
				return false;
			}
		}
		catch( Exception $e )
		{
			// 如果打开了“忽略错误”
			if ( $ignoreErrors === true )
				return false;
			// 抛出异常
			throw $e;
		}
	}
	/**
	 * 根据文件名解析缓存目录的实际路径
	 *
	 * 	   // 取得缓存目录的真实路径
	 * 	   $realpath = $this->_resolveDirectory($filename);
	 *
	 * @param	string	$filename 要解析的文件名
	 * @return	string
	 */
	protected function _resolveDirectory($filename)
	{
		return $this->_cacheDir->getRealPath() . DIRECTORY_SEPARATOR . $filename[0] . $filename[1] . DIRECTORY_SEPARATOR;
	}
	/**
	 * 生成缓存目录（如果不存在的话）。简单地封装 mkdir。
	 * @param	string	$directory 目录
	 * @param	string	$mode 模式
	 * @param	string	$recursive 递归
	 * @param	string	$context 上下文
	 * @return	SplFileInfo
	 * @throws	ExceptionHandler
	 */
	protected function _makeDirectory($directory, $mode = 0777, $recursive = false, $context = NULL)
	{
		if ( !mkdir($directory, $mode, $recursive, $context) )
		{
			throw new ExceptionHandler('创建默认缓存目录失败：' . $directory);
		}
		chmod($directory, $mode);
		return new \SplFileInfo($directory);
	}
	/**
	 * 测试缓存文件是否过期
	 *
	 * @param SplFileInfo $file 缓存文件
	 * @return	boolean 过期为 true，否则为 false
	 */
	protected function _isExpired(\SplFileInfo $file)
	{
		// 打开文件并解析数据
		$created = $file->getMTime();
		$fileObj = $file->openFile();
		$lifetime = (int)$fileObj->fgets();
		// 位于 EOF，损坏了！
		if ( $fileObj->eof() )
		{
			throw new ExceptionHandler(__METHOD__ . ' 损坏的缓存文件！');
		}
		// 关闭文件
		$fileObj = NULL;
		// 测试是否过期并返回
		return $lifetime !== 0 && ($created + $lifetime) < time();
	}
}