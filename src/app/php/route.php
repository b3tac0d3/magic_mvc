<?php

namespace Document;
use sm;
use Document\ErrorClass;

// Presto syntax is determined based on the contents of the file. The first line in the file should be @presto. If not, it will load as regualar HTML

class RouteClass{

    private $RequrestType = "get";
    private $Error;
    
    function __construct(){
        if(empty($_SESSION)) session_start();
        $this -> Error = new ErrorClass();
    } // __Construct()

    function View($FileName, $PassData = null){
        # Used for calling a view file directly with no controller, session or authorization needed.
        # PassData is used for passing data from the model to the view. Default value is PHP array format. Consider adding JSON option later.
        
        $FileName = $this -> CheckFileExists(sm::Dir("Views") . $FileName); // Make sure we have a valid file
        new ViewClass($FileName, $PassData); // Call the ViewClass to handle view data
        return $this; // Return for chaining purposes
    } // View()
        
    function Ctrl($FileName, $ClassName = null, $Function = null){
        # Used for calling a controller
        # FileName = Name of controller file
        # ClassName = Defaults to pascal case filename + "Class" or can be defined here
        # Function = Name of specific function to run if not otherwise run automatically when called

        $ClassName = $ClassName ?: ucwords($FileName) . "Controller"; // Define ClassName if not passed in
        $FileName = $this -> CheckFileExists(sm::Dir("Controllers") . $FileName); // Make sure we have a valid file
        new ControllerClass($FileName, $ClassName, $Function); // Call the ControllerClass to handle request
        return $this; // Return for chaining purposes
    } // Ctrl()

    function Sess(){
        # Used for checking a session before loading a logged-in or session file
        return $this;
    } // Sess()

    function Auth($PermissionRequired){
        // Used for broad page authorization through RBAC
        return $this;
    } // Auth()

    function SetRequestType($RequestType){
        $this -> RequrestType = $RequestType;
        return $this;
    } // SetRequestType()

    function GetUri(){
        $uri = $_SERVER['REQUEST_URI'];
        
        if(strpos($uri, "?")){
            $uri = substr($uri, 1, (strpos($uri, "?") - 1));
        }else{
            $uri = substr($uri, 1);
        }

        // If using local server or sub-direcotry, this is the line to prepend the uri with the correct path information
        if(!empty($base_uri_ext = $_SESSION["Root"]["App"]["BaseUriExt"])) $uri = str_replace($base_uri_ext, "", $uri);
        
        return $uri;
    } // GetUri()

    function RunScript($File, $CLass = null, $Function = null, $RequestType = "get", $Uri = null){
        // Script is passed automatically in the scope
        if(!str_contains($File, ".php")) $File .= ".php";
        require_once $File;
        if(!empty($Function) && !empty($Class)){
            $RunClass = new $Class();
            $RunClass -> $Function();
        }elseif(!empty($Function) && empty($Class)){
            $Function();
        }
    } // RunScript()

    private function CheckFileExists($FileName){
        // Start by parsing file name to replace dots with slashes. This is for simple routing
        $FileName = str_replace(".", "/", $FileName);
        if(is_dir($FileName)){ // Check if we have a directory first and default to index file if nothing is specified
            return $FileName . "/index.php";
        }elseif(is_file($FileName .= ".php")){ // Check if we have a files
            return $FileName;
        }else{ // Throw an error because it's not an existing dir or file
            $this -> Error -> SetError(404, $FileName);
            return false;
        }
    } // CheckFileExists()

} // class RouteClass