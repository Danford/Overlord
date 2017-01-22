<?php
/*
 *  $page is provided
 * 
 *  use command get_threads( $start, $limit )
 * 
 *  or API call getThreads
 *  
 *  
 *          either will give you array of:
 *          
 *              `id` - int
 *              `title` - string
 *              `owner` - profile object
 *              `detail`, text/html
 *              `sticky`, 1 or 0
 *              `locked`, 1 or 0 
 *              `created`, timestamp
 *              `edited`, timestamp of when 'detail' got changed.
 *              `msgcount`, int  
 *              `last_updated`, timestamp
 *              
 *              
 */

include(oe_frontend."page_minion.php");

include(oe_isotope."isotope.php");
include(oe_isotope."thread_tile.php");

$page = new page_minion("Group Threads");

$page->header();

$page->js_minion->addFile(oe_js . "isotope.pkgd.min.js");
$page->js_minion->addFile(oe_js . "imagesloaded.pkgd.js");
$page->js_minion->addFile(oe_js . "isotope.js");

$threads = get_threads();

$isotope = new Isotope($page);

foreach ($threads as $thread)
	$isotope->AddTile(new ThreadTile($thread));	

$page->footer();

?>