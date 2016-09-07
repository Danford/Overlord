<?php

require_once(oe_frontend.'html/element.php');


class Header extends ElementTag
{
    function __construct($sitename)
    {
        parent::__construct("div", array("class" => "header-container"));
        
        $header = $this->AddTag("header", array("class" => "wrapper clearfix"));
        $header->AddTag("h1", array("class" => "title"))->AddContent($sitename);
    }
}

?>