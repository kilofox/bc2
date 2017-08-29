<?php

namespace Bootphp;

use Bootphp\BootphpException;
use Bootphp\Profiler\Profiler;
use Bootphp\Http\Http;
use Bootphp\Http\Header;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

/**
 * Request. Uses the [Route] class to determine what [Controller] to send the request to.
 *
 * @package    Bootphp
 * @category   Base
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class Request implements ServerRequestInterface {

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
     * created using the [Request::factory] method.
     *
     *     $request = Request::factory($uri);
     *
     * If $cache parameter is set, the response for the request will attempt to
     * be retrieved from the cache.
     *
     * @param   string  $uri            URI of the request
     * @param   array   $clientParams   An array of params to pass to the request client
     * @param   array   $injectedRoutes An array of routes to use, for testing
     * @return  void|Request
     * @throws  BootphpException
     * @uses    Route::all
     * @uses    Route::matches
     */
    public static function factory($uri = true, $clientParams = [], $injectedRoutes = []) {
        // If this is the initial request
        if (!self::$initial) {
            // Use the server request method, default to GET requests
            $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';

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
            self::$initial = $request = new self($uri, $clientParams, $injectedRoutes);

            // Store global GET and POST data in the initial request only
            $request
                    //->setProtocolVersion('1.1')
                    ->withQueryParams($_GET)->post($_POST);

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
                $request->body = $body;
            }

            if (isset($cookies)) {
                $request->withCookieParams($cookies);
            }
        } else {
            $request = new self($uri, $clientParams, $injectedRoutes);
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
    public static function detectUri() {
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
    public static function current() {
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
    public static function initial() {
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
    public static function userAgent($value) {
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
    public static function postMaxSizeExceeded() {
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
    public static function process(Request $request, $routes = null) {
        // Load routes
        $routes = empty($routes) ? Route::all() : $routes;
        $params = null;

        foreach ($routes as $name => $route) {
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
    protected $method = 'GET';

    /**
     * @var boolean
     */
    protected $secure = false;

    /**
     * Referring URL.
     *
     * @var string
     */
    protected $referrer;

    /**
     * Route matched for this request.
     *
     * @var Route
     */
    protected $route;

    /**
     * Array of routes to manually look at instead of the global namespace.
     *
     * @var Route
     */
    protected $routes;

    /**
     * Headers to sent as part of the request.
     *
     * @var HTTP\Header
     */
    protected $header;

    /**
     * The body.
     *
     * @var string
     */
    protected $body;

    /**
     * Controller directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * Controller to be executed.
     *
     * @var string
     */
    protected $controller;

    /**
     * Action to be executed in the controller.
     *
     * @var string
     */
    protected $action;

    /**
     * The URI of the request.
     *
     * @var string
     */
    protected $uri;

    /**
     * Parameters from the route.
     *
     * @var array
     */
    protected $parms = [];

    /**
     * Parameters from the route.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * A list of properties that emulated by the PSR7 attribute methods.
     *
     * @var array
     */
    protected $emulatedAttributes = ['session', 'webroot', 'base', 'params'];

    /**
     * Query parameters.
     *
     * @var array
     */
    protected $query = [];

    /**
     * Post parameters.
     *
     * @var array
     */
    protected $post = [];

    /**
     * Post parameters.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Cookies to send with the request.
     *
     * @var array
     */
    protected $cookies = [];

    /**
     * Callbacks to use when response contains given headers.
     *
     * @var array
     */
    protected $headerCallbacks = [
        'Location' => 'self::onHeaderLocation'
    ];

    /**
     * Headers to preserve when following a redirect.
     *
     * @var array
     */
    protected $followHeaders = ['authorization'];

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
     * @param   array   $clientParams      Array of params to pass to the request client
     * @param   array   $injectedRoutes    An array of routes to use, for testing
     * @return  void
     * @throws  BootphpException
     * @uses    Route::all
     * @uses    Route::matches
     */
    public function __construct($uri, $clientParams = [], $injectedRoutes = []) {
        $clientParams = is_array($clientParams) ? $clientParams : [];

        // Initialise the header
        $this->header = new Header([]);

        // Assign injected routes
        $this->routes = $injectedRoutes;

        // Cleanse query parameters from URI (faster that parse_url())
        $splitUri = explode('?', $uri);
        $uri = array_shift($splitUri);

        if ($splitUri) {
            parse_str($splitUri[0], $this->query);
        }

        // Remove leading and trailing slashes from the URI
        $this->uri = trim($uri, '/');
    }

    /**
     * Returns the response as the string representation of a request.
     *
     *     echo $request;
     *
     * @return  string
     */
    public function __toString() {
        return $this->render();
    }

    /**
     * Sets and gets the uri from the request.
     *
     * @param   string  $uri
     * @return  mixed
     */
    public function getUri($uri = null) {
        if ($uri === null) {
            // Act as a getter
            return $this->uri === '' ? '/' : $this->uri;
        }

        // Act as a setter
        $this->uri = $uri;

        return $this;
    }

    /**
     * Sets and gets the uri from the request.
     *
     * @param   string  $uri
     * @return  mixed
     */
    public function withUri(UriInterface $uri, $preserveHost = false) {
        if ($uri === null) {
            // Act as a getter
            return $this->uri === '' ? '/' : $this->uri;
        }

        // Act as a setter
        $this->uri = $uri;

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
    public function url($protocol = null) {
        // Create a URI with the current route, convert to a URL and returns
        return URL::site($this->uri(), $protocol);
    }

    /**
     * Retrieves a value from the route parameters.
     *
     * @param   string  $key        Key of the value.
     * @param   mixed   $default    Default value if the key is not set.
     * @return  mixed
     */
    public function getParam($key, $default = null) {
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }

    /**
     * Read an attribute from the request, or get the default
     *
     * @param   string  $name       The attribute name.
     * @param   mixed   $default    The default value if the attribute has not been set.
     * @return  mixed
     */
    public function getAttribute($name, $default = null) {
        if (in_array($name, $this->emulatedAttributes, true)) {
            return $this->{$name};
        }
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        return $default;
    }

    /**
     * Get all the attributes in the request.
     *
     * This will include the params, webroot, and base attributes that Bootphp
     * provides.
     *
     * @return array
     */
    public function getAttributes() {
        $emulated = [
            'params' => $this->params,
            'webroot' => $this->webroot,
            'base' => $this->base
        ];

        return $this->attributes + $emulated;
    }

    /**
     * Return an instance with the specified request attribute.
     *
     * @param   string  $name   The attribute name
     * @param   mixed   $value  The value of the attribute
     * @return  Request
     */
    public function withAttribute($name, $value) {
        if (in_array($name, $this->emulatedAttributes, true)) {
            $this->{$name} = $value;
        } else {
            $this->attributes[$name] = $value;
        }

        return $this;
    }

    /**
     * Return an instance without the specified request attribute.
     *
     * @param   string  $name   The attribute name
     * @return  Request
     * @throws  BootphpException
     */
    public function withoutAttribute($name) {
        if (in_array($name, $this->emulatedAttributes, true)) {
            throw new BootphpException("You cannot unset '$name'. It is a required Bootphp attribute.");
        }
        unset($this->attributes[$name]);

        return $this;
    }

    /**
     * Retrieve server parameters.
     *
     * @return  mixed
     */
    public function getServerParams() {
        return $this->environment;
    }

    /**
     * Sets and gets the referrer from the request.
     *
     * @param   string  $referrer
     * @return  mixed
     */
    public function referrer($referrer = null) {
        if ($referrer === null) {
            // Act as a getter
            return $this->referrer;
        }

        // Act as a setter
        $this->referrer = (string) $referrer;

        return $this;
    }

    /**
     * Sets and gets the route from the request.
     *
     * @param   string  $route
     * @return  mixed
     */
    public function route(Route $route = null) {
        if ($route === null) {
            // Act as a getter
            return $this->route;
        }

        // Act as a setter
        $this->route = $route;

        return $this;
    }

    /**
     * Sets and gets the directory for the controller.
     *
     * @param   string  $directory  Directory to execute the controller from
     * @return  mixed
     */
    public function directory($directory = null) {
        if ($directory === null) {
            // Act as a getter
            return $this->directory;
        }

        // Act as a setter
        $this->directory = (string) $directory;

        return $this;
    }

    /**
     * Sets and gets the controller for the matched route.
     *
     * @param   string  $controller Controller to execute the action
     * @return  mixed
     */
    public function controller($controller = null) {
        if ($controller === null) {
            // Act as a getter
            return $this->controller;
        }

        // Act as a setter
        $this->controller = (string) $controller;

        return $this;
    }

    /**
     * Sets and gets the action for the controller.
     *
     * @param   string  $action Action to execute the controller from
     * @return  mixed
     */
    public function action($action = null) {
        if ($action === null) {
            // Act as a getter
            return $this->action;
        }

        // Act as a setter
        $this->action = (string) $action;

        return $this;
    }

    /**
     * Gets and sets the requested with property, which should
     * be relative to the x-requested-with pseudo header.
     *
     * @param   string  $requestedWith  Requested with value
     * @return  mixed
     */
    public function requestedWith($requestedWith = null) {
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
    public function execute() {
        $processed = self::process($this, $this->routes);

        if ($processed) {
            // Store the matching route
            $this->route = $processed['route'];
            $params = $processed['params'];

            if (isset($params['directory'])) {
                // Controllers are in a sub-directory
                $this->directory = $params['directory'];
            }

            // Store the controller
            $this->controller = isset($params['controller']) ? $params['controller'] : 'index';

            // Store the action
            $this->action = isset($params['action']) ? $params['action'] : 'index';

            // These are accessible as public vars and can be overloaded
            unset($params['controller'], $params['action'], $params['directory']);

            // Params cannot be changed once matched
            $this->params = $params;
            foreach ($params as $key => $attr) {
                $this->withAttribute($key, $attr);
            }
        }

        if (!$this->route instanceof Route) {
            throw new BootphpException('Unable to find a route to match the URI: ' . $this->uri, 404);
        }

        // Execute the request and pass the currently used protocol
        $origResponse = $response = Response::factory(array('_protocol' => 'HTTP/' . $this->getProtocolVersion()));

        // Directory
        $directory = $this->directory();

        // Controller
        $controller = $this->controller();

        if ($directory) {
            // Add the directory name to the class suffix
            $directory .= '\\';
        }

        if (\Bootphp\Core::$profiling) {
            // Set the benchmark name
            $benchmark = '"' . $this->getUri() . '"';

            // Start benchmarking
            $benchmark = Profiler::start('Requests', $benchmark);
        }

        $controller = 'App\\Controller\\' . $directory . ucfirst($controller) . 'Controller';

        try {
            if (!class_exists($controller)) {
                throw new \Bootphp\BootphpException('The requested URL ' . $this->getUri() . ' was not found on this server.', 404);
            }

            // Load the controller using reflection
            $class = new \ReflectionClass($controller);

            if ($class->isAbstract()) {
                throw new BootphpException('Cannot create instances of abstract ' . $controller . '.');
            }

            // Create a new instance of the controller
            $controller = $class->newInstance($this, $response);

            // Run the controller's execute() method
            $response = $class->getMethod('execute')->invoke($controller);

            if (!$response instanceof Response) {
                // Controller failed to return a Response.
                throw new BootphpException('Controller failed to return a Response.');
            }
        } catch (\Exception $e) {
            // Generate an appropriate Response object
            $response = BootphpException::handler($e);
        }

        if (isset($benchmark)) {
            // Stop the benchmark
            Profiler::stop($benchmark);
        }

        return $response;
    }

    /**
     * Returns whether this request is the initial request Kohana received.
     * Can be used to test for sub requests.
     *
     *     if (!$request->isInitial())
     *         // This is a sub request
     *
     * @return  boolean
     */
    public function isInitial() {
        return $this === self::$initial;
    }

    /**
     * Returns whether this is an ajax request (as used by JS frameworks).
     *
     * @return  boolean
     */
    public function isAjax() {
        return $this->requestedWith() === 'xmlhttprequest';
    }

    /**
     * Gets or sets the HTTP method. Usually GET, POST, PUT or DELETE in
     * traditional CRUD applications.
     *
     * @param   string   $method  Method to use for this request
     * @return  mixed
     */
    public function method($method = null) {
        if ($method === null) {
            // Act as a getter
            return $this->method;
        }

        // Act as a setter
        $this->method = strtoupper($method);

        return $this;
    }

    /**
     * Gets the HTTP protocol version.
     *
     * @return  string  HTTP protocol version
     */
    public function getProtocolVersion() {
        return \Bootphp\Http\Http::$protocol;
    }

    /**
     * Gets the HTTP protocol version.
     *
     * @return  string  HTTP protocol version
     */
    public function withProtocolVersion($version) {
        return \Bootphp\Http\Http::$protocol;
    }

    /**
     * Getter/Setter to the security settings for this request. This
     * method should be treated as immutable.
     *
     * @param   boolean $secure is this request secure?
     * @return  mixed
     */
    public function secure($secure = null) {
        if ($secure === null)
            return $this->secure;

        // Act as a setter
        $this->secure = (bool) $secure;

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
    public function hasHeader($key = null, $value = null) {
        if ($key instanceof HTTP\Header) {
            // Act a setter, replace all headers
            $this->header = $key;

            return $this;
        }

        if (is_array($key)) {
            // Act as a setter, replace all headers
            $this->header->exchangeArray($key);

            return $this;
        }

        if ($this->header->count() === 0 && $this->isInitial()) {
            // Lazy load the request headers
            $this->header = HTTP::requestHeaders();
        }

        if ($key === null) {
            // Act as a getter, return all headers
            return $this->header;
        } elseif ($value === null) {
            // Act as a getter, single header
            return ($this->header->offsetExists($key)) ? $this->header->offsetGet($key) : null;
        }

        // Act as a setter for a single header
        $this->header[$key] = $value;

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
    public function getHeader($key = null, $value = null) {
        if ($key instanceof HTTP\Header) {
            // Act a setter, replace all headers
            $this->header = $key;

            return $this;
        }

        if (is_array($key)) {
            // Act as a setter, replace all headers
            $this->header->exchangeArray($key);

            return $this;
        }

        if ($this->header->count() === 0 && $this->isInitial()) {
            // Lazy load the request headers
            $this->header = HTTP::requestHeaders();
        }

        if ($key === null) {
            // Act as a getter, return all headers
            return $this->header;
        } elseif ($value === null) {
            // Act as a getter, single header
            return ($this->header->offsetExists($key)) ? $this->header->offsetGet($key) : null;
        }

        // Act as a setter for a single header
        $this->header[$key] = $value;

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
    public function getHeaderLine($key = null, $value = null) {
        if ($key instanceof HTTP\Header) {
            // Act a setter, replace all headers
            $this->header = $key;

            return $this;
        }

        if (is_array($key)) {
            // Act as a setter, replace all headers
            $this->header->exchangeArray($key);

            return $this;
        }

        if ($this->header->count() === 0 && $this->isInitial()) {
            // Lazy load the request headers
            $this->header = HTTP::requestHeaders();
        }

        if ($key === null) {
            // Act as a getter, return all headers
            return $this->header;
        } elseif ($value === null) {
            // Act as a getter, single header
            return ($this->header->offsetExists($key)) ? $this->header->offsetGet($key) : null;
        }

        // Act as a setter for a single header
        $this->header[$key] = $value;

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
    public function withHeader($key = null, $value = null) {
        $this->header[$key] = $value;

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
    public function withAddedHeader($key = null, $value = null) {
        if ($key instanceof HTTP\Header) {
            // Act a setter, replace all headers
            $this->header = $key;

            return $this;
        }

        if (is_array($key)) {
            // Act as a setter, replace all headers
            $this->header->exchangeArray($key);

            return $this;
        }

        if ($this->header->count() === 0 && $this->isInitial()) {
            // Lazy load the request headers
            $this->header = HTTP::requestHeaders();
        }

        if ($key === null) {
            // Act as a getter, return all headers
            return $this->header;
        } elseif ($value === null) {
            // Act as a getter, single header
            return ($this->header->offsetExists($key)) ? $this->header->offsetGet($key) : null;
        }

        // Act as a setter for a single header
        $this->header[$key] = $value;

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
    public function withoutHeader($key = null, $value = null) {
        if ($key instanceof HTTP\Header) {
            // Act a setter, replace all headers
            $this->header = $key;

            return $this;
        }

        if (is_array($key)) {
            // Act as a setter, replace all headers
            $this->header->exchangeArray($key);

            return $this;
        }

        if ($this->header->count() === 0 && $this->isInitial()) {
            // Lazy load the request headers
            $this->header = HTTP::requestHeaders();
        }

        if ($key === null) {
            // Act as a getter, return all headers
            return $this->header;
        } elseif ($value === null) {
            // Act as a getter, single header
            return ($this->header->offsetExists($key)) ? $this->header->offsetGet($key) : null;
        }

        // Act as a setter for a single header
        $this->header[$key] = $value;

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
    public function getHeaders($key = null, $value = null) {
        if ($key instanceof HTTP\Header) {
            // Act a setter, replace all headers
            $this->header = $key;

            return $this;
        }

        if (is_array($key)) {
            // Act as a setter, replace all headers
            $this->header->exchangeArray($key);

            return $this;
        }

        if ($this->header->count() === 0 && $this->isInitial()) {
            // Lazy load the request headers
            $this->header = HTTP::requestHeaders();
        }

        if ($key === null) {
            // Act as a getter, return all headers
            return $this->header;
        } elseif ($value === null) {
            // Act as a getter, single header
            return ($this->header->offsetExists($key)) ? $this->header->offsetGet($key) : null;
        }

        // Act as a setter for a single header
        $this->header[$key] = $value;

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
    public function getCookieParams($key = null, $value = null) {
        if (is_array($key)) {
            // Act as a setter, replace all cookies
            $this->cookies = $key;
            return $this;
        } elseif ($key === null) {
            // Act as a getter, all cookies
            return $this->cookies;
        } elseif ($value === null) {
            // Act as a getting, single cookie
            return isset($this->cookies[$key]) ? $this->cookies[$key] : null;
        }

        // Act as a setter for a single cookie
        $this->cookies[$key] = (string) $value;

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
    public function withCookieParams(array $cookies) {
        $this->cookies = $cookies;

        return $this;
    }

    /**
     * Gets the HTTP body of the request.
     *
     * @return  mixed
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * Sets the HTTP body of the request.
     *
     * @param   string  $content    Content to set to the object
     * @return  mixed
     */
    public function withBody(StreamInterface $body) {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the parsed request body data.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, nd the request method is POST, this will be the
     * post data. For other content types, it may be the deserialized request
     * body.
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     */
    public function getParsedBody() {
        return $this->data;
    }

    /**
     * Update the parsed body and get a new instance.
     *
     * @param null|array|object $data The deserialized body data. This will
     *     typically be in an array or object.
     * @return static
     */
    public function withParsedBody($data) {
        $this->data = $data;

        return $this;
    }

    /**
     * Returns the length of the body for use with content header.
     *
     * @return  integer
     */
    public function contentLength() {
        return strlen($this->getBody());
    }

    /**
     * Renders the HTTP_Interaction to a string, producing
     *
     *  - Protocol
     *  - Headers
     *  - Body
     *
     *  If there are variables set to the `Kohana_Request::$post`
     *  they will override any values set to body.
     *
     * @return  string
     */
    public function render() {
        if (!$post = $this->post()) {
            $body = $this->getBody();
        } else {
            $body = http_build_query($post, null, '&');
            $this->getBody($body)->headers('content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
        }

        // Set the content length
        $this->headers('content-length', (string) $this->contentLength());

        // Prepare cookies
        if ($this->cookies) {
            $cookieString = [];

            // Parse each
            foreach ($this->cookies as $key => $value) {
                $cookieString[] = $key . '=' . $value;
            }

            // Create the cookie string
            $this->header['cookie'] = implode('; ', $cookieString);
        }

        $output = $this->method() . ' ' . $this->uri() . ' HTTP/' . $this->getProtocolVersion() . "\n";
        $output .= (string) $this->header . "\n\n";
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
    public function getQueryParams() {
        return $this->query;
    }

    /**
     * Gets or sets HTTP query string.
     *
     * @param   mixed   $key    Key or key value pairs to set
     * @param   string  $value  Value to set to a key
     * @return  mixed
     * @uses    Arr::path
     */
    public function withQueryParams(array $query) {
        $this->query = $query;

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
    public function post($key = null, $value = null) {
        if (is_array($key)) {
            // Act as a setter, replace all fields
            $this->post = $key;

            return $this;
        }

        if ($key === null) {
            // Act as a getter, all fields
            return $this->post;
        } elseif ($value === null) {
            // Act as a getter, single field
            return isset($this->post[$key]) ? $this->post[$key] : null;
        }

        // Act as a setter, single field
        $this->post[$key] = $value;

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
    public function data($key = null, $value = null) {
        if (in_array($this->method, ['PUT', 'DELETE', 'PATCH'])) {
            parse_str($this->body, $this->data);
        }

        if (is_array($key)) {
            // Act as a setter, replace all fields
            $this->data = $key;

            return $this;
        }

        if ($key === null) {
            // Act as a getter, all fields
            return $this->data;
        } elseif ($value === null) {
            // Act as a getter, single field
            return isset($this->data[$key]) ? $this->data[$key] : null;
        }

        // Act as a setter, single field
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Retrieves the request's target.
     *
     * Retrieves the message's request-target either as it was requested,
     * or as set with `withRequestTarget()`. By default this will return the
     * application relative path without base directory, and the query string
     * defined in the SERVER environment.
     *
     * @return string
     */
    public function getRequestTarget() {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();
        if ($this->uri->getQuery()) {
            $target .= '?' . $this->uri->getQuery();
        }

        if (empty($target)) {
            $target = '/';
        }

        return $target;
    }

    /**
     * Create a new instance with a specific request-target.
     *
     * You can use this method to overwrite the request target that is
     * inferred from the request's Uri. This also lets you change the request
     * target's form to an absolute-form, authority-form or asterisk-form
     *
     * @link http://tools.ietf.org/html/rfc7230#section-2.7 (for the various
     *   request-target forms allowed in request messages)
     * @param string $target The request target.
     * @return static
     */
    public function withRequestTarget($target) {
        $this->requestTarget = $target;

        return $this;
    }

    /**
     * Get the HTTP method used for this request.
     * There are a few ways to specify a method.
     *
     * - If your client supports it you can use native HTTP methods.
     * - You can set the HTTP-X-Method-Override header.
     * - You can submit an input with the name `_method`
     *
     * Any of these 3 approaches can be used to set the HTTP method used
     * by CakePHP internally, and will effect the result of this method.
     *
     * @return string The name of the HTTP method used.
     * @link http://www.php-fig.org/psr/psr-7/ This method is part of the PSR-7 server request interface.
     */
    public function getMethod() {
        return $this->env('REQUEST_METHOD');
    }

    /**
     * Update the request method and get a new instance.
     *
     * @param string $method The HTTP method to use.
     * @return static A new instance with the updated method.
     * @link http://www.php-fig.org/psr/psr-7/ This method is part of the PSR-7 server request interface.
     */
    public function withMethod($method) {
        if (!is_string($method) || !preg_match('/^[!#$%&\'*+.^_`\|~0-9a-z-]+$/i', $method)) {
            throw new InvalidArgumentException(sprintf('Unsupported HTTP method "%s" provided', $method));
        }
        $this->_environment['REQUEST_METHOD'] = $method;

        return $this;
    }

    /**
     * Get the array of uploaded files from the request.
     *
     * @return array
     */
    public function getUploadedFiles() {
        return $this->uploadedFiles;
    }

    /**
     * Update the request replacing the files, and creating a new instance.
     *
     * @param array $files An array of uploaded file objects.
     * @return static
     * @throws InvalidArgumentException when $files contains an invalid object.
     */
    public function withUploadedFiles(array $files) {
        $this->validateUploadedFiles($files, '');
        $this->uploadedFiles = $files;

        return $this;
    }

    /**
     * Recursively validate uploaded file data.
     *
     * @param array $uploadedFiles The new files array to validate.
     * @param string $path The path thus far.
     * @return void
     * @throws InvalidArgumentException If any leaf elements are not valid files.
     */
    protected function validateUploadedFiles(array $uploadedFiles, $path) {
        foreach ($uploadedFiles as $key => $file) {
            if (is_array($file)) {
                $this->validateUploadedFiles($file, $key . '.');
                continue;
            }

            if (!$file instanceof UploadedFileInterface) {
                throw new InvalidArgumentException("Invalid file at '{$path}{$key}'");
            }
        }
    }

    /**
     * The default handler for following redirects, triggered by the presence of
     * a Location header in the response.
     *
     * The client's follow property must be set true and the HTTP response status
     * one of 201, 301, 302, 303 or 307 for the redirect to be followed.
     *
     * @param Request $request
     * @param Response $response
     * @param Request_Client $client
     */
    public static function onHeaderLocation(Request $request, Response $response) {
        // Do we need to follow a Location header ?
        if (0 AND in_array($response->status(), [201, 301, 302, 303, 307])) {
            // Figure out which method to use for the follow request
            switch ($response->status()) {
                default:
                case 301:
                case 307:
                    $follow_method = $request->method();
                    break;
                case 201:
                case 303:
                    $follow_method = 'GET';
                    break;
                case 302:
                    // Cater for sites with broken HTTP redirect implementations
                    if (1) {
                        $follow_method = $request->method();
                    } else {
                        $follow_method = 'GET';
                    }
                    break;
            }

            $follow_request = Request::factory($response->headers('Location'))->method($follow_method);

            // Prepare the additional request, copying any follow_headers that were present on the original request
            $orig_headers = $request->getHeaders()->getArrayCopy();
            $follow_header_keys = array_intersect(array_keys($orig_headers), $request->followHeaders);
            foreach ($follow_header_keys as $key) {
                if (array_key_exists($key, $orig_headers)) {
                    $follow_request->withHeader($key, $orig_headers[$key]);
                }
            }

            if ($follow_method !== 'GET') {
                $follow_request->body($request->body());
            }

            return $follow_request;
        }

        return null;
    }

}
