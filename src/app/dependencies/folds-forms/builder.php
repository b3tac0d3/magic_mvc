<?php

namespace Folds;
use sm;

class Builder{
    
    private $Elements;

    function Label($Value, $Attributes = []){
        // $Elements is optional and allows for DOM Elements to be added inside label tags for certain types of CSS designs
        $ElementAttributes = $this -> ProcessAttributes($Attributes);

        $this -> Elements .= $Output = "<label $ElementAttributes>$Value</label>";
        return $Output;
    } // label()

    function Input($Type, $Attributes = []){
        /*  Example
            Input -> ("text", [
                "name:username", 
                "placeholder:username", 
                "id:username", 
                "readonly", 
                "class:class1 class2 class3"
            ]);
        */
        $ElementAttributes = $this -> ProcessAttributes($Attributes);
        
        $this -> Elements .= $Output = "<Input type = '$Type' $ElementAttributes/>";
        return $Output;
    } // Input()

    function Select($Attributes = [], $Options = null){
        $ElementAttributes = $this -> ProcessAttributes($Attributes);

        $this -> Elements .= $Output = "<Select $ElementAttributes>$Options</Select>";
        return $Output;
    } // Select()

    function SelectOption($Value, $Attributes = []){
        // If attributes doesn't contain a "value" option, option value will default to name value
        $ElementAttributes = $this -> ProcessAttributes($Attributes);

        if(!empty($ElementAttributes) && !strpos($ElementAttributes,"value=")){
            // No user assigned value so given by function
            $ElementAttributes .= " value='$Value'";
        }

        $this -> Elements .= $Output = "<option $ElementAttributes>$Value</option>";
        return $Output;
    } // SelectOption()

    function SelectOptGroup($Label, $Options, $Attributes = []){
        $ElementAttributes = $this -> ProcessAttributes($Attributes);

        $this -> Elements .= $Output = "<optgroup label='$Label' $ElementAttributes>$Options</optgroup>";
        return $Output;
    } // SelectOptGroup()

    function Textarea($Attributes = [], $Value = null){
        $ElementAttributes = $this -> ProcessAttributes($Attributes);

        $this -> Elements .= $Output = "<Textarea $ElementAttributes>$Value</Textarea>";
        return $Output;
    } // Textarea()

    function Button($Value, $Attributes = []){
        $ElementAttributes = $this -> ProcessAttributes($Attributes);

        $this -> Elements .= $Output = "<Button $ElementAttributes>$Value</Button>";
        return $Output;
    } // Button()

    function Meter(){} // Meter()

    function Progress(){} // Progress()

    function Div($Attributes = [], $ElementsArray = []){
        /* Example
        Div(
            ["class:text-center"],
            [$x -> Input("text", ["id:username"])]
        ); 

        $element_tag can be antying but defaults to Div
        */
        $DivElements = null;

        // Elements inside the Div
        foreach($ElementsArray as $e){
            $DivElements .= $e;
        }

        $ElementAttributes = $this -> ProcessAttributes($Attributes);

        $this -> Elements .= $Output = "<Div $ElementAttributes>$DivElements</Div>";
        return $Output;
    } // Div()

    function Element($Tag, $Attributes = [], $ElementsArray = []){
            $Elements = null;

            // Elements inside the Div
            foreach($ElementsArray as $e){
                $Elements .= $e;
            }
    
            $ElementAttributes = $this -> ProcessAttributes($Attributes);
    
            $this -> Elements .= $Output = "<$Tag $ElementAttributes>$Elements</$Tag>";
            return $Output;
    } // Element()

    function Form($Attributes = [], $ElementsArray = []){
        $ElementAttributes = $this -> ProcessAttributes($Attributes);
        $ElementOutput = null;
        foreach($ElementsArray as $e){
            $ElementOutput .= $e;
        }

        // FIND A WAY TO WORK IN THE MESSAGE CLEANER AND WITH OPTIONS IN THE FUTURE 10/6/23
        return "
            <Form $ElementAttributes>
                <Div class = 'form_message text-danger'></Div>
                {$ElementOutput}
            </Form>";
    } // mk_form()

    function PrintForm($Attributes = []){
        echo $this -> Form($Attributes);
    } // PrintForm()

    function Action($Input){
        // Allow Action to be entered without .php and with dir-dot syntax
        return $Input = str_replace(".", "/", $Input) . ".php";
    } // Action()

    private function ProcessAttributes($Attributes){
        // Stop if no attributes assigned
        if(empty($Attributes)) return;
        
        $OutputAttributes = null;
        
        // Loop attributes and process
        foreach($Attributes as $a){
            $att_arr = explode("|", $a);
            $Key = $att_arr[0];

            // Check for attribute short names
            if(strlen($Key) == 2) $Key = $this -> ProcessShortAttribute($Key);
            
            // Check if attribute is static or dynamic
            if(isset($att_arr[1]) && !empty($val = $att_arr[1])){
                $OutputAttributes .= "$Key='$val'";
            }else{
                $OutputAttributes .= "$Key"; // Assign static attribute (ex: autofocus)
            } // if
            
            // Space between attributes for output readability
            if(next($Attributes) != 0) $OutputAttributes .= " ";
        } // for

        return $OutputAttributes;
    } // ProcessAttributes()

    private function ProcessShortAttribute($In){
        switch(strtolower($In)){
            default:
                $Out = $In;
                break;
            case "nm":
                $Out = "name";
                break;
            case "cl":
                $Out = "class";
                break;
            case "tr":
                $Out = "target";
                break;
            case "ac":
                $Out = "Action";
                break;
            case "st":
                $Out = "style";
                break;
            case "ph":
                $Out = "placeholder";
                break;
        }
        return $Out;
    } // ProcessShortAttribute()

    private function ProcessShortInputName($In){
        match(strtolower($In)){
            default => $Out = $In,
            "cl" => $Out = "color",
            "dt" => $Out = "date",
            "dl" => $Out = "datetime-local",
            "em" => $Out = "email",
            "fl" => $Out = "file",
            "hd" => $Out = "hidden",
            "im" => $Out = "image",
            "mn" => $Out =  "month",
            "nm" => $Out = "number",
            "pw" => $Out = "password",
            "rd" => $Out = "radio",
            "rn" => $Out = "range",
            "re" => $Out = "reset",
            "se" => $Out = "search",
            "sm" => $Out = "submit",
            "te" => $Out = "tel",
            "tx" => $Out = "text",
            "tm" => $Out = "time",
            "ur" => $Out = "url",
            "wk" => $Out = "week"
        };
        return $Out;
    }
}