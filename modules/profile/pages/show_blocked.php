<?php


include( oe_frontend."page_minion.php" ) ;

$page = new page_minion( "Blocked users" ) ;

$page->header() ;

?>

This page will show who you've blocked, and let you unblock them.


<?php $page->footer() ; ?>