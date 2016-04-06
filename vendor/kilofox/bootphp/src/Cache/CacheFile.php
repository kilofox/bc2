<?php

namespace Bootphp\Cache;

use Bootphp\Cache\Cache;
use Bootphp\Exception\ExceptionHandler;

/**
 * [BootPHP Cache](api/Bootphp/Cache) File driver. Provides a file based
 * driver for the BootPHP Cache library. This is one of the slowest
 * caching methods.
 *
 * ### Configuration example
 *
 * Below is an example of a _file_ server configuration.
 *
 *     return array(
 *         'file' => array(							// File driver group
 *             'driver' => 'file',					// using File driver
 *             'cache_dir' => APPPATH . '/cache'	// Cache location
 *         )
 *     );
 *
 * In cases where only one cache group is required, if the group is named `default` there is
 * no need to pass the group name when instantiating a cache instance.
 *
 * #### General cache group configuration settings
 *
 * Name           | Required | Description
 * -------------- | -------- | ---------------------------------------------------------------
 * driver         | __YES__  | (_string_) The driver type to use
 * cache_dir      | __NO__   | (_string_) The cache directory to use for this cache instance
 *
 * @package	BootPHP/Cache
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class CacheFile extends Cache {
	/**
	 * @var	string	the caching directory
	 */
	protected $_cacheDir;

	/**
	 * Creates a hashed filename based on the string. This is used
	 * to create shorter unique IDs for each cache filename.
	 *
	 * 	   // Create the cache filename
	 *     $filename = CacheFile::filename($this->_sanitizeId($id));
	 *
	 * @param	string	$string string to hash into filename
	 * @return	string
	 */
	protected static function filename($string)
	{
		return sha1($string) . '.cache';
	}

	/**
	 * Constructs the file cache driver. This method cannot be invoked externally. The file cache driver must
	 * be instantiated using the `Cache::instance()` method.
	 *
	 * @param array $config config
	 * @throws ExceptionHandler
	 */
	protected function __construct(array $config)
	{
		// Setup parent
		parent::__construct($config);

		try
		{
			$directory = isset($this->_config['cache_dir']) ? $this->_config['cache_dir'] : 'cache';
			$this->_cacheDir = new \SplFileInfo($directory);
		}
		catch( UnexpectedValueException $e )
		{
			$this->_cacheDir = $this->_makeDirectory($directory, 0777, true);
		}

		// If the defined directory is a file, get outta here
		if ( $this->_cacheDir->isFile() )
		{
			throw new ExceptionHandler('Unable to create cache directory as a file already exists : ' . $this->_cacheDir->getRealPath());
		}

		// Check the read status of the directory
		if ( !$this->_cacheDir->isReadable() )
		{
			throw new ExceptionHandler('Unable to read from the cache directory ' . $this->_cacheDir->getRealPath());
		}

		// Check the write status of the directory
		if ( !$this->_cacheDir->isWritable() )
		{
			throw new ExceptionHandler('Unable to write to the cache directory ' . $this->_cacheDir->getRealPath());
		}
	}

	/**
	 * Retrieve a cached value entry by id.
	 *
	 * 	   // Retrieve cache entry from file group
	 *     $data = Cache::instance('file')->get('foo');
	 *
	 * @param	string	$id	id of cache to entry
	 * @return	mixed
	 * @throws ExceptionHandler
	 */
	public function get($id)
	{
		$filename = self::filename($this->_sanitizeId($id));
		$directory = $this->_resolveDirectory($filename);

		try
		{
			// Open file
			$file = new \SplFileInfo($directory . $filename);

			// If file does not exist
			if ( !$file->isFile() )
			{
				return NULL;
			}

			// Test the expiry
			if ( $this->_isExpired($file) )
			{
				// Delete the file
				$this->_deleteFile($file, false, true);
				return NULL;
			}

			// open the file to read data
			$fileObj = $file->openFile();

			// Run first fgets(). Cache data starts from the second line
			// as the first contains the lifetime timestamp.
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
			// Handle ErrorException caused by failed unserialization
			if ( $e->getCode() === E_NOTICE )
			{
				throw new ExceptionHandler(__METHOD__ . ' failed to unserialize cached object with message : ' . $e->getMessage());
			}

			// Otherwise throw the exception
			throw $e;
		}
	}

	/**
	 * Set a value to cache with id and lifetime.
	 *
	 * 	   $data = 'bar';
	 *
	 * 	   // Set 'bar' to 'foo' in file group, using default expiry
	 * 	   Cache::instance('file')->set('foo', $data);
	 *
	 * 	   // Set 'bar' to 'foo' in file group for 30 seconds
	 * 	   Cache::instance('file')->set('foo', $data, 30);
	 *
	 * @param	string	$id			id of cache entry
	 * @param	string	$data		data to set to cache
	 * @param	integer	$lifetime	lifetime in seconds
	 * @return	boolean
	 */
	public function set($id, $data, $lifetime = NULL)
	{
		$filename = CacheFile::filename($this->_sanitizeId($id));
		$directory = $this->_resolveDirectory($filename);

		// If lifetime is NULL
		if ( $lifetime === NULL )
		{
			// Set to the default expiry
			$lifetime = isset($this->_config['default_expire']) ? $this->_config['default_expire'] : Cache::DEFAULT_EXPIRE;
		}

		// Open directory
		$dir = new \SplFileInfo($directory);

		// If the directory path is not a directory
		if ( !$dir->isDir() )
		{
			$this->_makeDirectory($directory, 0777, true);
		}

		// Open file to inspect
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
			// If serialize through an error exception
			if ( $e->getCode() === E_NOTICE )
			{
				// Throw a caching error
				throw new ExceptionHandler(__METHOD__ . ' failed to serialize data for caching with message : ' . $e->getMessage());
			}

			// Else rethrow the error exception
			throw $e;
		}
	}

	/**
	 * Delete a cache entry based on id.
	 *
	 * 	   // Delete 'foo' entry from the file group
	 * 	   Cache::instance('file')->delete('foo');
	 *
	 * @param	string	$id	id to remove from cache
	 * @return	boolean
	 */
	public function delete($id)
	{
		$filename = self::filename($this->_sanitizeId($id));
		$directory = $this->_resolveDirectory($filename);
		return $this->_deleteFile(new \SplFileInfo($directory . $filename), false, true);
	}

	/**
	 * Delete all cache entries.
	 *
	 * Beware of using this method when using shared memory cache systems, as
	 * it will wipe every entry within the system for all clients.
	 *
	 *    	// Delete all cache entries in the file group
	 *    	Cache::instance('file')->deleteAll();
	 *
	 * @return	boolean
	 */
	public function deleteAll()
	{
		return $this->_deleteFile($this->_cacheDir, true);
	}

	/**
	 * Garbage collection method that cleans any expired
	 * cache entries from the cache.
	 *
	 * @return	void
	 */
	public function garbageCollect()
	{
		$this->_deleteFile($this->_cacheDir, true, false, true);
		return;
	}

	/**
	 * Deletes files recursively and returns FALSE on any errors.
	 *
	 *     // Delete a file or folder whilst retaining parent directory and ignore all errors
	 * 	   $this->_deleteFile($folder, true, true);
	 *
	 * @param	SplFileInfo	$file					file
	 * @param	boolean		$retainParentDirectory	retain the parent directory
	 * @param	boolean		$ignoreErrors			ignore_errors to prevent all exceptions interrupting exec
	 * @param	boolean		$onlyExpired			only expired files
	 * @return	boolean
	 * @throws ExceptionHandler
	 */
	protected function _deleteFile(\SplFileInfo $file, $retainParentDirectory = false, $ignoreErrors = false, $onlyExpired = false)
	{
		// Allow graceful error handling
		try
		{
			// If is file
			if ( $file->isFile() )
			{
				try
				{
					// Handle ignore files
					if ( in_array($file->getFilename(), $this->config('ignore_on_delete')) )
					{
						$delete = false;
					}
					// If only expired is not set
					elseif ( $onlyExpired === false )
					{
						// We want to delete the file
						$delete = true;
					}
					else
					{
						// Assess the file expiry to flag it for deletion
						$delete = $this->_isExpired($file);
					}

					// If the delete flag is set delete file
					if ( $delete )
						return unlink($file->getRealPath());
					else
						return false;
				}
				catch( \ErrorException $e )
				{
					// Catch any delete file warnings
					if ( $e->getCode() === E_WARNING )
					{
						throw new ExceptionHandler(__METHOD__ . ' failed to delete file : ' . $file->getRealPath());
					}
				}
			}
			// Else, is directory
			elseif ( $file->isDir() )
			{
				// Create new DirectoryIterator
				$files = new DirectoryIterator($file->getPathname());

				// Iterate over each entry
				while( $files->valid() )
				{
					// Extract the entry name
					$name = $files->getFilename();

					// If the name is not a dot
					if ( $name != '.' && $name != '..' )
					{
						// Create new file resource
						$fp = new \SplFileInfo($files->getRealPath());

						// Delete the file
						$this->_deleteFile($fp);
					}

					// Move the file pointer on
					$files->next();
				}

				// If set to retain parent directory, return now
				if ( $retainParentDirectory )
				{
					return true;
				}

				try
				{
					// Remove the files iterator
					// (fixes Windows PHP which has permission issues with open iterators)
					unset($files);

					// Try to remove the parent directory
					return rmdir($file->getRealPath());
				}
				catch( \ErrorException $e )
				{
					// Catch any delete directory warnings
					if ( $e->getCode() === E_WARNING )
					{
						throw new ExceptionHandler(__METHOD__ . ' failed to delete directory : ' . $file->getRealPath());
					}
					throw $e;
				}
			}
			else
			{
				// We get here if a file has already been deleted
				return false;
			}
		}
		catch( \Exception $e )
		{
			// If ignore_errors is on
			if ( $ignoreErrors === true )
				return false;
			// Throw exception
			throw $e;
		}
	}

	/**
	 * Resolves the cache directory real path from the filename.
	 *
	 * 	   // Get the realpath of the cache folder
	 * 	   $realpath = $this->_resolveDirectory($filename);
	 *
	 * @param	string	$filename filename to resolve
	 * @return	string
	 */
	protected function _resolveDirectory($filename)
	{
		return $this->_cacheDir->getRealPath() . DIRECTORY_SEPARATOR . $filename[0] . $filename[1] . DIRECTORY_SEPARATOR;
	}

	/**
	 * Makes the cache directory if it doesn't exist. Simply a wrapper for
	 * `mkdir` to ensure DRY principles.
	 *
	 * @param	string	$directory	directory path
	 * @param	string	$mode		chmod mode
	 * @param	string	$recursive	allows nested directories creation
	 * @param	string	$context	a stream context
	 * @return	SplFileInfo
	 * @throws	ExceptionHandler
	 */
	protected function _makeDirectory($directory, $mode = 0777, $recursive = false, $context = NULL)
	{
		// call mkdir according to the availability of a passed $context param
		$mkdir_result = $context ? mkdir($directory, $mode, $recursive, $context) : mkdir($directory, $mode, $recursive);

		// throw an exception if unsuccessful
		if ( !mkdir($directory, $mode, $recursive, $context) )
		{
			throw new ExceptionHandler('Failed to create the defined cache directory : ' . $directory);
		}

		// chmod to solve potential umask issues
		chmod($directory, $mode);

		return new \SplFileInfo($directory);
	}

	/**
	 * Test if cache file is expired
	 *
	 * @param SplFileInfo $file the cache file
	 * @return	boolean if expired false otherwise
	 */
	protected function _isExpired(\SplFileInfo $file)
	{
		// Open the file and parse data
		$created = $file->getMTime();
		$fileObj = $file->openFile();
		$lifetime = (int)$fileObj->fgets();

		// If we're at the EOF at this point, corrupted!
		if ( $fileObj->eof() )
		{
			throw new ExceptionHandler(__METHOD__ . ' corrupted cache file!');
		}

		// close file
		$fileObj = NULL;

		// test for expiry and return
		return $lifetime !== 0 && ($created + $lifetime) < time();
	}
}