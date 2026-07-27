<?php

namespace App\Exceptions;

use Exception;

class TaskCompletedException extends Exception
{
    public function __construct($message = "Bu görev tamamlandığı için yeni bir yorum eklenemez.", $code = 403)
    {
        parent::__construct($message, $code);
    }
    public function render($request){
        return back()->with("breadcrumb_error",$this->getMessage());
    }
}
