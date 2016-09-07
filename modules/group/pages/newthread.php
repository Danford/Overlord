<?php

include( oe_frontend."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$page=new page_minion( $group->name.' - Create Thread') ;

$page->addjs( '/js/tinymce/tinymce.min.js' ) ;
$page->addjs( '/js/invoketinymce.js') ;
$page->addjs( '/js/group.newthread.js' ) ;
$page->header() ;

$form = new form_minion('newthread', 'group');

$form->header();
$form->hidden( 'group_id', $group->id ) ;

?>

Subject: <br /><br />

<?php 

$form->if_error( 'subject', '%%ERROR%%<br />' ) ;

$form->text_field('subject', 'width: 400px') ;

if( $group->is_moderator() ) {
    ?>
    <br/><br/>
    
    Make Sticky:  <?php 
    
    $bit_options["0"] = "No" ;
    $bit_options["1"] = "Yes" ;
    
    $form->select( "sticky" , $bit_options ) ;  ?>
    
    <br/><br />
    
    <?php 
} 


$form->if_error('message', '%%ERROR%%<br/><br/>')
?>

Message: <br/><br/>

<?php

$form->if_error('message', '%%ERROR%%<br/><br/>') ;

?>
<div style="width: 650px">

<?php 
$form->textarea_field('message') ;

?>
</div>

<?php 

$form->plain_button('Submit', 'submitt', '', 'onclick="verify()"') ;


$form->footer();
$page->footer();