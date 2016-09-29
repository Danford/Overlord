<?php

include(oe_frontend."page_minion.php");
include(oe_lib."form_minion.php");

if( $uri[$pos] == 'edit' ){
    
    $pagetitle = 'Edit Photo' ;
    $form = new form_minion( 'editPhoto', 'photo');
    $form->fill_with_values( $photo ) ;
    
} else {
    
    $pagetitle = 'Upload Photo' ;
    $form = new form_minion( 'uploadPhoto', 'photo');
}

$page = new page_minion($pagetitle);
$page->header();

$form->has_file();
$form->if_error('photo', 'photoerror') ;
$form->if_error('title', 'titleerror') ;
$form->if_error('description', 'descriptionerrror') ;
global $privacyoptions;
?>

<div id="upload-photo-form">
	<?php $form->header();
	
    	if( $uri[$pos] == 'edit' ){
    	    $form->hidden( 'photo_id', $photo['id'] );
    	} else {
    	   ?><p>Uploaded Image: <?php $form->file_input("photo") ; ?></p><?php 
    	}
	?>
	<p>Privacy:	<?php $form->select("privacy", $privacyoptions ); ?></p>
	<p>Title: <?php $form->text_field("title" ); ?></p>
	<p>Description: <?php $form->text_field("description" ); ?></p>
	<p>Make Avatar <?php $form->checkbox("parentavatar") ; ?></p>
	<?php $form->submit_button( $pagetitle ); ?>
			
	<p>Album: 
		<select name="album">
			<option value="None">None</option>
			<option value="New">New</option>
			<?php foreach ($albums as $album): ?>
			<option value="<?php echo $album['id']; ?>"><?php echo $album['name']; ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>Album Title: <input name="new_album_title" type="text"/></p>
	<p>Album Description: <input name="new_album_description" type="text"/></p>
	<input name="albumavatar" title="Album Avatar" type="checkbox"/>
</div>

<?php 
	$form->footer(); // it's not just cosmetic, it does session cleanup.
    $page->footer();
?>