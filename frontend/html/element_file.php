<?php

include_once (oe_frontend. 'html/element.php');

class ElementFile extends Element
{
    private $file;

    function __construct($file)
    {
        parent::__construct();

        $this->file = $file;
    }

    public function Cook()
    {
        
    }

    public function Serve()
    {
        parent::Serve();
        require $this->file;
    }
}