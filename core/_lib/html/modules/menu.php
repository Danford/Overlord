<?php

require_once(oe_lib.'html/tags/nav.php');

class Menu extends Nav
{
    public $menuList;
    
    function __construct()
    {
        parent::__construct();
        
        $this->menuList = $this->AddTag("ul");
    }
    
    // Adds a menu item and creates a list that will appear when the main menu item is hovered over.
    public function AddMenuList(MenuItem $item)
    {
        return $this->menuList->AddElement($item)->AddTag("ul");
    }
}


?>