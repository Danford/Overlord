<?php

include( oe_lib."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$page = new page_minion( "Edit Photo", '', '/js/profile.photoedit.js' ) ;

$page->html_minion->body->AddField( 'onload', 'albumselect()' );

$page->addjs( '/js/tinymce/tinymce.min.js' ) ;
$page->addjs( '/js/invoketinymce.js') ;

$form = new form_minion( "editphoto", "profile" ) ;

$vis[1] = "Friends Only" ;
$vis[0] = "All Registered Users" ;

$form->fill_with_values( $photo ) ;
$page->header() ;

$form->header() ;
$form->hidden( 'photo_id', $photo['photo_id'] ) ;
?>

Visibility: <br />

<?php $form->select( "private", $vis ) ;  ?>

<br /><br />

Title (optional): <br />

<?php $form->text_field( 'title' ) ; 
    $form->if_error('title', "<br/>%%ERROR%%" ) ;?>

<br /><br />

Description (optional, 255 characters maximum): <br />

<?php $form->text_field( "description", "width: 500px") ;
    $form->if_error('description', "<br/>%%ERROR%%" ) ; ?>

<br /><br />

Set as profile photo 

<?php $form->checkbox( "setavatar", '', 'onchange="avatarChecked()"' )?>

<br /><br />

Put in Album (optional): <br /><br />

<?php 

$albums['None'] = "No Album" ;
$albums['New'] = "New Album" ;

$db->query( "SELECT `album_id`, `title` FROM `profile_albums` WHERE `owner`='".$user->id."'" ) ;

while( ( $a = $db->assoc() ) != false ) {
    
    $albums[$a['album_id']] = $a['title'] ;
    
}

$form->select( "album", $albums, false, '', 'onchange="albumselect()"' ) ;

?>

<div id="newalbum" style="display:none">

    New Album Title: <br />
    
    <?php  $form->text_field( "new_album_title" ) ; 
        $form->if_error('new_album_title', "<br/>%%ERROR%%" ) ;?>
    
    <br /><br />
    
    Description: <br />
    
    <div style="width: 600px">
    
    <?php $form->textarea_field( 'new_album_description', 'width: 500 ; height: 150px' ) ; ?>
    </div>

</div>


<br />

<?php $form->plain_button( "Submit" , 'x', '' , 'onclick="verify()"' ); 

$form->footer() ;

?>


<?php $page->footer() ; ?>