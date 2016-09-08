<?php

require_once(oe_frontend.'html/element.php');

require_once(oe_frontend.'html/tags/link.php');
require_once(oe_frontend.'html/tags/script.php');
require_once(oe_frontend.'html/tags/div.php');

class Head extends ElementTag
{
    function __construct($title)
    {
        parent::__construct("head");

        // todo: get details such as title from options.
        $this->AddTag("meta", array("charset" => "utf-8"));
        $this->AddTag("meta", array("http-equiv" => "X-UA-Compatible", "content" => "IE=edge,chrome=1"));
           
        $this->AddTag("title")->AddContent($title);
        
        $this->AddTag("meta", array("name" => "description", "content" => ""));
        $this->AddTag("meta", array("name" => "viewport", "content" => "width=device-width, initial-scale=1"));
        
        $this->AddElement(new Link("apple-touch-icon", "apple-touch-icon.png"));
        
        $this->AddElement(new Link("stylesheet", "css/normalize.min.css"));
        
        $this->AddElement(new Script("/js/modernizr-2.8.3-respond-1.4.2.min.js"));
        $this->AddElement(new Script("https://code.jquery.com/jquery-3.1.0.js"));
        $this->AddElement(new Script("/js/overlord.js"));
    }
}

?>