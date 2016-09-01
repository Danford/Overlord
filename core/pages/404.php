<?php

include( oe_lib.'page_minion.php' ) ;

$p = new page_minion( "404 - Not Found" ) ;

$p->header( false ) ;

?>

This is the 404 page.<br /><br />Ooops.

<?php $p->footer() ; ?>