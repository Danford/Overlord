<?php


/*
 *  apiCall - "write"
 *  
 *      title - string 75 chars max
 *      subtitle - string 25 chars max
 *      copy - text/html
 *      privacy - int
 * 
 */
include(oe_frontend."page_minion.php");
include(oe_frontend."html/modules/isotope.php");
include(oe_frontend."html/modules/add_writing_tile.php");


if (!isset($_GET['ajax']))
{
	$page = new page_minion("New Writing");
	
	$page->addjs( '/js/tinymce/tinymce.min.js' ) ;
	$page->addjs( '/js/invoketinymce.js') ;
	
	$page->header();
	
	$isotope = new Isotope($page);
	
	$isotope->AddTile(new UploadWritingTile());
	
	//$page->html_minion->content->AddElement($isotope);
}


$tile = new UploadWritingTile();
$tile->Serve();

if (!isset($_GET['ajax']))
    $page->footer();
?>