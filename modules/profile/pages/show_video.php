<?php

include( oe_frontend."page_minion.php" ) ;

$page = new page_minion( $profile['screen_name']." - Video - " ) ;

$page->header() ;

?>

This page will show a specific Video.


<?php $page->footer() ; ?>