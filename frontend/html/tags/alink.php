<?php

require_once(oe_frontend. '/html/element.php');

class ALink extends ElementTag
{

    function __construct($text, $href, $rel = null, $media = null, $fields = array())
    {
        parent::__construct("a", $fields);

        $this->AddField("href", $href);

        if (isset($rel))
            $this->AddField("rel", $rel);

        if (isset($media))
            $this->AddField("media", $media);

        $this->AddContent($text);
    }
}

?>