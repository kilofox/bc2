<?php

namespace Bootphp\Request\Client;

use Bootphp\Request;
use Bootphp\Response;
use Bootphp\Profiler\Profiler;

/**
 * Request Client for internal execution
 *
 * @package    Bootphp
 * @category   Base
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class Internal extends \Bootphp\Request\Client
{
    /**
     * @var    array
     */
    protected $_previous_environment;

    /**
     * Processes the request, executing the controller action that handles this
     * request, determined by the [Route].
     *
     *     $request->execute();
     *
     * @param   Request $request
     * @return  Response
     * @throws  BootphpException
     * @uses    [Core::$profiling]
     * @uses    [Profiler]
     */
    public function execute_request(Request $request, Response $response)
    {
        // Directory
        $directory = $request->directory();

        // Controller
        $controller = $request->controller();

        if ($directory) {
            // Add the directory name to the class suffix
            $directory .= '\\';
        }

        if (\Bootphp\Core::$profiling) {
            // Set the benchmark name
            $benchmark = '"' . $request->getUri() . '"';

            if ($request !== Request::$initial && Request::$current) {
                // Add the parent request uri
                $benchmark .= ' « "' . Request::$current->getUri() . '"';
            }

            // Start benchmarking
            $benchmark = Profiler::start('Requests', $benchmark);
        }

        // Store the currently active request
        $previous = Request::$current;

        // Change the current request to this request
        Request::$current = $request;

        // Is this the initial request
        $initial_request = ($request === Request::$initial);

        $controller = 'App\\Controller\\' . $directory . ucfirst($controller) . 'Controller';

        try {
            if (!class_exists($controller)) {
                throw new \Bootphp\BootphpException('The requested URL ' . $request->getUri() . ' was not found on this server.', 404);
            }

            // Load the controller using reflection
            $class = new \ReflectionClass($controller);

            if ($class->isAbstract()) {
                throw new BootphpException('Cannot create instances of abstract ' . $controller . '.');
            }

            // Create a new instance of the controller
            $controller = $class->newInstance($request, $response);

            // Run the controller's execute() method
            $response = $class->getMethod('execute')->invoke($controller);

            if (!$response instanceof Response) {
                // Controller failed to return a Response.
                throw new BootphpException('Controller failed to return a Response.');
            }
        } catch (HTTP_Exception $e) {
            // Store the request context in the Exception
            if ($e->request() === null) {
                $e->request($request);
            }

            // Get the response via the Exception
            $response = $e->get_response();
        } catch (Exception $e) {
            // Generate an appropriate Response object
            $response = BootphpException::_handler($e);
        }

        // Restore the previous request
        Request::$current = $previous;

        if (isset($benchmark)) {
            // Stop the benchmark
            Profiler::stop($benchmark);
        }

        // Return the response
        return $response;
    }

}
