<?php

require_once(oe_lib. '/html/tags/alink.php');

class MenuItem extends ElementTag
{
    function __construct($label, $target, $id = null, $css = null, $js = null)
    {
        parent::__construct("li");
        $a = $this->AddElement(new ALink($label, $target));
        
        if (isset($id))
            $a->AddField("id", $id);
        
        if (isset($css))
            $a->AddField("style", $css);

        if (isset($js))
            $a->AddField("onclick", $js);
    }
}

?>