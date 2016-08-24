<?php

require_once(oe_lib.'html/element.php');

class Body extends ElementTag
{
    function __construct()
    {
        parent::__construct("body");
        
        $ieMsg = new ElementTag("p", array("class" => "browserupgrade"));
        $ieMsg->AddContent('You are using an <string>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.');
        
        $this->AddIf("lt IE 8", $ieMsg);
    }
}
?>