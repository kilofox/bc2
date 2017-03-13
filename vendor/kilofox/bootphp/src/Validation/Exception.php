<?php

/**
 * @package    Bootphp
 * @category   Exceptions
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class ValidationException extends Kohana_Exception
{
    /**
     * @var  object  Validation instance
     */
    public $array;

    /**
     * @param  Validation   $array      Validation object
     * @param  string       $message    error message
     * @param  array        $values     translation variables
     * @param  int          $code       the exception code
     */
    public function __construct(Validation $array, $message = 'Failed to validate array', array $values = null, $code = 0, Exception $previous = null)
    {
        $this->array = $array;

        parent::__construct($message, $values, $code, $previous);
    }

}
