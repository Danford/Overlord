<?php

class UploadPhotoTile extends GridTile {

	function __construct ($photo = NULL) {
		parent::__construct(NULL, GridOption::Large | GridOption::Destructable);
		
		require_once(oe_lib."form_minion.php");
		
		global $oepc;
		
		if( isset($photo) ){
		
			$pagetitle = 'Edit Photo' ;
			$form = new form_minion( 'editPhoto', 'photo');
			$form->fill_with_values( $photo ) ;
		
		} else {
			$pagetitle = 'Upload Photo' ;
			$form = new form_minion( 'uploadPhoto', 'photo');
		}
		
		$form->has_file();
		$form->if_error('photo', 'photoerror') ;
		$form->if_error('title', 'titleerror') ;
		$form->if_error('description', 'descriptionerrror') ;
		global $privacyoptions;
		
		$this->OpenBuffer();
		?>
		<div id="close-button"></div>
		<div id="expand-button"></div>
				
		<div id="upload-photo-form">
			<h2><?php echo $pagetitle; ?></h2>
			<?php $form->header();
			
		    	if( isset($photo) ){
		    	    $form->hidden( 'photo_id', $photo['id'] );
		    	    ?><img src="/<?php echo $oepc[0]['type']; ?>/<?php echo $oepc[0]['id']; ?>/photo/<?php echo $photo['id']; ?>.png" /><?php
		    	} else {
		    	   ?><p>Uploaded Image: <?php $form->file_input("photo") ; ?></p><?php 
		    	}
			?>
			<p>Privacy:	<?php $form->select("privacy", $privacyoptions ); ?></p>
			<p>Title: <?php $form->text_field("title" ); ?></p>
			<p>Description: <?php $form->text_field("description" ); ?></p>
			<p>Make Avatar <?php $form->checkbox("parentavatar") ; ?></p>
			<?php $form->submit_button(); ?>		
			<?php

			$form->footer(); // it's not just cosmetic, it does session cleanup.
			
			if( isset($photo) ) {
				$form = new form_minion( 'deletePhoto', 'photo');
				$form->header();
				$form->hidden( 'photo_id', $photo['id'] );
				$form->submit_button("Delete Photo");
				
				$form->footer();
			}
			/*
			<div class="button" onclick="UploadImage()">Upload</div>
			
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
			*/
			?>
			<script>
			function UploadImage() {
				jsonRequest(OE_API.photo.name, OE_API.photo.func.upload, {oe_module: 'profile', oe_module_id: '<?php echo $user->id; ?>', photo: $('input#photo').val(), privacy: $('select#privacy').val(), title: $('input#title').val(), description: $('input#description').val(), parentavatar: $('input#parentavatar').prop('checked')}, SetLocation);
			}
			
			function SetLocation(response) {
				$('#upload-photo-form').append(response);
			}
			</script>
		</div>

		<?php 
		
		
		$this->CloseBuffer();
	}
}
?>