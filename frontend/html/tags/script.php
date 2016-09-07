<?php

require_once(oe_frontend. '/html/element.php');

class Script extends ElementTag
{

    function __construct($src = null, $code = null)
    {
        parent::__construct("script", array(), "/script");

        if (isset($src))
            $this->AddField("src", $src);

        if (isset($code))
            $this->AddContent($code);
    }
}

?>