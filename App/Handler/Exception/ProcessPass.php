<?php
namespace App\Handler\Exception;

use Exception;

/**
 *
 */
class ProcessPass extends Exception
{
    /**
     * [__construct description]
     * @param [type]  $message  [description]
     * @param integer $code     [description]
     * @param [type]  $previous [description]
     */
    public function __construct($message = null, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
