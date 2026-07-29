<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedTaskAccessException extends Exception
{
    public function __construct($message = "Bu göreve erşim veya işlem yapma yetkiniz bulunmamaktadır.", $code = 403,)
    {
        parent::__construct($message, $code);
    }
    public function render($request){
        return back()->withErrors(['error'=>$this->getMessage()]);
    }
}
