<?php

require_once (oe_frontend. '/html/element_tag.php');
/*
 * This class manages a table. To add cells use
 * AddTag, AddString, or AddElement as you would
 * with any other element.
 */
class Table extends ElementTag
{

    private $rowWidth;

    private $hasHead;

    function __construct($rowWidth, $fields = array(), $hasHead = true)
    {
        parent::__construct("table", $fields);

        $this->rowWidth = $rowWidth;
        $this->hasHead = $hasHead;
    }

    /*
     * This function is called before Render() to prepair
     * the object without bothering the developer to specify
     * things such as <tr> <td> <thead> <tbody>
     */
    public function Cook()
    {
        // asign elements to items so we can add new tags
        // without iterating through the new ones.
        $items = $this->elements;

        // if head is used create it.
        if ($this->hasHead)
            $tableHead = $this->AddTag("thead");

        // create body tag.
        $tableBody = $this->AddTag("tbody");

        $count = 0;
        $row = null;

        // iterate through the items and add them to the
        // proper slot in the table.
        foreach ($items as $item)
        {
            $element = null;

            // if this is the first row add to tableHead otherwise tableBody
            if ($this->hasHead && $count / $this->rowWidth < 1)
                $element = $tableHead;
            else
                $element = $tableBody;

            // creates a new tr for every x columns
            if ($count % $this->rowWidth == 0)
                $row = $element->AddTag("tr");

            // remove item from table and assign it to the table slot
            $this->RemoveElement($item);
            $column = $row->AddTag("td")->AddElement($item);

            $count++;
        }
    }
}
?>