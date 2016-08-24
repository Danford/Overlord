<?php

require_once(oe_lib.'html/element.php');

class Content extends ElementTag
{
function __construct()
    {
        parent::__construct("content");
        
        // todo: this should be moved somewhere else
        //$header = $this->AddTag("header", array("class" => "wrapper clearfix"));
        //$header->AddTag("h1", array("class" => "title"))->AddContent($title);
    }
}
?>