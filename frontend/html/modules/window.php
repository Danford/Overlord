<?php

require_once(oe_frontend.'html/element.php');
require_once(oe_frontend.'html/element_file.php');
require_once(oe_frontend.'html/tags/content.php');
require_once(oe_frontend.'html/modules/menu.php');

class Window extends Element
{
    public $submenu;
    public $section;
    public $content;

    function __construct($title, $file)
    {
        parent::__construct(array());

        $this->submenu = $this->header->AddElement(new Menu());
        $this->submenu->AddElement(new MenuItem("Profile", "profile"));

        $contentWrapper = $this->body->AddElement(new Div("content-wrapper"));
        $this->content = $contentWrapper->AddElement(new Content($title));

        $this->content->AddElement(new ElementFile($file));
    }
}

?>