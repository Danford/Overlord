<?php

include( oe_frontend."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$page=new page_minion('Create Group') ;

$page->addjs( '/js/tinymce/tinymce.min.js' ) ;
$page->addjs( '/js/invoketinymce.js') ;
$page->addjs( '/js/group.create.js' ) ;
$page->header() ;

$form = new form_minion('create', 'group');

$form->header();

?> 

Group Name:<br /><br />
<?php 

$form->if_error( 'name', '%%ERROR%%<br />' ) ;

$form->text_field('name') ;

?>

<br/><br/>

Short Description:<br />
<i>A one-line description of what the group is all about.  255 characters maximum.</i><br/><br/>

<?php 

$form->if_error( 'short_desc', '%%ERROR%%<br />' ) ;

$form->text_field('short_desc') ;

?>
<br/><br/>

Group Privacy: <br />

Public - Anyone can see and join the group.  Anyone can see the membership and messages, but must join to post.<br />
Private - Group can be seen but members have to be invited or approved by an owner or moderator.  
Members and messages only visible to members.<br />
Secret - Invite-only.  Only members and those who have been invited by an owner or moderator can see the group exists. 
Members and messages only visible to members.<br /><br />


<?php 

$privacy['1'] = "Public" ;
$privacy['2'] = "Private" ;
$privacy['3'] = "Secret" ;

$form->select('type', $privacy ) ;
?>

<br/><br />

Details:<br />
<i>More information, including rules and such.</i><br /><br />

<?php 

$form->if_error( 'detail', '%%ERROR%%<br />' ) ;

?>

<div style="width: 650px">

<?php 

$form->textarea_field('detail') ;

?>

</div>

Receive Notifications of New Threads?  <?php 


$bit_options["1"] = "Yes" ;
$bit_options["0"] = "No" ;

$form->select( "notify_thread" , $bit_options ) ;  ?>

<br/><br/>

Receive Notifications of Messages in threads you've participated in?  <?php 

$form->select( "notify_message" , $bit_options ) ;  ?>

<br/><br/>

<?php $form->plain_button('Submit', 'Submit', '', 'onclick="verify()"' );

$form->footer();