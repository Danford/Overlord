<?php

include_once 'element.php';

class ElementContent extends Element
{
    private $content;

    function __construct($content)
    {
        parent::__construct();

        $this->content = $content;
    }

    public function Cook()
    {
        
    }

    public function Serve()
    {
        parent::Serve();
        echo $this->content;
    }
}