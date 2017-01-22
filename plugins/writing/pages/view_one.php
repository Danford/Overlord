<?php
/*
 *      use
 * 
 *      `id`,
 *      `title`,
 *      `subtitle`,
 *      `privacy`, 
 *      `timestamp`,
 *      `last_updated`
 *      
 *      
 * 
 *  include( $oe_plugins['writing'].'conf/plugin.conf' ) before invoking likes and comments plugins.
 * 
 * 
 * 
 */

include(oe_frontend."page_minion.php");
include(oe_lib."form_minion.php");

include(oe_isotope."isotope.php");
include(oe_isotope."writing_tile.php");

if (!isset($_GET['ajax'])) {
	$page = new page_minion("View Writing");
	$page->header();

	$isotope = new Isotope($page);
}

$writingTile = new WritingTile($writing);


if (!isset($_GET['ajax'])) {
	$writingTile->SetFullscreen();
	$isotope->AddTile($writingTile, "photo");

	$page->footer();
}
else
	$writingTile->Serve();