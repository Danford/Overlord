<?php

include(oe_frontend."page_minion.php");

include(oe_isotope."isotope.php");
include(oe_isotope."upload_photo_tile.php");

include($oe_plugins['photo']."conf/conf.php");
include($oe_plugins['photo']."lib/photo.lib.php");

$isEdit = $uri[$pos] == 'edit';

if (!isset($_GET['ajax'])) {
	if ($isEdit) {
		$page = new page_minion("Edit Photo");
	} else {
		$page = new page_minion("Upload Photo");
	}
	
	$page->header();
}

if ($isEdit)
	$tile = new UploadPhotoTile($photo);
else 
	$tile = new UploadPhotoTile();

$tile->Serve();

if (!isset($_GET['ajax']))
	$page->footer(); 

?>