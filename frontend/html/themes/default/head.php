<?php

require_once(oe_frontend.'html/element.php');

require_once(oe_frontend.'html/tags/link.php');
require_once(oe_frontend.'html/tags/script.php');

class ThemeHead extends Head
{
	function __construct($title)
	{
		parent::__construct("head");

		$this->AddElement(new Link("apple-touch-icon", "apple-touch-icon.png"));
		
		$this->AddElement(new Link("stylesheet", "/css/normalize.min.css"));
		
		$this->AddElement(new Script("/js/modernizr-2.8.3-respond-1.4.2.min.js"));
		$this->AddElement(new Script("https://code.jquery.com/jquery-3.1.0.js"));
		$this->AddElement(new Script("/js/overlord.js"));
	}
}

?>
