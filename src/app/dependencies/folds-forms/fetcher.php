<?php

class Forms{

    public static function PrintForm($FormName, $FormDir = null){
        /*
            $FormDir is optional when the form is stored in a different
            directory other than the default.

            - Consider using init file to define default directory
            
         */
        if(empty($FormDir)) $FormDir = sm::dir("UserForms");
        if(!strpos($FormName, ".php")) $FormName .= ".php";
        $Form = include_once($FormDir . $FormName);
        echo $Form;
    }

} // class fetcher