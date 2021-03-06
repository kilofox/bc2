<?php

namespace Bootphp\Request\Client;

/**
 * [Request_Client_External] provides a wrapper for all external request
 * processing. This class should be extended by all drivers handling external
 * requests.
 *
 * Supported out of the box:
 *  - Curl (default)
 *  - PECL HTTP
 *  - Streams
 *
 * To select a specific external driver to use as the default driver, set the
 * following property within the Application bootstrap. Alternatively, the
 * client can be injected into the request object.
 *
 * @example
 *
 *       // In application bootstrap
 *       Request_Client_External::$client = 'Request_Client_Stream';
 *
 *       // Add client to request
 *       $request = Request::factory('http://some.host.tld/foo/bar')
 *           ->client(Request_Client_External::factory('Request_Client_HTTP));
 *
 * @package    Bootphp
 * @category   Base
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 * @uses       [PECL HTTP](http://php.net/manual/en/book.http.php)
 */
abstract class External extends Bootphp\Request\Client
{
    /**
     * Use:
     *  - Bootphp\Request\Client\Curl (default)
     *  - Bootphp\Request\Client\HTTP
     *  - Bootphp\Request\Client\Stream
     *
     * @var     string  Defines the external client to use by default
     */
    public static $client = 'Curl';

    /**
     * Factory method to create a new Request_Client_External object based on
     * the client name passed, or defaulting to Request_Client_External::$client
     * by default.
     *
     * Bootphp\Request\Client\External::$client can be set in the application bootstrap.
     *
     * @param   array   $params Parameters to pass to the client
     * @param   string  $client External client to use
     * @return  Bootphp\Request\Client\External
     * @throws  BootphpException
     */
    public static function factory(array $params = [], $client = null)
    {
        if ($client === null) {
            $client = self::$client;
        }

        $client = new $client($params);

        if (!$client instanceof External) {
            throw new BootphpException('Selected client is not a Bootphp\Request\Client\External object.');
        }

        return $client;
    }

    /**
     * @var     array   Curl options
     * @link    http://www.php.net/manual/function.curl-setopt
     * @link    http://www.php.net/manual/http.request.options
     */
    protected $_options = [];

    /**
     * Processes the request, executing the controller action that handles this
     * request, determined by the [Route].
     *
     * 1. Before the controller action is called, the [Controller::before] method
     * will be called.
     * 2. Next the controller action will be called.
     * 3. After the controller action is called, the [Controller::after] method
     * will be called.
     *
     * By default, the output from the controller is captured and returned, and
     * no headers are sent.
     *
     *     $request->execute();
     *
     * @param   Request   $request   A request object
     * @param   Response  $response  A response object
     * @return  Response
     * @throws  BootphpException
     */
    public function execute_request(Request $request, Response $response)
    {
        // Store the current active request and replace current with new request
        $previous = Request::$current;
        Request::$current = $request;

        // Resolve the POST fields
        if ($post = $request->post()) {
            $request->body(http_build_query($post, null, '&'))
                ->headers('content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
        }

        $request->headers('content-length', (string) $request->contentLength());

        try {
            $response = $this->_send_message($request, $response);
        } catch (\Exception $e) {
            // Restore the previous request
            Request::$current = $previous;

            // Re-throw the exception
            throw $e;
        }

        // Restore the previous request
        Request::$current = $previous;

        // Return the response
        return $response;
    }

    /**
     * Set and get options for this request.
     *
     * @param   mixed    $key    Option name, or array of options
     * @param   mixed    $value  Option value
     * @return  mixed
     * @return  Bootphp\Request\Client\External
     */
    public function options($key = null, $value = null)
    {
        if ($key === null)
            return $this->_options;

        if (is_array($key)) {
            $this->_options = $key;
        } elseif ($value === null) {
            return Arr::get($this->_options, $key);
        } else {
            $this->_options[$key] = $value;
        }

        return $this;
    }

    /**
     * Sends the HTTP message [Request] to a remote server and processes
     * the response.
     *
     * @param   Request   $request    Request to send
     * @param   Response  $response   Response to send
     * @return  Response
     */
    abstract protected function _send_message(Request $request, Response $response);
}
