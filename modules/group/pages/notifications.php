<?php

include( oe_frontend.'page_minion.php' ) ;
include( oe_lib.'form_minion.php' ) ;

$page = new page_minion( 'Group - '.$group->name.' - Notification Options' );
$form = new form_minion( 'notifications', 'group' ) ;
$form->fill_with_values($notifications) ;
$page->header() ;

$form->header();
$form->hidden( 'group_id', $group->id ) ;

?>


Receive Notifications of New Threads?  <?php 


$bit_options["1"] = "Yes" ;
$bit_options["0"] = "No" ;

$form->select( "notify_thread" , $bit_options ) ;  ?>

<br/><br/>

Receive Notifications of Messages in threads you've participated in?  <?php 

$form->select( "notify_message" , $bit_options ) ;  ?>

<br/><br/>

<?php 

$form->submit_button( 'Update Notification Options' ) ;
$form->footer() ;

?>