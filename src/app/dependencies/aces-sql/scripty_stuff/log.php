<?php

namespace Aces;

/* 
    fopen modes
    "r" - Read only. Starts at the beginning of the file
    "r+" - Read/Write. Starts at the beginning of the file
    "w" - Write only. Opens and truncates the file; or creates a new file if it doesn't exist. Place file pointer at the beginning of the file
    "w+" - Read/Write. Opens and truncates the file; or creates a new file if it doesn't exist. Place file pointer at the beginning of the file
    "a" - Write only. Opens and writes to the end of the file or creates a new file if it doesn't exist
    "a+" - Read/Write. Preserves file content by writing to the end of the file
    "x" - Write only. Creates a new file. Returns FALSE and an error if file already exists
    "x+" - Read/Write. Creates a new file. Returns FALSE and an error if file already exists
    "c" - Write only. Opens the file; or creates a new file if it doesn't exist. Place file pointer at the beginning of the file
    "c+" - Read/Write. Opens the file; or creates a new file if it doesn't exist. Place file pointer at the beginning of the file
    "e" - Only available in PHP compiled on POSIX.1-2008 conform systems.

    function set_log(){ 
        $Data = array("test"=>"jimmy", "userid"=>1);
        $size = filesize("src/logs/sql_schema.json");
        $File = fopen("src/logs/sql_schema.json", "w+");
        $readdata = array_merge(json_decode(fread($File, $size)), $Data);
        fwrite($File, json_encode($readdata));
        fclose($File);
    }

    function get_log(){
        $size = filesize("src/logs/sql_schema.json");
        $File = fopen("src/logs/sql_schema.json", "r"); 
        $Data = fread($File, $size);
        fclose($File);
        print_r(json_decode($Data));
    }

    get query execution time
    $start = microtime(true)
    run query
    $end = microtime(true)
    $execute = $end - $start
*/

class Log{

    // Log directory
    private $LogDirectory;

    // Log file name
    private $LogName;

    // Log file path (dir/name.txt)
    private $LogPath;

    // Header to insert in to log file
    private $Header;

    // Log type in case we need to reference it later or in future updates
    private $LogType;

    // Data to be inserted in to log record
    private $LogRecord;

    // ID of new record being added to file
    private $NewRecordId;

    function SetRecord($Type = "Query", $Data = array()){
        // Setup main variables to run scripts
        $this -> SetVariables($Type);
        
        // Check for log directory and make new if necessary
        if(!is_dir($this -> LogDirectory)) mkdir($this -> LogDirectory);
        
        // Open file for read/append
        $WriteFile = fopen($this -> LogPath, "a+");


        // Get file size for reading and applying Header if needed
        $ReadSize = filesize($this -> LogPath);

        // Check if file has data. If not, make Header to start
        if($ReadSize < 1){
            fwrite($WriteFile, $this -> Header);
            $this -> NewRecordId = 1;
        }else{
            // Get file data to put in to array
            $FileData = fread($WriteFile, $ReadSize);
        }

        // Load file data to array
        if(!empty($FileData)) $FileDataArray = explode("\n", $FileData);

        // Get last element of array for last ID
        if(empty($this -> NewRecordId))  $this -> NewRecordId = intval(explode(trim("|"), end($FileDataArray))[0]) + 1;
        
        // Make new record
        $this -> CreateRecord($Data);

        // Add the record to the file
        fwrite($WriteFile, $this -> LogRecord);

        // Close file
        if(!empty($File)) fclose($File);
    } // SetRecord()

    function SetVariables($Type){
        switch($this -> LogType = $Type){
            case "Query":
                $this -> Header = "id | date | time | ip | table | result count | run time | query";
                $this -> LogDirectory = AcesLogFileDirQuery;
                break;
            case "Connection":
                $this -> Header = "id | date | time | ip | database";
                $this -> LogDirectory = AcesLogFileDirConnection;
                break;
            case "QueryError":
                $this -> Header = "id | date | time | ip | error | query";
                $this -> LogDirectory = AcesLogFileDirQueryError;
                break;
            case "ConnectionError":
                $this -> Header = "id | date | time | ip | error";
                $this -> LogDirectory = AcesLogFileDirConnectionError;
                break;
        }
        
        // Generate log name
        $this -> LogName = date("Ym") . "_aces_{$Type}_log.txt";
        $this -> LogPath = $this -> LogDirectory . $this -> LogName;
        return $this;
    } // SetVariables()

    function CreateRecord($Data){
        date_default_timezone_set('America/New_York');
        extract($Data);
        
        $LogDate = date("Y_m_d");
        $LogTime = date("H:i:s");
        
        // Delimiter
        $Delimiter = "|";
        $RecordId = $this -> NewRecordId;
        $UserIp = $_SERVER["REMOTE_ADDR"];
        
        $InitialRecordData =  "\n$RecordId $Delimiter $LogDate $Delimiter $LogTime $Delimiter $UserIp $Delimiter";
        
        switch($this -> LogType){
            case "Query":
                // id | date | time | ip | table | result count | run time | query
                $this -> LogRecord = "$InitialRecordData $Table $Delimiter $ResultCount $Delimiter $RunTime $Delimiter $QueryString";
                break;
            case "Connection":
                // id | date | time | ip | database
                $this -> LogRecord = "$InitialRecordData $Database";
                break;
            case "QueryError":
                // id | date | time | ip | error | query string
                $this -> LogRecord = "$InitialRecordData $Error $Delimiter $QueryString";
                break;
            case "ConnectionError":
                // id | date | time | ip | error
                $this -> LogRecord = "$InitialRecordData $Error";
                break;
        }
        return $this;
    } // CreateRecord()

} // class log
?>