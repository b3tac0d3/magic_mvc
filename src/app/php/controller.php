<?php

namespace Document;
use sm;
use Document\RouteClass;
use Document\ViewClass;

class ControllerClass{

private $Controller;
public $Model;
private $ViewData = array();

function __Construct($ControllerFile = null, $Class = null, $Function = null){
    $this -> SetController($ControllerFile);
    if(!empty($Class)) $this -> SetClass($Class);
    if(!empty($Function)) $this -> RunFunction($Function);
} // Construct()

function SetController($File){
    require_once($File);
} // SetController()

function SetClass($Class){
    $this -> Controller = new $Class;
} //SetClass()

function RunFunction($Function){
    $this -> Controller -> $Function();
} // RunFunction()

function AddViewData($Key, $Data){
    if(isset($this -> ViewData[$Key])){
        $this -> ViewData[$Key] .= $Data;
    }else{
        $this -> ViewData[$Key] = $Data;
    }
} // AddViewData()

function GetModel($Model){
    $Model = sm::Dir("Models") . str_replace(".", "/", $Model) . ".php";
    require_once($Model);
} // GetModel()

function GetView($File){
    $Route = new RouteClass();
    $Route -> View($File, $this -> ViewData);
    return;
}


} // class ControllerClass