<?php

include( oe_frontend."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$page=new page_minion( 'Edit Group Profile' ) ;

$page->addjs( '/js/tinymce/tinymce.min.js' ) ;
$page->addjs( '/js/invoketinymce.js') ;
$page->addjs( '/js/group.create.js' ) ;
$page->header() ;

$form = new form_minion('edit', 'group');
$form->form_id = 'create' ; 
$form->fill_with_values( $info ) ;
$form->header();
$form->hidden( 'group_id', $group->id ) ;

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

$form->select('privacy', $privacy ) ;
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


<?php $form->plain_button('Submit', 'Submit', '', 'onclick="verify()"' );

$form->footer();