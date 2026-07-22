<?php

namespace App\Exceptions;

use Exception;

class InvalidManagerException extends Exception
{
    public function __construct($message = "Girdiğiniz eposta adresine ait geçerli bir müdür bulunamadı", $code = 422,){
        parent::__construct($message, $code);
    }
    public function render($request){
        return back()->withErrors(['manager_email'=>$this->getMessage()])->withInput();
    }
}
