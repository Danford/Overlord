<?php

require_once(oe_frontend.'html/element.php');
require_once(oe_frontend.'html/element_file.php');
require_once(oe_frontend.'html/tags/content.php');
require_once(oe_frontend.'html/tags/img.php');

require_once(oe_isotope.'utility_tile.php');

class GridOption {	
	const None = 0;
	const Stamp = 2;
	const StampLeft = 4;
	const StampTop = 8;
	const StampTopSec = 16;
	
	const Large = 32;
	const ExtraLarge = 64;
	const GridSizer = 128;
	const GridGutter = 256;
	const IgnoreClick = 512;
	const Destructable = 1024;
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

		if ($this->gridOptions & GridOption::GridSizer) {
			$class = "grid-sizer";
			return $class;
		}
		
		if ($this->gridOptions & GridOption::GridGutter) {
			$class = "grid-gutter";
			return $class;
		}
		
		if ($this->gridOptions & GridOption::IgnoreClick) {
			$class .= "ignore-click ";
		}
		
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
	
	function PrintPhoto($photoId = 0, $module, $moduleId) {
		global $oepc;
		
		if ($photoId == 0)
			echo "<img class='loading' onload='ImageLoaded(this)' src='/images/noavatar.png'>";
		else
			echo "<img class='loading' onload='ImageLoaded(this)' src='/". $module ."/". $moduleId ."/photo/". $photoId .".png'>";
	}
	function get_words($sentence, $count = 10) {
		return implode(' ', array_slice(explode(' ', $sentence), 0, $count));
	}
}

class Isotope extends ElementTag {
    
    var $gridCategories;
    var $page;
    var $grid;

    function __construct(page_minion $page)
    {
        parent::__construct("div");
 		$this->page = $page;

 		$page->js_minion->addFile("//npmcdn.com/isotope-layout@3/dist/isotope.pkgd.js");
 		$page->js_minion->addFile("//npmcdn.com/isotope-packery@2/packery-mode.pkgd.js");
 		$page->js_minion->addFile(oe_js . "imagesloaded.pkgd.js");
 		$page->js_minion->addFile(oe_js . "isotope.js", true);
 		
 		$this->page->html_minion->head->AddContent($this->GetHeadScript());
 		

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
    		if ($element->GetDataCategory() !== NULL && !in_array($element->GetDataCategory(), $this->gridCategories))
    			$this->gridCategories[] = $element->GetDataCategory();
    			
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
    
	function GetHeadScript() {
		$headscript = '<script type="text/javascript">
		//
		//Executed by onload from html for images loaded in grid.
		//
		
		function ImageLoaded(img){
			var $img = $(img);
				$img.removeClass("loading");
			$img.parent().find(".cssload-fond").css("display", "none");
		
			if ($img.width() / $img.height() > 1.6) {
				if ($img.parent().parent().hasClass("grid-item")) {
					$img.parent().parent().addClass("grid-item--medium");
				}
			}
			
			if (typeof $grid != "undefined")
				$grid.delay(310).isotope("layout");
		};
		
		</script>';
		
		return $headscript;
	}
	
	function Serve() {
		if (count($this->gridCategories) > 0)
			$this->AddTile(new UtilityTile($this->gridCategories));
		
		parent::Serve();
	}
}
?>
