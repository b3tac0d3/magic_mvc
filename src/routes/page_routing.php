<?php

$Route = new Document\RouteClass();

// $Uri is passed in from index file
match($Uri){
    
    default => $Route -> View($Uri),

    "", "index", "home" => $Route -> View("home"),

    "dev/config" => $Route -> Ctrl("dev", ClassName: "DevConfigController")

};