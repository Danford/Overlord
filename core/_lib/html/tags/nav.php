<?php

require_once(oe_lib. 'html/element.php');

class Nav extends ElementTag
{
    function __construct()
    {
        parent::__construct("nav");
    }
}