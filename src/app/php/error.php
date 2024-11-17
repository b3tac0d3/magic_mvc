<?php

namespace Document;
use sm;

class ErrorClass{

    private $ErrorCode = null;
    private $ErrorStatus = false;
    private $ErrorMessage = null;
    
    function __Construct($ErrorStatus = false){
        if(!empty($ErrorStatus)) $this -> ErrorStatus = $ErrorStatus;
    } // __Construct()

    function ThrowCodeError($ErrorMessage, $OriginFile, $ErrorStatus = true){
        echo "
            <h1>Oops!</h1>
            <p>It looks like there's an error in the code. The code reported the following error from file: <b>$OriginFile</b></p>
            <p>Error: <b>$ErrorMessage</b></p>
        ";
        exit;
    } // ThrowError()

    function PageError($Code = null, $Message = null, $Status = true){
        // For pages that have load issues, unrelated to scripts. This function redirects to a new page.
        $ErrorsDirectory = sm::Dir("Views") . "errors/";
        switch($Code){
            case 404:
                new ViewClass($ErrorsDirectory . "404.php");
                break;
            case "Auth":
                new ViewClass($ErrorsDirectory . "auth.php");
                break;
            case "Sess":
                new ViewClass($ErrorsDirectory . "session.php");
                break;
        }
        exit;
    } // PageError()

    function CodeError($Code = null, $Message = null, $Status = true, $File = null){
        // Mostly for scripting errors that are not caught by the error handler
    } // CodeError()

    function ThrowError(){
        // The final error function to be called
    } // ThrowError()

} // class ErrorClass