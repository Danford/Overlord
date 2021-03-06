<?php

require_once(oe_frontend. '/html/element.php');
require_once(oe_frontend. '/html/element_tag.php');

class ElementHandler
{
    private $elements;
    
    function __construct()
    {
        $elements = array();
    }
    
    public function AddElement(Element $newElement)
    {
        $this->elements[] = $newElement;
    }
    
    public function AddTag($tag, $fields = array())
    {
        $newTag = new ElementTag($tag, $fields);
        return $newTag;
    }
    
    public function AddContent($string)
    {
        $this->elements[] = $string;
    }
    
    public function RemoveElement(Element $element)
    {
        $this->elements = array_diff($this->elements, array($element));
    }
    
    public function GetElements()
    {
        return $this->elements;
    }
}