<?php

namespace Document;
use sm;
use User\SessionClass;
use Document\ErrorClass;

class ViewClass{

private $ViewsPath;
private $LayoutsPath;
private $RawViewData;
private $RawLayoutData;
private $FinalOutputData;
private $SessionVerified = 0;
private $ControllerData;
private $YieldsData;
private $SectionsData;
private $Error;
private $GlobalShortVars;

function __construct($ViewFileName, $ControllerData = null){
    $this -> ViewsPath = sm::Dir("Views");
    $this -> LayoutsPath = sm::Dir("Layouts");
    $this -> Error = new ErrorClass();
    $this -> GetViewData($ViewFileName);
    // If not using presto syntax, we can output the raw view file and stop the scripts
    if($this -> CheckPrestoSyntax() != true){
        $this -> OutputFinalView($this -> RawViewData);
        return;
    }
    if(!empty($ControllerData)) $this -> ControllerData = $ControllerData;
    $this -> ParseViewData(); // This handles checking for layout, includes and requires
    $this -> CaptureLayoutYields();
    $this -> CaptureViewSections();
    $this -> MergeLayoutViewData();
    $this -> OutputFinalView($this -> FinalOutputData);
} // __construct()

function GetViewData($ViewFileName){
    ob_start();
    require_once($ViewFileName);
    $this -> RawViewData = ob_get_clean();
    if (ob_get_contents()) ob_end_clean();
    return;
} // GetViewData()

function CheckPrestoSyntax(){
    return substr($this -> RawViewData, 0, 7) === "@presto" ?: 0;
}

function ParseViewData(){
    $SearchArray = [
        "@sess" => "GetSess",
        "@auth" => "GetAuth",
        "@layout" => "GetLayout"
    ];

    foreach($SearchArray as $Search => $Function){
        if(str_contains($this -> RawViewData, $Search))
            $this -> $Function();
    }
} // ParseViewData()

function GetSess($BypassMatch = 0){
    if(!empty(preg_match("/@sess/i", $this -> RawViewData, $SessRequired)) || $BypassMatch == 1){
        $Sess = new SessionClass();
        if($Sess -> ValidateUserSession() != 1){
            $this -> Error -> PageError("Sess");
            return false;
        }
    }
    // If we've made it this far, set SessionVerified in case auth is also being used at the same time so it doesn't repeat the cycle
    $this -> SessionVerified = 1;
} // GetSess()

function GetAuth(){
    /* 
        Pages only need to be authorized by session permission values 
        so we're building in a shortcut to check session as well if it 
        hasn't already been checked 
    */
    // If session hasn't been verified, we verify now
    if(!empty(preg_match("/@auth\((\d+)\)/i", $this -> RawViewData, $PermissionRequired))){
        if($this -> SessionVerified != 1) $this -> GetSess(true);
        $Auth = new AuthClass();
        if($Auth -> AuthUserPage($PermissionRequired[1]) != 1){
            $this -> Error -> PageError("Auth");
            return false;
        }
    }
} // GetAuth()

function GetLayout(){
    if(!empty(preg_match("/(?<!\\\)@layout\(\S+.\)/i", $this -> RawViewData, $Layout))){
        $LayoutFileName = preg_replace(array("/@layout\(/i", "/\)/"), "", $Layout[0]);
        $LayoutFileName = $this -> LayoutsPath . str_replace(".", "/", $LayoutFileName) . ".php";
        if(is_file($LayoutFileName)){
            ob_start();
                require_once($LayoutFileName);
                $this -> RawLayoutData = ob_get_clean();
            if (ob_get_contents()) ob_end_clean();
        }else{
            echo "Did you misname your layout file?";
            exit;
        }
    }
} // GetLayout()

function GetIncludes(){
    if(!empty(preg_match("/(?<!\\\)@include\(\S+.\)/i", $this -> RawViewData, $Includes))){
        $IncludesFile = preg_replace(array("/@include\(/i", "/\)/"), "", $Includes[0]);
        $IncludeFileName = str_replace(".", "/", $IncludesFile) . ".php";
        include_once sm::Dir("Views") . $IncludeFileName;
    }
} // GetIncludes()

function GetRequires(){
    if(!empty(preg_match("/(?<!\\\)@require\(\S+.\)/i", $this -> RawViewData, $Requires))){
        $RequiresFile = preg_replace(array("/@require\(/i", "/\)/"), "", $Requires[0]);
        $RequireFileName = str_replace(".", "/", $RequiresFile) . ".php";
        require_once sm::Dir("Views") . $RequireFileName;
    }
} // GetRequires()

function GetExternalView($View){
    // This is a placeholder for future functionality to be able to use multiple views, easily, in one view file.    
} // GetExternalView()

function CaptureLayoutYields(){
    preg_match_all("/@yield\(\S+\)/i", $this -> RawLayoutData, $FoundYields);
    $Yields = array();
    if(($YieldCount = count($FoundYields[0])) < 1) return false;
    $FoundYields = $FoundYields[0];
    for($i = 0; $i < $YieldCount; $i++){
        $YieldName = substr($FoundYields[$i], 6, strlen($FoundYields[$i]) - 6);
        $Yields[] = substr($YieldName, 1, strlen($YieldName) - 2);
    } // for
    if(!is_array($Yields) || count($Yields) < 1) $Yields = false;
    $this -> YieldsData = $Yields;
    return $Yields;
} // CaptureLayoutYields()

function CaptureViewSections(){
    $SectionKey = 0;

    // This expression is purposely using negactive lookback for the .0000001% chance that the user is trying to reference presto syntax in their code
    preg_match_all("/((?<!\\\)\@section\(\S+\))(.*?)((?<!\\\)\@endsection)/sm", $this -> RawViewData, $FoundSections);

    // One the .000001% off chance that there's a reference to an @section or @endsection which should be printed to the page, fix the backslashes
    $FoundSections[2] = str_replace("\@section", "@section", $FoundSections[2]);
    $FoundSections[2] = str_replace("\@endsection", "@endsection", $FoundSections[2]);
    
    foreach($FoundSections[1] as $Section){
        // Set up the section name for the array keys
        $SectionName = substr($Section, strpos($Section, "(") + 1, strpos($Section, ")") - 9);
        // Get the current section content
        $CurrentSection = $FoundSections[2][$SectionKey];
        // Check for possible dynamic variables in presto
        preg_match("{{.*}}", $CurrentSection, $Variables);
        // If variables are found, process accordingly
        if(count($Variables) > 0) $CurrentSection = $this -> ConvertSectionVariables($CurrentSection, $Variables);
        // Check for includes
        preg_match_all("/@include\(\S+\)/i", $CurrentSection, $Includes);
        if(count($Includes[0]) > 0) $CurrentSection = $this -> ConvertIncludes($CurrentSection, $Includes[0], "Include"); 
        // Check for requires
        preg_match_all("/@require\(\S+\)/i", $CurrentSection, $Requires);
        if(count($Requires[0]) > 0) $CurrentSection = $this -> ConvertIncludes($CurrentSection, $Requires[0], "Require"); 
        // Add section content to section array
        $SectionsArray[$SectionName] = $CurrentSection;
        $SectionKey ++;
    }
    if(empty($SectionsArray)) $SectionsArray = null; // Avoid warning errors if no sections in view file
    $this -> SectionsData = $SectionsArray;
    return $SectionsArray;
} // CaptureViewSections()

function ConvertSectionVariables($SectionData, $VariablesArray){
    $SplitArray = (explode(" ", str_replace(["{","}"], "", $VariablesArray[0])));
    foreach($SplitArray as $k => $v){
        if(str_contains($v, "Url::")){
            // Short Url
            $ProperVar = str_replace("Url::", "", $v);
            $SectionData = str_replace("{{{$v}}}", sm::Url($ProperVar), $SectionData);
        }elseif(str_contains($v, "Dir::")){
            // Short Dir
            $ProperVar = str_replace("Dir::", "", $v);
            $SectionData = str_replace("{{{$v}}}", sm::Dir($ProperVar), $SectionData);
        }elseif(str_contains($v, "Cus::")){
            // Short custom link
            $ProperVar = str_replace("Cus::", "", $v);
            $SectionData = str_replace("{{{$v}}}", sm::Cus($ProperVar), $SectionData);
        }elseif(!empty($this -> ControllerData[$v])){
            // Passed data from controller that matches the current variable name
            $SectionData = str_replace("{{{$v}}}", $this -> ControllerData[$v], $SectionData);
        }else{
            // The value has no matches so we just make it blank in assumption it doesn't have any core value
            $SectionData = str_replace("{{{$v}}}", "", $SectionData);
        }
    }
    return $SectionData;
} // CaptureSectionVariables()

function ConvertIncludes($SectionData, $CallsArray, $CallType){
    foreach($CallsArray as $k => $v){
        // Remove syntax and possible extension
        $FilePath = str_replace(["@require(", "@include(", ")", ".php"], "", $v);
        // Check file extension - add PHP by default
        if(!strpos($FilePath, ".")) $FilePath .= ".php";
        ob_start();
        if($CallType == "Require"){
            require_once($FilePath);
        }else{
            include_once($FilePath);
        }
        $FileData = ob_get_clean();
        $SectionData = str_replace($v, $FileData, $SectionData);
        if(ob_get_contents()) ob_end_clean();
    }
    return $SectionData;
}

function MergeLayoutViewData(){
    $OutputData = $this -> RawLayoutData;
    // Using presto but no yields in layout file
    if(empty($this -> YieldsData)){
        echo "<h2 style = 'color:red;background:black;text-align:center;padding:1em 0;'>{{{DEV ISSUE}}}</h2>It looks like your layout file has no yields but is trying to use presto.<br>To process a flat file without presto, put the HTML in the view file directly.";
        exit;
    }
    foreach($this -> YieldsData as $k => $y){
        if(!empty($this -> SectionsData[$y])){
            // $SectionData = $this -> CaptureSectionVariables($SectionData);
            $OutputData = str_replace("@yield($y)", $this -> SectionsData[$y], $OutputData);
        }else{
            // Remove unused yields
            $OutputData = str_replace("@yield($y)", "", $OutputData);
        }
    } // for
    $this -> FinalOutputData = $OutputData;
} // MergeLayoutViewData()

function OutputFinalView($OutputData){
    echo $OutputData;
} // OutputFinalView()

} // class ViewClass