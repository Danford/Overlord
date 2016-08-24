<?php

require_once(oe_lib.'html/element.php');

class Img extends ElementTag
{
    function __construct($src, $style = null)
    {
        parent::__construct("img");

        $this->AddField("src", $src);
        
        if (isset($style))
            $this->AddField("style", $style);
    }
}

?>