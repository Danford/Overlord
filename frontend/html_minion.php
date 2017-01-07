<?php
require_once (oe_frontend . 'html/element.php');

require_once (oe_frontend . 'html/tags/html.php');
require_once (oe_frontend . 'html/tags/head.php');
require_once (oe_frontend . 'html/tags/body.php');
require_once (oe_frontend . 'html/tags/header.php');
require_once (oe_frontend . 'html/tags/content.php');
require_once (oe_frontend . 'html/tags/footer.php');

require_once (oe_frontend . 'html/modules/menu.php');
require_once (oe_frontend . 'html/modules/menu_item.php');
require_once (oe_frontend."html/modules/menu_config.php");

class html_minion extends Element
{

    /*
     * html_minion( $title[, $externalcss, $externaljs, $doctype ] ) {
     *
     * $externalcss and $externaljs can be single values, arrays, or comma delimited strings
     *
     */
    public $html;
    public $head;
    public $body;
    public $header;
    public $menu;
    public $section;
    public $content;
    public $footer;

    function __construct($title)
    {
        global $user;
        $sitename = "Overlord";
        parent::__construct(array());
        
        $this->html = $this->AddElement(new Html());
        $this->head = $this->html->AddElement(new Head($sitename . " - " . $title));
        $this->body = $this->html->AddElement(new Body());
        $this->header = $this->body->AddElement(new Header($sitename));
        $contentWrapper = $this->body->AddElement(new Div("content-wrapper"));
        $this->content = $contentWrapper->AddElement(new Content($title));
        $this->footer = $this->body->AddElement(new Footer($sitename));

        $this->menu = $this->header->AddElement(new Menu());
        PrintMenu($this);
    }
}

?>