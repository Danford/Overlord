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
include(oe_isotope."isotope.php");
include(oe_isotope."add_writing_tile.php");


if (!isset($_GET['ajax']))
{
	$page = new page_minion("New Writing");
	
	$page->addjs( '/js/tinymce/tinymce.min.js' ) ;
	$page->addjs( '/js/invoketinymce.js') ;
	
	$page->header();
}


$tile = new UploadWritingTile();
$tile->Serve();

if (!isset($_GET['ajax']))
    $page->footer();
?>