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

function kwjobs_option_menu_page(){
    add_menu_page("KW Jobs Options", "KW Jobs","manage_options","kwplugin","kwjobs_option_page",get_stylesheet_directory_uri('stylesheet_directory')."/icon/logo_footer.png");
}
add_action('admin_menu','kwjobs_option_menu_page');

function kwjobs_option_page(){?>
    <dic class="wrap">
    <h1>Knowledgeworks Jobs Plugin Information</h1>
    </div>
    <style type="text/css">
    .tg  {border-collapse:collapse;border-spacing:0;border-color:#ccc;}
    .tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-top-width:1px;border-bottom-width:1px;border-color:#ccc;color:#333;background-color:#fff;}
    .tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-top-width:1px;border-bottom-width:1px;border-color:#ccc;color:#333;background-color:#f0f0f0;}
    .tg .tg-baqh{text-align:center;vertical-align:top}
    .tg .tg-sprd{background-color:#f9f9f9;border-color:#cccccc;vertical-align:top}
    .tg .tg-lv9y{font-weight:bold;background-color:#ff0066;color:#ffffff;text-align:center}
    .tg .tg-z8ml{font-weight:bold;background-color:#ff0066;color:#ffffff;vertical-align:top}
    .tg .tg-yw4l{vertical-align:top}
    .tg .tg-b7b8{background-color:#f9f9f9;vertical-align:top}
    </style>
    <table class="tg" >
    <colgroup>
    <col style="width: 263px">
    <col style="width: 816px">
    </colgroup>
    <tr>
    <th class="tg-lv9y">Shortcode Tag</th>
    <th class="tg-z8ml">Description</th>
    </tr>
    <tr>
    <td class="tg-baqh">[kwjobposting]<br></td>
    <td class="tg-sprd">Adiciona uma tabela numa página ou post com os empregos disponíveis</td>
    </tr>
    <tr>
    <td class="tg-baqh">pt-PT</td>
    <td class="tg-sprd">Linguagem default apresentada nos empregos.</td>
    </tr>
    </table>
    <?php }
    
    function content_creation() {
        $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,5);
        echo ("Current Language: ".$browserLang);
            $kw_url = "http://api.cvwarehouse.com/cvwOS_Wondercom/b1d5972d-df98-4f96-8b5d-86367700f93f/Job/own_website/Json1_7/".$browserLang;
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
            $kw_company = "Wondercom";
            $kw_output = "<table><tbody>";
            $kw_output = $kw_output."<tr><td>ID</td><td>Emprego</td><td>Local</td><td>Lang</td></tr>";
            foreach ($json as $element) {
                foreach ($element as $subElement) {
                    
                    if ($subElement["name"]["@lang"] === $browserLang && $subElement["owner"]["company"]["@internalName"] === $kw_company) {
                        $kw_output = $kw_output."<tr>";
                        $kw_output = $kw_output."<td>".$subElement["@id"] . "</td>";
                        $kw_output = $kw_output."<td><a target='_blank' href='".$subElement["urls"]["cleanApplicationUrl"]["#text"]."'>".$subElement["name"]["#text"] . "</a></td>";
                        $kw_output = $kw_output."<td>".$subElement["place"]["regions"][0]["name"]["#text"]."</td>";
                        //$kw_output = $kw_output."<td> English </td>";
                        $kw_output = $kw_output."</tr>";
                    }
                    else {
                        if($subElement["owner"]["company"]["@internalName"] === $kw_company){
                            $kw_output = $kw_output."<tr>";
                            $kw_output = $kw_output."<td>".$subElement["@id"] . "</td>";
                            $kw_output = $kw_output."<td><a target='_blank' href='".$subElement["urls"]["cleanApplicationUrl"]["#text"]."'>".$subElement["name"]["#text"] . "</a></td>";
                            $kw_output = $kw_output."<td>".$subElement["place"]["regions"][0]["name"]["#text"]."</td>";
                            $kw_output = $kw_output."</tr>";
                        }
                    }
                    
                }  
                
            }
            
            $kw_output = $kw_output."</tbody></table>";
        }
        
        return $kw_output;
    }
    add_shortcode('kwjobposting', 'content_creation');
    ?>