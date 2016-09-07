<?php

include( oe_frontend."page_minion.php" ) ;

$page = new page_minion( $profile['screen_name']." - Writing" ) ;

$page->header() ;

?>

This page will allow a user to upload a video, or set the following on both new and existing photos:

<ul>
	<li>Title</li>
	<li>Label</li>
	<li>Privacy (public/friends only)</li>
	<li>Which, if any, album it should be listed in</li>
	<li>If this photo is the user's avatar</li>

</ul>



<?php $page->footer() ; ?>