<?php

require_once(oe_frontend.'html/element.php');

class Img extends ElementTag
{
    function __construct($src, $class = NULL, $id = NULL, $style = NULL, $fields = array())
    {
        parent::__construct("img", $fields);

        if (isset($class))
        	$this->AddField("class", $class);
        
        $this->AddField("src", $src);
        
        if (isset($id))
        	$this->AddField("id", $id);

       	if (isset($style))
        	$this->AddField("style", $style);
    }
}

?>