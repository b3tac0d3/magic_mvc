<?php

/* 
    This is a set of short functions that can be used to quickly access the $_SESSION["Root"] array.
*/

use Document\ErrorClass;

class sm{
    // Quickly access a pre-defined URL
    public static function Url($Input){
        // Return any pre-definded URL
        if(empty($_SESSION["Root"]["Paths"][$Input]["Url"]))
            return "Shorts function Url not located";
        else
            return $_SESSION["Root"]["Paths"][$Input]["Url"];
    } // Url()

    // Quickly access a pre-defined directory
    public static function Dir($Input){
        // Return and pre-defined directory
        if(empty($_SESSION["Root"]["Paths"][$Input]["Dir"]))
            return "Shorts function Dir not located";
        else
            return $_SESSION["Root"]["Paths"][$Input]["Dir"];
    } // Dir()

    // Quickly access a pre-defined Dependence URL or directory
    public static function Dep($Dir, $Type = "Url"){
        // Quickly access a dependency directory
        if($Type == "Url")
            return $_SESSION["Root"]["Depends"][$Dir]["Url"];
        else
            return $_SESSION["Root"]["Depends"][$Dir]["Dir"];
    } // Dep()
    
    // Quickly access and pre-defined variable under App or Dev
    public static function Cus($Input, $Type = "App") {
        $Error = new Document\ErrorClass();
        // Check if $_SESSION["Root"] and the specified Type exist
        if (!isset($_SESSION["Root"]) || !isset($_SESSION["Root"][$Type])) {
            // Return null or handle the error if the Root or Type doesn't exist
            echo "Session array not initilized. Please check the session_start() function in your index.php file.";
            exit;
        }
    
        // Split the input by dots to navigate through nested arrays
        $InputArray = explode(".", $Input);
    
        // Initialize the return array to the base level based on the Type
        $ReturnArray = $_SESSION["Root"][$Type];
    
        // Iterate through each level in InputArray to go deeper in the nested array
        foreach ($InputArray as $Value) {
            if (array_key_exists($Value, $ReturnArray)) {
                $ReturnArray = $ReturnArray[$Value];
            } else {
                // Return null if any level does not exist
                return $Error -> ThrowError("Array key not found: $Value<br>", "app/php/shorts.php");
            }
        }
    
        // Output the final value
        return $ReturnArray;
    }  // Cus()
    
    
}