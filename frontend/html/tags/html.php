<?php

require_once(oe_frontend. 'html/element.php');

class Html extends ElementTag
{
    function __construct()
    {
        parent::__construct("!doctype html", array(), "html");
        
        $htmlIe6 = new ElementTag("html", array("class" => "no-js lt-ie9 lt-ie8 lt-ie7", "lang" => "en"));
        $htmlIe7 = new ElementTag("html", array("class" => "no-js lt-ie9 lt-ie8", "lang" => "en"));
        $htmlIe8 = new ElementTag("html", array("class" => "no-js lt-ie9", "lang" => "en"));
        $htmlIe9 = new ElementTag("html", array("class" => "no-js", "lang" => "en"));
        
        $this->AddIf("lt IE 7", $htmlIe6);
        $this->AddIf("IE 7", $htmlIe7);
        $this->AddIf("IE 8", $htmlIe8);
        $this->AddIf("gt IE 8", $htmlIe9);
    }
}

?>