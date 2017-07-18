<?php

namespace Bootphp\HTTP\Exception;

/**
 * "Expected" HTTP exception class. Used for all [HTTP_Exception]'s where a standard
 * Kohana error page should never be shown.
 *
 * Eg [HTTP_Exception_301], [HTTP_Exception_302] etc
 *
 * @package    Bootphp
 * @category   Exceptions
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
//abstract
class HTTPExceptionExpected extends \Bootphp\HTTP\HTTPException
{
    /**
     * @var  Response   Response Object
     */
    protected $_response;

    /**
     * Creates a new translated exception.
     *
     *     throw new Kohana_Exception('Something went terrible wrong, :user',
     *         array(':user' => $user));
     *
     * @param   string  $message    status message, custom content to display with error
     * @param   array   $variables  translation variables
     * @return  void
     */
    public function __construct($message = null, array $variables = null, \Exception $previous = null)
    {
        parent::__construct($message, $variables, $previous);

        // Prepare our response object and set the correct status code.
        $this->_response = \Bootphp\Response::factory()
                ->status($this->_code);
    }

    /**
     * Gets and sets headers to the [Response].
     *
     * @see     [Response::headers]
     * @param   mixed   $key
     * @param   string  $value
     * @return  mixed
     */
    public function headers($key = null, $value = null)
    {
        $result = $this->_response->headers($key, $value);

        if (!$result instanceof Response)
            return $result;

        return $this;
    }

    /**
     * Validate this exception contains everything needed to continue.
     *
     * @throws Kohana_Exception
     * @return bool
     */
    public function check()
    {
        return true;
    }

    /**
     * Generate a Response for the current Exception
     *
     * @uses   Kohana_Exception::response()
     * @return Response
     */
    public function get_response()
    {
        $this->check();

        return $this->_response;
    }

}
