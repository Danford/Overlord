<?php

require_once(oe_frontend.'html/element.php');

class Link extends ElementTag
{
    function __construct($rel, $href)
    {
        parent::__construct("link");

        $this->AddField("rel", $rel);
        $this->AddField("href", $href);
    }
}

?>