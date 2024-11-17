<?php

namespace Aces;
use PDO;
use PDOException;

class Table extends Db{
    
    // Databse object
    private $Db;

    // Table name
    private $TableName;

    // Executable query string
    private $QueryString;

    // List of columns for query
    private $ColumnsList;


    function __construct(){
        $this -> Db = $this -> Connect();
    }

    function Create($Name, $Columns = array()){
        /* 
            $tbl -> add_col("id", "int", 11, "not null primary key auto_increment")
            $tbl -> create($cols)

        */
        $this -> TableName = $Name;
        // $this -> columns_list = $this -> format_columns($columns);
    }

    function Truncate(){}

    function Update(){}

    function AddColumn($Name, $Type, $Length = null, $Params = null){}

    function DeleteColumn(){}

    private function sample_array(){
        // Just a sample array for reference
        $log_entry = array(
            "type" => "create",
            "date" => strtotime(time()),
            "command_count" => 4,
            "commands" => array(
                1 => array(
                    "short" => "create table users",
                    "full" => "create table users(id int not null primary key auto_increment, username varchar(255), active boolean)",
                ),
                2 => array(
                    "short" => "create table contacts",
                    "full" => "create table contacts(id int not null primary key auto_increment, username varchar(255), active boolean)",
                ),
                3 => array(
                    "short" => "create table emails",
                    "full" => "create table emails(id int not null primary key auto_increment, username varchar(255), active boolean)",
                )
            )
        );
    }

} // class Table
?>