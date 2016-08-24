<?php

include( oe_lib."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$page = new page_minion('Edit Event') ;

$page->addjs( '/js/tinymce/tinymce.min.js' ) ;
$page->addjs( '/js/invoketinymce.js') ;
$page->addjs( '/js/group.create.js' ) ;
$page->header() ;

$form = new form_minion('edit', 'event');

$value = $db->get_assoc("SELECT `event_id`, `title`, `subtitle`, `start`, `end`, `type`, `group`, `location`, `address`, `cost`, `dress`, `detail` FROM event_profile where `event_id`='".$event->id."'") ;

$value[ 'start_minute'] = substr( $value['start'], -2 ) ;
$value[ 'end_minute'] = substr( $value['start'], -2 ) ;

$starthour = substr($value['start'], 11, 2 ) ;
$endhour = substr($value['start'], 11, 2 ) ;

if( substr($starthour, 0, 1 ) =='0' ){ $starthour = substr( $starthour, 1,1 ) ; }
if( substr($endhour, 0, 1 ) =='0' ){ $endhour = substr( $endhour, 1,1 ) ; }


if( $starthour == 0 ){
    $value['start_meridian'] = 'AM' ;
    $value['start_hour'] = '12' ;
} elseif( $starthour < 12 ){
    $value['start_meridian'] = 'AM' ;
    $value['start_hour'] = $starthour;
} elseif( $starthour == 12 ) {
    $value['start_meridian'] = 'PM' ;
    $value['start_hour'] = '12' ;
} else {
    $value['start_meridian'] = 'PM' ;
    $value['start_hour'] = $starthour - 12 ;
}

if( $endhour == 00 ){
    $value['end_meridian'] = 'AM' ;
    $value['end_hour'] == '12' ;
} elseif( $endhour < 12 ){
    $value['end_meridian'] = 'AM' ;
    $value['end_hour'] = $endhour;
} elseif( $endhour == 12 ) {
    $value['end_meridian'] = 'PM' ;
    $value['end_hour'] = '12' ;
} else {
    $value['end_meridian'] = 'PM' ;
    $value['end_hour'] = $endhour - 12 ;
}
    $form->fill_with_values( $value ) ;
    
    // 9999-99-99 99:99


print( $starthour." ".$value['start_hour']."<br/><br/>" ) ;    
    
$form->header();
$form->hidden( 'event_id', $event->id ) ;
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

Event Type: <br /><br />

<?php 
$form->if_error( 'type', '%%ERROR%%<br />') ;

	// this will get modified when this page is cloned for groups

$form->select( 'type', array( 1 => "Public", 2 => "Closed", 3 => "Secret" ) ) ;

?><br/><br/>

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
