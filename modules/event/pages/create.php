<?php

include( oe_lib."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$page = new page_minion('Create Event') ;

$page->addjs( '/js/tinymce/tinymce.min.js' ) ;
$page->addjs( '/js/invoketinymce.js') ;
$page->addjs( '/js/group.create.js' ) ;
$page->header() ;

$form = new form_minion('create', 'event');

$form->header();


?>
Event Title: <br /><br />
<?php 

$form->if_error( 'title', '%%ERROR%%<br />' ) ;

$form->text_field('title') ;

?>
<br/><br/>
Event Subtitle: <br /><br />
<?php 

$form->if_error( 'subtitle', '%%ERROR%%<br />' ) ;

$form->text_field('subtitle') ;

?>
<br/><br/>
Start Date:<br /><br />

<?php 

$form->if_error( 'start', '%%ERROR%%<br />') ;

$form->date_input( 'start', date( 'Y', time() ), ( date( 'Y', time() ) + event_year_range ) ) ;

$hour[0] = 12 ;

for( $i = 1; $i < 12; $i++ ){
    $hour[$i] = $i ;
}

$form->select("start_hour", $hour ) ;

for( $i = 0 ; $i < 60; $i++ ){
    
    if( $i < 10 ) {
        $minute[$i] = "0".$i ;
    } else {
        $minute[$i] = $i ;
    }
}
$form->select( "start_minute", $minute ) ;

$form->select( "start_meridian", array( "AM" => "AM", "PM" => "PM" ) );


?><br /><br />

End Date:<br /><br />

<?php 

$form->if_error( 'end', '%%ERROR%%<br />') ;

$form->date_input( 'end', date( 'Y', time() ), ( date( 'Y', time() )+ event_year_range ) ) ;

$form->select("end_hour", $hour ) ;

$form->select( "end_minute", $minute ) ;

$form->select( "end_meridian", array( "AM" => "AM", "PM" => "PM" ) );


?>
<br /><br />
<?php 

if( isset( $group ) ){

    $form->hidden( 'group' , $group->id ) ;    

} else {
    
    print( 'Event Type: <br /><br />' );
    
    $form->if_error( 'type', '%%ERROR%%<br />') ;
    
    	// this will get modified when this page is cloned for groups
    
    $form->select( 'type', array( 1 => "Public", 2 => "Closed", 3 => "Secret" ) ) ;

} ?>

<br/><br/>

Address: <br/><br/>

<?php $form->text_field('address') ?>

<br/><br />

Cost: <br/><br />

<?php $form->text_field('cost') ?>

<br /><br />

Dress Code: <br/><br/>

<?php $form->text_field('dress') ?>

<br/><br />

Details: 
<div style="width:650px">

<?php $form->textarea_field('detail') ?>

</div>

<?php $form->submit_button() ;

$form->footer() ;

$page->footer() ;
