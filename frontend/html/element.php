<?php

require_once(oe_frontend."serveable_minion.php");
require_once(oe_frontend."html/field.php");

class Element extends serveable_minion
{
    function __construct($elements = array())
    {
        $this->parent = null;
        $this->elements = $elements;
        
        $cooked = null;
    }
    
    protected $parent;
    
    public function AddElement(ElementTag $newElement)
    {
        $newElement->SetParent($this);
        $this->elements[] = $newElement;
        return $newElement;
    }

    public function PrependContent($string)
    {
        array_unshift($this->elements, $string);
    }
    
    public function AddContent($string)
    {
        $this->elements[] = $string;
    }
    
    public function AddTag($tag, $fields = array(), $closeTag = null)
    {
        if (is_string($tag))
            $newTag = new ElementTag($tag, $fields, $closeTag);
        else
        {
            echo "Error: Invalid tag parameter defined!";
            return null;
        }
        
        $newTag->SetParent($this);
        
        $this->elements[] = $newTag;
        
        return $newTag;
    }

    public function RemoveElement(Element $element)
    {
        $this->elements = array_diff($this->elements, array($element));
    }

    public function SetParent(Element $parent)
    {
        $this->parent = $parent;
    }
}

class ElementTag extends Element
{
    private $tag;
    private $closeTag;
    private $fields;
    private $isBufferOpen;

    function __construct($tag, $fields = array(), $closeTag = null)
    {
        parent::__construct();

        $this->fields = array();
        $this->tag = $tag;
        $this->closeTag = $closeTag;
        $this->isBufferOpen = false;
        
        foreach ($fields as $key => $value)
            $this->AddField($key, $value);
    }
    
    function __destruct()
    {
    	if ($this->isBufferOpen == true)
    	{
    		$this->CloseBuffer();
    		$this->AddContent("Error: buffer was not closed properly.");
    	}
    }

    /* AddField will create a new field for the html tag.
     * ie <tag fieldName="value" fieldName="value">
     */
    public function AddField($name, $value)
    {
    	foreach ($this->fields as $field) {
    		if ($field->name == $name) {
    			$field->value = $value;
    			return;
    		}
    	}
    	
        $this->fields[] = new Field($name, $value);
    }

    public function AddIf($logic, $element)
    {
        $statment = $this->AddTag("!--[if $logic]", array(), "![endif]--");
        $statment->AddElement($element);
    }

    private function OpenTag()
    {
        // Add all fields as such <tag fieldName="value" fieldName="value">
        echo "<" . $this->tag;
        
        if (count($this->fields) > 0)
            foreach ($this->fields as $field)
                $field->Serve();

        echo ">";
    }

    private function CloseTag()
    {
        // Check for special closing tag.
        if (isset($this->closeTag))
            $code .= "<" . $this->closeTag . ">";
        else
            $code .= "</" . $this->tag . ">";

        echo $code;
    }

    public function Serve()
    {
        $this->OpenTag();

        // print the close tag if this contains elements or if it has special close tag.
        if (count($this->elements) > 0 || isset($this->closeTag))
        {
            parent::Serve();
            $this->CloseTag();
        }
    }
    
    public function OpenBuffer()
    {
    	if ($this->isBufferOpen == true)
    	{
    		$this->AddContent("Error: buffer is already open.");
    		return;
    	}
    	
    	$this->isBufferOpen = true;
    	
    	ob_start();
    }
    
    public function CloseBuffer()
    {
    	if ($this->isBufferOpen == false)
    	{
    		$this->AddContent("Error: buffer is already closed.");
    		return;
    	}
    	
    	$content = ob_get_contents();
    		 
		if (isset($content))
			$this->AddContent($content);
		
		$this->isBufferOpen = false;
			
		ob_end_clean();
    }
}
?>