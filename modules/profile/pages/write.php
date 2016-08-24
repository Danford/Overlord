<?php

include( oe_lib."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$page = new page_minion( "New Writing", '', '/js/profile.new_writing.js' ) ;
$page->addjs( '/js/tinymce/tinymce.min.js' ) ;
$page->addjs( '/js/invoketinymce.js') ;
$page->html_minion->body->AddField( 'onload', 'albumselect()' );

$form = new form_minion( "new_writing", "profile" ) ;

$form->fill_with_values( $writing ) ;

$page->header() ;

$form->header() ;
?>

Visibility: <br />

<?php 
$vis[1] = "Friends Only" ;
$vis[0] = "All Registered Users" ;

$form->select( "private", $vis ) ;
?>

<br /><br />

Title: <br />

<?php $form->text_field( 'title' ) ;

    $form->if_error('title', "<br/>%%ERROR%%" ) ; ?>
<br /><br />

Subtitle (optional, 255 characters maximum): <br />

<?php $form->text_field( "subtitle", "width: 500px" ) ; 

    $form->if_error('subtitle', "<br/>%%ERROR%%" ) ;?>

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
    
    $form->if_error('new_album_title', "<br/>%%ERROR%%" ) ; ?>
    
    <br /><br />
    
    Description: <br />
        
    <div style="width: 650px">
    
    <?php $form->textarea_field( 'new_album_description', 'width: 500 ; height: 150px' ) ; ?>

	</div>

</div>

<div style="width: 650px">
<?php $form->textarea_field('content') ;?>
</div>

<?php $form->submit_button( "Add" ); ?>

