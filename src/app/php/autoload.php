<?php
if(empty($_SESSION)) session_start();

function RequireScripts($Files) {
    $Files = func_get_args();
    foreach($Files as $File)
        require_once($_SESSION["Root"]["Paths"]["Php"]["Core"]["Dir"] . $File . ".php");
}

function RequireDependencies($Files){
    $Files = func_get_args();
    foreach($Files as $File)
        require_once($_SESSION["Root"]["Paths"]["Depends"]["Dir"] . $File . ".php");
}

// PHP Scripts
RequireScripts(
    "shorts",
    "view",
    "controller",
    "model",
    "authorize",
    "session",
    "route",
    "error"
);

// Dependencies
RequireDependencies(
    "aces-sql/loader",
    "folds-forms/loader",
    "spades-ajax/loader"
);