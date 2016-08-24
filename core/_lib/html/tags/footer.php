<?php

require_once(oe_lib.'html/element.php');

class Footer extends ElementTag
{
    function __construct($sitename)
    {
        parent::__construct("div", array("class" => "footer-container"));
        
        $footer = $this->AddTag("footer", array("class" => "wrapper"));
        $footer->AddTag("h3")->AddContent("Copyright &copy; ". date("Y"). " ". $sitename .". All rights reserved.");
        
    }
}

?>