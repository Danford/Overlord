<?php

require_once(oe_frontend.'html/element.php');

require_once(oe_frontend.'html/tags/html.php');
require_once(oe_frontend.'html/tags/head.php');
require_once(oe_frontend.'html/tags/body.php');
require_once(oe_frontend.'html/tags/header.php');
require_once(oe_frontend.'html/tags/content.php');
require_once(oe_frontend.'html/tags/footer.php');

require_once(oe_frontend.'html/modules/menu.php');

class Window extends Element
{
    public $html;
    public $head;
    public $body;
    public $header;
    public $menu;
    public $section;
    public $content;
    public $footer;

    function __construct()
    {
        parent::__construct(array());

        $this->html = $this->AddElement(new Html());
        $this->head = $this->html->AddElement(new Head());
        $this->body = $this->html->AddElement(new Body());
        $this->header = $this->body->AddElement(new Header());
        $this->menu = $this->header->AddElement(new Menu());
        $this->content = $this->body->AddElement(new Content());
        $this->footer = $this->body->AddElement(new Footer());
        
        $this->content->AddContent("Test");
        $this->menu->AddElement(new MenuItem("Profile", "profile"));
    }
}

?>