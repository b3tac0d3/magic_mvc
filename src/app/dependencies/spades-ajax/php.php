<?php
/*
	3/14/17 b3tac0d3
	To avoid errors, this class should have a built in function to 
	shut off all Redirects and Refreshes prior to turning a new one on.
	Consider for future releases.
*/

namespace Spades;

class Spades{
	
	protected $Message = NULL;
	protected $Status = 1;
	protected $Refresh = 0;
	protected $BadInputs = array();
	protected $Classes;
	protected $Text = NULL;
	protected $Html = NULL;
	protected $PushState = NULL;
	protected $Redirect = NULL;
	protected $RedirectTrue = 0;
	protected $RedirectColorbox = 0;
	protected $RedirectColorboxLink = NULL;
	protected $AlertText = NULL;
	protected $AlertTrue = 0;
	protected $ErrorCount = 0;
	protected $Arrays = array();
	protected $OpenData = NULL;
	
	function __construct($Class = 'red'){
		//$this -> Classes = $Class;
	} # __construct
	
	function QuickMessage($Message, $Class = null, $Status = 0){
		$this -> AppendMessage($Message);
		$this -> AddClass($Class); # add a quick class such as red
		$this -> SetStatus($Status);
		return $this -> MakeJson();
	}
	
	function AppendMessage($Input){
		# typically used on alert forms to reply with Message in form before processing
		# adds linebreaks by itself so isn't good for general Text return option
		$this -> Message .= $Input . "<br>\n";
	}
	
	function AddError($Message = NULL, $Input = 1){
		# add an error to the list of errors 
		if(!empty($Message)) $this -> AppendMessage($Message); // easily add return Message in one line
		$this -> SetStatus(0); // assume if errors are present, Status is 0 by default
		$this -> ErrorCount = $this -> ErrorCount + $Input; // keep count of the errors in case we decide to use it
	}
	
	function GetErrors(){
		# get current errors that have been logged by js/ajax/php
		return $this -> ErrorCount;
	}
	
	function SetAlert($Input){
		# used for pop up alerts on screen
		$this -> AlertText = $Input;
		$this -> AlertTrue = 1;
	}
	
	function SetStatus($Input){
		# class Status - typically in js 0 = bad, 1 = good
		$this -> Status = $Input;
	}
	
	function GetStatus(){
		# get Status - can be used by php to check current Status before proceeding
		return $this -> Status;
	}
	
	function CheckStatus(){
		# this function is used to check the Status after the form has been validated. 
		# if it returns anything other than 1, the form won't proceed.
		if($this -> Status != 1){
			echo $this -> MakeJson();
			exit();
		}
	}
	
	function SetRefresh($Input){
		$this -> Refresh = $Input;
	}
	
	function SetRedirect($Input){
		$this -> Redirect = $Input;
		$this -> RedirectTrue = 1;
	}
	
	function SetColorbox($Input){
		# if the Redirect is opening a new colorbox (subsquent or new popup)
		$this -> RedirectColorboxLink = $Input;
		$this -> RedirectColorbox = 1;
	}
	
	function SetText($Input){
		# this is the reply Text to show - ie settings - the new value to populate the Html with
		$this -> Text = $Input;
	}
	
	function SetHtml($Input){
		# return Html to populate an ajax element
		$this -> Html = $Input;
	}
	
	function AddBadInput($Input){
		# dependent on the input of "."(class) or "#"(id) to identify elements for js to highlight
		array_push($this -> BadInputs, $Input);
	}
		
	function AddClass($Input){
		# add class to return for js to assign to an element
		$this -> Classes = "$Input";
	}
	
	function SetPushState($Input){
		# for js history.PushState to change the url when ajaxing
		$this -> PushState = $Input;
	}
	
	function SetArray($Input){
		# this is a standard data array that we can add sub Arrays to
		# inp in this case = the sub-array name
		# this function can be used to generate multiple levels of sub-Arrays
		array_push($this -> Arrays, $Input);
	}
	
	function SetArrayData($a, $k, $v){
		# $a = array name (sub-array of $this -> Arrays)
		# $k = array key 
		# $v = array value (can also be a sub-array assigned to key)
		$this -> Arrays[$a][$k] = $v;
	}
	
	function SetOpenData($Input){
		# variable capable of accepting various data types for any other situation
		$this -> OpenData = $Input;
	}
	
	function AppendOpenData($Input){
		# alternate use for users who want to add multiple parts to $this -> OpenData
		# without having to code for appending
		$this -> OpenData .= $Input;
	}
	
	function MakeJson(){
		# return the compiled data of the entire class in a JSON array
		$newMessage = "<pre>" . $this -> Message . "<pre>";
        // var_dump(json_encode($this -> GenerateArray()));
		return json_encode($this -> GenerateArray());
	}
	
	function MakeArray(){
		# return the compiled data of the entire class in a standard array
		$newMessage = "<pre>" . $this -> Message . "<pre>";
		return $this -> GenerateArray();
	}
	
	private function GenerateArray(){
		# only called internally to produce compiled class data
		return array(
			"Message" => $this -> Message,
			"Status" => $this -> Status,
			"Refresh" => $this -> Refresh,
			"PushState" => $this -> PushState,
			"BadInputs" => $this -> BadInputs,
			"Classes" => $this -> Classes,
			"Text" => $this -> Text,
			"Html" => $this -> Html,
			"Redirect" => $this -> Redirect,
			"RedirectTrue" => $this -> RedirectTrue,
			"RedirectColorbox" => $this -> RedirectColorbox,
			"RedirectColorboxLink" => $this -> RedirectColorboxLink,
			"AlertText" => $this -> AlertText,
			"AlertTrue" => $this -> AlertTrue,
			"Arrays" => $this -> Arrays,
			"OpenData" => $this -> OpenData
		);
	}
	
}

?>