<?php

require_once(oe_frontend."serveable_minion.php");

class Field extends serveable_minion
{
    public $name;
    public $value;

    function __construct($fieldName, $fieldValue)
    {
        $this->name = $fieldName;
        $this->value = $fieldValue;
    }

    public function Serve()
    {
        echo ' ' . $this->name . '="' . $this->value . '"';
    }

    public function Cook()
    {
        
    }
}

?>