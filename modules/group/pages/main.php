<?php


include( oe_frontend."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$page=new page_minion('Groups') ;


$page->header() ;

?>

This page will eventually list the groups that the user is in and the groups that are in their area.  <Br/><br />

<a href="/group/create">Create a Group</a>




<?php 

$page->footer() ;

?>