<?php

require_once(oe_frontend.'html/element.php');
require_once(oe_frontend.'html/element_file.php');
require_once(oe_frontend.'html/tags/content.php');
require_once(oe_frontend.'html/tags/img.php');

class GridOption {	
	const None = 0;
	const Stamp = 2;
	const StampLeft = 4;
	const StampTop = 8;
	const StampTopSec = 16;
	
	const Large = 32;
	const ExtraLarge = 64;
	const GridSizer = 128;
}

class GridTile extends ElementTag {
	
	function __construct ($dataCategory = NULL, $gridOptions = GridOption::None, $feilds = array()) { 
		parent::__construct("div", $feilds);
		
		$this->gridOptions = $gridOptions;
		$this->dataCategory = $dataCategory;

		$this->AddField("class", $this->GetTileClass());
		$this->AddField("data-category", $dataCategory);
	}
	
	private $gridOptions;
	private $dataCategory;
	
	function GetTileClass() {
		$class = "";
		
		if ($this->gridOptions & GridOption::Stamp) {
			$class .= "stamp ";
		} else if ($this->gridOptions & GridOption::StampLeft) {
			$class .= "stamp stamp--left ";
		} else if ($this->gridOptions & GridOption::StampTop) {
			$class .= "stamp stamp--top ";
		} else if ($this->gridOptions & GridOption::StampTopSec) {
			$class .= "stamp stamp--top-sec ";
		}
		
		if ($this->gridOptions & GridOption::Large) {
			$class .= "grid-item grid-item--large ";
		} else if ($this->gridOptions & GridOption::ExtraLarge) {
			$class .= "grid-item grid-item--x-large ";
		}
		
		if ($this->gridOptions & GridOption::GridSizer) {
			$class = "grid-sizer";
			return $class;
		}
		
		if ($class == "")
			$class .= "grid-item ";
		
		$class .= "tile ";
		
		if (isset($this->dataCategory))
			$class .= $this->dataCategory;
		
		return $class;
	}
	
	function SetStamp() {
		$this->gridOptions |= GridOption::Stamp;
		$this->AddField("class", $this->GetTileClass());
		return $this;
	}
	
	function SetStampLeft() {
		$this->gridOptions |= GridOption::StampLeft;
		$this->AddField("class", $this->GetTileClass());
		return $this;
	}
	
	function SetStampTop() {
		$this->gridOptions |= GridOption::StampTop;
		$this->AddField("class", $this->GetTileClass());
		return $this;
	}
	
	function SetStampTopSecondary() {
		$this->gridOptions |= GridOption::StampTopSec;
		$this->AddField("class", $this->GetTileClass());
		return $this;
	}
	
	function SetLarge() {
		$this->gridOptions |= GridOption::Large;
		$this->AddField("class", $this->GetTileClass());
		return $this;
	}
	
	function SetXLarge() {
		$this->gridOptions |= GridOption::ExtraLarge;
		$this->AddField("class", $this->GetTileClass());
		return $this;
	}
	
	function GetDataCategory() {
		return $this->dataCategory;
	}
}

class Isotope extends ElementTag {
    
    var $gridCategories;
    var $html;
    var $grid;

    function __construct(html_minion $html)
    {
        parent::__construct("div");
 		$this->html = $html;
 		
 		$this->html->header->AddContent($this->GetHeadScript());
 		
        $this->grid = new ElementTag("div", array("class" => "grid"));
        $this->AddElement($this->grid);

        $this->AddGridSizer();
        
        $this->gridCategories = array();
    }
    
    function AddGridSizer() {
    	$tile = new GridTile(NULL, GridOption::GridSizer);
    	$tile->AddContent(" ");
    	$this->grid->AddElement($tile);
    }
    
    function AddTile(Element $element, $category = NULL, $options = GridOption::None) {
    	
    	if (isset($category) && !in_array($category, $this->gridCategories))
    		$this->gridCategories[] = $category;
    	
    	if ($element instanceof GridTile) {
    		$this->grid->AddElement($element);
    		return $element;
    	}
    		
    	$tile = new GridTile($category, $options);
    	$tile->AddElement($element);
    	$this->grid->AddElement($tile);
    	
    	return $tile;
    }
    
    function AddTileString($content, $category = NULL, $options = GridOption::None) {
    	if (isset($category) && !in_array($category, $this->gridCategories))
    		$this->gridCategories[] = $category;
    		 
    	$tile = new GridTile($category, $options);
    	$tile->AddContent($content);
    	$this->grid->AddElement($tile);
    	return $tile;
    }
    
    function AddTileWithTitle($title, $category = NULL, $options = GridOption::None) {
    	$element = new Element();
    	$element->AddTag("t1", array("id" => "tile-title"))->AddContent($title);
    	return $this->AddTitle($element, $category, $options);
    }
    
    function AddTileWithTitleAndImage($title, $img_url, $content = NULL, $category = NULL, $options = GridOption::None) {
    	$element = new Element();
    	$element->AddTag("t1", array("id" => "tile-title"))->AddContent($title);
    	$element->AddElement(new Img($img_url, "tile-img"));
    	
    	if (isset($content)) {
    		$element->AddTag("p", array("id" => "tile-content"))->AddContent($content);
    	}
    	return $this->AddTile($element, $category, $options);
    }

	function GetSortingButtonsElement($categories) {

		global $user;
		
		$html = '<div class="ui-group filters">
				<div class="button-group js-radio-button-group" data-filter-group="category">
					<button class="button is-checked" data-filter="">All</button>';
		
		foreach ($this->gridCategories as $category) {
			$html .= '<button class="button" data-filter=".'. $category .'">'. ucfirst($category) .'<span class="filter-count"></span></button>';
		}
		
		$html .= '	
				</div>
			</div>
			<div class="ui-group sortings">
				<div class="button-group sort-by-button-group">
					<button class="button is-checked" data-sort-by="">None</button>
					<button class="button" data-sort-by="date">Date</button>
					<button class="button" data-sort-by="title">Title</button>
				</div>
			</div>
			<div>
				<button class="button" onclick=\'AddTile("Upload Photo", "/profile/'. $user->id .'/photo/upload/");\'>Upload Photo</button>
				<button class="button" onclick=\'AddTile("Create Writing", "/profile/'. $user->id .'/writing/write/");\'>Create Writing</button>
				<button class="button" onclick=\'AddTile("Create Group", "/group/create");\'>Create Group</button>
			</div>';
		
		return $html;
	}
	
	function GetHeadScript() {
		$headscript = '<script type="text/javascript">
		//
		//Executed by onload from html for images loaded in grid.
		//
		
		function ImageLoaded(img){
			var $img = $(img);
				$img.removeClass("loading");
			$img.parent().find(".cssload-fond").css("display", "none");
		
			if (typeof $grid != "undefined")
				$grid.isotope("layout");
		};
		
		</script>';
		
		return $headscript;
	}
}
?>
