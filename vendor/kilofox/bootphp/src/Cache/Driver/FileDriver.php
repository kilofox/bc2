<?php

namespace Bootphp\Cache\Driver;

use Bootphp\BootphpException;

/**
 * [Cache](api/Cache) File driver. Provides a file based driver for the Bootphp
 * Cache library. This is one of the slowest caching methods.
 *
 * ### Configuration example
 *
 * Below is an example of a _file_ server configuration.
 *
 *     return [
 *         'file' => [ // File driver group
 *             'driver' => 'file', // Using File driver
 *             'cacheDir' => APP_PATH . '/cache', // Cache location
 *         ]
 *     ];
 *
 * In cases where only one cache group is required, if the group is named
 * `default` there is no need to pass the group name when instantiating a cache
 * instance.
 *
 * #### General cache group configuration settings
 *
 * Below are the settings available to all types of cache driver.
 *
 * Name     | Required | Description
 * -------- | -------- | -------------------------------------------------------------
 * driver   | __YES__  | (_string_) The driver type to use
 * cacheDir | __NO__   | (_string_) The cache directory to use for this cache instance
 *
 * @package    Bootphp/Cache
 * @category   Base
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class FileDriver extends \Bootphp\Cache\Cache
{
    /**
     * Creates a hashed filename based on the string. This is used to create
     * shorter unique IDs for each cache filename.
     *
     *     // Create the cache filename
     *     $filename = FileDriver::filename($this->sanitizeId($id));
     *
     * @param   string  $string String to hash into filename
     * @return  string
     */
    protected static function filename($string)
    {
        return sha1($string) . '.cache';
    }

    /**
     * The caching directory.
     *
     * @var string
     */
    protected $cacheDir;

    /**
     * Constructs the file cache driver. This method cannot be invoked
     * externally. The file cache driver must be instantiated using the
     * `Cache::instance()` method.
     *
     * @param   array   $config Config
     * @throws  BootphpException
     */
    protected function __construct(array $config)
    {
        // Setup parent
        parent::__construct($config);

        try {
            $directory = isset($this->config['cacheDir']) ? $this->config['cacheDir'] : \Bootphp\Core::$cacheDir;
            $this->cacheDir = new \SplFileInfo($directory);
        } catch (\UnexpectedValueException $e) {
            $this->cacheDir = $this->makeDirectory($directory, 0777, true);
        }

        // If the defined directory is a file, get outta here
        if ($this->cacheDir->isFile()) {
            throw new BootphpException('Unable to create cache directory as a file already exists: ' . $this->cacheDir->getRealPath());
        }

        // Check the read status of the directory
        if (!$this->cacheDir->isReadable()) {
            throw new BootphpException('Unable to read from the cache directory ' . $this->cacheDir->getRealPath());
        }

        // Check the write status of the directory
        if (!$this->cacheDir->isWritable()) {
            throw new BootphpException('Unable to write to the cache directory ' . $this->cacheDir->getRealPath());
        }
    }

    /**
     * Retrieve a cached value entry by id.
     *
     *     // Retrieve cache entry from file group
     *     $data = Cache::instance('file')->get('foo');
     *
     *     // Retrieve cache entry from file group and return 'bar' if miss
     *     $data = Cache::instance('file')->get('foo', 'bar');
     *
     * @param   string  $id         Id of cache to entry
     * @param   string  $default    Default value to return if cache miss
     * @return  mixed
     * @throws  BootphpException
     */
    public function get($id, $default = null)
    {
        $filename = self::filename($this->sanitizeId($id));
        $directory = $this->resolveDirectory($filename);

        // Wrap operations in try/catch to handle notices
        try {
            // Open file
            $file = new \SplFileInfo($directory . $filename);

            // If file does not exist
            if (!$file->isFile()) {
                // Return default value
                return $default;
            } else {
                // Test the expiry
                if ($this->isExpired($file)) {
                    // Delete the file
                    $this->_delete_file($file, false, true);
                    return $default;
                }

                // open the file to read data
                $data = $file->openFile();

                // Run first fgets(). Cache data starts from the second line
                // as the first contains the lifetime timestamp
                $data->fgets();

                $cache = '';

                while ($data->eof() === false) {
                    $cache .= $data->fgets();
                }

                return unserialize($cache);
            }
        } catch (ErrorException $e) {
            // Handle ErrorException caused by failed unserialization
            if ($e->getCode() === E_NOTICE) {
                throw new BootphpException(__METHOD__ . ' failed to unserialize cached object with message : ' . $e->getMessage());
            }

            // Otherwise throw the exception
            throw $e;
        }
    }

    /**
     * Set a value to cache with id and lifetime
     *
     *     $data = 'bar';
     *
     *     // Set 'bar' to 'foo' in file group, using default expiry
     *     Cache::instance('file')->set('foo', $data);
     *
     *     // Set 'bar' to 'foo' in file group for 30 seconds
     *     Cache::instance('file')->set('foo', $data, 30);
     *
     * @param   string   $id        id of cache entry
     * @param   string   $data      data to set to cache
     * @param   integer  $lifetime  lifetime in seconds
     * @return  boolean
     */
    public function set($id, $data, $lifetime = null)
    {
        $filename = self::filename($this->sanitizeId($id));
        $directory = $this->resolveDirectory($filename);

        // If lifetime is null
        if ($lifetime === null) {
            // Set to the default expiry
            $lifetime = isset($this->config['default_expire']) ? $this->config['default_expire'] : \Bootphp\Cache\Cache::DEFAULT_EXPIRE;
        }

        // Open directory
        $dir = new \SplFileInfo($directory);

        // If the directory path is not a directory
        if (!$dir->isDir()) {
            $this->makeDirectory($directory, 0777, true);
        }

        // Open file to inspect
        $resouce = new \SplFileInfo($directory . $filename);
        $file = $resouce->openFile('w');

        try {
            $data = $lifetime . "\n" . serialize($data);
            $file->fwrite($data, strlen($data));
            return (bool) $file->fflush();
        } catch (ErrorException $e) {
            // If serialize through an error exception
            if ($e->getCode() === E_NOTICE) {
                // Throw a caching error
                throw new BootphpException(__METHOD__ . ' failed to serialize data for caching with message : ' . $e->getMessage());
            }

            // Else rethrow the error exception
            throw $e;
        }
    }

    /**
     * Delete a cache entry based on id.
     *
     *     // Delete 'foo' entry from the file group
     *     Cache::instance('file')->delete('foo');
     *
     * @param   string  $id Id to remove from cache
     * @return  boolean
     */
    public function delete($id)
    {
        $filename = self::filename($this->sanitizeId($id));
        $directory = $this->resolveDirectory($filename);

        return $this->_delete_file(new \SplFileInfo($directory . $filename), false, true);
    }

    /**
     * Delete all cache entries.
     *
     * Beware of using this method when using shared memory cache systems, as it
     * will wipe every entry within the system for all clients.
     *
     *     // Delete all cache entries in the file group
     *     Cache::instance('file')->deleteAll();
     *
     * @return  boolean
     */
    public function deleteAll()
    {
        return $this->_delete_file($this->cacheDir, true);
    }

    /**
     * Garbage collection method that cleans any expired
     * cache entries from the cache.
     *
     * @return  void
     */
    public function garbageCollect()
    {
        $this->_delete_file($this->cacheDir, true, false, true);
        return;
    }

    /**
     * Deletes files recursively and returns false on any errors
     *
     *     // Delete a file or folder whilst retaining parent directory and ignore all errors
     *     $this->_delete_file($folder, true, true);
     *
     * @param   SplFileInfo $file                   File
     * @param   boolean     $retainParentDirectory  Retain the parent directory
     * @param   boolean     $ignoreErrors           Ignore_errors to prevent all exceptions interrupting exec
     * @param   boolean     $onlyExpired            Only expired files
     * @return  boolean
     * @throws  BootphpException
     */
    protected function _delete_file(\SplFileInfo $file, $retain_parent_directory = false, $ignore_errors = false, $only_expired = false)
    {
        // Allow graceful error handling
        try {
            // If is file
            if ($file->isFile()) {
                try {
                    // Handle ignore files
                    if (in_array($file->getFilename(), $this->config('ignoreOnDelete'))) {
                        $delete = false;
                    }
                    // If only expired is not set
                    elseif ($only_expired === false) {
                        // We want to delete the file
                        $delete = true;
                    }
                    // Otherwise...
                    else {
                        // Assess the file expiry to flag it for deletion
                        $delete = $this->isExpired($file);
                    }

                    // If the delete flag is set delete file
                    if ($delete === true)
                        return unlink($file->getRealPath());
                    else
                        return false;
                } catch (\ErrorException $e) {
                    // Catch any delete file warnings
                    if ($e->getCode() === E_WARNING) {
                        throw new BootphpException(__METHOD__ . ' failed to delete file : :file', array(':file' => $file->getRealPath()));
                    }
                }
            }
            // Else, is directory
            elseif ($file->isDir()) {
                // Create new DirectoryIterator
                $files = new \DirectoryIterator($file->getPathname());

                // Iterate over each entry
                while ($files->valid()) {
                    // Extract the entry name
                    $name = $files->getFilename();

                    // If the name is not a dot
                    if ($name != '.' AND $name != '..') {
                        // Create new file resource
                        $fp = new \SplFileInfo($files->getRealPath());
                        // Delete the file
                        $this->_delete_file($fp, $retain_parent_directory, $ignore_errors, $only_expired);
                    }

                    // Move the file pointer on
                    $files->next();
                }

                // If set to retain parent directory, return now
                if ($retain_parent_directory) {
                    return true;
                }

                try {
                    // Remove the files iterator
                    // (fixes Windows PHP which has permission issues with open iterators)
                    unset($files);

                    // Try to remove the parent directory
                    return rmdir($file->getRealPath());
                } catch (ErrorException $e) {
                    // Catch any delete directory warnings
                    if ($e->getCode() === E_WARNING) {
                        throw new \Bootphp\BootphpException(__METHOD__ . ' failed to delete directory: ' . $file->getRealPath() . '.');
                    }
                    throw $e;
                }
            } else {
                // We get here if a file has already been deleted
                return false;
            }
        }
        // Catch all exceptions
        catch (Exception $e) {
            // If ignore_errors is on
            if ($ignore_errors === true) {
                // Return
                return false;
            }
            // Throw exception
            throw $e;
        }
    }

    /**
     * Resolves the cache directory real path from the filename
     *
     *      // Get the realpath of the cache folder
     *      $realpath = $this->resolveDirectory($filename);
     *
     * @param   string  $filename  filename to resolve
     * @return  string
     */
    protected function resolveDirectory($filename)
    {
        return $this->cacheDir->getRealPath() . DIRECTORY_SEPARATOR . $filename[0] . $filename[1] . DIRECTORY_SEPARATOR;
    }

    /**
     * Makes the cache directory if it doesn't exist. Simply a wrapper for
     * `mkdir` to ensure DRY principles
     *
     * @link    http://php.net/manual/en/function.mkdir.php
     * @param   string    $directory    directory path
     * @param   integer   $mode         chmod mode
     * @param   boolean   $recursive    allows nested directories creation
     * @param   resource  $context      a stream context
     * @return  SplFileInfo
     * @throws  BootphpException
     */
    protected function makeDirectory($directory, $mode = 0777, $recursive = false, $context = null)
    {
        // call mkdir according to the availability of a passed $context param
        $mkdirResult = $context ? mkdir($directory, $mode, $recursive, $context) : mkdir($directory, $mode, $recursive);

        // throw an exception if unsuccessful
        if (!$mkdirResult) {
            throw new BootphpException('Failed to create the defined cache directory: ' . $directory);
        }

        // chmod to solve potential umask issues
        chmod($directory, $mode);

        return new \SplFileInfo($directory);
    }

    /**
     * Test if cache file is expired.
     *
     * @param   SplFileInfo $file   The cache file
     * @return  boolean     True if expired false otherwise
     */
    protected function isExpired(\SplFileInfo $file)
    {
        // Open the file and parse data
        $created = $file->getMTime();
        $data = $file->openFile("r");
        $lifetime = (int) $data->fgets();

        // If we're at the EOF at this point, corrupted!
        if ($data->eof()) {
            throw new BootphpException(__METHOD__ . ' corrupted cache file!');
        }

        // Close file
        $data = null;

        // Test for expiry and return
        return $lifetime !== 0 && $created + $lifetime < time();
    }

}
