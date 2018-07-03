<?php
/*
    Plugin Name: Knwoledgeworks Jobs Plugin
    Plugin URI: https://www.knowledgeworks.pt
    Description: Plugin to get information from Jobs Api
    Version: 1.0
    Author: Joao Tomaz
    Author URI: https://www.linkedin.com/in/joaotomaz
    License: GPLv2+
*/

function content_creation() {

    $kw_url = "http://api.cvwarehouse.com/cvwOS_Wondercom/b1d5972d-df98-4f96-8b5d-86367700f93f/Job/own_website/Json1_7";
    $kw_file = file_get_contents($kw_url);

    // Try and clean bad chars
    // This will remove unwanted characters.
    // Check http://www.php.net/chr for details
    for ($i = 0; $i <= 31; ++$i) { 
        $kw_file = str_replace(chr($i), "", $kw_file); 
    }
    $kw_file = str_replace(chr(127), "", $kw_file);

    // This is the most common part
    // Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
    // here we detect it and we remove it, basically it's the first 3 characters 
    if (0 === strpos(bin2hex($kw_file), 'efbbbf')) {
    $kw_file = substr($kw_file, 3);
    }

    $kw_output = "NO OUTPUT";
    global $json;
    $json = json_decode($kw_file, true);
    if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
        $kw_output = "incorrect data";
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $kw_output = $kw_output.' - No errors';
            break;
            case JSON_ERROR_DEPTH:
                $kw_output = $kw_output. ' - Maximum stack depth exceeded';
            break;
            case JSON_ERROR_STATE_MISMATCH:
                $kw_output = $kw_output. ' - Underflow or the modes mismatch';
            break;
            case JSON_ERROR_CTRL_CHAR:
                $kw_output = $kw_output. ' - Unexpected control character found';
            break;
            case JSON_ERROR_SYNTAX:
                $kw_output = $kw_output. ' - Syntax error, malformed JSON';
            break;
            case JSON_ERROR_UTF8:
                $kw_output = $kw_output. ' - Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
            default:
                $kw_output = $kw_output. ' - Unknown error';
            break;
        }
    } else {
        $kw_output = "<table><tbody>";
        foreach ($json as $element) {
            foreach ($element as $subElement) {
                $kw_output = $kw_output."<tr>";

                $kw_output = $kw_output."<td>".$subElement["@id"] . "</td>";
               // $jobName = $subElement["name"];

                //if ($name["@lang"] == "pt-PT") {
                    $kw_output = $kw_output."<td><a target='_blank' href='".$subElement["urls"]["cleanApplicationUrl"]["#text"]."'>".$subElement["name"]["#text"] . "</a></td>";
                //}
                    $kw_output = $kw_output."<td>".$subElement["place"]["regions"][0]["name"]["#text"]."</td>";
                

                $kw_output = $kw_output."</tr>";
            }
        }
        $kw_output = $kw_output."</tbody></table>";
    }

    return $kw_output;
}
add_shortcode('kwjobposting', 'content_creation');


function variables_json(){
    $kw_output = $kw_output.$json["job"][0]["@id"];
   // foreach($json as $element){
   //     foreach($element as $subelement){
   //             $kw_output = $kw_output.$element["@id"];
                //$kw_output = $kw_output.$subelement["place"]["regions"][0]["name"]["#text"];
            
   //     }
  //  }
    
    return $kw_output;
}

add_shortcode('variables','variables_json');
?>