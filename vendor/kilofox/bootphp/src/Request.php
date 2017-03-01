<?php

namespace Bootphp;

use Bootphp\Request\Client;

/**
 * Request. Uses the [Route] class to determine what [Controller] to send the request to.
 *
 * @package    Bootphp
 * @category   Base
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class Request
{
    /**
     * @var string  Client user agent
     */
    public static $userAgent = '';

    /**
     * @var string  Client IP address
     */
    public static $clientIp = '0.0.0.0';

    /**
     * @var string  Trusted proxy server IPs
     */
    public static $trustedProxies = ['127.0.0.1', 'localhost', 'localhost.localdomain'];

    /**
     * @var Request Main request instance
     */
    public static $initial;

    /**
     * @var Request Currently executing request instance
     */
    public static $current;

    /**
     * Creates a new request object for the given URI. New requests should be
     * Created using the [Request::factory] method.
     *
     *     $request = Request::factory($uri);
     *
     * If $cache parameter is set, the response for the request will attempt to
     * be retrieved from the cache.
     *
     * @param   string  $uri              URI of the request
     * @param   array   $client_params    An array of params to pass to the request client
     * @param   array   $injected_routes  An array of routes to use, for testing
     * @return  void|Request
     * @throws  BootphpException
     * @uses    Route::all
     * @uses    Route::matches
     */
    public static function factory($uri = true, $client_params = [], $injected_routes = [])
    {
        // If this is the initial request
        if (!self::$initial) {
            if (isset($_SERVER['REQUEST_METHOD'])) {
                // Use the server request method
                $method = $_SERVER['REQUEST_METHOD'];
            } else {
                // Default to GET requests
                $method = 'GET';
            }

            if ((!empty($_SERVER['HTTPS']) && filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN)) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') && in_array($_SERVER['REMOTE_ADDR'], self::$trustedProxies)) {
                // This request is secure
                $secure = true;
            }

            if (isset($_SERVER['HTTP_REFERER'])) {
                // There is a referrer for this request
                $referrer = $_SERVER['HTTP_REFERER'];
            }

            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                // Browser type
                self::$userAgent = $_SERVER['HTTP_USER_AGENT'];
            }

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                // Typically used to denote AJAX requests
                $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'];
            }

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], self::$trustedProxies)) {
                // Use the forwarded IP address, typically set when the
                // client is using a proxy server.
                // Format: "X-Forwarded-For: client1, proxy1, proxy2"
                $clientIps = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                self::$clientIp = array_shift($clientIps);

                unset($clientIps);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], self::$trustedProxies)) {
                // Use the forwarded IP address, typically set when the
                // client is using a proxy server.
                $clientIps = explode(',', $_SERVER['HTTP_CLIENT_IP']);

                self::$clientIp = array_shift($clientIps);

                unset($clientIps);
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                // The remote IP address
                self::$clientIp = $_SERVER['REMOTE_ADDR'];
            }

            if ($method !== 'GET') {
                // Ensure the raw body is saved for future use
                $body = file_get_contents('php://input');
            }

            if ($uri === true) {
                // Attempt to guess the proper URI
                $uri = self::detectUri();
            }

            $cookies = [];

            if (($cookie_keys = array_keys($_COOKIE))) {
                foreach ($cookie_keys as $key) {
                    $cookies[$key] = Cookie::get($key);
                }
            }

            // Create the instance singleton
            self::$initial = $request = new self($uri, $client_params, $injected_routes);

            // Store global GET and POST data in the initial request only
            $request->protocol('HTTP/1.1')->query($_GET)->post($_POST);

            if (isset($secure)) {
                // Set the request security
                $request->secure($secure);
            }

            if (isset($method)) {
                // Set the request method
                $request->method($method);
            }

            if (isset($referrer)) {
                // Set the referrer
                $request->referrer($referrer);
            }

            if (isset($requestedWith)) {
                // Apply the requested with variable
                $request->requestedWith($requestedWith);
            }

            if (isset($body)) {
                // Set the request body (probably a PUT type)
                $request->body($body);
            }

            if (isset($cookies)) {
                $request->cookie($cookies);
            }
        } else {
            $request = new self($uri, $client_params, $injected_routes);
        }

        return $request;
    }

    /**
     * Automatically detects the URI of the main request using PATH_INFO,
     * REQUEST_URI, PHP_SELF or REDIRECT_URL.
     *
     *     $uri = Request::detectUri();
     *
     * @return  string  URI of the main request
     * @throws  BootphpException
     */
    public static function detectUri()
    {
        $uri = isset($_GET['u']) ? $_GET['u'] : '';

        // Get the path from the base URL, including the index file
        $baseUrl = parse_url(Core::$baseUrl, PHP_URL_PATH);

        if (strpos($uri, $baseUrl) === 0) {
            // Remove the base URL from the URI
            $uri = (string) substr($uri, strlen($baseUrl));
        }

        return $uri;
    }

    /**
     * Return the currently executing request. This is changed to the current
     * request when [Request::execute] is called and restored when the request
     * is completed.
     *
     *     $request = Request::current();
     *
     * @return  Request
     */
    public static function current()
    {
        return self::$current;
    }

    /**
     * Returns the first request encountered by this framework. This will should
     * only be set once during the first [Request::factory] invocation.
     *
     *     // Get the first request
     *     $request = Request::initial();
     *
     *     // Test whether the current request is the first request
     *     if (Request::initial() === Request::current())
     *          // Do something useful
     *
     * @return  Request
     */
    public static function initial()
    {
        return self::$initial;
    }

    /**
     * Returns information about the initial user agent.
     *
     * @param   mixed   $value  array or string to return: browser, version, robot, mobile, platform
     * @return  mixed   requested information, false if nothing is found
     * @uses    Request::$userAgent
     * @uses    Text::userAgent
     */
    public static function userAgent($value)
    {
        return Text::userAgent(self::$userAgent, $value);
    }

    /**
     * Determines if a file larger than the post_max_size has been uploaded. PHP
     * does not handle this situation gracefully on its own, so this method
     * helps to solve that problem.
     *
     * @return  boolean
     * @uses    Num::bytes
     * @uses    Arr::get
     */
    public static function postMaxSizeExceeded()
    {
        // Make sure the request method is POST
        if (self::$initial->method() !== 'POST')
            return false;

        // Get the post_max_size in bytes
        $max_bytes = Num::bytes(ini_get('post_max_size'));

        // Error occurred if method is POST, and content length is too long
        return (Arr::get($_SERVER, 'CONTENT_LENGTH') > $max_bytes);
    }

    /**
     * Process a request to find a matching route
     *
     * @param   object  $request Request
     * @param   array   $routes  Route
     * @return  array
     */
    public static function process(Request $request, $routes = null)
    {
        // Load routes
        $routes = empty($routes) ? Route::all() : $routes;
        $params = null;

        foreach ($routes as $name => $route) {
            // Use external routes for reverse routing only
            if ($route->is_external()) {
                continue;
            }

            // We found something suitable
            if ($params = $route->matches($request)) {
                return [
                    'params' => $params,
                    'route' => $route,
                ];
            }
        }

        return null;
    }

    /**
     * @var string  The x-requested-with header which most likely will be xmlhttprequest.
     */
    protected $requestedWith;

    /**
     * Method: GET, POST, PUT, DELETE, HEAD, etc.
     *
     * @var string
     */
    protected $_method = 'GET';

    /**
     * Protocol: HTTP/1.1, FTP, CLI, etc.
     *
     * @var string
     */
    protected $_protocol;

    /**
     * @var boolean
     */
    protected $_secure = false;

    /**
     * Referring URL.
     *
     * @var string
     */
    protected $_referrer;

    /**
     * Route matched for this request.
     *
     * @var Route
     */
    protected $_route;

    /**
     * Array of routes to manually look at instead of the global namespace.
     *
     * @var Route
     */
    protected $_routes;

    /**
     * Headers to sent as part of the request.
     *
     * @var HTTP\Header
     */
    protected $_header;

    /**
     * The body.
     *
     * @var string
     */
    protected $_body;

    /**
     * Controller directory.
     *
     * @var string
     */
    protected $_directory = '';

    /**
     * Controller to be executed.
     *
     * @var string
     */
    protected $_controller;

    /**
     * Action to be executed in the controller.
     *
     * @var string
     */
    protected $_action;

    /**
     * The URI of the request.
     *
     * @var string
     */
    protected $_uri;

    /**
     * External request.
     *
     * @var boolean
     */
    protected $_external = false;

    /**
     * Parameters from the route.
     *
     * @var array
     */
    protected $_params = [];

    /**
     * Query parameters.
     *
     * @var array
     */
    protected $_get = [];

    /**
     * Post parameters.
     *
     * @var array
     */
    protected $_post = [];

    /**
     * Cookies to send with the request.
     *
     * @var array
     */
    protected $_cookies = [];

    /**
     * @var Client
     */
    protected $_client;

    /**
     * Creates a new request object for the given URI. New requests should be
     * Created using the [Request::factory] method.
     *
     *     $request = new Request($uri);
     *
     * If $cache parameter is set, the response for the request will attempt to
     * be retrieved from the cache.
     *
     * @param   string  $uri                URI of the request
     * @param   array   $client_params      Array of params to pass to the request client
     * @param   array   $injected_routes    An array of routes to use, for testing
     * @return  void
     * @throws  BootphpException
     * @uses    Route::all
     * @uses    Route::matches
     */
    public function __construct($uri, $client_params = [], $injected_routes = [])
    {
        $client_params = is_array($client_params) ? $client_params : [];

        // Initialise the header
        $this->_header = new HTTP\Header([]);

        // Assign injected routes
        $this->_routes = $injected_routes;

        // Cleanse query parameters from URI (faster that parse_url())
        $split_uri = explode('?', $uri);
        $uri = array_shift($split_uri);

        if ($split_uri) {
            parse_str($split_uri[0], $this->_get);
        }

        // Detect protocol (if present)
        if (strpos($uri, '://') === false) {
            // Remove leading and trailing slashes from the URI
            $this->_uri = trim($uri, '/');

            // Apply the client
            $this->_client = new Request\Client\Internal($client_params);
        } else {
            // Create a route
            $this->_route = new Route($uri);

            // Store the URI
            $this->_uri = $uri;

            // Set the security setting if required
            if (strpos($uri, 'https://') === 0) {
                $this->secure(true);
            }

            // Set external state
            $this->_external = true;

            // Setup the client
            $this->_client = Client_External::factory($client_params);
        }
    }

    /**
     * Returns the response as the string representation of a request.
     *
     *     echo $request;
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Sets and gets the uri from the request.
     *
     * @param   string  $uri
     * @return  mixed
     */
    public function uri($uri = null)
    {
        if ($uri === null) {
            // Act as a getter
            return ($this->_uri === '') ? '/' : $this->_uri;
        }

        // Act as a setter
        $this->_uri = $uri;

        return $this;
    }

    /**
     * Create a URL string from the current request. This is a shortcut for:
     *
     *     echo URL::site($this->request->uri(), $protocol);
     *
     * @param   mixed    $protocol  protocol string or Request object
     * @return  string
     * @uses    URL::site
     */
    public function url($protocol = null)
    {
        if ($this->is_external()) {
            // If it's an external request return the URI
            return $this->uri();
        }

        // Create a URI with the current route, convert to a URL and returns
        return URL::site($this->uri(), $protocol);
    }

    /**
     * Retrieves a value from the route parameters.
     *
     *     $id = $request->param('id');
     *
     * @param   string  $key        Key of the value
     * @param   mixed   $default    Default value if the key is not set
     * @return  mixed
     */
    public function param($key = null, $default = null)
    {
        if ($key === null) {
            // Return the full array
            return $this->_params;
        }

        return isset($this->_params[$key]) ? $this->_params[$key] : $default;
    }

    /**
     * Sets and gets the referrer from the request.
     *
     * @param   string  $referrer
     * @return  mixed
     */
    public function referrer($referrer = null)
    {
        if ($referrer === null) {
            // Act as a getter
            return $this->_referrer;
        }

        // Act as a setter
        $this->_referrer = (string) $referrer;

        return $this;
    }

    /**
     * Sets and gets the route from the request.
     *
     * @param   string  $route
     * @return  mixed
     */
    public function route(Route $route = null)
    {
        if ($route === null) {
            // Act as a getter
            return $this->_route;
        }

        // Act as a setter
        $this->_route = $route;

        return $this;
    }

    /**
     * Sets and gets the directory for the controller.
     *
     * @param   string  $directory  Directory to execute the controller from
     * @return  mixed
     */
    public function directory($directory = null)
    {
        if ($directory === null) {
            // Act as a getter
            return $this->_directory;
        }

        // Act as a setter
        $this->_directory = (string) $directory;

        return $this;
    }

    /**
     * Sets and gets the controller for the matched route.
     *
     * @param   string  $controller Controller to execute the action
     * @return  mixed
     */
    public function controller($controller = null)
    {
        if ($controller === null) {
            // Act as a getter
            return $this->_controller;
        }

        // Act as a setter
        $this->_controller = (string) $controller;

        return $this;
    }

    /**
     * Sets and gets the action for the controller.
     *
     * @param   string  $action Action to execute the controller from
     * @return  mixed
     */
    public function action($action = null)
    {
        if ($action === null) {
            // Act as a getter
            return $this->_action;
        }

        // Act as a setter
        $this->_action = (string) $action;

        return $this;
    }

    /**
     * Provides access to the [Client].
     *
     * @return  Client
     * @return  self
     */
    public function client(Client $client = null)
    {
        if ($client === null)
            return $this->_client;
        else {
            $this->_client = $client;
            return $this;
        }
    }

    /**
     * Gets and sets the requested with property, which should
     * be relative to the x-requested-with pseudo header.
     *
     * @param   string  $requestedWith  Requested with value
     * @return  mixed
     */
    public function requestedWith($requestedWith = null)
    {
        if ($requestedWith === null) {
            // Act as a getter
            return $this->requestedWith;
        }

        // Act as a setter
        $this->requestedWith = strtolower($requestedWith);

        return $this;
    }

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
     * @return  Response
     * @throws  BootphpException
     * @uses    [Core::$profiling]
     * @uses    [Profiler]
     */
    public function execute()
    {
        if (!$this->_external) {
            $processed = self::process($this, $this->_routes);

            if ($processed) {
                // Store the matching route
                $this->_route = $processed['route'];
                $params = $processed['params'];

                // Is this route external?
                $this->_external = $this->_route->is_external();

                if (isset($params['directory'])) {
                    // Controllers are in a sub-directory
                    $this->_directory = $params['directory'];
                }

                // Store the controller
                $this->_controller = isset($params['controller']) ? $params['controller'] : 'index';

                // Store the action
                $this->_action = isset($params['action']) ? $params['action'] : 'index';

                // These are accessible as public vars and can be overloaded
                unset($params['controller'], $params['action'], $params['directory']);

                // Params cannot be changed once matched
                $this->_params = $params;
            }
        }

        if (!$this->_route instanceof Route) {
            return HTTP_Exception::factory(404, 'Unable to find a route to match the URI: :uri', array(
                        ':uri' => $this->_uri,
                    ))->request($this)
                    ->get_response();
        }

        if (!$this->_client instanceof Client) {
            throw new \Bootphp\BootphpException('Unable to execute ' . $this->_uri . ' without a Kohana_Client');
        }

        return $this->_client->execute($this);
    }

    /**
     * Returns whether this request is the initial request Kohana received.
     * Can be used to test for sub requests.
     *
     *     if (!$request->is_initial())
     *         // This is a sub request
     *
     * @return  boolean
     */
    public function is_initial()
    {
        return $this === self::$initial;
    }

    /**
     * Readonly access to the [Request::$_external] property.
     *
     *     if (!$request->is_external())
     *          // This is an internal request
     *
     * @return  boolean
     */
    public function is_external()
    {
        return $this->_external;
    }

    /**
     * Returns whether this is an ajax request (as used by JS frameworks)
     *
     * @return  boolean
     */
    public function isAjax()
    {
        return $this->requestedWith() === 'xmlhttprequest';
    }

    /**
     * Gets or sets the HTTP method. Usually GET, POST, PUT or DELETE in
     * traditional CRUD applications.
     *
     * @param   string   $method  Method to use for this request
     * @return  mixed
     */
    public function method($method = null)
    {
        if ($method === null) {
            // Act as a getter
            return $this->_method;
        }

        // Act as a setter
        $this->_method = strtoupper($method);

        return $this;
    }

    /**
     * Gets or sets the HTTP protocol. If there is no current protocol set,
     * it will use the default set in HTTP::$protocol
     *
     * @param   string   $protocol  Protocol to set to the request
     * @return  mixed
     */
    public function protocol($protocol = null)
    {
        if ($protocol === null) {
            if ($this->_protocol)
                return $this->_protocol;
            else
                return $this->_protocol = HTTP::$protocol;
        }

        // Act as a setter
        $this->_protocol = strtoupper($protocol);
        return $this;
    }

    /**
     * Getter/Setter to the security settings for this request. This
     * method should be treated as immutable.
     *
     * @param   boolean $secure is this request secure?
     * @return  mixed
     */
    public function secure($secure = null)
    {
        if ($secure === null)
            return $this->_secure;

        // Act as a setter
        $this->_secure = (bool) $secure;
        return $this;
    }

    /**
     * Gets or sets HTTP headers oo the request. All headers
     * are included immediately after the HTTP protocol definition during
     * transmission. This method provides a simple array or key/value
     * interface to the headers.
     *
     * @param   mixed   $key   Key or array of key/value pairs to set
     * @param   string  $value Value to set to the supplied key
     * @return  mixed
     */
    public function headers($key = null, $value = null)
    {
        if ($key instanceof HTTP\Header) {
            // Act a setter, replace all headers
            $this->_header = $key;

            return $this;
        }

        if (is_array($key)) {
            // Act as a setter, replace all headers
            $this->_header->exchangeArray($key);

            return $this;
        }

        if ($this->_header->count() === 0 && $this->is_initial()) {
            // Lazy load the request headers
            $this->_header = HTTP::request_headers();
        }

        if ($key === null) {
            // Act as a getter, return all headers
            return $this->_header;
        } elseif ($value === null) {
            // Act as a getter, single header
            return ($this->_header->offsetExists($key)) ? $this->_header->offsetGet($key) : null;
        }

        // Act as a setter for a single header
        $this->_header[$key] = $value;

        return $this;
    }

    /**
     * Set and get cookies values for this request.
     *
     * @param   mixed    $key    Cookie name, or array of cookie values
     * @param   string   $value  Value to set to cookie
     * @return  string
     * @return  mixed
     */
    public function cookie($key = null, $value = null)
    {
        if (is_array($key)) {
            // Act as a setter, replace all cookies
            $this->_cookies = $key;
            return $this;
        } elseif ($key === null) {
            // Act as a getter, all cookies
            return $this->_cookies;
        } elseif ($value === null) {
            // Act as a getting, single cookie
            return isset($this->_cookies[$key]) ? $this->_cookies[$key] : null;
        }

        // Act as a setter for a single cookie
        $this->_cookies[$key] = (string) $value;

        return $this;
    }

    /**
     * Gets or sets the HTTP body of the request. The body is
     * included after the header, separated by a single empty new line.
     *
     * @param   string  $content Content to set to the object
     * @return  mixed
     */
    public function body($content = null)
    {
        if ($content === null) {
            // Act as a getter
            return $this->_body;
        }

        // Act as a setter
        $this->_body = $content;

        return $this;
    }

    /**
     * Returns the length of the body for use with
     * content header
     *
     * @return  integer
     */
    public function content_length()
    {
        return strlen($this->body());
    }

    /**
     * Renders the HTTP_Interaction to a string, producing
     *
     *  - Protocol
     *  - Headers
     *  - Body
     *
     *  If there are variables set to the `Kohana_Request::$_post`
     *  they will override any values set to body.
     *
     * @return  string
     */
    public function render()
    {
        if (!$post = $this->post()) {
            $body = $this->body();
        } else {
            $body = http_build_query($post, null, '&');
            $this->body($body)
                ->headers('content-type', 'application/x-www-form-urlencoded; charset=' . Core::$charset);
        }

        // Set the content length
        $this->headers('content-length', (string) $this->content_length());

        // If Kohana expose, set the user-agent
        if (Core::$expose) {
            $this->headers('user-agent', Core::version());
        }

        // Prepare cookies
        if ($this->_cookies) {
            $cookie_string = [];

            // Parse each
            foreach ($this->_cookies as $key => $value) {
                $cookie_string[] = $key . '=' . $value;
            }

            // Create the cookie string
            $this->_header['cookie'] = implode('; ', $cookie_string);
        }

        $output = $this->method() . ' ' . $this->uri() . ' ' . $this->protocol() . "\r\n";
        $output .= (string) $this->_header;
        $output .= $body;

        return $output;
    }

    /**
     * Gets or sets HTTP query string.
     *
     * @param   mixed   $key    Key or key value pairs to set
     * @param   string  $value  Value to set to a key
     * @return  mixed
     * @uses    Arr::path
     */
    public function query($key = null, $value = null)
    {
        if (is_array($key)) {
            // Act as a setter, replace all query strings
            $this->_get = $key;

            return $this;
        }

        if ($key === null) {
            // Act as a getter, all query strings
            return $this->_get;
        } elseif ($value === null) {
            // Act as a getter, single query string
            return Arr::path($this->_get, $key);
        }

        // Act as a setter, single query string
        $this->_get[$key] = $value;

        return $this;
    }

    /**
     * Gets or sets HTTP POST parameters to the request.
     *
     * @param   mixed  $key    Key or key value pairs to set
     * @param   string $value  Value to set to a key
     * @return  mixed
     * @uses    Arr::path
     */
    public function post($key = null, $value = null)
    {
        if (is_array($key)) {
            // Act as a setter, replace all fields
            $this->_post = $key;

            return $this;
        }

        if ($key === null) {
            // Act as a getter, all fields
            return $this->_post;
        } elseif ($value === null) {
            // Act as a getter, single field
            return isset($this->_post[$key]) ? $this->_post[$key] : null;
        }

        // Act as a setter, single field
        $this->_post[$key] = $value;

        return $this;
    }

}
