<?php

include( oe_frontend."page_minion.php" ) ;

$page = new page_minion( $profile['screen_name']." - Videos" ) ;

$page->header() ;

?>

This page will list a user's videos.


<?php $page->footer() ; ?>