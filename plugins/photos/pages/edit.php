<?php
  /*
   * 
   *    values are provided as $original.  use $form->fill_with_values( $original )
   *    at the moment there is no prefill for the avatar fields
   *    but the functionality works for 'parentavatar' checkbox
   * 
   *    Form should contain
   *    
   *        oe_api          photo
   *        oe_call         edit
   *        oe_module       $oepc[0]['type']
   *        oe_module_id    $oepc[0]['id']
   *        oe_parent       to which module or plugin is this attached?
   *                        if omitted, oe_module is assumed
   *        oe_parent_id    to which item in that module or plugin?
   *                        if omitted, oe_module_id is assumed

            photo_id

    		privacy         int, options defined by module as $privacyoptions ;
    		title           string, optional 75 chars max, no html
    		description     text, optional 255 chars max, no html
    		
    		parentavatar    checkbox      only if admin ( $oepc[0]['admin'] == true ; )
    		
    		
    		album fields - not yet implemented
    		
    		  - may be part of an include, as any plugin that wants to use albums
    		    will want it.
    		
    		album - 'None', 'New', or ID
    		new_album_title - string, optional
    		new_album_description - string, optional 
    		
    		albumavatar     checkbox      only if admin and albums are set 
		                                  ( $oepcinconf[ $tier ]['photo']['useAlbum'] == true )
    		
   * 
   * 
   * 
   * 
   */

include(oe_frontend."page_minion.php");
include(oe_lib."form_minion.php");

$page = new page_minion("Edit Photo");

$page->header();
$form = new form_minion("editPhoto", "photo");

$form->fill_with_values( $photo ) ;

$form->if_error('title', 'titleerror') ;
$form->if_error('description', 'descriptionerrror') ;
global $privacyoptions;
?>

<div id="upload-photo-form">
	<?php 
	
	       $form->header(); 
	       $form->hidden( 'photo_id', $photo['id'] ) ;
	
	?>

	<p>Privacy:	<?php $form->select("privacy", $privacyoptions ); ?></p>
	<p>Title: <?php $form->text_field("title" ); ?></p>
	<p>Description: <?php $form->text_field("description" ); ?></p>
	<p>Make Avatar <?php $form->checkbox("parentavatar") ; ?></p>
	<?php $form->submit_button("Edit Photo" ); ?>
			
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