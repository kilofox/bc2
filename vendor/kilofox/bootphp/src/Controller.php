<?php

namespace Bootphp;

/**
 * Abstract controller class. Controllers should only be created using a [Request].
 *
 * Controllers methods will be automatically called in the following order by
 * the request:
 *
 *     $controller = new FooController($request);
 *     $controller->before();
 *     $controller->barAction();
 *     $controller->after();
 *
 * The controller action should add the output it creates to
 * `$this->response->body($output)`, typically in the form of a [View], during
 * the "action" part of execution.
 *
 * @package    Bootphp
 * @category   Controller
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
abstract class Controller
{
    /**
     * Request that created the controller.
     *
     * @var     Request
     */
    public $request;

    /**
     * The response that will be returned from controller.
     *
     * @var     Response
     */
    public $response;

    /**
     * Page view.
     *
     * @var     View
     */
    public $view;

    /**
     * Auto render template.
     *
     * @var     boolean
     * */
    public $autoRender = true;

    /**
     * Creates a new controller instance. Each controller must be constructed
     * with the request object that created it.
     *
     * @param   Request     $request    Request that created the controller
     * @param   Response    $response   The request's response
     * @return  void
     */
    public function __construct(Request $request, Response $response)
    {
        // Assign the request to the controller
        $this->request = $request;

        // Assign a response to the controller
        $this->response = $response;
    }

    /**
     * Executes the given action and calls the [Controller::before] and [Controller::after] methods.
     *
     * Can also be used to catch exceptions from actions in a single place.
     *
     * 1. Before the controller action is called, the [Controller::before] method
     * will be called.
     * 2. Next the controller action will be called.
     * 3. After the controller action is called, the [Controller::after] method
     * will be called.
     *
     * @throws  BootphpException
     * @return  Response
     */
    public function execute()
    {
        // Execute the "before action" method
        $this->before();

        // Determine the action to use
        $action = $this->request->action() . 'Action';

        // If the action doesn't exist, it's a 404
        if (!method_exists($this, $action)) {
            throw new BootphpException('The requested URL ' . $this->request->uri() . ' was not found on this server.', 404);
        }

        // Execute the action itself
        $this->{$action}();

        // Execute the "after action" method
        $this->after();

        // Return the response
        return $this->response;
    }

    /**
     * Automatically executed before the controller action. Can be used to set
     * class properties, do authorization checks, and execute other custom code.
     *
     * @return  void
     */
    public function before()
    {
        // Loads the [View] object.
        if ($this->autoRender === true) {
            $directory = $this->request->directory() ? $this->request->directory() . '/' : '';

            $this->baseUrl = URL::base();
            $this->view = new \Bootphp\View();
            $this->view->layoutPath(APP_PATH . '/View/' . $directory . 'layout/')
                ->layout('default')
                ->templatePath(APP_PATH . '/View/')
                ->template($this->request->action())
                ->set('baseUrl', $this->baseUrl)
                ->set('controller', $this->request->controller());
        }
    }

    /**
     * Automatically executed after the controller action. Can be used to apply
     * transformation to the response, add extra output, and execute other
     * custom code.
     *
     * @return  void
     */
    public function after()
    {
        // Assigns the [View] as the request response.
        if ($this->autoRender === true) {
            $this->response->body($this->view->render());
        }
    }

    /**
     * Issues a HTTP redirect.
     *
     * @param   string  $uri    URI to redirect to
     * @param   integer $code   HTTP Status code to use for the redirect
     * @throws  BootphpException
     */
    public function redirect($uri = '', $code = 303)
    {
        if (!in_array($code, [300, 301, 302, 303, 307])) {
            throw new BootphpException('Invalid redirect code `' . $code . '`.');
        }

        if (strpos($uri, '://') === false) {
            // Make the URI into a URL
            $uri = \Bootphp\URL::site($uri, true);
        }

        return $this->response->status($code)->headers('Location', (string) $uri);
    }

}
