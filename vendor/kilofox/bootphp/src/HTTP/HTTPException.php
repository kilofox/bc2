<?php

namespace Bootphp\HTTP;

use Bootphp\BootphpException;

//abstract
class HTTPException extends BootphpException
{
    /**
     * Creates an HTTPException of the specified type.
     *
     * @param   string  $message    Status message, custom content to display with error
     * @param   array   $variables  Translation variables
     * @param   integer $code       The http status code
     * @return  HTTPException
     */
    public static function factory($message = null, array $variables = null, $code = 0)
    {
        return new self($message, $variables, $code);
    }

    /**
     * @var  int        http status code
     */
    protected $_code = 0;

    /**
     * @var  Request    Request instance that triggered this exception.
     */
    protected $_request;

    /**
     * Creates a new translated exception.
     *
     *     throw new Kohana_Exception('Something went terrible wrong, :user',
     *         array(':user' => $user));
     *
     * @param   string  $message    Status message, custom content to display with error
     * @param   array   $variables  Translation variables
     * @param   integer $code       The http status code
     * @return  void
     */
    public function __construct($message = null, array $variables = null, $code)
    {
        $message = \Bootphp\I18n::get($message, $variables);

        parent::__construct($message, $variables, $code);
    }

    /**
     * Store the Request that triggered this exception.
     *
     * @param   Request   $request  Request object that triggered this exception.
     * @return  HTTP_Exception
     */
    public function request(Request $request = null)
    {
        if ($request === null)
            return $this->_request;

        $this->_request = $request;

        return $this;
    }

    /**
     * Generate a Response for the current Exception.
     *
     * @return Response
     * @uses   Bootphp\ootphpException::response()
     */
    public function getResponse()
    {
        return BootphpException::response($this);
    }

}
