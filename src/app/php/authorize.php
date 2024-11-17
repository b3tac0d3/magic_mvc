<?php

namespace Document;
use sm;

class AuthClass{

function AuthUserPage($PermissionRequired){
    if(empty($_SESSION)) session_start();
    if(isset($_SESSION["UserSession"])){
        if($_SESSION["UserSession"]["MainRole"] < $PermissionRequired)
            return 0;
        else
            return 1;
    }
} // validate_user_permission

function AuthUserFeature($PermissionRequired){
    // Here we're going to dive deeper in to the user permissions to check for the proper values
}

} // AuthClass