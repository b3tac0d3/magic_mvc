<?php

namespace User;
use Document\ViewClass;

class SessionClass{

function CreateUserSession($UserName, $UserRole, $UserId){ // pass info in array instead of variables?
    // Set user session variables
    $UserSession["LoginTime"] = time();
    $UserSession["ExpireTime"] = 1800; // 30 minutes
    $UserSession["LastActivity"] = time(); // Updates with every click or change if not passed expiration limit
    $UserSession["UserName"] = $UserName;
    $UserSession["MainRole"] = $UserRole;
    $UserSession["UserId"] = $UserId;
    // Set session variables
    $_SESSION["UserSession"] = $UserSession;
    return;
} // CreateUserSession()

function ValidateUserSession(){
    /* 
    Two tasks here
        1) Validate session existence *** Returns 0
        2) Validate session hasn't expired *** Returns -1
        *** Good session returns 1
    */

    // Session not valid or not set
    if(empty($_SESSION["UserSession"])) return 0;
    
    $Session = $SessionLastAct = $_SESSION["UserSession"];
    $CurrentTime = time();
    $SessionExpire = $Session["LastActivity"] + $Session["ExpireTime"];
    
    // Session expired
    if($SessionExpire < $CurrentTime) return -1;

    // If we've made it this far, return true
    return true;
} // ValidateUserSession()

function UpdateUserSession(){
    $_SESSION["UserSession"]["LastActivity"] = time();
} // UpdateUserSession()

function EndUserSession(){
    $_SESSION["UserSession"] = null;
    session_unset();
    session_destroy();
    return $this;
} // EndUserSession()

} // SessionClass