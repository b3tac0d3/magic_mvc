<?php
session_start();

// See src/spp/config/config.php to manually update any settings
define("AppState", "Alpha"); // Alpha / Beta / Live

// Only set foundation info if not already set or if Alpha mode
if(!isset($_SESSION["Root"]["Id"]) || AppState == "Alpha"){
    // Reset Session Options if Alpha Mode
    $_SESSION["Root"] = null;
    
    // Get control info from hard code lib file
    $SetupVars = require_once "src/app/config.php";

    // Set session with control variable data
    $_SESSION["Root"] = $SetupVars;

    // Set dynamic session info
    $_SESSION["Root"]["Open"] = time();
    $_SESSION["Root"]["Id"] = session_id();
}

// Added to separate file so resources can be loaded on independent scripts (login, register, etc)
require_once "src/app/php/autoload.php";

// Final step is to call route file to direct traffic
$RouteClass = new Document\RouteClass();

// Get the URI and Route File
$Uri = $RouteClass -> GetUri();
$Dir = sm::Dir("Routes");

// Set the route file based on the URI
if(str_contains($Uri, "MagicFormRoute/")){
    // Submitting a form
    $File = "form_routing.php";
}elseif(str_contains($Uri, "MagicScriptRoute/")){
    // Running a script
    $File = "script_routing.php";
}else{
    // Default route
    $File = "page_routing.php";
}

// Load the route file
require_once $Dir . $File; 