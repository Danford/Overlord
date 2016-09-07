<?php

require_once(oe_frontend. 'html/element.php');

class Main extends ElementTag
{
    function __construct()
    {
        parent::__construct("div", array("class" => "main-container"));

        $main = $this->AddTag("main", array("class" => "main wrapper clearfix"));

        // todo: implement ARIA option.
        //if ($isARIA == true)
        $main->AddField("role", "main");
        
        $article = $main->AddTag("article");
        $header = $article->AddTag("header");
        $header->AddTag("h1")->AddContent("article header h1");
        $header->AddTag("p")->AddContent("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales urna non odio egestas tempor. Nunc vel vehicula ante. Etiam bibendum iaculis libero, eget molestie nisl pharetra in. In semper consequat est, eu porta velit mollis nec.");
        
        for ($i = 0; $i < 2; $i++)
        {
            $section = $article->AddTag("section");
            $section->AddTag("h2")->AddContent("article section h2");
            $section->AddTag("p")->AddContent("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales urna non odio egestas tempor. Nunc vel vehicula ante. Etiam bibendum iaculis libero, eget molestie nisl pharetra in. In semper consequat est, eu porta velit mollis nec. Curabitur posuere enim eget turpis feugiat tempor. Etiam ullamcorper lorem dapibus velit suscipit ultrices. Proin in est sed erat facilisis pharetra.");
        }
        
        $footer = $article->AddTag("footer");
        $footer->AddTag("h3")->AddContent("article footer h3");
        
        $aside = $main->AddTag("aside");
        $aside->AddTag("h3")->AddContent("aside");
        $aside->AddTag("p")->AddContent("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales urna non odio egestas tempor. Nunc vel vehicula ante. Etiam bibendum iaculis libero, eget molestie nisl pharetra in. In semper consequat est, eu porta velit mollis nec. Curabitur posuere enim eget turpis feugiat tempor. Etiam ullamcorper lorem dapibus velit suscipit ultrices.");
    }
}