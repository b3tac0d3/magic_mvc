<?php

namespace Aces;
use PDO;
use PDOException;

if(!defined("AcesConfig")) require_once($_SERVER['DOCUMENT_ROOT'] . "/src/app/plugins/aces/config.php");

class Db{
    private $Log;
    private $Db;

    function Connect($DbName = null){
        $this -> Log = new Log();
        if(empty($_SESSION)) session_start();
        $Host = AcesDbHost;
        $Charset = AcesDbCharset;
        $Port = AcesDbPort;
        $User = AcesDbUser;
        $Pass = AcesDbPass;
        if(empty($DbName)) $DbName = AcesDbName;
        
        try{
            $this -> Db = new PDO("mysql:host=$Host;charset=$Charset;port=$Port;dbname=$DbName", $User, $Pass);
        }catch(PDOException $e){
            $this -> Log -> SetRecord("connection_error", ["error" => trim($e -> getMessage())]);
            echo "Error" . $e -> getMessage();
            return;
        } // try

        $this -> Db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // If we've made it this far, Log the good connection
        $this -> Log -> SetRecord("Connection", ["Database" => $DbName]);
        return $this -> Db;
    } // connect()
} // class Db

?>