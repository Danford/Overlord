<?php

require_once(oe_frontend.'html/element.php');

class Div extends ElementTag
{
    function __construct($id = null, $style = null)
    {
        parent::__construct("div");

        if (isset($id))
            $this->AddField("id", $id);
        
        if (isset($style))
            $this->AddField("style", $style);
    }
}

?>