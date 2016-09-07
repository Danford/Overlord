<?php


include( oe_frontend."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$page=new page_minion('Events') ;


$page->header() ;

?>

This page will eventually list the events that the user is attending or invited to, and public events in their area.  <Br/><br />

<a href="/group/create">Create a Group</a>




<?php 

$page->footer() ;

?>