<?php

/* 
    This is an optional use file.
    
    If you're going to use active status to track records in the database, this is the file that automatically logs
    the record life and edits in the database log tables.

    To shut this file off, you can change the setttings in aces/config.php
*/
namespace Aces;

class QueryAudits{
/*==================================================================================================================
    Database Audit Logging
==================================================================================================================*/

function AuditDbRecordCreate($Table, $RecordId, $LogNote = null){
    if(empty($_SESSION)) session_start();
    $UserId = $_SESSION["UserSession"]["UserId"] ?? null;
    $InsertColumns = ["record_table", "record_id", "audit_type", "audit_note", "create_date", "create_id", "create_ip", "create_sess_id"];
    $InsertValues = [$Table, $RecordId, 1, $LogNote, date("Y-m-d h:i:s"), $UserId, $_SERVER["REMOTE_ADDR"], $_SESSION["Root"]["Id"]];
    $Db = new query();
    $Db -> SetInsertArray($InsertColumns, $InsertValues) -> Insert("log_record_audit");
    return $this;
} // AuditDbRecordCreate()

function audit_db_record_edit(){} // audit_db_record_edit()

function AuditDbRecordDelete($Table, $RecordId){
    if(empty($_SESSION)) session_start();
    $UserId = $_SESSION["UserSession"]["UserId"] ?? null;
    $UpdateColumns = ["rec_table_name", "rec_row_id", "kill_date", "kill_ip", "kill_id", "kill_sess_id"];
    $UpdateValues = [$Table, $RecordId, date("Y-m-d h:i:s"), $_SERVER["REMOTE_ADDR"], $UserId, $_SESSION["Root"]["Id"]];
    $Db = new query();
    $Db -> SetUpdateArray($UpdateColumns, $UpdateValues) -> SetWhereArray(["rec_table_name", "rec_row_id"], [$Table, $RecordId]) -> Update("log_record_life");
    return $this;
} // AuditDbRecordDelete()

} // class query_audits
?>