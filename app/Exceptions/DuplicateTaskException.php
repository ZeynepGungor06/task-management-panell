<?php

namespace App\Exceptions;

use Exception;

class DuplicateTaskException extends Exception
{
    public function __construct($message = "Bu görev zaten listenizde mevcut!", $code = 409){
        parent::__construct($message, $code);
    }
    public function render($request){
        return back()->with("breadcrumb_error", $this->getMessage());
    }
}
